<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExerciseQuestion extends Model
{
    protected $fillable = [
        'exercise_id',
        'question_type',
        'question_text',
        'question_image',
        'marks',
        'order',
        'correct_answer',
    ];

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }

    public function options()
    {
        return $this->hasMany(ExerciseQuestionOption::class, 'question_id')->orderBy('order');
    }

    public function answers()
    {
        return $this->hasMany(ExerciseAnswer::class, 'question_id');
    }

    public function getQuestionTypeLabel()
    {
        $labels = [
            'multiple_choice' => 'Multiple Choice',
            'true_false' => 'True/False',
            'short_answer' => 'Short Answer',
            'file_upload' => 'File Upload',
        ];
        return $labels[$this->question_type] ?? $this->question_type;
    }

    public function isAutoMarkable()
    {
        return in_array($this->question_type, ['multiple_choice', 'true_false']);
    }
}
