<?php

namespace App\Http\Controllers;

use App\Grade;
use App\Assessment;
use App\AssessmentMark;
use App\Student;
use Illuminate\Http\Request;

class AdminMarkingSchemeController extends Controller
{
    /**
     * Display all classes for marking scheme view.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $classes = Grade::withCount(['students', 'assessments'])
            ->with('teacher.user')
            ->having('assessments_count', '>', 0)
            ->orderBy('class_numeric')
            ->get();

        return view('backend.admin.marking-scheme.index', compact('classes'));
    }

    /**
     * Display assessments for a specific class.
     *
     * @param  int  $class_id
     * @return \Illuminate\Http\Response
     */
    public function classAssessments($class_id)
    {
        $class = Grade::with(['students', 'subjects'])->findOrFail($class_id);
        
        $assessments = Assessment::where('class_id', $class_id)
            ->with(['subject', 'teacher.user'])
            ->orderBy('date', 'desc')
            ->get();

        return view('backend.admin.marking-scheme.assessments', compact('class', 'assessments'));
    }

    /**
     * Display student marks for a specific assessment.
     *
     * @param  int  $assessment_id
     * @return \Illuminate\Http\Response
     */
    public function assessmentMarks($assessment_id)
    {
        $assessment = Assessment::with(['subject', 'class', 'teacher.user'])->findOrFail($assessment_id);
        
        $students = Student::where('class_id', $assessment->class_id)
            ->with('user')
            ->orderBy('roll_number')
            ->get();

        $marks = AssessmentMark::where('assessment_id', $assessment_id)
            ->get()
            ->groupBy('student_id');

        return view('backend.admin.marking-scheme.marks', compact('assessment', 'students', 'marks'));
    }

    /**
     * Get assessment marks as JSON for AJAX requests.
     *
     * @param  int  $assessment_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAssessmentMarks($assessment_id)
    {
        $assessment = Assessment::findOrFail($assessment_id);
        
        $students = Student::where('class_id', $assessment->class_id)
            ->with('user')
            ->orderBy('roll_number')
            ->get();

        $marks = AssessmentMark::where('assessment_id', $assessment_id)->get();

        $result = [];
        foreach ($students as $student) {
            $studentMarks = $marks->where('student_id', $student->id);
            $papers = [];
            
            foreach ($studentMarks as $mark) {
                $papers[$mark->paper_index] = [
                    'mark' => $mark->mark,
                    'total_marks' => $mark->total_marks,
                    'comment' => $mark->comment,
                    'absence_reason' => $mark->absence_reason
                ];
            }

            $result[] = [
                'student_id' => $student->id,
                'student_name' => $student->user->name ?? 'Unknown',
                'roll_number' => $student->roll_number,
                'papers' => $papers
            ];
        }

        return response()->json(['marks' => $result]);
    }
}
