<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'subject_code',
        'teacher_id',
        'periods_per_week'
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    // A subject has many results
    public function results()
    {
        return $this->hasMany(Result::class, 'subject_id');
    }

    // A subject has many reading materials
    public function readings()
    {
        return $this->hasMany(Reading::class, 'subject_id');
    }
}
