<?php

namespace App\Services;

use Exception;

class EsbApiAuth
{
    private $username;
    private $password;
    private $apiUrl;
    private $accessToken;
    private $refreshToken;
    private $accessTokenExpiresAt;
    private $refreshTokenExpiresAt;

    private const ACCESS_TOKEN_BUFFER_SECONDS = 300;
    private const REFRESH_TOKEN_BUFFER_SECONDS = 3600;

    public function __construct($username, $password, $baseUrl = null, $environment = 'sandbox')
    {
        $this->username = $username;
        $this->password = $password;
        $this->apiUrl = $baseUrl && is_string($baseUrl)
            ? rtrim($baseUrl, '/')
            : ($environment === 'production'
                ? 'https://services.esb.co.id/core'
                : 'https://stg7.esb.co.id/core-stg');
    }

    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    public function setAccessToken($token)
    {
        $this->accessToken = $token;
    }

    public function setRefreshToken($token)
    {
        $this->refreshToken = $token;
    }

    public function isAccessTokenValid()
    {
        return !empty($this->accessToken)
            && !empty($this->accessTokenExpiresAt)
            && $this->accessTokenExpiresAt > time() + self::ACCESS_TOKEN_BUFFER_SECONDS;
    }

    public function isRefreshTokenValid()
    {
        return !empty($this->refreshToken)
            && !empty($this->refreshTokenExpiresAt)
            && $this->refreshTokenExpiresAt > time() + self::REFRESH_TOKEN_BUFFER_SECONDS;
    }

    public function authenticateIfNeeded()
    {
        if ($this->isAccessTokenValid()) {
            return;
        }

        if ($this->isRefreshTokenValid()) {
            try {
                $this->refreshAccessToken();
                return;
            } catch (Exception $e) {
                // refresh token may no longer be valid, fall back to full authentication
            }
        }

        $this->authenticate();
    }

    public function authenticate()
    {
        $ch = curl_init();
        $authUrl = $this->apiUrl . '/auth/login';

        curl_setopt($ch, CURLOPT_URL, $authUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'username' => $this->username,
            'password' => $this->password,
        ]));

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            curl_close($ch);
            throw new Exception('cURL Error: ' . curl_error($ch));
        }

        curl_close($ch);

        $result = json_decode($response, true);

        if ($httpCode >= 400) {
            throw new Exception('Authentication failed: ' . ($result['message'] ?? 'Unknown error'));
        }

        $this->storeTokens($result);

        return $result;
    }

    public function refreshAccessToken()
    {
        if (!$this->refreshToken) {
            throw new Exception('No refresh token available. Re-authenticate first.');
        }

        $ch = curl_init();
        $refreshUrl = $this->apiUrl . '/auth/refresh';

        curl_setopt($ch, CURLOPT_URL, $refreshUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'refresh_token' => $this->refreshToken,
        ]));

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            curl_close($ch);
            throw new Exception('cURL Error: ' . curl_error($ch));
        }

        curl_close($ch);

        $result = json_decode($response, true);

        if ($httpCode >= 400) {
            throw new Exception('Token refresh failed: ' . ($result['message'] ?? 'Unknown error'));
        }

        $this->storeTokens($result);

        return $result;
    }

    private function storeTokens(array $result)
    {
        if (isset($result['data']['access_token'])) {
            $this->accessToken = $result['data']['access_token'];
            $this->accessTokenExpiresAt = time() + 3600;
        }

        if (isset($result['data']['refresh_token'])) {
            $this->refreshToken = $result['data']['refresh_token'];
            $this->refreshTokenExpiresAt = time() + 86400;
        }

        if (isset($result['data']['expires_in'])) {
            $this->accessTokenExpiresAt = time() + intval($result['data']['expires_in']);
        }

        if (isset($result['data']['refresh_expires_in'])) {
            $this->refreshTokenExpiresAt = time() + intval($result['data']['refresh_expires_in']);
        }
    }
}
