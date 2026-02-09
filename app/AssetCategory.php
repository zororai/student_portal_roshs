<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'useful_life_years',
        'depreciation_method',
        'is_active',
    ];

    protected $casts = [
        'useful_life_years' => 'integer',
        'is_active' => 'boolean',
    ];

    public function assets()
    {
        return $this->hasMany(Asset::class, 'category_id');
    }

    public static function getDepreciationMethods()
    {
        return [
            'straight_line' => 'Straight Line',
            'reducing_balance' => 'Reducing Balance',
        ];
    }

    public static function generateCode($name)
    {
        $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $name), 0, 3));
        $lastCategory = self::where('code', 'like', $prefix . '%')
            ->orderBy('code', 'desc')
            ->first();

        if ($lastCategory) {
            $lastNumber = intval(substr($lastCategory->code, 3));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return $prefix . $newNumber;
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
