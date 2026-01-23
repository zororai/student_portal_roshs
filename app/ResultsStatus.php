<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class ResultsStatus extends Model
{
    protected $fillable = [
   
        'year',
        'result_period',
        'total_fees',
        'total_day_fees',
        'total_boarding_fees',
        'zimsec_day_fees',
        'zimsec_boarding_fees',
        'cambridge_day_fees',
        'cambridge_boarding_fees',

    ];

    protected $casts = [
        'total_fees' => 'decimal:2',
        'total_day_fees' => 'decimal:2',
        'total_boarding_fees' => 'decimal:2',
        'zimsec_day_fees' => 'decimal:2',
        'zimsec_boarding_fees' => 'decimal:2',
        'cambridge_day_fees' => 'decimal:2',
        'cambridge_boarding_fees' => 'decimal:2',
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

    // A result status has many fee structures (level group based)
    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class);
    }

    // A result status has many level fee adjustments
    public function levelFeeAdjustments()
    {
        return $this->hasMany(LevelFeeAdjustment::class);
    }
}