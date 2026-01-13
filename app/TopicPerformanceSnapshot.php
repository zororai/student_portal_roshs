<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TopicPerformanceSnapshot extends Model
{
    protected $fillable = [
        'syllabus_topic_id',
        'class_id',
        'subject_id',
        'teacher_id',
        'term',
        'academic_year',
        'students_assessed',
        'total_students',
        'average_score',
        'highest_score',
        'lowest_score',
        'pass_rate',
        'mastery_level',
        'assessments_count',
        'calculated_at'
    ];

    protected $casts = [
        'students_assessed' => 'integer',
        'total_students' => 'integer',
        'average_score' => 'decimal:2',
        'highest_score' => 'decimal:2',
        'lowest_score' => 'decimal:2',
        'pass_rate' => 'decimal:2',
        'assessments_count' => 'integer',
        'calculated_at' => 'datetime'
    ];

    public function syllabusTopic()
    {
        return $this->belongsTo(SyllabusTopic::class, 'syllabus_topic_id');
    }

    public function class()
    {
        return $this->belongsTo(Grade::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function scopeByTerm($query, $term)
    {
        return $query->where('term', $term);
    }

    public function scopeByAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeWeak($query)
    {
        return $query->where('mastery_level', 'weak');
    }

    public function scopeMastered($query)
    {
        return $query->where('mastery_level', 'mastered');
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

    public function getAssessmentCoverageAttribute()
    {
        if ($this->total_students == 0) {
            return 0;
        }
        return round(($this->students_assessed / $this->total_students) * 100, 1);
    }
}
