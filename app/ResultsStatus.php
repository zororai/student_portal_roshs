<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class ResultsStatus extends Model
{
    protected $fillable = [
   
        'year',
        'result_period',
        'total_fees',

    ];

    protected $casts = [
        'total_fees' => 'decimal:2',
    ];

    // A result status belongs to ONE class (grade)
    public function class()
    {
        return $this->belongsTo(Grade::class, 'class_id');
    }

    // A result status has many term fees
    public function termFees()
    {
        return $this->hasMany(TermFee::class);
    }
}