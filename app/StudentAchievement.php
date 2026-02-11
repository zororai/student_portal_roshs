<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentAchievement extends Model
{
    protected $fillable = [
        'student_name',
        'achievement_title',
        'description',
        'points',
        'subjects',
        'image_path',
        'is_active',
        'order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'subjects' => 'array'
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }
}
