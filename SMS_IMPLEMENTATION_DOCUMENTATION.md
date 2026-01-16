# SMS Implementation Documentation

## Overview
This system uses **InboxIQ API** (Zimbabwe-based SMS gateway) to send SMS messages. The implementation includes retry logic, phone number validation, configurable templates, and usage tracking.

---

## 1. Core SMS Helper Class

**Location:** `app/Helpers/SmsHelper.php`

### Main Method: `sendSms()`

```php
public static function sendSms($destination, $messageText, $maxRetries = 3)
```

**Parameters:**
- `$destination` - Phone number with country code (e.g., `+263771234567`)
- `$messageText` - SMS message content
- `$maxRetries` - Number of retry attempts (default: 3)

**Returns:**
```php
[
    'success' => true/false,
    'message' => 'Status message',
    'http_code' => 200,
    'response' => [...] // API response data
]
```

### Key Features:

#### 1. Phone Number Validation
```php
if (! preg_match('/^\+\d{10,15}$/', $destination)) {
    return ['success' => false, 'message' => 'Invalid phone number'];
}
```

#### 2. API Configuration
```php
$apiUrl  = 'https://api.inboxiq.co.zw/api/v1/send-sms';
$username = env('INBOXIQ_USERNAME');
$password = env('INBOXIQ_PASSWORD');
$apiKey   = env('INBOXIQ_API_KEY');
```

#### 3. Authentication
```php
$authToken = base64_encode("$username:$password");
```

#### 4. Request Headers
```php
'Authorization: Basic ' . $authToken,
'key: ' . $apiKey,
'Content-Type: application/json'
```

#### 5. Request Body
```php
$data = [
    'destination' => $destination,
    'messageText' => $messageText
];
```

#### 6. Retry Logic
- Retries up to 3 times for cURL errors or 500 server errors
- 1-second delay between retries
- No retry for 4xx client errors

#### 7. SMS Counter
Automatically increments `sms_sent_count` in settings on successful send.

---

## 2. Environment Configuration

Add these to your `.env` file:

```env
INBOXIQ_USERNAME=your_username
INBOXIQ_PASSWORD=your_password
INBOXIQ_API_KEY=your_api_key
```

---

## 3. Database Settings

### SchoolSetting Model
**Location:** `app/SchoolSetting.php`

**Table:** `school_settings`

**Columns:**
- `setting_key` - Unique identifier
- `setting_value` - The value
- `setting_type` - Type (text, integer, textarea)
- `description` - Description

**Methods:**
```php
// Get a setting
SchoolSetting::get('sms_country_code', '+263');

// Set a setting
SchoolSetting::set('sms_country_code', '+263', 'text', 'Default country code');
```

### SMS-Related Settings:
1. **`sms_country_code`** - Default country code (e.g., `+263`)
2. **`sms_sent_count`** - Total SMS sent counter
3. **`sms_teacher_credentials_template`** - Template for teacher credentials SMS

---

## 4. Usage Examples

### Example 1: Simple SMS Send
```php
use App\Helpers\SmsHelper;

$result = SmsHelper::sendSms('+263771234567', 'Hello, this is a test message');

if ($result['success']) {
    echo "SMS sent successfully!";
} else {
    echo "Failed: " . $result['message'];
}
```

### Example 2: Send with Template
```php
use App\Helpers\SmsHelper;
use App\SchoolSetting;

// Get template from settings
$template = SchoolSetting::get(
    'sms_teacher_credentials_template',
    'RSH School: Teacher account created. Login: {phone}, Password: {password}.'
);

// Replace placeholders
$message = str_replace(
    ['{name}', '{phone}', '{password}'],
    ['John Doe', 'john@example.com', 'Pass123'],
    $template
);

// Send SMS
$result = SmsHelper::sendSms('+263771234567', $message);
```

### Example 3: Format Phone Number
```php
// Get country code from settings
$countryCode = SchoolSetting::get('sms_country_code', '+263');

// Format phone number
$phone = preg_replace('/\s+/', '', $phone); // Remove spaces
if (!preg_match('/^\+/', $phone)) {
    $phone = $countryCode . ltrim($phone, '0'); // Add country code
}

// Example: "0771234567" becomes "+263771234567"
```

