<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExpenseForecast extends Model
{
    protected $fillable = [
        'budget_period_id',
        'category_id',
        'category_name',
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

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function getVarianceAttribute()
    {
        return $this->expected_amount - $this->actual_amount;
    }
}
