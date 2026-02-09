<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentAccount extends Model
{
    protected $fillable = [
        'student_id',
        'opening_balance',
        'current_balance',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function invoices()
    {
        return $this->hasMany(StudentInvoice::class, 'student_id', 'student_id');
    }

    /**
     * Update account balance
     */
    public function updateBalance()
    {
        $totalInvoiced = $this->invoices()->sum('amount');
        $totalPaid = $this->invoices()->sum('paid_amount');
        
        $this->current_balance = $this->opening_balance + $totalInvoiced - $totalPaid;
        $this->save();
    }

    /**
     * Get outstanding balance
     */
    public function getOutstandingBalance()
    {
        return $this->current_balance;
    }

    /**
     * Check if account is in arrears
     */
    public function isInArrears()
    {
        return $this->current_balance > 0;
    }

    /**
     * Get aging breakdown (30/60/90+ days)
     */
    public function getAgingBreakdown()
    {
        $now = now();
        
        $current = 0;      // 0-30 days
        $days30 = 0;       // 31-60 days
        $days60 = 0;       // 61-90 days
        $days90Plus = 0;   // 90+ days
        
        $unpaidInvoices = $this->invoices()
            ->whereIn('status', ['unpaid', 'partial'])
            ->get();
        
        foreach ($unpaidInvoices as $invoice) {
            $daysOverdue = $now->diffInDays($invoice->due_date);
            $outstanding = $invoice->amount - $invoice->paid_amount;
            
            if ($daysOverdue <= 30) {
                $current += $outstanding;
            } elseif ($daysOverdue <= 60) {
                $days30 += $outstanding;
            } elseif ($daysOverdue <= 90) {
                $days60 += $outstanding;
            } else {
                $days90Plus += $outstanding;
            }
        }
        
        return [
            'current' => $current,
            '30_days' => $days30,
            '60_days' => $days60,
            '90_plus_days' => $days90Plus,
            'total' => $current + $days30 + $days60 + $days90Plus,
        ];
    }
}
