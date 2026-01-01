<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MedicalReport extends Model
{
    protected $fillable = [
        'parent_id',
        'student_id',
        'condition_type',
        'condition_name',
        'description',
        'medications',
        'emergency_instructions',
        'diagnosis_date',
        'doctor_name',
        'doctor_contact',
        'attachment_path',
        'status',
        'acknowledged_by',
        'acknowledged_at',
        'admin_response',
    ];

    protected $casts = [
        'diagnosis_date' => 'date',
        'acknowledged_at' => 'datetime',
    ];

    public function parent()
    {
        return $this->belongsTo(Parents::class, 'parent_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function acknowledgedBy()
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    public function getStatusBadgeAttribute()
    {
        switch ($this->status) {
            case 'pending':
                return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-700">Pending</span>';
            case 'acknowledged':
                return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-700">Acknowledged</span>';
            case 'reviewed':
                return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700">Reviewed</span>';
            default:
                return '<span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">Unknown</span>';
        }
    }
}
