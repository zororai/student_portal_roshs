<?php
namespace App\Http\Controllers;

use App\Result;
use App\Grade;
use App\Student;
use App\Parents;
use App\Subject;
use App\ResultsStatus;
use App\Teacher;
use App\StudentPayment;
use App\Assessment;
use App\AssessmentMark;
use App\PaymentVerification;
use App\Http\Controllers\GroceryController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index()
    {
        // Get the logged-in teacher
        $teacher = Auth::user()->teacher;

        if (!$teacher) {
            return view('backend.results.index', ['classes' => collect()]);
        }

        // Get all subjects taught by this teacher
        $teacherSubjects = $teacher->subjects;
        
        // Get all unique class IDs from the subjects this teacher teaches
        $classIds = [];
        foreach ($teacherSubjects as $subject) {
            $subjectClassIds = $subject->grades()->pluck('grades.id')->toArray();
            $classIds = array_merge($classIds, $subjectClassIds);
        }
        
        // Remove duplicates
        $classIds = array_unique($classIds);
        
        // Get all classes where teacher teaches subjects, with student count
        $classes = Grade::whereIn('id', $classIds)
            ->withCount('students')
            ->get();

        return view('backend.results.index', compact('classes'));
    }

    public function parentindex()
    {
        // Get the logged-in teacher
        $teacher = Auth::user()->teacher;

        // Get classes assigned to this teacher
        $classes = $teacher ? $teacher->classes : [];

        return view('backend.results.index', compact('classes'));
    }
    public function resultsactive()
    {
        // Get the logged-in teacher
        $classes = Grade::withCount('students')->latest()->paginate(10);

        return view('backend.activeresults.index', compact('classes'));
    }


    public function recordindex()
    {
        // Get the logged-in teacher
        $teacher = Auth::user()->teacher;

        if (!$teacher) {
            return view('backend.results.resultsrecord', ['classes' => collect(), 'years' => collect()]);
        }

        // Get all subjects taught by this teacher
        $teacherSubjects = $teacher->subjects;
        
        // Get all unique class IDs from the subjects this teacher teaches
        $classIds = [];
        foreach ($teacherSubjects as $subject) {
            $subjectClassIds = $subject->grades()->pluck('grades.id')->toArray();
            $classIds = array_merge($classIds, $subjectClassIds);
        }
        
        // Remove duplicates
        $classIds = array_unique($classIds);
        
        // Get all classes where teacher teaches subjects
        $classes = Grade::whereIn('id', $classIds)->get();
        
        $years = ResultsStatus::select('year')->distinct()->pluck('year');

        return view('backend.results.resultsrecord', compact('classes','years'));
    }

    public function Classnames($class_id)
    {
        // Get the class with its students
        $classes = Grade::with('students')->findOrFail($class_id);

        // Get results for students in this class
        $studentIds = $classes->students->pluck('id');
        $results = Result::whereIn('student_id', $studentIds)->get();

        return view('backend.results.classname', compact('results', 'classes'));
    }

    public function adminclassnames($class_id)
    {
        // Get the class with its students
        $classes = Grade::with('students')->findOrFail($class_id);

        // Get results for students in this class
        $studentIds = $classes->students->pluck('id');
        $results = Result::whereIn('student_id', $studentIds)->get();

        return view('results.classname', compact('results', 'classes'));
    }



    ////test



    public function listResults($class_id)
    {
        // Get the class with its students
        $classes = Grade::with('students')->findOrFail($class_id);

        // Get student IDs from this class
        $studentIds =$classes->students->pluck('id');

        // Get the last inserted ResultsStatus record
        $lastRecord = ResultsStatus::latest()->first();

        // Initialize results query
        $resultsQuery = Result::whereIn('student_id', $studentIds);

        $exists = false;
        $results = collect();
        
        if ($lastRecord) {
            // Check if any results exist for the current period
            $exists = Result::whereIn('student_id', $studentIds)
                ->where('year', $lastRecord->year)
                ->where('result_period', $lastRecord->result_period)
                ->exists();
            
            // Fetch only results for the current period
            $results = Result::whereIn('student_id', $studentIds)
                ->where('year', $lastRecord->year)
                ->where('result_period', $lastRecord->result_period)
                ->get();
        }

        return view('backend.results.results', compact('results', 'classes', 'lastRecord', 'exists'));
    }



    public function activelistResults($class_id)
    {
        // Get the class with its students
        $classes = Grade::with('students')->findOrFail($class_id);

        // Get student IDs from this class
        $studentIds =$classes->students->pluck('id');

        // Get the last inserted ResultsStatus record
        $lastRecord = ResultsStatus::latest()->first();

        // Retrieve results for the specified students and check if they match the last record
        $resultsQuery = Result::whereIn('student_id', $studentIds);
        $Pending='Paid';
        $exists = false;
        if ($lastRecord) {
            $exists = $resultsQuery
                ->where('year', $lastRecord->year)
                ->where('result_period', $lastRecord->result_period)
                ->where('status', $Pending)
                ->exists();
        }

        // Fetch results after filtering
        $results = $resultsQuery->get();

        return view('backend.activeresults.results', compact('results', 'classes', 'lastRecord', 'exists'));
    }



    public function createByTeacher($classid)
    {
        $class = Grade::with(['students', 'subjects', 'teacher'])->findOrFail($classid);
        return view('backend.results.create', compact('class'));
    }


    public function edit($id)
    {
        $result = Result::findOrFail($id);

        return view('backend.results.edit', compact('result'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'score' => 'required|numeric',
            'comment' => 'nullable|string',
            'mark_grade' => 'nullable|string',
        ]);

        $result = Result::findOrFail($id);
        $result->update($request->all());

        return redirect()->route('results.index')->with('status', 'Result updated successfully!');
    }

    public function destroy($id)
    {
        // Find the result by ID or fail if it doesn't exist
        $result = Result::findOrFail($id);

        // Store the student ID for redirection after deletion
        $studentId = $result->student_id;

        // Delete the result
        $result->delete();

        // Fetch the updated results for the student
        $studentResults = Result::with('subject')->where('student_id', $studentId)->get();

        // Return the view with the updated student results
        return view('backend.results.viewstudentresults', compact('studentResults'));
    }
    public function showstudentresults($id)
    {
        // Retrieve the student by ID

        $studentResults = Result::with('subject')->where('student_id', $id)->get();
        return view('backend.results.viewstudentresults', compact('studentResults'));

    }

    public function viewupdateresults($id)
    {
        // Retrieve the student by ID

        $studentResults = Result::with('subject')->where('student_id', $id)->get();
        return view('backend.activeresults.viewstudentresults', compact('studentResults'));

    }

    public function changestatus($id)
    {
        // Retrieve the student results by ID and update the status
        $newStatus = 'Paid';
        Result::where('student_id', $id)->update(['status' => $newStatus]);

        // Fetch updated results for the student
        $studentResults = Result::where('student_id', $id)->get();
        $class_id = optional($studentResults->first())->class_id;

        //// zoro

        // Get the class with its students
        $classes = Grade::with('students')->findOrFail($class_id);

        // Get student IDs from this class
        $studentIds =$classes->students->pluck('id');

        // Get the last inserted ResultsStatus record
        $lastRecord = ResultsStatus::latest()->first();

        // Retrieve results for the specified students and check if they match the last record
        $resultsQuery = Result::whereIn('student_id',$studentIds);
        $Pending='Paid';
        $exists = false;

        if ($lastRecord) {
            $exists = $resultsQuery
                ->where('year', $lastRecord->year)
                ->where('result_period', $lastRecord->result_period)
                ->where('status', $Pending)
                ->exists();
        }

        // Fetch results after filtering
        $results = $resultsQuery->get();

        return view('backend.activeresults.results', compact('results', 'classes', 'lastRecord', 'exists'));
    }

    public function Showssubject(Student $student)
    {
        // Get the authenticated teacher
        $teacher = Teacher::findOrFail(auth()->user()->teacher->id);
        
        // Get the class with its subjects
        $class = Grade::with('subjects')->findOrFail($student->class_id);
        
        // Get the IDs of subjects that this teacher teaches
        $teacherSubjectIds = $teacher->subjects->pluck('id')->toArray();
        
        // Filter the class subjects to only include those the teacher teaches
        $class->subjects = $class->subjects->filter(function($subject) use ($teacherSubjectIds) {
            return in_array($subject->id, $teacherSubjectIds);
        });

        // Get the current period
        $lastRecord = ResultsStatus::latest()->first();
        
        // Fetch existing results for this student in the current period
        $existingResults = collect();
        if ($lastRecord) {
            $existingResults = Result::where('student_id', $student->id)
                ->where('year', $lastRecord->year)
                ->where('result_period', $lastRecord->result_period)
                ->get()
                ->keyBy('subject_id'); // Key by subject_id for easy lookup
        }

        return view('backend.results.studentsubject', compact('class', 'student', 'existingResults', 'lastRecord'));
    }
    public function Stuntentname($id)
    {
        $class = $id;
        $years = ResultsStatus::select('year')->distinct()->pluck('year');
        return view('backend.results.Allresults', compact('class','years'));
    }
    public function adminStuntentname($id)
    {
        $class = $id;
        $years = ResultsStatus::select('year')->distinct()->pluck('year');
        return view('results.Allresults', compact('class','years'));
    }

    public function store(Request $request)
    {
        $classid = $request->class_id;
        $teacher = Teacher::findOrFail(auth()->user()->teacher->id);
        $class = Grade::find($classid);



        $request->validate([
            'class_id' => 'required|numeric',
            'teacher_id' => 'required|numeric',
            'student_id' => 'required|numeric',
            'results' => 'required|array',
            'result_period'=> 'required',
        ]);

        // Get the explicit student ID from the form
        $targetStudentId = $request->student_id;
        
        // Get the teacher's subject IDs
        $teacherSubjectIds = $teacher->subjects->pluck('id')->toArray();
        
        // Get the class to verify subjects belong to this class
        $class = Grade::with('subjects')->findOrFail($request->class_id);
        $classSubjectIds = $class->subjects->pluck('id')->toArray();
        
        // Loop through results - only process for the target student
        foreach ($request->results as $studentId => $subjects) {
            // IMPORTANT: Only process results for the explicitly specified student
            if ($studentId != $targetStudentId) {
                continue;
            }
            
            foreach ($subjects as $subjectId => $data) {
                // Only allow results for subjects the teacher teaches AND that belong to this class
                if (!in_array($subjectId, $teacherSubjectIds) || !in_array($subjectId, $classSubjectIds)) {
                    continue;
                }
                
                Result::updateOrCreate(
                    [
                        'class_id' => $request->class_id,
                        'teacher_id' => $request->teacher_id,
                        'student_id' => $targetStudentId,
                        'subject_id' => $subjectId,
                        'year' => date('Y'),
                        'result_period' => $request->result_period,
                    ],
                    [
                        'marks' => $data['marks'],
                        'comment' => $data['comment'] ?? null,
                        'mark_grade' => $data['mark_grade'] ?? null,
                        'status' => 'Pending',
                    ]
                );
            }
        }

        return redirect()->route('results.results', $classid)
            ->with('status', 'Results entered successfully!');
    }

    public function viewstatus($classid)
    {
        $results = $classid;
        return view ('backend.results.status', compact('results'));
    }

    public function classname($classid)
    {
        $classes = $classid;
        return view ('backend.results.classname', compact('classes'));
    }







