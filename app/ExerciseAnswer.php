<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExerciseAnswer extends Model
{
    protected $table = 'exercise_answers';
    protected $fillable = [
        'submission_id',
        'question_id',
        'answer_text',
        'selected_option_id',
        'file_path',
        'is_correct',
        'marks_awarded',
        'feedback',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function submission()
    {
        return $this->belongsTo(ExerciseSubmission::class, 'submission_id');
    }

    public function question()
    {
        return $this->belongsTo(ExerciseQuestion::class, 'question_id');
    }

    public function selectedOption()
    {
        return $this->belongsTo(ExerciseQuestionOption::class, 'selected_option_id');
    }
}
