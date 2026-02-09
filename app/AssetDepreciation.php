<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetDepreciation extends Model
{
    protected $fillable = [
        'asset_id',
        'year',
        'opening_value',
        'depreciation_amount',
        'closing_value',
        'posted_to_ledger',
        'ledger_entry_id',
    ];

    protected $casts = [
        'year' => 'integer',
        'opening_value' => 'decimal:2',
        'depreciation_amount' => 'decimal:2',
        'closing_value' => 'decimal:2',
        'posted_to_ledger' => 'boolean',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function ledgerEntry()
    {
        return $this->belongsTo(LedgerEntry::class);
    }

    public function isPosted()
    {
        return $this->posted_to_ledger;
    }

    public function canBeModified()
    {
        return !$this->posted_to_ledger;
    }

    public function getDepreciationRateAttribute()
    {
        if ($this->opening_value <= 0) {
            return 0;
        }
        return ($this->depreciation_amount / $this->opening_value) * 100;
    }
}
