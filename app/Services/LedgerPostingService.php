<?php

namespace App\Services;

use App\LedgerAccount;
use App\LedgerEntry;
use App\AuditTrail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class LedgerPostingService
{
    /**
     * Post a double-entry transaction to the ledger
     * 
     * @param array $debitEntries Array of ['account_code' => string, 'amount' => float, 'description' => string]
     * @param array $creditEntries Array of ['account_code' => string, 'amount' => float, 'description' => string]
     * @param array $metadata Additional metadata (reference_number, entry_date, term, year, etc.)
     * @return array Created ledger entries
     * @throws Exception
     */
    public function postTransaction(array $debitEntries, array $creditEntries, array $metadata = [])
    {
        // Validate double-entry rule
        $totalDebits = array_sum(array_column($debitEntries, 'amount'));
        $totalCredits = array_sum(array_column($creditEntries, 'amount'));
        
        if (round($totalDebits, 2) !== round($totalCredits, 2)) {
            throw new Exception("Transaction not balanced. Debits: {$totalDebits}, Credits: {$totalCredits}");
        }
        
        DB::beginTransaction();
        
        try {
            $createdEntries = [];
            $referenceNumber = $metadata['reference_number'] ?? LedgerEntry::generateReferenceNumber();
            $entryDate = $metadata['entry_date'] ?? now();
            $term = $metadata['term'] ?? null;
            $year = $metadata['year'] ?? null;
            $createdBy = $metadata['created_by'] ?? Auth::id();
            $notes = $metadata['notes'] ?? null;
            
            // Process debit entries
            foreach ($debitEntries as $entry) {
                $account = $this->getAccount($entry['account_code']);
                
                $ledgerEntry = LedgerEntry::create([
                    'entry_date' => $entryDate,
                    'term' => $term,
                    'year' => $year,
                    'reference_number' => $referenceNumber,
                    'account_id' => $account->id,
                    'entry_type' => 'debit',
                    'amount' => $entry['amount'],
                    'description' => $entry['description'] ?? $metadata['description'] ?? 'Transaction',
                    'cash_book_entry_id' => $metadata['cash_book_entry_id'] ?? null,
                    'payroll_id' => $metadata['payroll_id'] ?? null,
                    'created_by' => $createdBy,
                    'notes' => $notes,
                ]);
                
                $account->updateBalance();
                $createdEntries[] = $ledgerEntry;
            }
            
            // Process credit entries
            foreach ($creditEntries as $entry) {
                $account = $this->getAccount($entry['account_code']);
                
                $ledgerEntry = LedgerEntry::create([
                    'entry_date' => $entryDate,
                    'term' => $term,
                    'year' => $year,
                    'reference_number' => $referenceNumber,
                    'account_id' => $account->id,
                    'entry_type' => 'credit',
                    'amount' => $entry['amount'],
                    'description' => $entry['description'] ?? $metadata['description'] ?? 'Transaction',
                    'cash_book_entry_id' => $metadata['cash_book_entry_id'] ?? null,
                    'payroll_id' => $metadata['payroll_id'] ?? null,
                    'created_by' => $createdBy,
                    'notes' => $notes,
                ]);
                
                $account->updateBalance();
                $createdEntries[] = $ledgerEntry;
            }
            
            // Log to audit trail
            AuditTrail::log(
                'ledger_posting',
                "Posted transaction {$referenceNumber}. Debits: {$totalDebits}, Credits: {$totalCredits}",
                null,
                null,
                [
                    'reference_number' => $referenceNumber,
                    'total_debits' => $totalDebits,
                    'total_credits' => $totalCredits,
                    'entry_count' => count($createdEntries),
                ]
            );
            
            DB::commit();
            
            return $createdEntries;
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Reverse a transaction by creating opposite entries
     * 
     * @param string $referenceNumber Original transaction reference
     * @param string $reason Reason for reversal
     * @return array Created reversal entries
     * @throws Exception
     */
    public function reverseTransaction(string $referenceNumber, string $reason)
    {
        $originalEntries = LedgerEntry::where('reference_number', $referenceNumber)->get();
        
        if ($originalEntries->isEmpty()) {
            throw new Exception("Transaction {$referenceNumber} not found");
        }
        
        DB::beginTransaction();
        
        try {
            $reversalEntries = [];
            $reversalReference = 'REV-' . $referenceNumber;
            
            foreach ($originalEntries as $original) {
                // Create opposite entry
                $reversalEntry = LedgerEntry::create([
                    'entry_date' => now(),
                    'term' => $original->term,
                    'year' => $original->year,
                    'reference_number' => $reversalReference,
                    'account_id' => $original->account_id,
                    'entry_type' => $original->entry_type === 'debit' ? 'credit' : 'debit',
                    'amount' => $original->amount,
                    'description' => "REVERSAL: {$reason}",
                    'created_by' => Auth::id(),
                    'notes' => "Reversal of {$referenceNumber}. Reason: {$reason}",
                ]);
                
                $original->account->updateBalance();
                $reversalEntries[] = $reversalEntry;
            }
            
            // Log to audit trail
            AuditTrail::log(
                'ledger_reversal',
                "Reversed transaction {$referenceNumber}. Reason: {$reason}",
                null,
                null,
                [
                    'original_reference' => $referenceNumber,
                    'reversal_reference' => $reversalReference,
                    'reason' => $reason,
                ]
            );
            
            DB::commit();
            
            return $reversalEntries;
            
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
    
    /**
     * Get ledger account by code
     * 
     * @param string $accountCode
     * @return LedgerAccount
     * @throws Exception
     */
    protected function getAccount(string $accountCode)
    {
        $account = LedgerAccount::where('account_code', $accountCode)
            ->where('is_active', true)
            ->first();
        
        if (!$account) {
            throw new Exception("Ledger account {$accountCode} not found or inactive");
        }
        
        return $account;
    }
    
    /**
     * Validate transaction balance
     * 
     * @param array $debitEntries
     * @param array $creditEntries
     * @return bool
     */
    public function isBalanced(array $debitEntries, array $creditEntries)
    {
        $totalDebits = array_sum(array_column($debitEntries, 'amount'));
        $totalCredits = array_sum(array_column($creditEntries, 'amount'));
        
        return round($totalDebits, 2) === round($totalCredits, 2);
    }
    
    /**
     * Get trial balance
     * 
     * @param string|null $asOfDate
     * @return array
     */
    public function getTrialBalance($asOfDate = null)
    {
        $query = LedgerAccount::with(['entries' => function ($q) use ($asOfDate) {
            if ($asOfDate) {
                $q->where('entry_date', '<=', $asOfDate);
            }
        }])->where('is_active', true);
        
        $accounts = $query->get();
        $trialBalance = [];
        $totalDebits = 0;
        $totalCredits = 0;
        
        foreach ($accounts as $account) {
            $debits = $account->entries->where('entry_type', 'debit')->sum('amount');
            $credits = $account->entries->where('entry_type', 'credit')->sum('amount');
            
            // Calculate balance based on account type
            if (in_array($account->account_type, ['asset', 'expense'])) {
                $balance = $account->opening_balance + $debits - $credits;
                $debitBalance = $balance > 0 ? $balance : 0;
                $creditBalance = $balance < 0 ? abs($balance) : 0;
            } else {
                $balance = $account->opening_balance + $credits - $debits;
                $debitBalance = $balance < 0 ? abs($balance) : 0;
                $creditBalance = $balance > 0 ? $balance : 0;
            }
            
            if ($debitBalance != 0 || $creditBalance != 0) {
                $trialBalance[] = [
                    'account_code' => $account->account_code,
                    'account_name' => $account->account_name,
                    'account_type' => $account->account_type,
                    'debit' => $debitBalance,
                    'credit' => $creditBalance,
                ];
                
                $totalDebits += $debitBalance;
                $totalCredits += $creditBalance;
            }
        }
        
        return [
            'accounts' => $trialBalance,
            'total_debits' => $totalDebits,
            'total_credits' => $totalCredits,
            'is_balanced' => round($totalDebits, 2) === round($totalCredits, 2),
            'as_of_date' => $asOfDate ?? now()->toDateString(),
        ];
    }
}
