<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Services\LedgerPostingService;

class StudentInvoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'student_id',
        'term',
        'year',
        'amount',
        'invoice_date',
        'due_date',
        'status',
        'paid_amount',
        'description',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'invoice_date' => 'date',
        'due_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function items()
    {
        return $this->hasMany(StudentInvoiceItem::class, 'invoice_id');
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
        $prefix = 'INV-' . date('Ymd') . '-';
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
     * Post invoice to ledger (Debit A/R, Credit Income)
     */
    public function postToLedger()
    {
        $ledgerService = new LedgerPostingService();
        
        // Determine income account based on description/items
        $incomeAccountCode = '4000'; // Default: Tuition Fees - Day Students
        
        // You can customize this logic based on invoice items
        if (stripos($this->description, 'boarding') !== false) {
            $incomeAccountCode = '4040'; // Boarding Fees
        } elseif (stripos($this->description, 'registration') !== false) {
            $incomeAccountCode = '4020'; // Registration Fees
        } elseif (stripos($this->description, 'exam') !== false) {
            $incomeAccountCode = '4030'; // Exam Fees
        }
        
        $ledgerService->postTransaction(
            debitEntries: [
                [
                    'account_code' => '1100', // Accounts Receivable - Students
                    'amount' => $this->amount,
                    'description' => "Invoice {$this->invoice_number} - {$this->student->name}",
                ]
            ],
            creditEntries: [
                [
                    'account_code' => $incomeAccountCode,
                    'amount' => $this->amount,
                    'description' => "Invoice {$this->invoice_number} - {$this->student->name}",
                ]
            ],
            metadata: [
                'reference_number' => $this->invoice_number,
                'entry_date' => $this->invoice_date,
                'term' => $this->term,
                'year' => $this->year,
                'description' => $this->description,
                'notes' => "Student invoice posted to ledger",
            ]
        );
    }

    /**
     * Record payment against invoice
     */
    public function recordPayment($amount, $paymentDate = null)
    {
        $paymentDate = $paymentDate ?? now();
        
        // Update paid amount
        $this->paid_amount += $amount;
        
        // Update status
        if ($this->paid_amount >= $this->amount) {
            $this->status = 'paid';
        } elseif ($this->paid_amount > 0) {
            $this->status = 'partial';
        }
        
        $this->save();
        
        // Post payment to ledger (Debit Cash/Bank, Credit A/R)
        $ledgerService = new LedgerPostingService();
        
        $ledgerService->postTransaction(
            debitEntries: [
                [
                    'account_code' => '1010', // Bank Account - USD
                    'amount' => $amount,
                    'description' => "Payment for invoice {$this->invoice_number} - {$this->student->name}",
                ]
            ],
            creditEntries: [
                [
                    'account_code' => '1100', // Accounts Receivable - Students
                    'amount' => $amount,
                    'description' => "Payment for invoice {$this->invoice_number} - {$this->student->name}",
                ]
            ],
            metadata: [
                'reference_number' => 'PMT-' . $this->invoice_number,
                'entry_date' => $paymentDate,
                'term' => $this->term,
                'year' => $this->year,
                'description' => "Student payment received",
                'notes' => "Payment recorded against invoice {$this->invoice_number}",
            ]
        );
        
        // Update student account balance
        $studentAccount = StudentAccount::firstOrCreate(
            ['student_id' => $this->student_id],
            ['opening_balance' => 0, 'current_balance' => 0]
        );
        $studentAccount->updateBalance();
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
