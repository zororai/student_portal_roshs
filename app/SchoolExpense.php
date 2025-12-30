<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolExpense extends Model
{
    protected $table = 'school_expenses';

    protected $fillable = [
        'date',
        'term',
        'year',
        'category',
        'description',
        'amount',
        'payment_method',
        'reference_number',
        'paid_to',
        'approved_by'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2'
    ];
}