public function deleteResult($id)
{
    $result = Result::findOrFail($id);
    $result->delete();

    return redirect()->back()->with('status', 'Result deleted successfully!');
}

public function showResult(Request $request)
{
    // Validate the incoming request
    $request->validate([
        'year' => 'required|integer',
        'result_period' => 'required|string',
        'class_id' => 'required|integer',
    ]);

    // Note: class_id in the form actually contains the student_id
    $studentId = $request->class_id;

    $results = Result::with('subject')
        ->where('student_id', $studentId)
        ->where('year', $request->year)
        ->where('result_period', $request->result_period)
        ->get();

    return view('backend.results.studentview', compact('results'));
}

public function adminshowResult(Request $request)
{
    // Validate the incoming request
    $request->validate([
        'year' => 'required|integer',
        'result_period' => 'required|string',
        'class_id' => 'required|integer',
    ]);

    // Note: class_id in the form actually contains the student_id
    $studentId = $request->class_id;

    $results = Result::with('subject')
        ->where('student_id', $studentId)
        ->where('year', $request->year)
        ->where('result_period', $request->result_period)
        ->get();

    return view('results.studentview', compact('results'));
}




    public function show($studentId)
    {
        $results = Result::where('student_id', $studentId)->with(['subject', 'class'])->get();
        return view('backend.results.show', compact('results'));
    }


    public function studentshow()
    {
        $studentId = Student::where('user_id', Auth::user()->id)->value('id');
        
        // Check for outstanding fees
        $allTerms = ResultsStatus::all();
        $totalFees = 0;
        foreach ($allTerms as $term) {
            $totalFees += floatval($term->total_fees);
        }
        $totalPaid = floatval(StudentPayment::where('student_id', $studentId)->sum('amount_paid'));
        $outstandingFees = $totalFees - $totalPaid;
        
        // Check for grocery arrears
        $groceryArrears = GroceryController::calculateGroceryBalance($studentId);
        
        // If student has outstanding fees OR grocery arrears, block results view
        if ($outstandingFees > 0 || $groceryArrears > 0) {
            $messages = [];
            if ($outstandingFees > 0) {
                $messages[] = 'Outstanding school fees: $' . number_format($outstandingFees, 2);
            }
            if ($groceryArrears > 0) {
                $messages[] = 'Outstanding groceries: $' . number_format($groceryArrears, 2);
            }
            
            return view('reports.blocked', [
                'message' => 'You cannot view your results due to outstanding balance.',
                'outstanding' => $outstandingFees,
                'grocery_arrears' => $groceryArrears,
                'details' => $messages
            ]);
        }
        
        $paid = 'Paid';
        $lastRecord = ResultsStatus::latest()->first();
        $year = $lastRecord->year;
        $period = $lastRecord->result_period;
        $results = Result::where('student_id', $studentId)
        ->where('result_period',$period)
        ->where('status',$paid)
        ->where('year', $year)
        ->with(['subject', 'teacher', 'class'])
        ->get();
        
        return view('reports.index', compact('results'));
    }


    public function viewstudentshow()
    {
        $parentId = Parents::where('user_id', Auth::user()->id)->value('id');
        
        // Check if parent record exists
        if (!$parentId) {
            return view('reports.index', [
                'students' => collect(),
                'results' => collect(),
                'error' => 'Parent record not found. Please contact administrator.'
            ]);
        }
        
        // Get ALL students for this parent
        $students = Student::where('parent_id', $parentId)->with('user', 'class')->get();
        
        if ($students->isEmpty()) {
            return view('reports.index', [
                'students' => collect(),
                'results' => collect(),
                'error' => 'No students linked to this parent account.'
            ]);
        }
        
        $studentIds = $students->pluck('id');
        
        // Check for outstanding fees first
        $allTerms = ResultsStatus::all();
        $totalFees = 0;
        if ($allTerms->isNotEmpty()) {
            foreach ($allTerms as $term) {
                $totalFees += floatval($term->total_fees ?? 0);
            }
        }
        $totalPaid = floatval(StudentPayment::whereIn('student_id', $studentIds)->sum('amount_paid'));
        $outstandingFees = $totalFees - $totalPaid;
        
        // Check for grocery arrears for all children
        $totalGroceryArrears = 0;
        foreach ($studentIds as $studentId) {
            $totalGroceryArrears += GroceryController::calculateGroceryBalance($studentId);
        }
        
        // If there are outstanding fees OR grocery arrears, block results
        if ($outstandingFees > 0 || $totalGroceryArrears > 0) {
            // Check for verified payment for this parent (only for fees)
            $hasVerifiedPayment = PaymentVerification::where('parent_id', $parentId)
                ->where('status', 'verified')
                ->exists();
            
            // If no verified payment OR has grocery arrears, block results
            if (!$hasVerifiedPayment || $totalGroceryArrears > 0) {
                // Check if there's a pending verification
                $pendingVerification = PaymentVerification::where('parent_id', $parentId)
                    ->where('status', 'pending')
                    ->exists();
                
                $messages = [];
                if ($outstandingFees > 0 && !$hasVerifiedPayment) {
                    $messages[] = 'Outstanding school fees: $' . number_format($outstandingFees, 2);
                }
                if ($totalGroceryArrears > 0) {
                    $messages[] = 'Outstanding groceries: $' . number_format($totalGroceryArrears, 2);
                }
                
                $message = 'You cannot view your child\'s results due to outstanding balance.';
                if ($pendingVerification && $outstandingFees > 0) {
                    $message = 'Your payment verification is pending approval. Please wait for admin confirmation.';
                }
                
                return view('reports.blocked', [
                    'message' => $message,
                    'outstanding' => $outstandingFees,
                    'grocery_arrears' => $totalGroceryArrears,
                    'details' => $messages,
                    'show_verification_link' => $outstandingFees > 0 && !$hasVerifiedPayment,
                    'pending' => $pendingVerification
                ]);
            }
        }
        
        // Get ALL results for all students (all terms, all years)
        $results = Result::whereIn('student_id', $studentIds)
            ->with(['subject', 'teacher', 'class', 'student.user'])
            ->orderBy('year', 'desc')
            ->orderBy('result_period', 'desc')
            ->orderBy('student_id')
            ->get();

        return view('reports.index', compact('results', 'students'));
    }

    /**
     * Admin view results - Display class cards with view results button
     */
    public function adminViewResults()
    {
        $classes = Grade::withCount('students')->orderBy('class_name')->get();
        $years = ResultsStatus::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');
        $terms = ResultsStatus::all();
        
        return view('backend.results.admin-view-results', compact('classes', 'years', 'terms'));
    }

    /**
     * Get results for admin by class, year and term
     */
    public function getAdminResults(Request $request)
    {
        $classId = $request->class_id;
        $year = $request->year;
        $term = $request->term;

        $class = Grade::with('students.user')->findOrFail($classId);
        $studentIds = $class->students->pluck('id');

        $results = Result::with(['student.user', 'subject'])
            ->whereIn('student_id', $studentIds)
            ->where('year', $year)
            ->where('result_period', $term)
            ->get()
            ->groupBy('student_id');

        return response()->json([
            'class' => $class,
            'results' => $results,
            'students' => $class->students
        ]);
    }

    /**
     * Parent view student assessments
     */
    public function parentAssessments()
    {
        $parentId = Parents::where('user_id', Auth::user()->id)->value('id');
        
        if (!$parentId) {
            return view('backend.parent.assessments', [
                'students' => collect(),
                'assessments' => collect(),
                'error' => 'Parent record not found. Please contact administrator.'
            ]);
        }
        
        // Get ALL students for this parent
        $students = Student::where('parent_id', $parentId)->with('user', 'class')->get();
        
        if ($students->isEmpty()) {
            return view('backend.parent.assessments', [
                'students' => collect(),
                'assessments' => collect(),
                'error' => 'No students linked to this parent account.'
            ]);
        }
        
        $studentIds = $students->pluck('id');
        
        // Get assessment marks for all students
        $assessmentMarks = AssessmentMark::whereIn('student_id', $studentIds)
            ->with(['assessment.subject', 'assessment.class', 'assessment.teacher.user', 'student.user'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Group by assessment for better display
        $groupedAssessments = $assessmentMarks->groupBy('assessment_id');
        
        return view('backend.parent.assessments', compact('students', 'assessmentMarks', 'groupedAssessments'));
    }

    /**
     * Clean/Delete results records by class, term, and year
     */
    public function cleanResults(Request $request)
    {
        try {
            $cleanAll = $request->clean_all;
            $classId = $request->class_id;
            $year = $request->year;
            $term = $request->term;

            $query = Result::query();

            if ($cleanAll) {
                // Delete all results
                $deletedCount = $query->delete();
            } else {
                // Apply filters
                if ($classId) {
                    $query->where('class_id', $classId);
                }
                if ($year) {
                    $query->where('year', $year);
                }
                if ($term) {
                    $query->where('result_period', $term);
                }

                $deletedCount = $query->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Results cleaned successfully.',
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clean results: ' . $e->getMessage()
            ], 500);
        }
    }
}



