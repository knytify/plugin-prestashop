<?php

namespace Knytify\Service\Admin;

use Symfony\Component\Form\AbstractType;
use Knytify\Service\Validation\KnytifyValidation;
use Symfony\Component\HttpFoundation\Request;

class KnytifyClient extends AbstractType
{
    /**
     * A client for Knytify back-end API.
     */
    public const BACK_URL = 'https://back.knytify.com';

    protected ?string $api_key = null;

    protected ?int $status_code = null;

    protected $response = null; // mixed

    protected ?string $error = null;

    public function setupPassword(string $email, string $password): bool
    {
        /**
         * On subscription, the account is automatically created, but we need still the user to setup a password to
         * complete the account creation and retrieve an api key, which will allow the plug-in to communicate with Knytify.
         * The route is the same as for login.
         */
        $validator = new KnytifyValidation();
        if (!$validator->validateEmail($email) || !$validator->validatePassword($password)) {
            $this->error = $validator->getError();
            $this->status_code = 400;

            return false;
        }

        return $this->login($email, $password);
    }

    public function login(string $email, string $password, ?string $domain = null): bool
    {
        /**
         * Log-in if the account already exists, and retrieve the api-key.
         */
        $validator = new KnytifyValidation();
        if (!$validator->validateEmail($email) || !$validator->validatePassword($password)) {
            $this->error = $validator->getError();
            $this->status_code = 400;

            return false;
        }

        $body = [
            'username' => $email,
            'password' => $password,
            'authorize_domain' => $domain,
        ];

        $success = $this->query('/auth/login-for-plugin/', $body);

        if ($success) {
            $this->api_key = $this->response['api_key'];
        }

        return $success;
    }

    public function getUser(): bool
    {
        /*
         * This route is useful to test wether the current Api-Key is valid.
         */
        return $this->query('/me/');
    }

    public function getStatsRecap(Request $request): bool
    {
        /*
         * Retrieves data that is processed in Knytify backend,
         * to be displayed in the stats page.
         */
        return $this->query('/stats/recap/');
    }

    public function getStatsAdvanced(Request $request): bool
    {
        /**
         * This method will be useful to explore stats in more detail.
         */
        $from_date = $request->get('from_date', null);

        if (empty($from_date)) {
            $from_date = (new \DateTime('1 month ago'))->format('Y-m-d');
        }

        return $this->query('/stats/advanced/', [
            'dimensions' => $request->query->get('dimensions'),
            'interval' => $request->query->get('interval', 'daily'),
            'from_date' => $from_date
        ], 'GET');
    }

    public function query($path, $payload = null, string $method = null): bool
    {
        /*
         * Generic HTTP request helper
         * It injects the Api Key
         */

        $this->resetResponse();

        $success = true;

        $headers = [];

        if (!empty($this->api_key)) {
            $headers[] = 'Api-Key: ' . $this->api_key;
        }

        $ch = curl_init();

        $url = KnytifyClient::BACK_URL . $path;

        try {
            if (!empty($payload)) {
                if ($method == 'GET') {
                    $url .= '?' . http_build_query($payload);
                } else {
                    $payload = json_encode($payload);

                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                    $headers[] = 'Content-Type: application/json';
                    $headers[] = 'Content-Length: ' . strlen($payload);
                }
            }

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false); // Do not return headers
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true); // Prevents usage of a cached version of the URL

            $curl_response = curl_exec($ch);

            $this->status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            $success = $this->status_code >= 200 && $this->status_code < 300;

            if (!empty($curl_response)) {
                $curl_response = json_decode($curl_response, true);
            }

            if ($success) {
                $this->response = $curl_response;
            } else {
                if ($this->status_code == 401) {
                    $this->error = 'invalid_api_key';
                } elseif (!empty($curl_response) && !empty($curl_response['detail'])) {
                    $this->error = is_array($curl_response['detail']) ? json_encode($curl_response['detail']) : $curl_response['detail'];
                } else {
                    $this->error = 'CURL Error: ' . curl_error($ch);
                }
            }
        } catch (\Throwable $th) {
            $success = false;
            $this->error = 'Exception: ' . $th->getMessage();
            // throw $th;
        } finally {
            curl_close($ch);
        }

        return $success;
    }

    protected function resetResponse()
    {
        $this->status_code = null;
        $this->response = null;
        $this->error = null;
    }

    /**
     * Get the value of status_code
     */
    public function getStatusCode()
    {
        return $this->status_code;
    }

    /**
     * Get the value of response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Get the value of error
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Set the value of api_key
     *
     * @return  self
     */
    public function setApiKey($api_key)
    {
        $this->api_key = $api_key;

        return $this;
    }
}
