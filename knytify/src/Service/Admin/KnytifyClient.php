<?php

namespace Knytify\Service\Admin;

use Symfony\Component\Form\AbstractType;
use Knytify\Entity\Admin\RegistrationEntity;
use Knytify\Entity\Admin\LoginEntity;


class KnytifyClient extends AbstractType
{
    const BACK_URL = "https://back.knytify.com/";
    protected ?string $api_key = null;
    protected ?int $status_code = null;
    protected mixed $response = null;
    protected ?string $error = null;


    public function __construct(?string $api_key = null)
    {
        $this->api_key = $api_key;
    }

    public function register(RegistrationEntity $data): bool
    {

        // TODO: E-mail regex / Password regex validation.

        if ($data->getPassword() != $data->getPasswordCheck()) {
            $this->error = "Passwords must match";
            return false;
        }

        $success = $this->query('/me/', [
            "email" => $data->getUsername(),
            "password" => $data->getPassword(),
            "source" => "prestashop"
        ]);

        if ($success) {
            $this->api_key = $this->response;
        }

        return $success;
    }

    public function login(LoginEntity $data): bool
    {
        $success = $this->query('/auth/login-for-plugin', [
            "email" => $data->getUsername(),
            "password" => $data->getPassword()
        ]);

        if ($success) {
            $this->api_key = $this->response;
        }

        return $success;
    }

    public function getUser(): bool
    {
        return $this->query('/me/');
    }


    public function getStats(): bool
    {

        return $this->query('/stats/graphs/');
    }

    public function query($path, $payload = null): bool
    {
        $this->resetResponse();

        $headers = [];

        if (!empty($this->api_key)) {
            $headers[] = 'Api-Key: ' . $this->api_key;
        }

        $ch = curl_init(KnytifyClient::BACK_URL);

        try {

            if (!empty($payload)) {
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                $headers[] = 'Content-Type: application/json';
                $headers[] = 'Content-Length: ' . strlen($payload);
            }

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false); // Do not return headers
            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true); // Prevents usage of a cached version of the URL

            $curl_response = curl_exec($ch);

            $this->status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            $success = $this->status_code >= 200 && $this->status_code < 300;

            if ($success) {
                $this->response = empty($payload) ? $curl_response : json_decode($curl_response, true);
            } else {
                $this->error = curl_error($ch);
            }

        } catch (\Throwable $th) {
            throw $th;
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
}