<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'po_number',
        'order_date',
        'expected_delivery_date',
        'supplier_id',
        'status',
        'invoice_number',
        'invoice_date',
        'payment_status',
        'payment_date',
        'payment_method',
        'amount_paid',
        'expense_id',
        'cashbook_entry_id',
        'subtotal',
        'tax_amount',
        'total_amount',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'invoice_date' => 'date',
        'payment_date' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function expense()
    {
        return $this->belongsTo(\App\SchoolExpense::class, 'expense_id');
    }

    public function cashbookEntry()
    {
        return $this->belongsTo(\App\CashBookEntry::class, 'cashbook_entry_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public static function generatePONumber()
    {
        $prefix = 'PO-' . Carbon::now()->format('Ymd') . '-';
        $lastPO = self::where('po_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastPO) {
            $lastNumber = intval(substr($lastPO->po_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $newNumber;
    }

    public function calculateTotals()
    {
        $this->subtotal = $this->items()->sum('total_price');
        $this->total_amount = $this->subtotal + $this->tax_amount;
        $this->save();
    }
}
