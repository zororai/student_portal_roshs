<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BudgetPeriod extends Model
{
    protected $fillable = [
        'name',
        'period_type',
        'start_date',
        'end_date',
        'status',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function budgetItems()
    {
        return $this->hasMany(BudgetItem::class);
    }

    public function revenueForecasts()
    {
        return $this->hasMany(RevenueForecast::class);
    }

    public function expenseForecasts()
    {
        return $this->hasMany(ExpenseForecast::class);
    }

    public function getTotalBudgetedIncomeAttribute()
    {
        return $this->budgetItems()->where('type', 'income')->sum('budgeted_amount');
    }

    public function getTotalBudgetedExpenseAttribute()
    {
        return $this->budgetItems()->where('type', 'expense')->sum('budgeted_amount');
    }

    public function getTotalActualIncomeAttribute()
    {
        return $this->budgetItems()->where('type', 'income')->sum('actual_amount');
    }

    public function getTotalActualExpenseAttribute()
    {
        return $this->budgetItems()->where('type', 'expense')->sum('actual_amount');
    }
}
