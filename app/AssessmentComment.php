<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssessmentComment extends Model
{
    protected $fillable = [
        'class_id',
        'subject_id',
        'comment',
        'grade'
    ];

    public function class()
    {
        return $this->belongsTo(Grade::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
