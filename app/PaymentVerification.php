<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PaymentVerification extends Model
{
    protected $fillable = [
        'parent_id',
        'student_id',
        'receipt_number',
        'receipt_file',
        'amount_paid',
        'payment_date',
        'notes',
        'status',
        'verified_by',
        'verified_at',
        'admin_notes'
    ];

    protected $dates = ['payment_date', 'verified_at'];

    protected $casts = [
        'amount_paid' => 'decimal:2',
    ];

    public function parent()
    {
        return $this->belongsTo(Parents::class, 'parent_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function getStatusBadgeAttribute()
    {
        switch ($this->status) {
            case 'pending':
                return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>';
            case 'verified':
                return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Verified</span>';
            case 'rejected':
                return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>';
            default:
                return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Unknown</span>';
        }
    }
}
