<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Asset extends Model
{
    protected $fillable = [
        'asset_code',
        'name',
        'category_id',
        'serial_number',
        'purchase_date',
        'purchase_cost',
        'residual_value',
        'current_value',
        'condition',
        'status',
        'location_id',
        'assigned_type',
        'assigned_id',
        'purchase_order_id',
        'notes',
        'created_by',
        'disposed_at',
        'disposal_reason',
        'disposal_value',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'disposed_at' => 'date',
        'purchase_cost' => 'decimal:2',
        'residual_value' => 'decimal:2',
        'current_value' => 'decimal:2',
        'disposal_value' => 'decimal:2',
    ];

    protected $appends = ['age_in_years', 'condition_badge', 'status_badge'];

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function location()
    {
        return $this->belongsTo(AssetLocation::class, 'location_id');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignment()
    {
        return $this->morphTo('assigned', 'assigned_type', 'assigned_id');
    }

    public function maintenances()
    {
        return $this->hasMany(AssetMaintenance::class);
    }

    public function depreciations()
    {
        return $this->hasMany(AssetDepreciation::class);
    }

    public function assignmentHistories()
    {
        return $this->hasMany(AssetAssignmentHistory::class);
    }

    public function getAgeInYearsAttribute()
    {
        return $this->purchase_date ? $this->purchase_date->diffInYears(Carbon::now()) : 0;
    }

    public function getConditionBadgeAttribute()
    {
        $badges = [
            'new' => 'bg-green-100 text-green-800',
            'good' => 'bg-blue-100 text-blue-800',
            'fair' => 'bg-yellow-100 text-yellow-800',
            'damaged' => 'bg-red-100 text-red-800',
        ];
        return $badges[$this->condition] ?? 'bg-gray-100 text-gray-800';
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'active' => 'bg-green-100 text-green-800',
            'under_maintenance' => 'bg-yellow-100 text-yellow-800',
            'disposed' => 'bg-red-100 text-red-800',
        ];
        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public static function getConditions()
    {
        return [
            'new' => 'New',
            'good' => 'Good',
            'fair' => 'Fair',
            'damaged' => 'Damaged',
        ];
    }

    public static function getStatuses()
    {
        return [
            'active' => 'Active',
            'under_maintenance' => 'Under Maintenance',
            'disposed' => 'Disposed',
        ];
    }

    public static function generateAssetCode($categoryCode)
    {
        $prefix = 'AST-' . $categoryCode . '-';
        $lastAsset = self::where('asset_code', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastAsset) {
            $lastNumber = intval(substr($lastAsset->asset_code, -5));
            $newNumber = str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '00001';
        }

        return $prefix . $newNumber;
    }

    public function isDisposed()
    {
        return $this->status === 'disposed';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function canBeAssigned()
    {
        return $this->status === 'active' && $this->condition !== 'damaged';
    }

    public function getAssignedToNameAttribute()
    {
        if (!$this->assigned_type || !$this->assigned_id) {
            return 'Unassigned';
        }

        switch ($this->assigned_type) {
            case 'user':
                $user = User::find($this->assigned_id);
                return $user ? $user->name : 'Unknown User';
            case 'teacher':
                $teacher = Teacher::find($this->assigned_id);
                return $teacher ? $teacher->name : 'Unknown Teacher';
            case 'student':
                $student = Student::find($this->assigned_id);
                return $student ? $student->name : 'Unknown Student';
            case 'class':
                $grade = Grade::find($this->assigned_id);
                return $grade ? $grade->name : 'Unknown Class';
            default:
                return 'Unknown';
        }
    }

    public function getRemainingUsefulLifeAttribute()
    {
        if (!$this->category) {
            return 0;
        }
        $remaining = $this->category->useful_life_years - $this->age_in_years;
        return max(0, $remaining);
    }

    public function getDepreciableAmountAttribute()
    {
        return $this->purchase_cost - $this->residual_value;
    }

    public function calculateAnnualDepreciation()
    {
        if (!$this->category || $this->category->useful_life_years <= 0) {
            return 0;
        }

        $depreciableAmount = $this->depreciable_amount;

        if ($this->category->depreciation_method === 'straight_line') {
            return $depreciableAmount / $this->category->useful_life_years;
        }

        // Reducing balance method (typically 2x straight-line rate)
        $rate = (2 / $this->category->useful_life_years);
        return $this->current_value * $rate;
    }
}
