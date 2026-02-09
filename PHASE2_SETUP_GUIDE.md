# Phase 2 Setup Guide - Quick Start

## 🚀 Quick Setup (5 Minutes)

### Step 1: Run Migrations
```bash
php artisan migrate
```

**This creates:**
- `journal_batches` and `journal_entries` tables
- `student_accounts`, `student_invoices`, `student_invoice_items` tables
- `supplier_invoices` and `supplier_payments` tables

---

### Step 2: Create Permissions
```bash
php artisan tinker
```

Then run:
```php
DB::table('permissions')->insert([
    ['name' => 'sidebar-journals', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'sidebar-receivables', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
    ['name' => 'sidebar-payables', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
]);
```

Type `exit` to leave tinker.

---

### Step 3: Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

---

### Step 4: Assign Permissions

1. Go to: `http://student_portal_roshs.test/role-edit/1`
2. Under **Finance & Accounting**, enable:
   - ✅ General Journal
   - ✅ Accounts Receivable
   - ✅ Accounts Payable
3. Click **Save**

---

## 📊 Testing the System

### Test 1: Create a Journal Entry
```
1. Go to: /admin/finance/journals
2. Click "Create Journal"
3. Add entries:
   - Debit: 5000 (Salaries) - $1000
   - Credit: 1010 (Bank) - $1000
4. Save as Draft
5. Approve
6. Post to Ledger
7. Check Trial Balance to verify
```

### Test 2: Create Student Invoice
```
1. Go to: /admin/finance/receivables/invoices/create
2. Select a student
3. Add invoice items (e.g., Tuition $500)
4. Set due date
5. Save
6. Invoice automatically posts to ledger
7. Check A/R Aging report
```

### Test 3: Create Supplier Invoice & Payment
```
1. Go to: /admin/finance/payables/invoices/create
2. Select supplier
3. Enter amount and category
4. Save (posts to ledger)
5. Click "Record Payment"
6. Enter payment details
7. Save (posts to ledger)
8. Check A/P Aging report
```

---

## 🔗 Available URLs

### General Journal
- `/admin/finance/journals` - List journals
- `/admin/finance/journals/create` - Create journal

### Accounts Receivable
- `/admin/finance/receivables` - A/R Dashboard
- `/admin/finance/receivables/invoices` - List invoices
- `/admin/finance/receivables/invoices/create` - Create invoice
- `/admin/finance/receivables/aging` - A/R Aging Report
- `/admin/finance/receivables/student/{id}/statement` - Student Statement

### Accounts Payable
- `/admin/finance/payables` - A/P Dashboard
- `/admin/finance/payables/invoices` - List invoices
- `/admin/finance/payables/invoices/create` - Create invoice
- `/admin/finance/payables/aging` - A/P Aging Report

---

## 📝 What's Working

✅ **Models & Migrations**: All database tables created  
✅ **Controllers**: Full CRUD for journals, invoices, payments  
✅ **Ledger Integration**: All transactions post to ledger automatically  
✅ **Routes**: All routes configured with permissions  
✅ **Permissions**: Created and added to RolePermissionController  
✅ **Audit Trail**: All actions logged  
✅ **Balance Validation**: Journal entries must balance  
✅ **Approval Workflow**: Draft → Approved → Posted  

---

## ⚠️ What's Pending

**Views Need to be Created:**
- General Journal views (index, create, edit, show)
- A/R views (dashboard, invoices, aging, statements)
- A/P views (dashboard, invoices, payments, aging)

**Note**: Controllers are ready and will work once views are created. Views should follow the same pattern as existing finance views in `resources/views/backend/finance/`.

---

## 🎯 Usage Examples

### Example 1: Post a Manual Journal Entry
```php
use App\JournalBatch;
use App\JournalEntry;

$batch = JournalBatch::create([
    'reference' => JournalBatch::generateReference(),
    'description' => 'Salary payment - January',
    'created_by' => auth()->id(),
]);

// Add debit entry
JournalEntry::create([
    'journal_batch_id' => $batch->id,
    'ledger_account_id' => 10, // Salaries account
    'debit_amount' => 5000,
    'credit_amount' => 0,
    'narration' => 'January salaries',
]);

// Add credit entry
JournalEntry::create([
    'journal_batch_id' => $batch->id,
    'ledger_account_id' => 5, // Bank account
    'debit_amount' => 0,
    'credit_amount' => 5000,
    'narration' => 'Bank payment',
]);

$batch->calculateTotals();
$batch->approve(auth()->id());
$batch->post(auth()->id());
```

