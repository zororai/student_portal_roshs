<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroceryList extends Model
{
    protected $fillable = [
        'term',
        'year',
        'status'
    ];

    public function classes()
    {
        return $this->belongsToMany(Grade::class, 'grocery_list_class', 'grocery_list_id', 'class_id');
    }

    public function items()
    {
        return $this->hasMany(GroceryItem::class);
    }

    public function responses()
    {
        return $this->hasMany(GroceryResponse::class);
    }
}
