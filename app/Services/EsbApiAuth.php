<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;

class EsbApiAuth
{
    private $username;

    private $password;

    private EsbApiRequestLog $requestLog;

    private const ACCESS_TOKEN_BUFFER_SECONDS = 300;

    private const REFRESH_TOKEN_BUFFER_SECONDS = 3600;

    // Key untuk Cache agar tidak bentrok antar user jika diperlukan
    private const CACHE_KEY_PREFIX = 'esb_api_tokens_';

    public function __construct($username, $password)
    {
        // dd(__FILE__);
        $this->username = $username;
        $this->password = $password;

        $this->requestLog = new EsbApiRequestLog;
    }

    public function getApiUrl($baseUrl = 'core')
    {
        if ($baseUrl === 'core') {
            return $this->coreUrl();
        } elseif ($baseUrl === 'fnb') {
            return $this->fnbUrl();
        } else {
            if (is_array($baseUrl)) {
                $baseUrl = json_encode($baseUrl);
            }
            throw new Exception('Invalid base URL: '.$baseUrl);
        }
    }

    public function coreUrl(): string
    {
        return config(
            'ESB.core.'.config('ESB.env')
        );
    }

    public function fnbUrl(): string
    {
        return config(
            'ESB.fnb.'.config('ESB.env')
        );
    }

    public function getStaticToken()
    {
        return config('ESB.static_token');
    }

    public function getAccessToken()
    {
        return Cache::get(self::CACHE_KEY_PREFIX.'access_token');
    }

    public function getRefreshToken()
    {
        return Cache::get(self::CACHE_KEY_PREFIX.'refresh_token');
    }

    public function setAccessToken($token)
    {
        Cache::put(self::CACHE_KEY_PREFIX.'access_token', $token, now()->addHour());
    }

    public function setRefreshToken($token)
    {
        Cache::put(self::CACHE_KEY_PREFIX.'refresh_token', $token, now()->addDays(30));
    }

    public function isAccessTokenValid()
    {
        $token = $this->getAccessToken();
        $expiry = Cache::get(self::CACHE_KEY_PREFIX.'access_token_expires_at');

        return ! empty($token)
            && ! empty($expiry)
            && $expiry > time() + self::ACCESS_TOKEN_BUFFER_SECONDS;
    }

    public function isRefreshTokenValid()
    {
        $token = $this->getRefreshToken();
        $expiry = Cache::get(self::CACHE_KEY_PREFIX.'refresh_token_expires_at');

        return ! empty($token)
            && ! empty($expiry)
            && $expiry > time() + self::REFRESH_TOKEN_BUFFER_SECONDS;
    }

    public function authenticateIfNeeded()
    {
        // dd(
        //     // $this->getRefreshToken(),
        //     $this->isRefreshTokenValid(),
        //     $this->isAccessTokenValid(),
        //     $this->getAccessToken(),
        //     $this->getRefreshToken(),
        //     Cache::get(self::CACHE_KEY_PREFIX.'access_token_expires_at'),
        //     Cache::get(self::CACHE_KEY_PREFIX.'refresh_token_expires_at'),
        //     time(),
        // );
        if ($this->isAccessTokenValid()) {
            return;
        }

        if ($this->isRefreshTokenValid()) {
            try {
                $this->refreshAccessToken();

                return;
            } catch (Exception $e) {
            }
        }

        $this->authenticate();
    }

    public function authenticate()
    {
        $ch = curl_init();
        $authUrl = $this->getApiUrl().'/auth/login';

        curl_setopt($ch, CURLOPT_URL, $authUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Bypass SSL certificate issues
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
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
            throw new Exception('cURL Error: '.curl_error($ch));
        }

        curl_close($ch);

        $result = json_decode($response, true);

        if ($httpCode >= 400) {
            $errorDetail = is_array($result) ? ($result['message'] ?? 'Authentication failed') : substr(strip_tags($response), 0, 100);
            $this->logAuthEvent([
                'method' => 'POST',
                'request_url' => $authUrl,
                'request_body' => json_encode(['username' => $this->username]),
                'request_source' => 'auth',
                'request_type' => 'auth_login',
                'response_code' => $httpCode,
                'response_body' => json_encode($result),
                'success' => false,
                'retried_with_refresh' => false,
                'error_message' => $errorDetail,
            ]);

            if (! config('app.debug')) {
                session()->flash('error', 'Authentication failed ('.$httpCode.'): '.$errorDetail);

                return false;
            } else {
                throw new Exception('Authentication failed ('.$httpCode.'): '.$errorDetail);
            }
        }

        $this->storeTokens($result);

        $this->logAuthEvent([
            'method' => 'POST',
            'request_url' => $authUrl,
            'request_body' => json_encode(['username' => $this->username]),
            'request_source' => 'auth',
            'request_type' => 'auth_login',
            'response_code' => $httpCode,
            'response_body' => json_encode($result),
            'success' => true,
            'retried_with_refresh' => false,
            'error_message' => null,
        ]);

        return $result;
    }

