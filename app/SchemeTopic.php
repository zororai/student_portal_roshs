<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchemeTopic extends Model
{
    protected $fillable = [
        'scheme_id',
        'syllabus_topic_id',
        'week_number',
        'planned_periods',
        'actual_periods',
        'expected_performance',
        'actual_performance',
        'mastery_level',
        'status',
        'teaching_methods',
        'resources',
        'remarks',
        'start_date',
        'end_date',
        'remedial_required',
        'order_index'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'planned_periods' => 'integer',
        'actual_periods' => 'integer',
        'expected_performance' => 'decimal:2',
        'actual_performance' => 'decimal:2',
        'remedial_required' => 'boolean',
        'order_index' => 'integer'
    ];

    public function scheme()
    {
        return $this->belongsTo(SchemeOfWork::class, 'scheme_id');
    }

    public function syllabusTopic()
    {
        return $this->belongsTo(SyllabusTopic::class, 'syllabus_topic_id');
    }

    public function remedialLessons()
    {
        return $this->hasMany(RemedialLesson::class, 'scheme_topic_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeNeedsRemedial($query)
    {
        return $query->where('remedial_required', true);
    }

    public function scopeWeak($query)
    {
        return $query->where('mastery_level', 'weak');
    }

    public function getPerformanceGapAttribute()
    {
        if ($this->expected_performance === null || $this->actual_performance === null) {
            return null;
        }
        return round($this->actual_performance - $this->expected_performance, 1);
    }

    public function getPeriodProgressAttribute()
    {
        if ($this->planned_periods == 0) {
            return 0;
        }
        return round(($this->actual_periods / $this->planned_periods) * 100, 1);
    }

    public function getMasteryColorAttribute()
    {
        switch ($this->mastery_level) {
            case 'mastered': return 'green';
            case 'partial': return 'yellow';
            case 'weak': return 'red';
            default: return 'gray';
        }
    }

    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case 'completed': return 'green';
            case 'in_progress': return 'blue';
            case 'needs_remedial': return 'red';
            default: return 'gray';
        }
    }

    public function updateMasteryLevel()
    {
        if ($this->actual_performance === null) {
            $this->mastery_level = 'not_assessed';
        } elseif ($this->actual_performance >= 75) {
            $this->mastery_level = 'mastered';
            $this->remedial_required = false;
        } elseif ($this->actual_performance >= 50) {
            $this->mastery_level = 'partial';
            $this->remedial_required = false;
        } else {
            $this->mastery_level = 'weak';
            $this->remedial_required = true;
            $this->status = 'needs_remedial';
        }
        $this->save();
    }
}
