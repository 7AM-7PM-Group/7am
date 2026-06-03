<?php

namespace App\Services;

use Exception;

class JurnalApi
{
    private $hmacUsername;
    private $hmacSecret;
    private $apiUrl;

    public function __construct($username, $secret, $environment = 'sandbox')
    {
        $this->hmacUsername = $username;
        $this->hmacSecret = $secret;

        // Set the environment (default to sandbox)
        $this->apiUrl = $environment === 'production'
            ? 'https://api.mekari.com'
            : 'https://sandbox-api.mekari.com';
    }

    private function generateSignature($method, $path)
    {
        $dateString = gmdate('D, d M Y H:i:s') . ' GMT';
        $requestLine = $method . ' ' . $path . ' HTTP/1.1';
        $dataToSign = 'date: ' . $dateString . "\n" . $requestLine;
        $signature = base64_encode(hash_hmac('sha256', $dataToSign, $this->hmacSecret, true));

        return [
            'signature' => $signature,
            'date' => $dateString
        ];
    }

    public function request($method, $path, $body = null)
    {
        $signatureData = $this->generateSignature($method, $path);

        $hmacHeader = 'hmac username="' . $this->hmacUsername . '", algorithm="hmac-sha256", headers="date request-line", signature="' . $signatureData['signature'] . '"';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl . $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
        // WARNING: Disabling SSL verification is not recommended for production.
        // Bypass issues with self-signed certificates in dev/sandbox.
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        // Set headers dasar
        $headers = [
            'Authorization: ' . $hmacHeader,
            'Date: ' . $signatureData['date'],
            'Accept: application/json'
        ];

        // Jika ada body, ubah ke JSON dan tambahkan Content-Type
        if (!empty($body)) {
            $jsonBody = json_encode($body);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Content-Length: ' . strlen($jsonBody);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);


        $response = curl_exec($ch);
        // dd($response);
        if (curl_errno($ch)) {
            throw new Exception('cURL Error: ' . curl_error($ch));
        } else {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            return json_decode($response, true);
        }

        curl_close($ch);
    }
}
