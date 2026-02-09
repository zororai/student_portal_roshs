<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetLocation extends Model
{
    protected $fillable = [
        'name',
        'building',
        'floor',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function assets()
    {
        return $this->hasMany(Asset::class, 'location_id');
    }

    public function getFullNameAttribute()
    {
        $parts = array_filter([$this->building, $this->floor, $this->name]);
        return implode(' - ', $parts);
    }

    public function getActiveAssetsCountAttribute()
    {
        return $this->assets()->where('status', 'active')->count();
    }

    public function getTotalValueAttribute()
    {
        return $this->assets()->where('status', 'active')->sum('current_value');
    }
}
