<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['title', 'description', 'event_date', 'event_time', 'location', 'is_published', 'image_path'];

    protected $casts = [
        'event_date' => 'date',
        'is_published' => 'boolean',
    ];
}
