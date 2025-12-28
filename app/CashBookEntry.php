<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CashBookEntry extends Model
{
    protected $fillable = [
        'entry_date',
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
}
