<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = [
        'class_id',
        'teacher_id',
        'student_id',
        'subject_id',
        'marks',
        'comment',
        'mark_grade',
        'year',
        'result_period',
        'status', 
        
    ];

    // A result belongs to ONE subject
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    // A result belongs to ONE teacher
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    // A result belongs to ONE student
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // A result belongs to ONE class (grade)
    public function class()
    {
        return $this->belongsTo(Grade::class, 'class_id');
    }

    
}
