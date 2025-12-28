<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankTransaction extends Model
{
    protected $fillable = [
        'bank_account_id',
        'transaction_date',
        'reference_number',
        'transaction_type',
        'amount',
        'balance_after',
        'description',
        'is_reconciled',
        'reconciled_with',
        'reconciled_at',
        'reconciled_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'is_reconciled' => 'boolean',
        'reconciled_at' => 'datetime',
    ];

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function cashBookEntry()
    {
        return $this->belongsTo(CashBookEntry::class, 'reconciled_with');
    }

    public function reconciledByUser()
    {
        return $this->belongsTo(User::class, 'reconciled_by');
    }
}
