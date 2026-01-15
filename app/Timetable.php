<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    protected $fillable = [
        'class_id',
        'subject_id',
        'teacher_id',
        'day',
        'start_time',
        'end_time',
        'slot_type',
        'slot_name',
        'slot_order',
        'academic_year',
        'term'
    ];

    public function grade()
    {
        return $this->belongsTo(Grade::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public static function checkTeacherConflict($teacherId, $day, $startTime, $endTime, $excludeId = null)
    {
        $query = self::where('teacher_id', $teacherId)
            ->where('day', $day)
            ->where('slot_type', 'subject')
            ->where(function($q) use ($startTime, $endTime) {
                // Check for any time overlap:
                // 1. New slot starts during existing slot
                // 2. New slot ends during existing slot  
                // 3. New slot completely contains existing slot
                // 4. Existing slot completely contains new slot
                $q->where(function($q2) use ($startTime, $endTime) {
                    $q2->where('start_time', '<', $endTime)
                       ->where('end_time', '>', $startTime);
                });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }
}
