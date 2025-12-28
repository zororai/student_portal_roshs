<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $fillable = [
        'account_name',
        'bank_name',
        'account_number',
        'opening_balance',
        'current_balance',
        'is_active',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(BankTransaction::class);
    }

    public function updateBalance()
    {
        $deposits = $this->transactions()->whereIn('transaction_type', ['deposit', 'interest'])->sum('amount');
        $withdrawals = $this->transactions()->whereIn('transaction_type', ['withdrawal', 'fee', 'transfer'])->sum('amount');
        $this->current_balance = $this->opening_balance + $deposits - $withdrawals;
        $this->save();
    }
}
