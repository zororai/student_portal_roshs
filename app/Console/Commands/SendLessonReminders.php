<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Timetable;
use App\Teacher;
use App\PushSubscription;
use Carbon\Carbon;

class SendLessonReminders extends Command
{
    protected $signature = 'lessons:send-reminders';
    protected $description = 'Send push notification reminders for upcoming lessons (5 minutes before)';

    public function handle()
    {
        $now = Carbon::now();
        $reminderTime = $now->copy()->addMinutes(5);
        
        // Get current day of week (Monday = 1, Sunday = 7)
        $dayOfWeek = $now->dayOfWeekIso;
        $dayNames = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'];
        $today = $dayNames[$dayOfWeek] ?? null;

        if (!$today) {
            $this->info('Invalid day');
            return;
        }

        // Find lessons starting in 5 minutes
        $upcomingLessons = Timetable::with(['teacher.user', 'subject', 'grade'])
            ->where('day', $today)
            ->where('slot_type', 'subject')
            ->where('start_time', '>=', $reminderTime->format('H:i:s'))
            ->where('start_time', '<=', $reminderTime->copy()->addMinute()->format('H:i:s'))
            ->get();

        $this->info("Found {$upcomingLessons->count()} upcoming lessons at " . $reminderTime->format('H:i'));

        foreach ($upcomingLessons as $lesson) {
            if (!$lesson->teacher || !$lesson->teacher->user) {
                continue;
            }

            $userId = $lesson->teacher->user->id;
            $subscriptions = PushSubscription::where('user_id', $userId)
                ->where('is_active', true)
                ->get();

            if ($subscriptions->isEmpty()) {
                $this->info("No subscriptions for teacher: " . ($lesson->teacher->user->name ?? 'Unknown'));
                continue;
            }

            $subjectName = $lesson->subject->name ?? 'Unknown Subject';
            $className = $lesson->grade->class_name ?? 'Unknown Class';
            $startTime = Carbon::parse($lesson->start_time)->format('H:i');

            $title = "🔔 Lesson Reminder";
            $body = "{$subjectName} with {$className} starts in 5 minutes ({$startTime})";

            foreach ($subscriptions as $subscription) {
                $result = $this->sendPushNotification($subscription, $title, $body, [
                    'url' => '/teacher/timetable',
                    'lesson_id' => $lesson->id,
                ]);

                if ($result) {
                    $this->info("Notification sent to: " . $lesson->teacher->user->name);
                } else {
                    $this->warn("Failed to send notification to: " . $lesson->teacher->user->name);
                }
            }
        }

        $this->info('Lesson reminders processed.');
    }

    private function sendPushNotification($subscription, $title, $body, $data = [])
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
            $this->error('VAPID keys not configured');
            return false;
        }

        try {
            $auth = [
                'VAPID' => [
                    'subject' => config('app.url'),
                    'publicKey' => $publicKey,
                    'privateKey' => $privateKey,
                ],
            ];

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
                    $this->warn('Subscription expired and removed');
                }
                return false;
            }
        } catch (\Exception $e) {
            $this->error('Push error: ' . $e->getMessage());
            return false;
        }
    }
}
