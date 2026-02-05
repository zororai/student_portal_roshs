<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroceryStockItem extends Model
{
    protected $fillable = [
        'name',
        'description',
        'unit',
        'current_balance',
        'is_active',
        'is_manual'
    ];

    protected $casts = [
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
        'is_manual' => 'boolean'
    ];

    public function transactions()
    {
        return $this->hasMany(GroceryStockTransaction::class, 'stock_item_id');
    }

    public function updateBalance()
    {
        $received = $this->transactions()->whereIn('type', ['received', 'balance_bf'])->sum('quantity');
        $deducted = $this->transactions()->whereIn('type', ['usage', 'bad_stock'])->sum('quantity');
        $adjustments = $this->transactions()->where('type', 'adjustment')->sum('quantity');
        
        $this->current_balance = $received - $deducted + $adjustments;
        $this->save();
        
        return $this->current_balance;
    }
}
