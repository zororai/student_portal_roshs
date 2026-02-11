<?php

namespace App\Traits;

use Exception;

/**
 * Financial Model Protection Trait
 * 
 * GOVERNANCE COMPLIANCE:
 * - Blocks deletion of posted financial records
 * - Blocks modification of critical fields on posted records
 * - Enforces use of reversals for corrections
 * 
 * Use this trait on all financial models:
 * LedgerEntry, CashBookEntry, StudentPayment, SupplierPayment,
 * Payroll, JournalEntry, AssetDepreciation, StudentInvoice, SupplierInvoice
 */
trait FinancialModelProtection
{
    /**
     * Boot the trait
     */
    public static function bootFinancialModelProtection()
    {
        // Block deletion of posted records
        static::deleting(function ($model) {
            if ($model->isPostedFinancialRecord()) {
                throw new Exception(
                    'Cannot delete posted financial record. ' .
                    'Use reversal transaction instead. ' .
                    'Reference: ' . ($model->reference_number ?? $model->id)
                );
            }
        });

        // Block critical field updates on posted records
        static::updating(function ($model) {
            if ($model->isPostedFinancialRecord()) {
                $protectedFields = $model->getProtectedFinancialFields();
                
                foreach ($protectedFields as $field) {
                    if ($model->isDirty($field)) {
                        throw new Exception(
                            "Cannot modify '{$field}' on posted financial record. " .
                            'Use reversal transaction instead.'
                        );
                    }
                }
            }
        });
    }

    /**
     * Check if this record is considered "posted" and should be protected
     * Override in specific models for custom logic
     */
    public function isPostedFinancialRecord(): bool
    {
        // Default checks for common posted indicators
        if (property_exists($this, 'posted_to_ledger') || isset($this->posted_to_ledger)) {
            return (bool) $this->posted_to_ledger;
        }

        if (property_exists($this, 'is_posted') || isset($this->is_posted)) {
            return (bool) $this->is_posted;
        }

        if (property_exists($this, 'status') || isset($this->status)) {
            return in_array($this->status, ['posted', 'approved', 'paid', 'completed']);
        }

        // For LedgerEntry - always protected once created
        if ($this->getTable() === 'ledger_entries') {
            return true;
        }

        return false;
    }

    /**
     * Get list of fields that cannot be modified on posted records
     * Override in specific models for custom fields
     */
    public function getProtectedFinancialFields(): array
    {
        return [
            'amount',
            'debit_amount',
            'credit_amount',
            'account_id',
            'entry_type',
            'total_amount',
            'net_amount',
            'gross_amount',
        ];
    }

    /**
     * Check if this record can be deleted
     */
    public function canDelete(): bool
    {
        return !$this->isPostedFinancialRecord();
    }

    /**
     * Check if this record can be edited
     */
    public function canEdit(): bool
    {
        return !$this->isPostedFinancialRecord();
    }

    /**
     * Get reversal instructions for this record type
     */
    public function getReversalInstructions(): string
    {
        return 'To correct this record, create a reversal entry using the Journal system or contact your administrator.';
    }
}
