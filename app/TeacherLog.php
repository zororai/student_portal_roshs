<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TeacherLog extends Model
{
    protected $fillable = [
        'teacher_id',
        'log_date',
        'time_in',
        'time_out',
        'check_in_lat',
        'check_in_lng',
        'check_out_lat',
        'check_out_lng',
        'status',
        'within_boundary',
        'notes',
    ];

    protected $casts = [
        'log_date' => 'date',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    /**
     * Check if teacher is currently clocked in (has time_in but no time_out)
     */
    public function isClockedIn()
    {
        return $this->time_in !== null && $this->time_out === null;
    }

    /**
     * Check if teacher has completed their shift (has both time_in and time_out)
     */
    public function isComplete()
    {
        return $this->time_in !== null && $this->time_out !== null;
    }

    /**
     * Get the duration worked in hours
     */
    public function getDurationAttribute()
    {
        if (!$this->time_in || !$this->time_out) {
            return null;
        }

        $timeIn = \Carbon\Carbon::parse($this->time_in);
        $timeOut = \Carbon\Carbon::parse($this->time_out);

        return $timeOut->diffInMinutes($timeIn) / 60;
    }
}
