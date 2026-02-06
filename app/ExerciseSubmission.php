<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExerciseSubmission extends Model
{
    protected $fillable = [
        'exercise_id',
        'student_id',
        'started_at',
        'submitted_at',
        'status',
        'total_score',
        'teacher_feedback',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function answers()
    {
        return $this->hasMany(ExerciseAnswer::class, 'submission_id');
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'not_started' => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">Not Started</span>',
            'in_progress' => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">In Progress</span>',
            'submitted' => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Submitted</span>',
            'marked' => '<span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">Marked</span>',
        ];
        return $badges[$this->status] ?? $badges['not_started'];
    }

    public function calculateAutoMarks()
    {
        $totalScore = 0;
        foreach ($this->answers as $answer) {
            $question = $answer->question;
            if ($question->isAutoMarkable()) {
                if ($question->question_type === 'multiple_choice' || $question->question_type === 'true_false') {
                    $correctOption = $question->options()->where('is_correct', true)->first();
                    if ($correctOption && $answer->selected_option_id == $correctOption->id) {
                        $answer->is_correct = true;
                        $answer->marks_awarded = $question->marks;
                        $totalScore += $question->marks;
                    } else {
                        $answer->is_correct = false;
                        $answer->marks_awarded = 0;
                    }
                    $answer->save();
                }
            }
        }
        return $totalScore;
    }

    public function getPercentageScore()
    {
        if (!$this->total_score || !$this->exercise->total_marks) {
            return null;
        }
        return round(($this->total_score / $this->exercise->total_marks) * 100, 1);
    }
}
