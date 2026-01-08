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
        'comment',
        'absence_reason',
        'approved',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'mark' => 'decimal:2',
        'total_marks' => 'decimal:2',
        'approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
