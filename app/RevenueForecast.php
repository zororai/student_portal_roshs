<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RevenueForecast extends Model
{
    protected $fillable = [
        'budget_period_id',
        'source',
        'expected_amount',
        'actual_amount',
        'assumptions',
    ];

    protected $casts = [
        'expected_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
    ];

    public function budgetPeriod()
    {
        return $this->belongsTo(BudgetPeriod::class);
    }

    public function getVarianceAttribute()
    {
        return $this->expected_amount - $this->actual_amount;
    }
}
