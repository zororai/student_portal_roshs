<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Teachers extends Model
{


    public function classes()
    {
        return $this->hasMany(Grade::class, 'teacher_id'); // Ensure 'teacher_id' is the correct foreign key
    }
}
