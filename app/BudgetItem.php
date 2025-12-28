<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BudgetItem extends Model
{
    protected $fillable = [
        'budget_period_id',
        'type',
        'category',
        'description',
        'budgeted_amount',
        'actual_amount',
        'variance',
    ];

    protected $casts = [
        'budgeted_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
        'variance' => 'decimal:2',
    ];

    public function budgetPeriod()
    {
        return $this->belongsTo(BudgetPeriod::class);
    }

    public function updateVariance()
    {
        $this->variance = $this->budgeted_amount - $this->actual_amount;
        $this->save();
    }

    public function getVariancePercentageAttribute()
    {
        if ($this->budgeted_amount == 0) {
            return 0;
        }
        return round(($this->variance / $this->budgeted_amount) * 100, 2);
    }
}
