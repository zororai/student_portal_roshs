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
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index()
    {
        // Get the logged-in teacher
        $teacher = Auth::user()->teacher;

        // Get classes assigned to this teacher with student count
        $classes = $teacher ? $teacher->classes()->withCount('students')->get() : [];

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

        // Get classes assigned to this teacher
        $classes = $teacher ? $teacher->classes : [];
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

        // Retrieve results for the specified students and check if they match the last record
        $resultsQuery = Result::whereIn('student_id', $studentIds);

        $exists = false;
        if ($lastRecord) {
            $exists = $resultsQuery
                ->where('year', $lastRecord->year)
                ->where('result_period', $lastRecord->result_period)
                ->exists();
        }

        // Fetch results after filtering
        $results = $resultsQuery->get();

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
        $class = Grade::with('subjects')->where('id', $student->class_id)->first();

        return view('backend.results.studentsubject', compact('class','student'));
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
            'results' => 'required|array',
            'result_period'=> 'required',

        ]);

        foreach ($request->results as $studentId => $subjects) {
            foreach ($subjects as $subjectId => $data) {
                Result::updateOrCreate(
                    [
                        'class_id' => $request->class_id,
                        'teacher_id' => $request->teacher_id,
                        'student_id' => $studentId,
                        'subject_id' => $subjectId,
                    ],
                    [
                        'marks' => $data['marks'],
                        'comment' => $data['comment'] ?? null,  // Use null if no comment is provided
                        'mark_grade' => $data['mark_grade'] ?? null,  // Use null if no grade is provided
                        'year' => date('Y'),
                        'result_period' => $request->result_period,
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


    $results = Result::with('subject')
    ->when($request->student_id, fn($query) => $query->where('student_id', $request->class_id))
    ->when($request->year, fn($query) => $query->where('year', $request->year))
    ->when($request->term, fn($query) => $query->where('result_period', $request->result_period))
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


    $results = Result::with('subject')
    ->when($request->student_id, fn($query) => $query->where('student_id', $request->class_id))
    ->when($request->year, fn($query) => $query->where('year', $request->year))
    ->when($request->term, fn($query) => $query->where('result_period', $request->result_period))
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
        $outstandingBalance = $totalFees - $totalPaid;
        
        // If student has outstanding fees, block results view
        if ($outstandingBalance > 0) {
            return view('reports.blocked', [
                'message' => 'You cannot view your results due to outstanding fees.',
                'outstanding' => $outstandingBalance
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
        $outstandingBalance = $totalFees - $totalPaid;
        
        // If there are outstanding fees, check for verified payment proof
        if ($outstandingBalance > 0) {
            // Check for verified payment for this parent
            $hasVerifiedPayment = PaymentVerification::where('parent_id', $parentId)
                ->where('status', 'verified')
                ->exists();
            
            // If no verified payment, require proof of payment
            if (!$hasVerifiedPayment) {
                // Check if there's a pending verification
                $pendingVerification = PaymentVerification::where('parent_id', $parentId)
                    ->where('status', 'pending')
                    ->exists();
                
                return view('reports.blocked', [
                    'message' => $pendingVerification 
                        ? 'Your payment verification is pending approval. Please wait for admin confirmation.'
                        : 'You have outstanding fees. Please submit proof of payment to view your child\'s results.',
                    'outstanding' => $outstandingBalance,
                    'show_verification_link' => true,
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
}



