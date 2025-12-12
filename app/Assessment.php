<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $fillable = [
        'teacher_id',
        'class_id',
        'subject_id',
        'topic',
        'assessment_type',
        'date',
        'due_date',
        'papers'
    ];

    protected $casts = [
        'papers' => 'array',
        'date' => 'date',
        'due_date' => 'date'
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
