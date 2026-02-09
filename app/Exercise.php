<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    protected $table = 'exercises';
    protected $fillable = [
        'teacher_id',
        'class_id',
        'subject_id',
        'assessment_id',
        'title',
        'instructions',
        'type',
        'total_marks',
        'duration_minutes',
        'due_date',
        'is_published',
        'show_results',
        'term',
        'academic_year',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'is_published' => 'boolean',
        'show_results' => 'boolean',
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

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function questions()
    {
        return $this->hasMany(ExerciseQuestion::class)->orderBy('order');
    }

    public function submissions()
    {
        return $this->hasMany(ExerciseSubmission::class);
    }

    public function getSubmissionForStudent($studentId)
    {
        return $this->submissions()->where('student_id', $studentId)->first();
    }

    public function isOverdue()
    {
        return $this->due_date && now()->gt($this->due_date);
    }

    public function getStatusBadgeAttribute()
    {
        if (!$this->is_published) {
            return '<span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Draft</span>';
        }
        if ($this->isOverdue()) {
            return '<span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">Closed</span>';
        }
        return '<span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Active</span>';
    }

    public function getTypeLabel()
    {
        return ucfirst($this->type);
    }
}
