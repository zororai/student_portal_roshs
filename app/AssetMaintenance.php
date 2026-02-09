<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetMaintenance extends Model
{
    protected $fillable = [
        'asset_id',
        'maintenance_type',
        'description',
        'reported_date',
        'scheduled_date',
        'completed_date',
        'cost',
        'status',
        'performed_by',
        'approved_by',
        'notes',
    ];

    protected $casts = [
        'reported_date' => 'date',
        'scheduled_date' => 'date',
        'completed_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function performer()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public static function getMaintenanceTypes()
    {
        return [
            'repair' => 'Repair',
            'service' => 'Service',
            'inspection' => 'Inspection',
        ];
    }

    public static function getStatuses()
    {
        return [
            'pending' => 'Pending',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
        ];
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'in_progress' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
        ];
        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getTypeBadgeAttribute()
    {
        $badges = [
            'repair' => 'bg-red-100 text-red-800',
            'service' => 'bg-blue-100 text-blue-800',
            'inspection' => 'bg-purple-100 text-purple-800',
        ];
        return $badges[$this->maintenance_type] ?? 'bg-gray-100 text-gray-800';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function complete($completedDate = null)
    {
        $this->status = 'completed';
        $this->completed_date = $completedDate ?? now();
        $this->save();

        // Update asset status back to active if it was under maintenance
        if ($this->asset && $this->asset->status === 'under_maintenance') {
            $this->asset->status = 'active';
            $this->asset->save();
        }
    }
}
