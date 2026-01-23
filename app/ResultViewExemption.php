<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResultViewExemption extends Model
{
    protected $fillable = [
        'student_id',
        'year',
        'term',
        'exempted_by',
        'reason',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function exemptedByUser()
    {
        return $this->belongsTo(User::class, 'exempted_by');
    }

    /**
     * Check if a student is exempted for a specific year/term
     */
    public static function isExempted($studentId, $year, $term)
    {
        return self::where('student_id', $studentId)
            ->where('year', $year)
            ->where('term', $term)
            ->exists();
    }
}
