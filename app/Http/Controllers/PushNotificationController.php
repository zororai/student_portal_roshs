<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PushSubscription;
use App\Timetable;
use App\Teacher;

class PushNotificationController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
        ]);

        $endpointHash = hash('sha256', $request->endpoint);
        
        $subscription = PushSubscription::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'endpoint_hash' => $endpointHash,
            ],
            [
                'endpoint' => $request->endpoint,
                'public_key' => $request->keys['p256dh'],
                'auth_token' => $request->keys['auth'],
                'content_encoding' => $request->contentEncoding ?? 'aesgcm',
                'is_active' => true,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Push notification subscription saved successfully.'
        ]);
    }

    public function unsubscribe(Request $request)
    {
        $request->validate([
            'endpoint' => 'required|string',
        ]);

        PushSubscription::where('user_id', auth()->id())
            ->where('endpoint', $request->endpoint)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Push notification subscription removed.'
        ]);
    }

    public function getVapidPublicKey()
    {
        return response()->json([
            'publicKey' => config('services.webpush.public_key')
        ]);
    }

    public static function sendNotification($subscription, $title, $body, $data = [])
    {
        $payload = json_encode([
            'title' => $title,
            'body' => $body,
            'icon' => '/images/logo.png',
            'badge' => '/images/badge.png',
            'data' => $data,
            'requireInteraction' => true,
        ]);

        $publicKey = config('services.webpush.public_key');
        $privateKey = config('services.webpush.private_key');

        if (!$publicKey || !$privateKey) {
            return false;
        }

        $auth = [
            'VAPID' => [
                'subject' => config('app.url'),
                'publicKey' => $publicKey,
                'privateKey' => $privateKey,
            ],
        ];

        try {
            $webPush = new \Minishlink\WebPush\WebPush($auth);
            
            $sub = \Minishlink\WebPush\Subscription::create([
                'endpoint' => $subscription->endpoint,
                'publicKey' => $subscription->public_key,
                'authToken' => $subscription->auth_token,
                'contentEncoding' => $subscription->content_encoding ?? 'aesgcm',
            ]);

            $report = $webPush->sendOneNotification($sub, $payload);

            if ($report->isSuccess()) {
                return true;
            } else {
                if ($report->isSubscriptionExpired()) {
                    $subscription->delete();
                }
                return false;
            }
        } catch (\Exception $e) {
            \Log::error('Push notification failed: ' . $e->getMessage());
            return false;
        }
    }
}
