<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reading extends Model
{


    protected $table = 'reading';

    // Define the fillable attributes
    protected $fillable = [
        'name',
        'description',
        'path',
        'youtube_link',
        'subject_id',
    ];


}


