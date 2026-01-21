<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TermFee extends Model
{
    protected $fillable = [
        'results_status_id',
        'fee_type_id',
        'student_type',
        'curriculum_type',
        'amount',
        'is_for_new_student'
    ];

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    public function resultsStatus()
    {
        return $this->belongsTo(ResultsStatus::class);
    }

    public function feeType()
    {
        return $this->belongsTo(FeeType::class);
    }
}
