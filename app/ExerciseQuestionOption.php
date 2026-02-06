<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExerciseQuestionOption extends Model
{
    protected $fillable = [
        'question_id',
        'option_text',
        'is_correct',
        'order',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function question()
    {
        return $this->belongsTo(ExerciseQuestion::class, 'question_id');
    }
}
