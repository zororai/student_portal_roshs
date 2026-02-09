<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Services\LedgerPostingService;

class SupplierInvoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'supplier_id',
        'supplier_invoice_number',
        'amount',
        'invoice_date',
        'due_date',
        'status',
        'paid_amount',
        'description',
        'expense_type',
        'expense_category',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'invoice_date' => 'date',
        'due_date' => 'date',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate unique invoice number
     */
    public static function generateInvoiceNumber()
    {
        $prefix = 'SINV-' . date('Ymd') . '-';
        $lastInvoice = self::where('invoice_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastInvoice) {
            $lastNumber = intval(substr($lastInvoice->invoice_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $newNumber;
    }

    /**
     * Post invoice to ledger (Debit Expense/Asset, Credit A/P)
     */
    public function postToLedger()
    {
        $ledgerService = new LedgerPostingService();
        
        // Determine debit account based on expense type and category
        $debitAccountCode = '5900'; // Default: Miscellaneous Expenses
        
        if ($this->expense_type === 'asset') {
            $debitAccountCode = '1500'; // Property, Plant & Equipment
        } else {
            // Map expense categories to accounts
            $categoryMapping = [
                'salaries' => '5000',
                'utilities' => '5100',
                'electricity' => '5100',
                'water' => '5110',
                'maintenance' => '5200',
                'teaching_materials' => '5400',
                'office_supplies' => '5500',
                'transport' => '5600',
                'food' => '5700',
            ];
            
            $category = strtolower($this->expense_category ?? '');
            $debitAccountCode = $categoryMapping[$category] ?? '5900';
        }
        
        $ledgerService->postTransaction(
            debitEntries: [
                [
                    'account_code' => $debitAccountCode,
                    'amount' => $this->amount,
                    'description' => "Supplier invoice {$this->invoice_number} - {$this->supplier->name}",
                ]
            ],
            creditEntries: [
                [
                    'account_code' => '2000', // Accounts Payable - Suppliers
                    'amount' => $this->amount,
                    'description' => "Supplier invoice {$this->invoice_number} - {$this->supplier->name}",
                ]
            ],
            metadata: [
                'reference_number' => $this->invoice_number,
                'entry_date' => $this->invoice_date,
                'description' => $this->description,
                'notes' => "Supplier invoice posted to ledger",
            ]
        );
    }

    /**
     * Get outstanding amount
     */
    public function getOutstandingAmount()
    {
        return $this->amount - $this->paid_amount;
    }

    /**
     * Check if invoice is overdue
     */
    public function isOverdue()
    {
        return $this->status !== 'paid' && $this->due_date < now();
    }

    /**
     * Get days overdue
     */
    public function getDaysOverdue()
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        
        return now()->diffInDays($this->due_date);
    }
}
