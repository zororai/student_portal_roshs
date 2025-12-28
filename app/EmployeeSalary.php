<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmployeeSalary extends Model
{
    protected $fillable = [
        'user_id',
        'basic_salary',
        'housing_allowance',
        'transport_allowance',
        'medical_allowance',
        'other_allowances',
        'tax_deduction',
        'pension_deduction',
        'other_deductions',
        'bank_name',
        'bank_account',
        'payment_method',
        'is_active',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'housing_allowance' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'medical_allowance' => 'decimal:2',
        'other_allowances' => 'decimal:2',
        'tax_deduction' => 'decimal:2',
        'pension_deduction' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class, 'salary_id');
    }

    public function getTotalAllowancesAttribute()
    {
        return $this->housing_allowance + $this->transport_allowance + 
               $this->medical_allowance + $this->other_allowances;
    }

    public function getTotalDeductionsAttribute()
    {
        return $this->tax_deduction + $this->pension_deduction + $this->other_deductions;
    }

    public function getGrossSalaryAttribute()
    {
        return $this->basic_salary + $this->total_allowances;
    }

    public function getNetSalaryAttribute()
    {
        return $this->gross_salary - $this->total_deductions;
    }
}
