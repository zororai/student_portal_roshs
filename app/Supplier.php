<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'tax_number',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function invoices()
    {
        return $this->hasMany(SupplierInvoice::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(SupplierPayment::class, SupplierInvoice::class);
    }

    /**
     * Get total outstanding balance
     */
    public function getOutstandingBalance()
    {
        return $this->invoices()
            ->whereIn('status', ['unpaid', 'partial'])
            ->sum('amount') - $this->invoices()
            ->whereIn('status', ['unpaid', 'partial'])
            ->sum('paid_amount');
    }
}
