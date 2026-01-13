<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TeacherAttendance extends Model
{
    protected $table = 'teacher_attendance';

    protected $fillable = [
        'teacher_id',
        'date',
        'check_in_time',
        'check_out_time',
        'checkout_reason',
        'expected_checkout_time',
        'status',
        'device_id',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in_time' => 'datetime:H:i:s',
        'check_out_time' => 'datetime:H:i:s',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Check if teacher is currently checked in (has check_in but no check_out)
     */
    public function isCheckedIn()
    {
        return $this->check_in_time !== null && $this->check_out_time === null;
    }

    /**
     * Check if teacher has completed attendance (has both check_in and check_out)
     */
    public function isComplete()
    {
        return $this->check_in_time !== null && $this->check_out_time !== null;
    }

    /**
     * Get the duration worked in hours
     */
    public function getDurationAttribute()
    {
        if (!$this->check_in_time || !$this->check_out_time) {
            return null;
        }

        $checkIn = \Carbon\Carbon::parse($this->check_in_time);
        $checkOut = \Carbon\Carbon::parse($this->check_out_time);

        return $checkOut->diffInMinutes($checkIn) / 60;
    }
}
