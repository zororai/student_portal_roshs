<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{

    protected $fillable = [
        'image_path_1',
        'image_path_2',
        'image_path_3'
    ];

}