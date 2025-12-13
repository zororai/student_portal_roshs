<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssessmentMark extends Model
{
    protected $fillable = [
        'assessment_id',
        'student_id',
        'paper_name',
        'paper_index',
        'mark',
        'total_marks',
        'comment'
    ];

    protected $casts = [
        'mark' => 'decimal:2',
        'total_marks' => 'decimal:2'
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
