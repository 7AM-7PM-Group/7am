<?php

namespace App\Http\Controllers;

use App\Services\EsbApiAuth;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class ESBAuthController extends Controller
{
    public function login()
    {
        try {
            $esb = new EsbApiAuth(
                config('ESB.esb_username'),
                config('ESB.esb_password'),
                config('ESB.esb_base_url'),
                config('ESB.esb_environment', 'sandbox')
            );

            $response = $esb->authenticate();

            Session::put('esb_access_token', $esb->getAccessToken());
            Session::put('esb_refresh_token', $esb->getRefreshToken());

            if (isset($response['data']['expires_in'])) {
                Session::put('esb_expires_in', now()->addSeconds($response['data']['expires_in']));
            }

            return redirect()->route('dashboard')->with('success', 'Terhubung ke ESB!');
        } catch (Exception $exception) {
            Log::error('ESB login failed: ' . $exception->getMessage());
            return redirect()->route('dashboard')->with('error', 'Gagal terhubung ke ESB: ' . $exception->getMessage());
        }
    }

    public function refreshToken()
    {
        try {
            $refreshToken = Session::get('esb_refresh_token');

            if (!$refreshToken) {
                return redirect()->route('dashboard')->with('error', 'Refresh token ESB tidak ditemukan. Silakan login ulang.');
            }

            $esb = new EsbApiAuth(
                config('ESB.esb_username'),
                config('ESB.esb_password'),
                config('ESB.esb_base_url'),
                config('ESB.esb_environment', 'sandbox')
            );
            $esb->setRefreshToken($refreshToken);

            $response = $esb->refreshAccessToken();

            Session::put('esb_access_token', $esb->getAccessToken());
            Session::put('esb_refresh_token', $esb->getRefreshToken());

            if (isset($response['data']['expires_in'])) {
                Session::put('esb_expires_in', now()->addSeconds($response['data']['expires_in']));
            }

            return redirect()->back()->with('success', 'Refresh token ESB berhasil diperbarui.');
        } catch (Exception $exception) {
            Log::error('ESB refresh failed: ' . $exception->getMessage());
            return redirect()->route('dashboard')->with('error', 'Gagal refresh token ESB: ' . $exception->getMessage());
        }
    }

    public function logout()
    {
        Session::forget(['esb_access_token', 'esb_refresh_token', 'esb_expires_in']);
        return redirect()->route('dashboard')->with('success', 'Koneksi ESB telah diputus.');
    }
}
