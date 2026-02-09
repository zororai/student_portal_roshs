<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    protected $fillable = [
        'journal_batch_id',
        'ledger_account_id',
        'debit_amount',
        'credit_amount',
        'narration',
    ];

    protected $casts = [
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
    ];

    public function batch()
    {
        return $this->belongsTo(JournalBatch::class, 'journal_batch_id');
    }

    public function ledgerAccount()
    {
        return $this->belongsTo(LedgerAccount::class, 'ledger_account_id');
    }

    /**
     * Get the entry type (debit or credit)
     */
    public function getEntryType()
    {
        if ($this->debit_amount > 0) {
            return 'debit';
        } elseif ($this->credit_amount > 0) {
            return 'credit';
        }
        return null;
    }

    /**
     * Get the entry amount
     */
    public function getAmount()
    {
        return $this->debit_amount > 0 ? $this->debit_amount : $this->credit_amount;
    }
}
