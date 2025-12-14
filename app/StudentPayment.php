<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Auditable;

class StudentPayment extends Model
{
    use Auditable;
    protected $fillable = [
        'student_id',
        'results_status_id',
        'term_fee_id',
        'amount_paid',
        'payment_date',
        'payment_method',
        'reference_number',
        'notes'
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'payment_date' => 'date'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function resultsStatus()
    {
        return $this->belongsTo(ResultsStatus::class);
    }

    public function termFee()
    {
        return $this->belongsTo(TermFee::class);
    }
}
