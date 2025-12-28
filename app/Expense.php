<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Expense extends Model
{
    protected $fillable = [
        'expense_number',
        'expense_date',
        'category_id',
        'vendor_name',
        'description',
        'amount',
        'payment_status',
        'payment_method',
        'receipt_number',
        'attachment',
        'approved_by',
        'approved_at',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateExpenseNumber()
    {
        $prefix = 'EXP-' . Carbon::now()->format('Ymd') . '-';
        $lastExpense = self::where('expense_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($lastExpense) {
            $lastNumber = intval(substr($lastExpense->expense_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return $prefix . $newNumber;
    }
}
