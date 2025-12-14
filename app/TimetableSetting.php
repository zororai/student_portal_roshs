<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimetableSetting extends Model
{
    protected $fillable = [
        'class_id',
        'start_time',
        'break_start',
        'break_end',
        'lunch_start',
        'lunch_end',
        'end_time',
        'subject_duration',
        'academic_year',
        'term'
    ];

    protected $casts = [
        'subject_duration' => 'integer',
    ];

    public function grade()
    {
        return $this->belongsTo(Grade::class, 'class_id');
    }
}
