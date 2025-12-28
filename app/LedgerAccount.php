<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LedgerAccount extends Model
{
    protected $fillable = [
        'account_code',
        'account_name',
        'account_type',
        'category',
        'description',
        'opening_balance',
        'current_balance',
        'is_active',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function entries()
    {
        return $this->hasMany(LedgerEntry::class, 'account_id');
    }

    public static function getAccountTypes()
    {
        return [
            'asset' => 'Asset',
            'liability' => 'Liability',
            'equity' => 'Equity',
            'income' => 'Income',
            'expense' => 'Expense',
        ];
    }

    public static function generateAccountCode($type)
    {
        $prefixes = [
            'asset' => '1',
            'liability' => '2',
            'equity' => '3',
            'income' => '4',
            'expense' => '5',
        ];

        $prefix = $prefixes[$type] ?? '9';
        $lastAccount = self::where('account_code', 'like', $prefix . '%')
            ->orderBy('account_code', 'desc')
            ->first();

        if ($lastAccount) {
            $lastNumber = intval(substr($lastAccount->account_code, 1));
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return $prefix . $newNumber;
    }

    public function updateBalance()
    {
        $debits = $this->entries()->where('entry_type', 'debit')->sum('amount');
        $credits = $this->entries()->where('entry_type', 'credit')->sum('amount');

        // For asset and expense accounts: Debit increases, Credit decreases
        // For liability, equity, and income accounts: Credit increases, Debit decreases
        if (in_array($this->account_type, ['asset', 'expense'])) {
            $this->current_balance = $this->opening_balance + $debits - $credits;
        } else {
            $this->current_balance = $this->opening_balance + $credits - $debits;
        }

        $this->save();
    }
}
