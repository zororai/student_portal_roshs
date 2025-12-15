<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClassFormat extends Model
{
    protected $fillable = [
        'format_name',
        'numeric_value',
        'display_name',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'numeric_value' => 'integer',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc');
    }
}
