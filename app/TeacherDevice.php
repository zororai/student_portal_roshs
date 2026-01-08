<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class TeacherDevice extends Model
{
    use Auditable;

    protected $fillable = [
        'teacher_id',
        'device_id',
        'device_name',
        'browser',
        'ip_address',
        'status',
        'registered_at',
        'registered_by',
        'revoked_by',
        'revoked_at',
        'revoke_reason',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_REVOKED = 'revoked';
    const STATUS_PENDING = 'pending';

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function registeredByUser()
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function revokedByUser()
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isRevoked()
    {
        return $this->status === self::STATUS_REVOKED;
    }

    public function activate($userId = null)
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'registered_at' => now(),
            'registered_by' => $userId,
        ]);
    }

    public function revoke($userId = null, $reason = null)
    {
        $this->update([
            'status' => self::STATUS_REVOKED,
            'revoked_at' => now(),
            'revoked_by' => $userId,
            'revoke_reason' => $reason,
        ]);
    }

    public static function findActiveByTeacher($teacherId)
    {
        return self::where('teacher_id', $teacherId)
            ->where('status', self::STATUS_ACTIVE)
            ->first();
    }

    public static function validateDevice($teacherId, $deviceId)
    {
        $device = self::where('teacher_id', $teacherId)
            ->where('device_id', $deviceId)
            ->where('status', self::STATUS_ACTIVE)
            ->first();

        return $device !== null;
    }
}
