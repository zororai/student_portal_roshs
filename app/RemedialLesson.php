<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RemedialLesson extends Model
{
    protected $fillable = [
        'syllabus_topic_id',
        'scheme_topic_id',
        'class_id',
        'subject_id',
        'teacher_id',
        'title',
        'description',
        'objectives',
        'scheduled_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'status',
        'trigger_type',
        'trigger_score',
        'pre_remedial_avg',
        'post_remedial_avg',
        'resources',
        'notes',
        'parent_notified',
        'parent_notified_at',
        'completed_at'
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'duration_minutes' => 'integer',
        'trigger_score' => 'decimal:2',
        'pre_remedial_avg' => 'decimal:2',
        'post_remedial_avg' => 'decimal:2',
        'parent_notified' => 'boolean',
        'parent_notified_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function syllabusTopic()
    {
        return $this->belongsTo(SyllabusTopic::class, 'syllabus_topic_id');
    }

    public function schemeTopic()
    {
        return $this->belongsTo(SchemeTopic::class, 'scheme_topic_id');
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

    public function students()
    {
        return $this->belongsToMany(Student::class, 'remedial_lesson_student')
            ->withPivot(['pre_score', 'post_score', 'attended', 'notes'])
            ->withTimestamps();
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeAutoTriggered($query)
    {
        return $query->where('trigger_type', 'auto');
    }

    public function scopeByTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case 'completed': return 'green';
            case 'in_progress': return 'blue';
            case 'scheduled': return 'yellow';
            case 'cancelled': return 'red';
            default: return 'gray';
        }
    }

    public function getImprovementAttribute()
    {
        if ($this->pre_remedial_avg === null || $this->post_remedial_avg === null) {
            return null;
        }
        return round($this->post_remedial_avg - $this->pre_remedial_avg, 1);
    }

    public function getStudentsCountAttribute()
    {
        return $this->students()->count();
    }

    public function getAttendedCountAttribute()
    {
        return $this->students()->wherePivot('attended', true)->count();
    }

    public function markCompleted()
    {
        $this->status = 'completed';
        $this->completed_at = now();
        
        // Calculate post-remedial average from student scores
        $avgPostScore = $this->students()
            ->whereNotNull('remedial_lesson_student.post_score')
            ->avg('remedial_lesson_student.post_score');
        
        if ($avgPostScore !== null) {
            $this->post_remedial_avg = $avgPostScore;
        }
        
        $this->save();
    }
}
