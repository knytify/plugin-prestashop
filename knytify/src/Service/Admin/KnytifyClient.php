<?php

namespace Knytify\Service\Admin;

use Symfony\Component\Form\AbstractType;
use Knytify\Service\Validation\KnytifyValidation;
use Symfony\Component\HttpFoundation\Request;

class KnytifyClient extends AbstractType
{
    /**
     * A lightweight, curl-based, client for Knytify back-end API.
     */

    const BACK_URL = "https://back.knytify.com";
    protected ?string $api_key = null;
    protected ?int $status_code = null;
    protected $response = null; // mixed
    protected ?string $error = null;

    public function getUser(): bool
    {
        return $this->query('/me/');
    }

    public function postDomain(string $domain): bool
    {
        return $this->query('/me/domain/', ['domain' => $domain]);
    }

    public function getStatsGraphs(Request $request): bool
    {
        return $this->query('/stats/graphs/');
    }

    public function getStatsAdvanced(Request $request): bool //array $dimensions, string $interval = 'daily', \DateTime $from_date = null)
    {
    //     $dimensions_str = implode(",", $dimensions);
        $from_date = $request->get('from_date', null);

        if (empty($from_date)) {
            $from_date = (new \DateTime('1 month ago'))->format('Y-m-d');
        }

        return $this->query('/stats/advanced/', [
            "dimensions" => $request->query->get('dimensions'),
            "interval" => $request->query->get('interval', 'daily'),
            "from_date" => $from_date
        ], 'GET');
    }

    public function query($path, $payload = null, string $method = null): bool
    {
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
                if (!empty($curl_response) && !empty($curl_response['detail'])) {
                    $this->error = is_array($curl_response['detail']) ? json_encode($curl_response['detail']) : $curl_response['detail'];
                } else {
                    $this->error = "CURL Error: " . curl_error($ch);
                }
            }
        } catch (\Throwable $th) {
            $success = false;
            $this->error = "Exception: " . $th->getMessage();
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
