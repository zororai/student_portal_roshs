<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class SmsHelper
{
    /**
     * Send SMS using InboxIQ API
     *
     * @param string $destination Phone number with country code (e.g., +27123456789)
     * @param string $messageText The SMS message content
     * @return array Response with 'success' boolean and 'message' string
     */
    public static function sendSms($destination, $messageText)
    {
        $apiUrl = 'https://api.inboxiq.co.zw/api/v1/send-sms';
        $username = env('INBOXIQ_USERNAME', 'username');
        $password = env('INBOXIQ_PASSWORD', 'password');
        $apiKey = env('INBOXIQ_API_KEY', 'YOUR_API_KEY');

        // Prepare authorization header
        $authToken = base64_encode($username . ':' . $password);

        // Prepare data
        $data = [
            'destination' => $destination,
            'messageText' => $messageText
        ];

        try {
            // Initialize cURL
            $ch = curl_init($apiUrl);

            // Set cURL options
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Basic ' . $authToken,
                'key: ' . $apiKey,
                'Content-Type: application/json'
            ]);

            // Execute request
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Check for cURL errors
            if (curl_errno($ch)) {
                $error = curl_error($ch);
                curl_close($ch);

                Log::error('SMS sending failed (cURL error)', [
                    'error' => $error,
                    'destination' => $destination
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to send SMS: ' . $error
                ];
            }

            curl_close($ch);

            // Check HTTP response code
            if ($httpCode >= 200 && $httpCode < 300) {
                Log::info('SMS sent successfully', [
                    'destination' => $destination,
                    'response' => $response
                ]);

                return [
                    'success' => true,
                    'message' => 'SMS sent successfully',
                    'response' => json_decode($response, true)
                ];
            } else {
                Log::error('SMS sending failed (HTTP error)', [
                    'http_code' => $httpCode,
                    'response' => $response,
                    'destination' => $destination
                ]);

                return [
                    'success' => false,
                    'message' => 'Failed to send SMS. HTTP Code: ' . $httpCode,
                    'response' => json_decode($response, true)
                ];
            }

        } catch (\Exception $e) {
            Log::error('SMS sending exception', [
                'exception' => $e->getMessage(),
                'destination' => $destination
            ]);

            return [
                'success' => false,
                'message' => 'Exception occurred: ' . $e->getMessage()
            ];
        }
    }
}
