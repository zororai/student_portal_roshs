<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    protected $fillable = [
        'results_status_id',
        'fee_level_group_id',
        'fee_type_id',
        'student_type',
        'curriculum_type',
        'is_for_new_student',
        'amount'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_for_new_student' => 'boolean',
    ];

    public function resultsStatus()
    {
        return $this->belongsTo(ResultsStatus::class);
    }

    public function feeLevelGroup()
    {
        return $this->belongsTo(FeeLevelGroup::class);
    }

    public function feeType()
    {
        return $this->belongsTo(FeeType::class);
    }

    public static function getFeesForStudent($student, $resultsStatusId)
    {
        $classNumeric = optional($student->class)->class_numeric;
        if (!$classNumeric) {
            return collect();
        }

        $levelGroup = FeeLevelGroup::getGroupForClass($classNumeric);
        if (!$levelGroup) {
            return collect();
        }

        $studentType = $student->student_type ?? 'day';
        $curriculumType = $student->curriculum_type ?? 'zimsec';
        $isNewStudent = $student->is_new_student ?? false;

        return self::where('results_status_id', $resultsStatusId)
            ->where('fee_level_group_id', $levelGroup->id)
            ->where('student_type', $studentType)
            ->where('curriculum_type', $curriculumType)
            ->where(function ($query) use ($isNewStudent) {
                if ($isNewStudent) {
                    $query->where('is_for_new_student', true)
                          ->orWhere('is_for_new_student', false);
                } else {
                    $query->where('is_for_new_student', false);
                }
            })
            ->with('feeType')
            ->get();
    }

    public static function calculateTotalForStudent($student, $resultsStatusId)
    {
        $fees = self::getFeesForStudent($student, $resultsStatusId);
        $scholarshipPercentage = floatval($student->scholarship_percentage ?? 0);
        
        $total = $fees->sum('amount');
        
        if ($scholarshipPercentage > 0 && $scholarshipPercentage <= 100) {
            $discount = $total * ($scholarshipPercentage / 100);
            $total = $total - $discount;
        }

        return $total;
    }
}
