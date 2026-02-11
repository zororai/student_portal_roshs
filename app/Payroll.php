<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\FinancialModelProtection;

class Payroll extends Model
{
    use FinancialModelProtection;
    protected $fillable = [
        'user_id',
        'salary_id',
        'pay_period',
        'pay_date',
        'basic_salary',
        'total_allowances',
        'total_deductions',
        'gross_salary',
        'net_salary',
        'days_worked',
        'days_absent',
        'notes',
        'status',
        'approved_by',
        'approved_at',
        'paid_by',
        'paid_at',
    ];

    protected $casts = [
        'pay_date' => 'date',
        'approved_at' => 'datetime',
        'paid_at' => 'datetime',
        'basic_salary' => 'decimal:2',
        'total_allowances' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'net_salary' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function salary()
    {
        return $this->belongsTo(EmployeeSalary::class, 'salary_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function payer()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function cashBookEntry()
    {
        return $this->hasOne(CashBookEntry::class, 'related_payroll_id');
    }

    public function ledgerEntries()
    {
        return $this->hasMany(LedgerEntry::class);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function getStatusBadgeAttribute()
    {
        switch ($this->status) {
            case 'pending':
                return '<span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">Pending</span>';
            case 'approved':
                return '<span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Approved</span>';
            case 'paid':
                return '<span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Paid</span>';
            default:
                return '<span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Unknown</span>';
        }
    }
}
