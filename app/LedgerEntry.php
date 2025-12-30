<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LedgerEntry extends Model
{
    protected $fillable = [
        'entry_date',
        'term',
        'year',
        'reference_number',
        'account_id',
        'entry_type',
        'amount',
        'description',
        'cash_book_entry_id',
        'payroll_id',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function account()
    {
        return $this->belongsTo(LedgerAccount::class, 'account_id');
    }

    public function cashBookEntry()
    {
        return $this->belongsTo(CashBookEntry::class);
    }

    public function payroll()
    {
        return $this->belongsTo(Payroll::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isDebit()
    {
        return $this->entry_type === 'debit';
    }

    public function isCredit()
    {
        return $this->entry_type === 'credit';
    }

    public static function generateReferenceNumber()
    {
        $prefix = 'LE-' . date('Ymd') . '-';
        $lastEntry = self::where('reference_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastEntry) {
            $lastNumber = intval(substr($lastEntry->reference_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $newNumber;
    }
}
