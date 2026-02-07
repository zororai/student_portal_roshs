<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoginLockout extends Model
{
    protected $fillable = [
        'ip_address',
        'email',
        'lockout_type',
        'locked_until',
        'attempt_count',
        'reason',
    ];

    protected $casts = [
        'locked_until' => 'datetime',
    ];

    public static function isIpLocked($ip)
    {
        return static::where('ip_address', $ip)
            ->where('lockout_type', 'ip')
            ->where('locked_until', '>', now())
            ->exists();
    }

    public static function isAccountLocked($email)
    {
        return static::where('email', $email)
            ->where('lockout_type', 'account')
            ->where('locked_until', '>', now())
            ->exists();
    }

    public static function getLockoutTime($ip, $email = null)
    {
        $lockout = static::where(function ($q) use ($ip, $email) {
            $q->where('ip_address', $ip);
            if ($email) {
                $q->orWhere('email', $email);
            }
        })
        ->where('locked_until', '>', now())
        ->orderBy('locked_until', 'desc')
        ->first();

        return $lockout ? $lockout->locked_until : null;
    }

    public static function lockIp($ip, $minutes, $attemptCount, $reason = null)
    {
        return static::updateOrCreate(
            ['ip_address' => $ip, 'lockout_type' => 'ip'],
            [
                'locked_until' => now()->addMinutes($minutes),
                'attempt_count' => $attemptCount,
                'reason' => $reason,
            ]
        );
    }

    public static function lockAccount($email, $minutes, $attemptCount, $reason = null)
    {
        return static::updateOrCreate(
            ['email' => $email, 'lockout_type' => 'account'],
            [
                'locked_until' => now()->addMinutes($minutes),
                'attempt_count' => $attemptCount,
                'reason' => $reason,
            ]
        );
    }

    public static function clearLockout($ip = null, $email = null)
    {
        $query = static::query();
        
        if ($ip) {
            $query->orWhere('ip_address', $ip);
        }
        if ($email) {
            $query->orWhere('email', $email);
        }
        
        return $query->delete();
    }

    public static function getProgressiveDelay($attemptCount)
    {
        // Progressive delay: 0, 1, 2, 4, 8, 16... seconds
        if ($attemptCount < 3) return 0;
        return min(pow(2, $attemptCount - 3), 30); // Max 30 seconds
    }

    public static function getProgressiveLockoutMinutes($attemptCount)
    {
        // Progressive lockout: 1, 5, 15, 30, 60 minutes
        $lockouts = [1, 5, 15, 30, 60];
        $index = min(floor(($attemptCount - 5) / 5), count($lockouts) - 1);
        return $lockouts[max(0, $index)];
    }
}
