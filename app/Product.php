<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'name',
        'sku',
        'barcode',
        'description',
        'category',
        'image',
        'price',
        'cost_price',
        'quantity',
        'min_stock_level',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'quantity' => 'integer',
        'min_stock_level' => 'integer',
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function saleItems()
    {
        return $this->hasMany(ProductSaleItem::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getTotalRevenueAttribute()
    {
        return $this->saleItems()->sum('total_price');
    }

    public function getTotalSoldAttribute()
    {
        return $this->saleItems()->sum('quantity');
    }

    public function isLowStock()
    {
        return $this->quantity <= $this->min_stock_level;
    }

    public static function generateBarcode()
    {
        $prefix = 'PRD';
        $timestamp = now()->format('ymdHis');
        $random = str_pad(mt_rand(0, 999), 3, '0', STR_PAD_LEFT);
        return $prefix . $timestamp . $random;
    }

    public static function generateSKU($name)
    {
        $words = explode(' ', strtoupper($name));
        $sku = '';
        foreach ($words as $word) {
            $sku .= substr($word, 0, 2);
        }
        $sku .= '-' . str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
        return substr($sku, 0, 15);
    }
}
