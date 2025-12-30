<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'name',
        'description',
        'price',
        'cost_price',
        'quantity_in_stock',
        'quantity_sold',
        'category',
        'supplier',
        'term',
        'year'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'quantity_in_stock' => 'integer',
        'quantity_sold' => 'integer'
    ];

    public function getTotalRevenueAttribute()
    {
        return $this->price * $this->quantity_sold;
    }

    public function getProfitAttribute()
    {
        return ($this->price - $this->cost_price) * $this->quantity_sold;
    }
}
