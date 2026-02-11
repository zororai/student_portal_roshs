# ROSHS Finance Hardening Governance Specification

## PURPOSE
This document defines **mandatory financial hardening rules** for the ROSHS Student Portal.
Any AI agent, developer, or refactoring process MUST follow this specification.

### Goals
- Financial correctness
- Double-entry integrity
- Audit safety
- Compliance readiness
- Zero silent data corruption

---

## CORE PRINCIPLES (NON-NEGOTIABLE)

| # | Principle | Enforcement |
|---|-----------|-------------|
| 1 | All money MUST pass through the Ledger | Use `LedgerPostingService` only |
| 2 | Debit MUST equal Credit at all times | Validated in `postTransaction()` |
| 3 | Posted financial records are IMMUTABLE | Block updates on posted entries |
| 4 | No deletes on financial data — only reversals | Use `reverseTransaction()` |
| 5 | Closed accounting periods CANNOT be modified | Check `period_closed` before posting |
| 6 | Every financial action MUST be audited | `AuditTrail::log()` on all actions |
| 7 | Authority separation MUST be enforced | Separate creator/approver/poster roles |

---

## 1. CENTRAL LEDGER POSTING RULE

### RULE
No model, controller, job, or service may create `LedgerEntry` records directly.

### ONLY ALLOWED PATH
```php
app('App\Services\LedgerPostingService')->postTransaction($debits, $credits, $metadata);
```

### ENFORCEMENT
- Any `LedgerEntry::create()` outside `LedgerPostingService` is a **VIOLATION**
- All financial modules must call the service

### REQUIRED SERVICE INTERFACE
```php
LedgerPostingService::postTransaction(
    array $debitEntries,   // [['account_code' => '1001', 'amount' => 100, 'description' => '...']]
    array $creditEntries,  // [['account_code' => '4001', 'amount' => 100, 'description' => '...']]
    array $metadata        // ['reference_number' => '', 'entry_date' => '', 'notes' => '']
): array
```

---

## 2. VIOLATION AUDIT (Current State)

### ❌ LedgerEntry::create() Violations Found

| File | Violations | Status |
|------|------------|--------|
| `app/Services/AssetManagementService.php` | 8 calls | **NEEDS FIX** |
| `app/CashBookEntry.php` | 4 calls | **NEEDS FIX** |
| `app/Http/Controllers/LedgerController.php` | 1 call | **NEEDS FIX** |
| `app/Services/LedgerPostingService.php` | 3 calls | ✅ ALLOWED |

### ❌ Financial Record Delete Violations Found

| File | Issue | Status |
|------|-------|--------|
| `FinanceController.php` | Deletes `CashBookEntry`, `SchoolIncome`, `SchoolExpense` | **NEEDS FIX** |
| `CashBookController.php` | Deletes `CashBookEntry` directly | **NEEDS FIX** |
| `JournalController.php` | Deletes `JournalBatch` (draft only - acceptable) | ⚠️ CONDITIONAL |

---

## 3. FINANCIAL MODELS PROTECTION

### Immutable When Posted
These models MUST NOT be deleted or updated after posting:

| Model | Protection Required |
|-------|---------------------|
| `LedgerEntry` | Block delete, block update |
| `CashBookEntry` | Block delete if `is_posted = true` |
| `StudentPayment` | Block delete, use reversal |
| `SupplierPayment` | Block delete, use reversal |
| `Payroll` | Block delete if `status != pending` |
| `JournalEntry` | Block delete if batch is posted |
| `AssetDepreciation` | Block delete if `ledger_entry_id` exists |
| `StudentInvoice` | Block delete if has payments |
| `SupplierInvoice` | Block delete if has payments |

### Implementation Pattern
```php
// In Model boot method
protected static function boot()
{
    parent::boot();
    
    static::deleting(function ($model) {
        if ($model->isPosted()) {
            throw new \Exception('Cannot delete posted financial record. Use reversal instead.');
        }
    });
    
    static::updating(function ($model) {
        if ($model->isPosted() && $model->isDirty(['amount', 'account_id'])) {
            throw new \Exception('Cannot modify posted financial record.');
        }
    });
}
```

---

## 4. DOUBLE-ENTRY VALIDATION

### Pre-Posting Check
Before any ledger posting, validate:
```php
$totalDebits = array_sum(array_column($debitEntries, 'amount'));
$totalCredits = array_sum(array_column($creditEntries, 'amount'));

if (round($totalDebits, 2) !== round($totalCredits, 2)) {
    throw new Exception("Transaction not balanced");
}
```

### Trial Balance Check
Periodic validation that total debits = total credits across all accounts.

---

## 5. AUDIT TRAIL REQUIREMENTS

### All Financial Actions Must Log
```php
AuditTrail::log(
    string $action,      // 'ledger_posting', 'payment_received', etc.
    string $description, // Human-readable description
    ?string $model_type, // Model class name
    ?int $model_id,      // Model ID
    ?array $metadata     // Additional context
);
```

### Required Audit Events
- Ledger posting
- Ledger reversal
- Payment received
- Payment made
- Invoice created
- Journal approved
- Journal posted
- Period closed
- Period reopened

---

## 6. PERIOD CONTROL

