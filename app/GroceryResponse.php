<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroceryResponse extends Model
{
    protected $fillable = [
        'grocery_list_id',
        'student_id',
        'parent_id',
        'items_bought',
        'extra_items',
        'item_extra_qty',
        'item_short_qty',
        'submitted',
        'acknowledged',
        'submitted_at',
        'acknowledged_at',
        'notes'
    ];

    protected $casts = [
        'items_bought' => 'array',
        'extra_items' => 'array',
        'item_extra_qty' => 'array',
        'item_short_qty' => 'array',
        'submitted' => 'boolean',
        'acknowledged' => 'boolean',
        'submitted_at' => 'datetime',
        'acknowledged_at' => 'datetime'
    ];

    public function groceryList()
    {
        return $this->belongsTo(GroceryList::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function parent()
    {
        return $this->belongsTo(Parents::class, 'parent_id');
    }
}
