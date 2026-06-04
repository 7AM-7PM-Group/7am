<?php

namespace App\Services;

use App\Services\EsbApiAuth;
use Exception;

class EsbApiRequest
{
    protected EsbApiAuth $auth;
    protected EsbApiRequestLog $log;

    public function __construct(EsbApiAuth $auth)
    {
        $this->auth = $auth;
        $this->log = new EsbApiRequestLog();
    }

    public function request($method, $path, $body = null, array $logContext = [])
    {
        $this->auth->authenticateIfNeeded();

        if (!$this->auth->getAccessToken()) {
            throw new Exception('Not authenticated. Call authenticate() first.');
        }

        $url = rtrim($this->auth->getApiUrl(), '/') . $path;
        $retried = false;

        try {
            $responseData = $this->executeRequest($method, $url, $body);

            if ($responseData['httpCode'] === 401 && $this->auth->getRefreshToken()) {
                $this->auth->refreshAccessToken();
                $retried = true;
                $responseData = $this->executeRequest($method, $url, $body);
            }
        } catch (Exception $exception) {
            $this->log->store([
                'method' => strtoupper($method),
                'request_url' => $url,
                'request_body' => $body ? json_encode($body) : null,
                'user_id' => $logContext['user_id'] ?? null,
                'request_source' => $logContext['request_source'] ?? 'system',
                'request_type' => $logContext['request_type'] ?? null,
                'response_code' => null,
                'response_body' => null,
                'success' => false,
                'retried_with_refresh' => $retried,
                'error_message' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        $this->log->store([
            'method' => strtoupper($method),
            'request_url' => $url,
            'request_body' => $body ? json_encode($body) : null,
            'user_id' => $logContext['user_id'] ?? null,
            'request_source' => $logContext['request_source'] ?? 'system',
            'request_type' => $logContext['request_type'] ?? null,
            'response_code' => $responseData['httpCode'],
            'response_body' => json_encode($responseData['result']),
            'success' => $responseData['httpCode'] < 400,
            'retried_with_refresh' => $retried,
            'error_message' => $responseData['httpCode'] >= 400 ? ($responseData['result']['message'] ?? 'API Error') : null,
        ]);

        if ($responseData['httpCode'] >= 400) {
            $errorMessage = $responseData['result']['message'] ?? 'API Error';
            throw new Exception("ESB API Error ({$responseData['httpCode']}): {$errorMessage}");
        }

        return $responseData['result'];
    }

    public function get($path, array $logContext = [])
    {
        return $this->request('GET', $path, null, $logContext);
    }

    public function post($path, $body = [], array $logContext = [])
    {
        return $this->request('POST', $path, $body, $logContext);
    }

    public function put($path, $body = [], array $logContext = [])
    {
        return $this->request('PUT', $path, $body, $logContext);
    }

    public function delete($path, array $logContext = [])
    {
        return $this->request('DELETE', $path, null, $logContext);
    }

    protected function executeRequest($method, $url, $body = null)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));

        $headers = [
            'Authorization: Bearer ' . $this->auth->getAccessToken(),
            'Accept: application/json',
            'Content-Type: application/json',
        ];

        if (!empty($body)) {
            $jsonBody = json_encode($body);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);
            $headers[] = 'Content-Length: ' . strlen($jsonBody);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            curl_close($ch);
            throw new Exception('cURL Error: ' . curl_error($ch));
        }

        curl_close($ch);

        return [
            'httpCode' => $httpCode,
            'result' => json_decode($response, true),
        ];
    }
}