### Example 4: Bulk SMS
```php
use App\Helpers\SmsHelper;

$phoneNumbers = ['+263771234567', '+263772345678', '+263773456789'];
$message = 'Important school announcement!';

$sent = 0;
$failed = 0;

foreach ($phoneNumbers as $phone) {
    $result = SmsHelper::sendSms($phone, $message);
    if ($result['success']) {
        $sent++;
    } else {
        $failed++;
        Log::warning('SMS failed', ['phone' => $phone, 'error' => $result['message']]);
    }
}

echo "Sent: $sent, Failed: $failed";
```

---

## 5. Controllers Using SMS

### A. SmsTestController
**Location:** `app/Http/Controllers/SmsTestController.php`

Simple test interface for sending SMS:
```php
public function send(Request $request)
{
    $request->validate([
        'phone' => 'required|string|regex:/^\+\d{10,15}$/',
        'message' => 'required|string|max:500'
    ]);

    $result = SmsHelper::sendSms($request->phone, $request->message);

    if ($result['success']) {
        return redirect()->back()->with('success', 'SMS sent successfully!');
    } else {
        return redirect()->back()->with('error', 'Failed: ' . $result['message']);
    }
}
```

### B. TeacherController
**Location:** `app/Http/Controllers/TeacherController.php`

Sends credentials to new teachers:
```php
private function sendCredentialsSms($phone, $name, $email, $password)
{
    // Get country code
    $countryCode = SchoolSetting::get('sms_country_code', '+263');
    
    // Format phone
    $phone = preg_replace('/\s+/', '', $phone);
    if (!preg_match('/^\+/', $phone)) {
        $phone = $countryCode . ltrim($phone, '0');
    }
    
    // Get template
    $messageTemplate = SchoolSetting::get(
        'sms_teacher_credentials_template',
        'RSH School: Teacher account created. Login: {phone}, Password: {password}.'
    );
    
    // Replace placeholders
    $message = str_replace(
        ['{name}', '{phone}', '{password}'],
        [$name, $email, $password],
        $messageTemplate
    );
    
    // Send
    $result = \App\Helpers\SmsHelper::sendSms($phone, $message);
}
```

### C. SchoolNotificationController
**Location:** `app/Http/Controllers/SchoolNotificationController.php`

Sends bulk notifications:
```php
public function sendSms(Request $request)
{
    // Collect phone numbers based on recipient type
    $phoneNumbers = [];
    
    switch ($request->recipient_type) {
        case 'all_parents':
            $parents = Parents::with('user')->get();
            foreach ($parents as $parent) {
                if ($parent->user && $parent->user->phone) {
                    $phoneNumbers[] = $this->formatPhoneNumber($parent->user->phone);
                }
            }
            break;
        // ... other cases
    }
    
    // Send to all
    foreach ($phoneNumbers as $phone) {
        $result = SmsHelper::sendSms($phone, $request->message);
        if ($result['success']) {
            $sent++;
        } else {
            $failed++;
        }
    }
}

private function formatPhoneNumber($phone)
{
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    
    if (substr($phone, 0, 1) === '0') {
        $phone = '+263' . substr($phone, 1);
    }
    
    if (substr($phone, 0, 1) !== '+') {
        $phone = '+' . $phone;
    }
    
    return $phone;
}
```

---

## 6. Queue Job (Optional)

**Location:** `app/Jobs/SendParentSms.php`

For asynchronous SMS sending:
```php
use App\Jobs\SendParentSms;

// Dispatch job
SendParentSms::dispatch($phone, $message, $parentId, $studentId);
```

**Job Configuration:**
- `$tries = 3` - Retry 3 times on failure
- `$backoff = 10` - Wait 10 seconds between retries

---

## 7. Error Handling

### HTTP Status Code Mapping
```php
$errorMessages = [
    400 => 'Bad Request - Invalid request parameters',
    401 => 'Unauthorized - Invalid authentication credentials',
    403 => 'Forbidden - Insufficient permissions',
    404 => 'Not Found - Resource doesn\'t exist',
    429 => 'Too Many Requests - Rate limit exceeded',
    500 => 'Internal Server Error - Try again later',
];
```

