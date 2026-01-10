<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use App\SchoolSetting;

class SmsHelper
{
    /**
     * Increment the SMS sent count in settings.
     */
    private static function incrementSmsCount()
    {
        try {
            $currentCount = (int) SchoolSetting::get('sms_sent_count', 0);
            SchoolSetting::set('sms_sent_count', $currentCount + 1, 'integer', 'Total number of SMS messages sent through the system');
        } catch (\Exception $e) {
            Log::warning('Failed to increment SMS count: ' . $e->getMessage());
        }
    }

    /**
     * Send SMS using InboxIQ API
     *
     * @param string $destination Phone number with country code (e.g., +27123456789)
     * @param string $messageText The SMS message content
     * @return array Response with 'success' boolean and 'message' string
     */
    public static function sendSms($destination, $messageText, $maxRetries = 3)
    {
        if (! preg_match('/^\+\d{10,15}$/', $destination)) {
            return ['success' => false, 'message' => 'Invalid phone number'];
        }

        $apiUrl  = 'https://api.inboxiq.co.zw/api/v1/send-sms';
        $username = env('INBOXIQ_USERNAME');
        $password = env('INBOXIQ_PASSWORD');
        $apiKey   = env('INBOXIQ_API_KEY');

        if (! $username || ! $password || ! $apiKey) {
            Log::error('InboxIQ credentials missing');
            return ['success' => false, 'message' => 'SMS configuration error'];
        }

        $authToken = base64_encode("$username:$password");

        $data = [
            'destination' => $destination,
            'messageText' => $messageText
        ];

        $lastHttpCode = 0;
        $lastResponse = null;

        // Retry logic for intermittent API failures
        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            $ch = curl_init($apiUrl);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Basic ' . $authToken,
                    'key: ' . $apiKey,
                    'Content-Type: application/json'
                ]
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if (curl_errno($ch)) {
                Log::error('SMS cURL error', ['error' => curl_error($ch), 'attempt' => $attempt]);
                curl_close($ch);
                if ($attempt < $maxRetries) {
                    sleep(1); // Wait 1 second before retry
                    continue;
                }
                return ['success' => false, 'message' => 'SMS sending failed after ' . $maxRetries . ' attempts'];
            }

            curl_close($ch);
            $lastHttpCode = $httpCode;
            $lastResponse = $response;

            // Success - exit retry loop
            if ($httpCode >= 200 && $httpCode < 300) {
                break;
            }

            // Server error (500) - retry
            if ($httpCode >= 500 && $attempt < $maxRetries) {
                Log::warning('SMS API returned 500, retrying...', ['attempt' => $attempt, 'destination' => $destination]);
                sleep(1); // Wait 1 second before retry
                continue;
            }

            // Client error (4xx) or final attempt - don't retry
            break;
        }

        $httpCode = $lastHttpCode;
        $response = $lastResponse;

        $responseData = json_decode($response, true) ?? $response;

        if ($httpCode >= 200 && $httpCode < 300) {
            Log::info('SMS sent', compact('destination', 'httpCode'));
            
            // Increment SMS sent count
            self::incrementSmsCount();
            
            return [
                'success' => true, 
                'message' => 'SMS sent successfully',
                'http_code' => $httpCode,
                'response' => $responseData
            ];
        }

        // Map HTTP status codes to user-friendly messages
        $errorMessages = [
            400 => 'Bad Request - Invalid request parameters or missing required fields',
            401 => 'Unauthorized - Missing or invalid authentication credentials',
            403 => 'Forbidden - Insufficient permissions for this operation',
            404 => 'Not Found - Requested resource doesn\'t exist',
            429 => 'Too Many Requests - Rate limit exceeded',
            500 => 'Internal Server Error - Server error, please try again later',
        ];

        $errorMessage = $errorMessages[$httpCode] ?? "HTTP Error {$httpCode} - Unknown error occurred";

        Log::error('SMS HTTP error', [
            'http_code' => $httpCode,
            'error_message' => $errorMessage,
            'destination' => $destination,
            'response' => $response
        ]);

        return [
            'success' => false, 
            'message' => $errorMessage,
            'http_code' => $httpCode,
            'response' => $responseData
        ];
    }
}
