<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroceryItem extends Model
{
    protected $fillable = [
        'grocery_list_id',
        'name',
        'quantity',
        'description'
    ];

    public function groceryList()
    {
        return $this->belongsTo(GroceryList::class);
    }
}
