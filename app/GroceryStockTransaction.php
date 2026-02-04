<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroceryStockTransaction extends Model
{
    protected $fillable = [
        'stock_item_id',
        'type',
        'quantity',
        'balance_after',
        'term',
        'year',
        'description',
        'recorded_by',
        'transaction_date'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'transaction_date' => 'date'
    ];

    public function stockItem()
    {
        return $this->belongsTo(GroceryStockItem::class, 'stock_item_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getTypeLabel()
    {
        $labels = [
            'received' => 'Received from Students',
            'usage' => 'Usage/Deduction',
            'bad_stock' => 'Bad Stock',
            'balance_bf' => 'Balance B/F',
            'adjustment' => 'Adjustment'
        ];
        return $labels[$this->type] ?? $this->type;
    }

    public function getTypeBadgeClass()
    {
        $classes = [
            'received' => 'bg-green-100 text-green-700',
            'usage' => 'bg-blue-100 text-blue-700',
            'bad_stock' => 'bg-red-100 text-red-700',
            'balance_bf' => 'bg-purple-100 text-purple-700',
            'adjustment' => 'bg-yellow-100 text-yellow-700'
        ];
        return $classes[$this->type] ?? 'bg-gray-100 text-gray-700';
    }
}
