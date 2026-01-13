<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SchemeOfWork extends Model
{
    protected $table = 'schemes_of_work';

    protected $fillable = [
        'teacher_id',
        'subject_id',
        'class_id',
        'term',
        'academic_year',
        'title',
        'description',
        'start_date',
        'end_date',
        'status',
        'total_planned_periods',
        'total_actual_periods',
        'expected_performance',
        'actual_performance',
        'notes',
        'approved_at',
        'approved_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'total_planned_periods' => 'integer',
        'total_actual_periods' => 'integer',
        'expected_performance' => 'decimal:2',
        'actual_performance' => 'decimal:2'
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function class()
    {
        return $this->belongsTo(Grade::class, 'class_id');
    }

    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function schemeTopics()
    {
        return $this->hasMany(SchemeTopic::class, 'scheme_id')->orderBy('order_index');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByTerm($query, $term)
    {
        return $query->where('term', $term);
    }

    public function scopeByAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->total_planned_periods == 0) {
            return 0;
        }
        return round(($this->total_actual_periods / $this->total_planned_periods) * 100, 1);
    }

    public function getPerformanceGapAttribute()
    {
        if ($this->expected_performance === null || $this->actual_performance === null) {
            return null;
        }
        return round($this->actual_performance - $this->expected_performance, 1);
    }

    public function getTopicsCountAttribute()
    {
        return $this->schemeTopics()->count();
    }

    public function getCompletedTopicsCountAttribute()
    {
        return $this->schemeTopics()->where('status', 'completed')->count();
    }

    public function getWeakTopicsCountAttribute()
    {
        return $this->schemeTopics()->where('mastery_level', 'weak')->count();
    }

    public function getNeedsRemedialCountAttribute()
    {
        return $this->schemeTopics()->where('remedial_required', true)->count();
    }

    public function recalculateTotals()
    {
        $this->total_planned_periods = $this->schemeTopics()->sum('planned_periods');
        $this->total_actual_periods = $this->schemeTopics()->sum('actual_periods');
        
        $avgPerformance = $this->schemeTopics()
            ->whereNotNull('actual_performance')
            ->avg('actual_performance');
        
        $this->actual_performance = $avgPerformance;
        $this->save();
    }
}
