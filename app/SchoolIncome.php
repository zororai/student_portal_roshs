<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchoolIncome extends Model
{
    protected $table = 'school_incomes';

    protected $fillable = [
        'date',
        'category',
        'description',
        'amount',
        'payment_method',
        'reference_number',
        'received_by'
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2'
    ];
}
