<?php

namespace App\services;

use CURLFile;
use Illuminate\Support\Facades\Log;

class bodService
{
    protected string $baseUrl;
    protected string $apiKey;
    protected string $apiSecret;

    public static function sendRawatJalan(array $payload)
    {
        $baseUrl   = rtrim(config('services.bod.base_url'), '/');
        $apiKey    = config('services.bod.api_key');
        $apiSecret = config('services.bod.api_secret');

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => $baseUrl . '/rawat-jalan/addData',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'X-API-Key: ' . $apiKey,
                'X-API-Secret: ' . $apiSecret,
            ],
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_TIMEOUT        => 15,
        ]);

        $response = curl_exec($curl);
        $error    = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        Log::info('BOD Rawat Jalan', [
            'payload'   => $payload,
            'http_code' => $httpCode,
            'response'  => $response,
            'error'     => $error,
        ]);

        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'code'    => $httpCode,
            'data'    => json_decode($response, true),
            'error'   => $error,
        ];
    }

    public static function sendRawatInap(array $payload)
    {
        $baseUrl   = rtrim(config('services.bod.base_url'), '/');
        $apiKey    = config('services.bod.api_key');
        $apiSecret = config('services.bod.api_secret');

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => $baseUrl . '/rawat-inap/addData',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'X-API-Key: ' . $apiKey,
                'X-API-Secret: ' . $apiSecret,
            ],
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_TIMEOUT        => 15,
        ]);

        $response = curl_exec($curl);
        $error    = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        Log::info('BOD Rawat Inap', [
            'payload'   => $payload,
            'http_code' => $httpCode,
            'response'  => $response,
            'error'     => $error,
        ]);

        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'code'    => $httpCode,
            'data'    => json_decode($response, true),
            'error'   => $error,
        ];
    }

    public static function updateDataPasien(string $noRegistrasi, array $payload)
    {
        $baseUrl   = rtrim(config('services.bod.base_url'), '/');
        $apiKey    = config('services.bod.api_key');
        $apiSecret = config('services.bod.api_secret');

        $url = $baseUrl . '/rawat-inap/update/' . $noRegistrasi;

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => 'PUT', // atau PATCH jika API BOD pakai PATCH
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'X-API-Key: ' . $apiKey,
                'X-API-Secret: ' . $apiSecret,
            ],
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_TIMEOUT        => 15,
        ]);

        $response = curl_exec($curl);
        $error    = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        Log::info('BOD Update Rawat Inap', [
            'url'        => $url,
            'payload'    => $payload,
            'http_code'  => $httpCode,
            'response'   => $response,
            'error'      => $error,
        ]);

        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'code'    => $httpCode,
            'data'    => json_decode($response, true),
            'error'   => $error,
        ];
    }
}