### Logging
```php
// Success
Log::info('SMS sent', compact('destination', 'httpCode'));

// Error
Log::error('SMS HTTP error', [
    'http_code' => $httpCode,
    'error_message' => $errorMessage,
    'destination' => $destination,
    'response' => $response
]);
```

---

## 8. Complete Implementation Checklist

### Step 1: Create Helper Class
- [ ] Create `app/Helpers/SmsHelper.php`
- [ ] Copy the `sendSms()` method
- [ ] Copy the `incrementSmsCount()` method

### Step 2: Environment Setup
- [ ] Add InboxIQ credentials to `.env`
- [ ] Test credentials with API

### Step 3: Database Setup
- [ ] Create `school_settings` table if needed
- [ ] Create `SchoolSetting` model
- [ ] Add default settings:
  - `sms_country_code` = `+263`
  - `sms_sent_count` = `0`

### Step 4: Basic Usage
- [ ] Import `SmsHelper` in your controller
- [ ] Format phone numbers correctly
- [ ] Call `SmsHelper::sendSms()`
- [ ] Handle response

### Step 5: Advanced Features (Optional)
- [ ] Create SMS templates
- [ ] Add bulk sending capability
- [ ] Create queue job for async sending
- [ ] Add SMS settings page

---

## 9. Quick Copy-Paste Implementation

### Minimal Implementation (No Database)

```php
<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class SmsHelper
{
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
                Log::error('SMS cURL error', ['error' => curl_error($ch)]);
                curl_close($ch);
                if ($attempt < $maxRetries) {
                    sleep(1);
                    continue;
                }
                return ['success' => false, 'message' => 'SMS sending failed'];
            }

            curl_close($ch);

            if ($httpCode >= 200 && $httpCode < 300) {
                Log::info('SMS sent', compact('destination', 'httpCode'));
                return [
                    'success' => true, 
                    'message' => 'SMS sent successfully',
                    'http_code' => $httpCode,
                    'response' => json_decode($response, true)
                ];
            }

            if ($httpCode >= 500 && $attempt < $maxRetries) {
                sleep(1);
                continue;
            }

            break;
        }

        Log::error('SMS failed', ['http_code' => $httpCode, 'destination' => $destination]);
        return [
            'success' => false, 
            'message' => "HTTP Error {$httpCode}",
            'http_code' => $httpCode
        ];
    }
}
```

### Usage:
```php
use App\Helpers\SmsHelper;

$result = SmsHelper::sendSms('+263771234567', 'Your message here');

if ($result['success']) {
    // Success
} else {
    // Handle error: $result['message']
}
```

---

## 10. Testing

### Test Route (Optional)
Add to `routes/web.php`:
```php
Route::get('/sms-test', function() {
    $result = \App\Helpers\SmsHelper::sendSms('+263771234567', 'Test message');
    return response()->json($result);
});
```

### Validation Rules
```php
$request->validate([
    'phone' => 'required|string|regex:/^\+\d{10,15}$/',
    'message' => 'required|string|max:160'
]);
```

---

## 11. Important Notes

1. **Phone Format:** Always use international format with `+` (e.g., `+263771234567`)
2. **Message Length:** Keep messages under 160 characters for single SMS
3. **Rate Limiting:** InboxIQ may have rate limits - handle 429 errors
4. **Credentials:** Never commit `.env` file with real credentials
5. **Logging:** Always log SMS attempts for debugging and auditing
6. **Error Handling:** Always check `$result['success']` before assuming SMS was sent
7. **Retry Logic:** Built-in retry handles temporary API failures
8. **Country Code:** Zimbabwe default is `+263`, adjust for your country

---

## 12. API Reference

**InboxIQ API Endpoint:**
```
POST https://api.inboxiq.co.zw/api/v1/send-sms
```

**Headers:**
```
Authorization: Basic {base64(username:password)}
key: {api_key}
Content-Type: application/json
```

**Request Body:**
```json
{
    "destination": "+263771234567",
    "messageText": "Your message here"
}
```

**Success Response (200):**
```json
{
    "status": "success",
    "message": "SMS sent successfully"
}
```

---

## Support

For InboxIQ API issues, contact their support or check their documentation at:
- Website: https://inboxiq.co.zw
- API Docs: (Contact InboxIQ for documentation)

---

**Last Updated:** January 2026
