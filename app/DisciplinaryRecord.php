<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DisciplinaryRecord extends Model
{
    protected $fillable = [
        'student_id',
        'class_id',
        'recorded_by',
        'offense_type',
        'offense_status',
        'offense_date',
        'description'
    ];

    protected $dates = ['offense_date'];

    /**
     * Get the student associated with the disciplinary record.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the class associated with the disciplinary record.
     */
    public function class()
    {
        return $this->belongsTo(Grade::class, 'class_id');
    }

    /**
     * Get the teacher who recorded the disciplinary record.
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'recorded_by');
    }
}
