<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProductSale extends Model
{
    protected $fillable = [
        'sale_number',
        'sale_date',
        'total_amount',
        'amount_paid',
        'change_given',
        'payment_method',
        'customer_name',
        'customer_phone',
        'notes',
        'sold_by',
    ];

    protected $casts = [
        'sale_date' => 'date',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'change_given' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(ProductSaleItem::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'sold_by');
    }

    public static function generateSaleNumber()
    {
        $prefix = 'SL-' . Carbon::now()->format('Ymd') . '-';
        $lastSale = self::where('sale_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastSale) {
            $lastNumber = intval(substr($lastSale->sale_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $newNumber;
    }
}
