<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Services\LedgerPostingService;
use App\Traits\FinancialModelProtection;

class SupplierPayment extends Model
{
    use FinancialModelProtection;
    protected $fillable = [
        'payment_number',
        'supplier_invoice_id',
        'amount',
        'payment_date',
        'payment_method',
        'reference',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function invoice()
    {
        return $this->belongsTo(SupplierInvoice::class, 'supplier_invoice_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Generate unique payment number
     */
    public static function generatePaymentNumber()
    {
        $prefix = 'SPMT-' . date('Ymd') . '-';
        $lastPayment = self::where('payment_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastPayment) {
            $lastNumber = intval(substr($lastPayment->payment_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $newNumber;
    }

    /**
     * Post payment to ledger (Debit A/P, Credit Cash/Bank)
     */
    public function postToLedger()
    {
        $ledgerService = new LedgerPostingService();
        
        // Determine cash/bank account based on payment method
        $creditAccountCode = '1010'; // Default: Bank Account - USD
        
        if (strtolower($this->payment_method) === 'cash') {
            $creditAccountCode = '1000'; // Cash on Hand
        }
        
        $ledgerService->postTransaction(
            debitEntries: [
                [
                    'account_code' => '2000', // Accounts Payable - Suppliers
                    'amount' => $this->amount,
                    'description' => "Payment {$this->payment_number} - {$this->invoice->supplier->name}",
                ]
            ],
            creditEntries: [
                [
                    'account_code' => $creditAccountCode,
                    'amount' => $this->amount,
                    'description' => "Payment {$this->payment_number} - {$this->invoice->supplier->name}",
                ]
            ],
            metadata: [
                'reference_number' => $this->payment_number,
                'entry_date' => $this->payment_date,
                'description' => "Supplier payment",
                'notes' => $this->notes ?? "Payment for invoice {$this->invoice->invoice_number}",
            ]
        );

        // Update invoice paid amount and status
        $invoice = $this->invoice;
        $invoice->paid_amount += $this->amount;
        
        if ($invoice->paid_amount >= $invoice->amount) {
            $invoice->status = 'paid';
        } elseif ($invoice->paid_amount > 0) {
            $invoice->status = 'partial';
        }
        
        $invoice->save();
    }
}
