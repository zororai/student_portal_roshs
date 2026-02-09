<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentInvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'description',
        'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(StudentInvoice::class, 'invoice_id');
    }
}
