<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeeCategory extends Model
{
    protected $fillable = [
        'name', // e.g., 'Tuition', 'Library', 'Sports'
        'amount', // Amount for the category
        'period', // Time period for the fee
    ];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'fee_category_student', 'fee_category_id', 'student_id');
    }
}