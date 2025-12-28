<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class Teacher extends Model
{
    use Auditable;
    protected $fillable = [
        'user_id',
        'gender',
        'phone',
        'dateofbirth',
        'current_address',
        'permanent_address',
        'qr_code',
        'qr_code_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function classes()
    {
        return $this->hasMany(Grade::class);
    }

    public function students() 
    {
        return $this->classes()->withCount('students');
    }

    public function logs()
    {
        return $this->hasMany(TeacherLog::class);
    }

    /**
     * Get today's log entry
     */
    public function todayLog()
    {
        return $this->logs()->where('log_date', today())->first();
    }

    /**
     * Check if teacher is currently clocked in today
     */
    public function isClockedInToday()
    {
        $log = $this->todayLog();
        return $log && $log->isClockedIn();
    }
}
