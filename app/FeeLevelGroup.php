<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeeLevelGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
        'min_class_numeric',
        'max_class_numeric',
        'display_order',
        'is_active'
    ];

    protected $casts = [
        'min_class_numeric' => 'integer',
        'max_class_numeric' => 'integer',
        'display_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function feeStructures()
    {
        return $this->hasMany(FeeStructure::class);
    }

    public function getClassesInGroup()
    {
        return Grade::whereBetween('class_numeric', [$this->min_class_numeric, $this->max_class_numeric])
            ->orderBy('class_numeric')
            ->get();
    }

    public function containsClass($classNumeric)
    {
        return $classNumeric >= $this->min_class_numeric && $classNumeric <= $this->max_class_numeric;
    }

    public static function getGroupForClass($classNumeric)
    {
        return self::where('is_active', true)
            ->where('min_class_numeric', '<=', $classNumeric)
            ->where('max_class_numeric', '>=', $classNumeric)
            ->first();
    }

    public function getClassRangeAttribute()
    {
        if ($this->min_class_numeric == $this->max_class_numeric) {
            return "Form/Grade {$this->min_class_numeric}";
        }
        return "Form/Grade {$this->min_class_numeric} - {$this->max_class_numeric}";
    }
}
