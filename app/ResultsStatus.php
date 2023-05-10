<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class ResultsStatus extends Model
{
    protected $fillable = [
   
        'year',
        'result_period',

    ];

 

    // A result status belongs to ONE class (grade)
    public function class()
    {
        return $this->belongsTo(Grade::class, 'class_id');
    }
}