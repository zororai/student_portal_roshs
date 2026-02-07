<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    protected $fillable = [
        'ip_address',
        'email',
        'user_agent',
        'successful',
        'failure_reason',
        'captcha_required',
        'captcha_passed',
    ];

    protected $casts = [
        'successful' => 'boolean',
        'captcha_required' => 'boolean',
        'captcha_passed' => 'boolean',
    ];

    public static function getRecentFailedByIp($ip, $minutes = 1)
    {
        return static::where('ip_address', $ip)
            ->where('successful', false)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->count();
    }

    public static function getRecentFailedByEmail($email, $hours = 1)
    {
        return static::where('email', $email)
            ->where('successful', false)
            ->where('created_at', '>=', now()->subHours($hours))
            ->count();
    }

    public static function getTotalFailedByIp($ip, $minutes = 60)
    {
        return static::where('ip_address', $ip)
            ->where('successful', false)
            ->where('created_at', '>=', now()->subMinutes($minutes))
            ->count();
    }

    public static function requiresCaptcha($ip, $email = null)
    {
        $ipFailures = static::getRecentFailedByIp($ip, 60);
        $emailFailures = $email ? static::getRecentFailedByEmail($email, 1) : 0;
        
        return $ipFailures >= 3 || $emailFailures >= 3;
    }

    public static function logAttempt($ip, $email, $userAgent, $successful, $reason = null, $captchaRequired = false, $captchaPassed = false)
    {
        return static::create([
            'ip_address' => $ip,
            'email' => $email,
            'user_agent' => $userAgent,
            'successful' => $successful,
            'failure_reason' => $reason,
            'captcha_required' => $captchaRequired,
            'captcha_passed' => $captchaPassed,
        ]);
    }
}