### Example 2: Create Student Invoice
```php
use App\StudentInvoice;
use App\StudentInvoiceItem;

$invoice = StudentInvoice::create([
    'invoice_number' => StudentInvoice::generateInvoiceNumber(),
    'student_id' => 1,
    'term' => 'Term 1',
    'year' => 2026,
    'amount' => 500,
    'invoice_date' => now(),
    'due_date' => now()->addDays(30),
    'description' => 'Term 1 Tuition Fees',
    'created_by' => auth()->id(),
]);

StudentInvoiceItem::create([
    'invoice_id' => $invoice->id,
    'description' => 'Tuition - Day Student',
    'amount' => 500,
]);

$invoice->postToLedger(); // Posts: Dr A/R, Cr Income
```

### Example 3: Record Student Payment
```php
$invoice = StudentInvoice::find(1);
$invoice->recordPayment(250); // Partial payment

// Posts to ledger: Dr Bank, Cr A/R
// Updates invoice status to 'partial'
// Updates student account balance
```

### Example 4: Create Supplier Invoice & Payment
```php
use App\SupplierInvoice;
use App\SupplierPayment;

// Create invoice
$invoice = SupplierInvoice::create([
    'invoice_number' => SupplierInvoice::generateInvoiceNumber(),
    'supplier_id' => 1,
    'amount' => 1000,
    'invoice_date' => now(),
    'due_date' => now()->addDays(30),
    'description' => 'Electricity bill',
    'expense_type' => 'expense',
    'expense_category' => 'utilities',
    'created_by' => auth()->id(),
]);

$invoice->postToLedger(); // Posts: Dr Utilities, Cr A/P

// Record payment
$payment = SupplierPayment::create([
    'payment_number' => SupplierPayment::generatePaymentNumber(),
    'supplier_invoice_id' => $invoice->id,
    'amount' => 1000,
    'payment_date' => now(),
    'payment_method' => 'bank',
    'created_by' => auth()->id(),
]);

$payment->postToLedger(); // Posts: Dr A/P, Cr Bank
```

---

## 🔍 Troubleshooting

### Issue: Permissions not showing
**Solution**: Clear cache and refresh permissions
```bash
php artisan cache:clear
php artisan permission:cache-reset
```

### Issue: Migration fails
**Solution**: Check if tables already exist
```bash
php artisan migrate:status
```

### Issue: Journal won't approve
**Solution**: Check that debits = credits
```php
$batch->calculateTotals();
echo "Debit: " . $batch->total_debit;
echo "Credit: " . $batch->total_credit;
echo "Balanced: " . ($batch->isBalanced() ? 'Yes' : 'No');
```

### Issue: Invoice not posting to ledger
**Solution**: Check LedgerPostingService and account codes
```php
// Verify accounts exist
use App\LedgerAccount;
LedgerAccount::where('account_code', '1100')->first(); // A/R
LedgerAccount::where('account_code', '4000')->first(); // Income
```

---

## 📚 Next Steps

1. **Create Views**: Build Blade views for all controllers
2. **Add to Sidebar**: Add navigation links in `sidebar.blade.php`
3. **Test Workflow**: Complete end-to-end testing
4. **Train Users**: Create user documentation
5. **Phase 3**: Implement Bank Reconciliation and Cost Centers

---

## 🎓 Key Concepts

**Immutable Ledger**: Once posted, journal entries cannot be edited. Use reversals.

**Approval Workflow**: 
- Draft = Editable
- Approved = Locked, ready to post
- Posted = In ledger, immutable

**Automatic Posting**:
- Student invoices → Dr A/R, Cr Income
- Student payments → Dr Bank, Cr A/R
- Supplier invoices → Dr Expense, Cr A/P
- Supplier payments → Dr A/P, Cr Bank

**Balance Validation**: All journal batches must balance before approval.

**Audit Trail**: Every financial action is logged to `audit_trails` table.

---

**Setup Complete!** 🎉

Your Phase 2 implementation is ready. Run migrations, create permissions, and start testing!
