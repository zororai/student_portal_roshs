<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroceryItem extends Model
{
    protected $fillable = [
        'grocery_list_id',
        'name',
        'quantity',
        'price',
        'description'
    ];

    protected $casts = [
        'price' => 'decimal:2'
    ];

    public function groceryList()
    {
        return $this->belongsTo(GroceryList::class);
    }
}