    public function refreshAccessToken()
    {
        $refreshToken = $this->getRefreshToken();
        if (! $refreshToken) {
            throw new Exception('No refresh token available. Re-authenticate first.');
        }

        $ch = curl_init();
        $refreshUrl = $this->getApiUrl().'/auth/refresh';

        curl_setopt($ch, CURLOPT_URL, $refreshUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        curl_setopt($ch, CURLOPT_HTTPGET, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Authorization: Bearer '.$refreshToken,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $errorMessage = 'cURL Error: '.curl_error($ch);
            curl_close($ch);

            $this->logAuthEvent([
                'method' => 'POST',
                'request_url' => $refreshUrl,
                'request_body' => json_encode(['refresh_token' => '***']),
                'request_source' => 'auth',
                'request_type' => 'auth_refresh',
                'response_code' => null,
                'response_body' => null,
                'success' => false,
                'retried_with_refresh' => false,
                'error_message' => $errorMessage,
            ]);

            if (config('app.debug')) {
                throw new Exception($errorMessage);
            }

            return;
        }

        curl_close($ch);

        $result = json_decode($response, true);

        if ($httpCode >= 400) {
            $this->logAuthEvent([
                'method' => 'POST',
                'request_url' => $refreshUrl,
                'request_body' => json_encode(['refresh_token' => '***']),
                'request_source' => 'auth',
                'request_type' => 'auth_refresh',
                'response_code' => $httpCode,
                'response_body' => json_encode($result),
                'success' => false,
                'retried_with_refresh' => false,
                'error_message' => $result['message'] ?? 'Token refresh failed',
            ]);

            return $this->authenticate();

            if (! config('app.debug')) {
                session()->flash('error', 'Token refresh failed: '.($result['message'] ?? 'Unknown error'));

                return false;
            } else {
                throw new Exception('Token refresh failed: '.($result['message'] ?? 'Unknown error'));
            }
        }

        $this->storeTokens($result);

        // dd($result, $this->getAccessToken(), $this->getRefreshToken()); // Hapus atau komentari baris ini
        // Jika refresh token ditolak (401), otomatis login ulang dengan username/password
        // Logika ini sudah ada di dalam blok if ($httpCode >= 400) di atas, jadi tidak perlu di sini.
        // Pastikan tidak ada dd() yang menghentikan eksekusi setelah token berhasil di-refresh.

        $this->logAuthEvent([
            'method' => 'POST',
            'request_url' => $refreshUrl,
            'request_body' => json_encode(['refresh_token' => '***']),
            'request_source' => 'auth',
            'request_type' => 'auth_refresh',
            'response_code' => $httpCode,
            'response_body' => json_encode($result),
            'success' => true,
            'retried_with_refresh' => false,
            'error_message' => null,
        ]);

        return $result;
    }

    private function storeTokens(array $result)
    {
        $data = $result['result'] ?? [];

        if (isset($data['accessToken'])) {
            Cache::put(self::CACHE_KEY_PREFIX.'access_token', $data['accessToken'], now()->addDays(7));
            // Gunakan expires_in dari API jika tersedia, jika tidak, default ke 1 jam (3600 detik)
            $expiresIn = $data['expires_in'] ?? 3600;
            Cache::put(self::CACHE_KEY_PREFIX.'access_token_expires_at', time() + $expiresIn, now()->addDays(7));
        }

        if (isset($data['refreshToken'])) {
            Cache::put(self::CACHE_KEY_PREFIX.'refresh_token', $data['refreshToken'], now()->addDays(30));
            Cache::put(self::CACHE_KEY_PREFIX.'refresh_token_expires_at', time() + 86400, now()->addDays(30)); // Refresh token biasanya memiliki masa berlaku yang lebih lama
        }
    }

    protected function logAuthEvent(array $data): void
    {
        $this->requestLog->store(array_merge([
            'method' => $data['method'] ?? 'POST',
            'request_url' => $data['request_url'] ?? null,
            'request_body' => $data['request_body'] ?? null,
            'user_id' => null,
            'request_source' => $data['request_source'] ?? 'auth',
            'request_type' => $data['request_type'] ?? 'auth_login',
            'response_code' => $data['response_code'] ?? null,
            'response_body' => $data['response_body'] ?? null,
            'success' => $data['success'] ?? false,
            'retried_with_refresh' => $data['retried_with_refresh'] ?? false,
            'error_message' => $data['error_message'] ?? null,
        ], $data));
    }
}
