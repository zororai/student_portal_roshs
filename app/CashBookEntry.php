<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\LedgerAccount;
use App\LedgerEntry;
use App\Services\LedgerPostingService;
use App\Traits\FinancialModelProtection;

class CashBookEntry extends Model
{
    use FinancialModelProtection;
    protected $fillable = [
        'entry_date',
        'term',
        'year',
        'reference_number',
        'transaction_type',
        'category',
        'description',
        'amount',
        'balance',
        'payment_method',
        'payer_payee',
        'related_payroll_id',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'amount' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function payroll()
    {
        return $this->belongsTo(Payroll::class, 'related_payroll_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function ledgerEntries()
    {
        return $this->hasMany(LedgerEntry::class);
    }

    public function isReceipt()
    {
        return $this->transaction_type === 'receipt';
    }

    public function isPayment()
    {
        return $this->transaction_type === 'payment';
    }

    public static function generateReferenceNumber()
    {
        $prefix = 'CB-' . date('Ymd') . '-';
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

    public static function getCategories()
    {
        return [
            'receipt' => [
                'school_fees' => 'School Fees',
                'registration' => 'Registration Fees',
                'exam_fees' => 'Exam Fees',
                'uniform' => 'Uniform Sales',
                'donations' => 'Donations',
                'grants' => 'Government Grants',
                'other_income' => 'Other Income',
            ],
            'payment' => [
                'salaries' => 'Salaries & Wages',
                'utilities' => 'Utilities (Electricity, Water)',
                'supplies' => 'Office Supplies',
                'maintenance' => 'Maintenance & Repairs',
                'transport' => 'Transport',
                'communication' => 'Communication',
                'equipment' => 'Equipment Purchase',
                'other_expense' => 'Other Expenses',
            ],
        ];
    }

    /**
     * Auto-post this cash book entry to the General Ledger
     * Creates double-entry: Cash account + corresponding Income/Expense account
     */
    public function postToLedger()
    {
        // Find or create Cash account
        $cashAccount = LedgerAccount::firstOrCreate(
            ['account_code' => '1001'],
            [
                'account_name' => 'Cash',
                'account_type' => 'asset',
                'category' => 'Current Assets',
                'description' => 'Cash on hand and in bank',
                'opening_balance' => 0,
                'current_balance' => 0,
            ]
        );

        // Map categories to ledger accounts
        $accountMappings = [
            'receipt' => [
                'school_fees' => ['code' => '4001', 'name' => 'School Fees Income', 'type' => 'income'],
                'registration' => ['code' => '4002', 'name' => 'Registration Fees Income', 'type' => 'income'],
                'exam_fees' => ['code' => '4003', 'name' => 'Exam Fees Income', 'type' => 'income'],
                'uniform' => ['code' => '4004', 'name' => 'Uniform Sales Income', 'type' => 'income'],
                'donations' => ['code' => '4005', 'name' => 'Donations Income', 'type' => 'income'],
                'grants' => ['code' => '4006', 'name' => 'Government Grants Income', 'type' => 'income'],
                'other_income' => ['code' => '4099', 'name' => 'Other Income', 'type' => 'income'],
            ],
            'payment' => [
                'salaries' => ['code' => '5001', 'name' => 'Salaries & Wages Expense', 'type' => 'expense'],
                'utilities' => ['code' => '5002', 'name' => 'Utilities Expense', 'type' => 'expense'],
                'supplies' => ['code' => '5003', 'name' => 'Office Supplies Expense', 'type' => 'expense'],
                'maintenance' => ['code' => '5004', 'name' => 'Maintenance & Repairs Expense', 'type' => 'expense'],
                'transport' => ['code' => '5005', 'name' => 'Transport Expense', 'type' => 'expense'],
                'communication' => ['code' => '5006', 'name' => 'Communication Expense', 'type' => 'expense'],
                'equipment' => ['code' => '5007', 'name' => 'Equipment Expense', 'type' => 'expense'],
                'other_expense' => ['code' => '5099', 'name' => 'Other Expenses', 'type' => 'expense'],
            ],
        ];

        // Get the corresponding account for this category
        $mapping = $accountMappings[$this->transaction_type][$this->category] ?? null;
        if (!$mapping) {
            // Default to other income/expense
            $mapping = $this->transaction_type === 'receipt' 
                ? $accountMappings['receipt']['other_income']
                : $accountMappings['payment']['other_expense'];
        }

        // Find or create the corresponding account
        $correspondingAccount = LedgerAccount::firstOrCreate(
            ['account_code' => $mapping['code']],
            [
                'account_name' => $mapping['name'],
                'account_type' => $mapping['type'],
                'category' => ucfirst($mapping['type']),
                'description' => $mapping['name'],
                'opening_balance' => 0,
                'current_balance' => 0,
            ]
        );

        // Post through LedgerPostingService (GOVERNANCE COMPLIANT)
        $ledgerService = app(LedgerPostingService::class);
        
        if ($this->transaction_type === 'receipt') {
            // Receipt: Debit Cash, Credit Income
            $ledgerService->postTransaction(
                [['account_code' => '1001', 'amount' => $this->amount, 'description' => $this->description]],
                [['account_code' => $mapping['code'], 'amount' => $this->amount, 'description' => $this->description]],
                [
                    'entry_date' => $this->entry_date,
                    'cash_book_entry_id' => $this->id,
                    'created_by' => $this->created_by,
                    'notes' => 'Auto-posted from Cash Book: ' . $this->reference_number,
                ]
            );
        } else {
            // Payment: Debit Expense, Credit Cash
            $ledgerService->postTransaction(
                [['account_code' => $mapping['code'], 'amount' => $this->amount, 'description' => $this->description]],
                [['account_code' => '1001', 'amount' => $this->amount, 'description' => $this->description]],
                [
                    'entry_date' => $this->entry_date,
                    'cash_book_entry_id' => $this->id,
                    'created_by' => $this->created_by,
                    'notes' => 'Auto-posted from Cash Book: ' . $this->reference_number,
                ]
            );
        }
    }

    /**
     * Ensure a ledger account exists
     */
    protected function ensureAccountExists($code, $name, $type, $category, $description)
    {
        return LedgerAccount::firstOrCreate(
            ['account_code' => $code],
            [
                'account_name' => $name,
                'account_type' => $type,
                'category' => $category,
                'description' => $description,
                'opening_balance' => 0,
                'current_balance' => 0,
            ]
        );
    }
}
