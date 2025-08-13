<?php

namespace App\services;

use CURLFile;

class fonnteServices
{
    public static function sendText($phone, $pesan)
    {
        $token = env('FONNTE_TOKEN'); // Ensure you have set your token in the .env file
        $curl = curl_init();
        // dd($phone, $pesan);

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $phone,
                'message' => $pesan,  // Optional: if you want to send an image
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: Y7cXJcaP9VKLVqJHW9vq'  //change TOKEN to your actual token
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        // echo $response;
        $result = json_decode($response, true);

        // Contoh sukses: "detail": "success! message in queue"
        if ($result && isset($result['detail']) && str_contains($result['detail'], 'success')) {
            return response()->json([
                'status' => 'success',
                'message' => 'Pesan berhasil dikirim',
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirim pesan: ' . ($result['detail'] ?? 'Unknown error'),
            ], 500);
        }
    }
}
