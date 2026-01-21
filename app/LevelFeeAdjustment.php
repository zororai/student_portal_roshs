<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LevelFeeAdjustment extends Model
{
    protected $fillable = [
        'results_status_id',
        'class_level',
        'curriculum_type',
        'student_type',
        'adjustment_amount',
        'adjustment_type',
        'description'
    ];

    protected $casts = [
        'adjustment_amount' => 'decimal:2',
        'class_level' => 'integer',
    ];

    public function resultsStatus()
    {
        return $this->belongsTo(ResultsStatus::class);
    }
}
