<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $fillable = [
        'teacher_id',
        'class_id',
        'subject_id',
        'syllabus_topic_id',
        'topic',
        'assessment_type',
        'date',
        'due_date',
        'term',
        'academic_year',
        'exam',
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
        return $this->belongsTo(Grade::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function marks()
    {
        return $this->hasMany(AssessmentMark::class);
    }

    public function syllabusTopic()
    {
        return $this->belongsTo(SyllabusTopic::class, 'syllabus_topic_id');
    }
}