### Accounting Period Structure
```php
Schema::create('accounting_periods', function (Blueprint $table) {
    $table->id();
    $table->integer('year');
    $table->integer('term'); // 1, 2, 3
    $table->date('start_date');
    $table->date('end_date');
    $table->boolean('is_closed')->default(false);
    $table->timestamp('closed_at')->nullable();
    $table->foreignId('closed_by')->nullable();
    $table->timestamps();
});
```

### Period Check Before Posting
```php
public function postTransaction(...) {
    $period = AccountingPeriod::where('year', $year)
        ->where('term', $term)
        ->first();
    
    if ($period && $period->is_closed) {
        throw new Exception("Cannot post to closed period: Term {$term}, {$year}");
    }
    
    // Continue with posting...
}
```

---

## 7. AUTHORITY SEPARATION

### Roles & Permissions
| Action | Required Permission | Cannot Be Same As |
|--------|---------------------|-------------------|
| Create Journal | `journal.create` | - |
| Approve Journal | `journal.approve` | Creator |
| Post Journal | `journal.post` | Creator |
| Approve Payroll | `payroll.approve` | Creator |
| Mark Payroll Paid | `payroll.pay` | Creator, Approver |
| Close Period | `period.close` | - |
| Reopen Period | `period.reopen` | - |

### Implementation
```php
public function approve($id)
{
    $journal = JournalBatch::findOrFail($id);
    
    if ($journal->created_by === auth()->id()) {
        return back()->with('error', 'You cannot approve your own journal entry');
    }
    
    // Continue...
}
```

---

## 8. CHART OF ACCOUNTS (Zimbabwe School Context)

### Account Code Structure
| Range | Type | Examples |
|-------|------|----------|
| 1000-1999 | Assets | Cash, Bank, A/R, Fixed Assets, Accum. Depreciation |
| 2000-2999 | Liabilities | A/P, Tax Payable, Salaries Payable |
| 3000-3999 | Equity | Retained Earnings, Opening Balance Equity |
| 4000-4999 | Income | Tuition Fees, Registration Fees, Boarding Fees |
| 5000-5999 | Expenses | Salaries, Utilities, Maintenance, Depreciation |

### Standard Accounts
```
1001 - Cash on Hand
1002 - Bank Account (USD)
1003 - Bank Account (ZWL)
1100 - Accounts Receivable - Students
1200 - Fixed Assets
1250 - Accumulated Depreciation
2001 - Accounts Payable
2100 - PAYE Payable
2101 - NSSA Payable
3001 - Retained Earnings
4001 - Tuition Fees Income
4002 - Registration Fees Income
4003 - Boarding Fees Income
4099 - Other Income
5001 - Salaries & Wages
5002 - Utilities Expense
5003 - Maintenance Expense
5010 - Depreciation Expense
5099 - Other Expenses
```

---

## 9. LEDGER POSTING RULES

| Transaction | Debit | Credit |
|-------------|-------|--------|
| Student Payment Received | Cash/Bank (1001/1002) | A/R Students (1100) |
| Student Invoice Created | A/R Students (1100) | Fee Income (4001-4003) |
| Payroll Processed | Salary Expense (5001) | Cash/Payables (1001/2001) |
| Purchase Order (Expense) | Expense Account (5xxx) | Cash/A/P (1001/2001) |
| Purchase Order (Asset) | Fixed Assets (1200) | Cash/A/P (1001/2001) |
| Asset Depreciation | Depreciation Expense (5010) | Accum. Depreciation (1250) |
| Asset Disposal (Loss) | Cash + Loss | Fixed Asset + Accum. Dep. |
| Supplier Invoice | Expense/Asset | A/P (2001) |
| Supplier Payment | A/P (2001) | Cash/Bank (1001/1002) |
| Journal Entry | Any (balanced) | Any (balanced) |

---

## 10. COMPLIANCE CHECKLIST

### Before Going Live
- [ ] All `LedgerEntry::create()` calls go through `LedgerPostingService`
- [ ] Financial models have delete protection
- [ ] Financial models have update protection (for posted records)
- [ ] All financial actions create audit trail entries
- [ ] Authority separation enforced on approvals
- [ ] Period close/reopen functionality implemented
- [ ] Trial balance report available
- [ ] Double-entry validation active

### Periodic Checks
- [ ] Run trial balance - must be balanced
- [ ] Review audit trail for anomalies
- [ ] Verify no orphaned ledger entries
- [ ] Check for unposted journals older than 7 days

---

## 11. MIGRATION PATH FOR VIOLATIONS

### Step 1: AssetManagementService
Refactor all `LedgerEntry::create()` calls to use:
```php
$this->ledgerService->postTransaction($debits, $credits, $metadata);
```

### Step 2: CashBookEntry Model
Replace `postToLedger()` method to use `LedgerPostingService`:
```php
public function postToLedger()
{
    $ledgerService = app(LedgerPostingService::class);
    return $ledgerService->postTransaction(
        $this->getDebitEntries(),
        $this->getCreditEntries(),
        ['cash_book_entry_id' => $this->id, ...]
    );
}
```

### Step 3: LedgerController
Remove direct `storeEntry()` or route through service.

### Step 4: Add Delete Protection
Add `deleting` event handlers to all financial models.

### Step 5: Replace Delete Operations
Convert `destroyIncome()`, `destroyExpense()` in `FinanceController` to use reversals.

---

*Last Updated: February 2026*
*ROSHS Student Portal - Finance Module Governance*
