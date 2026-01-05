<?php

namespace App\Http\Controllers;

use App\User;
use App\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $teachers = Teacher::with('user')->latest()->paginate(10);

        return view('backend.teachers.index', compact('teachers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.teachers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|string|email|max:255|unique:users',
            'gender' => 'required|string',
            'phone'  => 'required|string|max:255|unique:teachers,phone'
        ]);

        // Default password for first login
        $password = '12345678';

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($password),
            'profile_picture' => 'avatar.png'
        ]);

        $user->teacher()->create([
            'gender'            => $request->gender,
            'phone'             => $request->phone,
            'dateofbirth'       => null,
            'current_address'   => null,
            'permanent_address' => null
        ]);

        $user->assignRole('Teacher');

        // Send email notification with credentials
        $this->sendCredentialsEmail($user, $password);

        // Send SMS notification with credentials (phone is username)
        $this->sendCredentialsSms($request->phone, $user->name, $request->phone, $password);

        return redirect()->route('teacher.index')
            ->with('success', 'Teacher created successfully! Login credentials have been sent via email and SMS.');
    }

    /**
     * Send credentials email to new teacher.
     */
    private function sendCredentialsEmail($user, $password)
    {
        try {
            Mail::send('emails.teacher-credentials', [
                'name' => $user->name,
                'email' => $user->email,
                'password' => $password
            ], function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Your Teacher Account Credentials - ' . config('app.name'));
            });
        } catch (\Exception $e) {
            \Log::error('Failed to send teacher credentials email: ' . $e->getMessage());
        }
    }

    /**
     * Send credentials SMS to new teacher.
     */
    private function sendCredentialsSms($phone, $name, $email, $password)
    {
        try {
            // Format phone number (ensure it has country code)
            $phone = preg_replace('/\s+/', '', $phone);
            if (!preg_match('/^\+/', $phone)) {
                $phone = '+263' . ltrim($phone, '0');
            }
            
            // Shorter message to avoid InboxIQ HTTP 500 error
            $message = "RSH School: Teacher account created. Login: {$phone}, Password: {$password}. Complete profile on first login.";
            
            // Send SMS using SmsHelper
            $result = \App\Helpers\SmsHelper::sendSms($phone, $message);
            
            if ($result['success']) {
                \Log::info('Teacher credentials SMS sent successfully', [
                    'phone' => $phone,
                    'teacher_name' => $name
                ]);
            } else {
                \Log::warning('Failed to send teacher credentials SMS', [
                    'phone' => $phone,
                    'error' => $result['message'] ?? 'Unknown error'
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send teacher credentials SMS: ' . $e->getMessage());
        }
    }

    /**
     * Show password change form for teacher.
     */
    public function showChangePasswordForm()
    {
        return view('backend.teacher.change-password');
    }

    /**
     * Update teacher profile and password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'dateofbirth'       => 'required|date',
            'current_address'   => 'required|string|max:500',
            'permanent_address' => 'required|string|max:500',
            'current_password'  => 'required',
            'password'          => 'required|string|min:8|confirmed|different:current_password',
        ], [
            'password.different' => 'New password must be different from the default password.',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $profile = Str::slug($user->name).'-'.$user->id.'.'.$request->profile_picture->getClientOriginalExtension();
            $request->profile_picture->move(public_path('images/profile'), $profile);
            $user->update(['profile_picture' => $profile]);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Update teacher profile details
        $user->teacher()->update([
            'dateofbirth'       => $request->dateofbirth,
            'current_address'   => $request->current_address,
            'permanent_address' => $request->permanent_address
        ]);

        return redirect()->route('home')->with('success', 'Profile completed successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function show(Teacher $teacher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function edit(Teacher $teacher)
    {
        $teacher = Teacher::with('user')->findOrFail($teacher->id);

        return view('backend.teachers.edit', compact('teacher'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Teacher $teacher)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|string|email|max:255|unique:users,email,'.$teacher->user_id,
            'gender'            => 'required|string',
            'phone'             => 'required|string|max:255',
            'dateofbirth'       => 'required|date',
            'current_address'   => 'required|string|max:255',
            'permanent_address' => 'required|string|max:255'
        ]);

        $user = User::findOrFail($teacher->user_id);

        if ($request->hasFile('profile_picture')) {
            $profile = Str::slug($user->name).'-'.$user->id.'.'.$request->profile_picture->getClientOriginalExtension();
            $request->profile_picture->move(public_path('images/profile'), $profile);
        } else {
            $profile = $user->profile_picture;
        }

        $user->update([
            'name'              => $request->name,
            'email'             => $request->email,
            'profile_picture'   => $profile
        ]);

        $user->teacher()->update([
            'gender'            => $request->gender,
            'phone'             => $request->phone,
            'dateofbirth'       => $request->dateofbirth,
            'current_address'   => $request->current_address,
            'permanent_address' => $request->permanent_address
        ]);

        return redirect()->route('teacher.index');
    }

    /**
     * Display classes taught by the logged-in teacher.
     *
     * @return \Illuminate\Http\Response
     */
    public function studentRecord()
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            return redirect()->route('home')->with('error', 'Teacher profile not found.');
        }

        $classes = $teacher->classes()->withCount('students')->get();

        return view('backend.teacher.student-record', compact('classes'));
    }

    /**
     * Display students in a specific class.
     *
     * @param  int  $class_id
     * @return \Illuminate\Http\Response
     */
    public function classStudents($class_id)
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            return redirect()->route('home')->with('error', 'Teacher profile not found.');
        }

        // Verify the class belongs to this teacher
        $class = $teacher->classes()->withCount('students')->findOrFail($class_id);

        // Get students in the class with their parent relationships and subjects
        $students = $class->students()->with(['user', 'parents.user', 'class.subjects'])->get();

        return view('backend.teacher.class-students', compact('class', 'students'));
    }

    /**
     * Mark a student as transferred.
     *
     * @param  int  $student_id
     * @return \Illuminate\Http\Response
     */
    public function transferStudent($student_id)
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            return redirect()->route('home')->with('error', 'Teacher profile not found.');
        }

        $student = \App\Student::findOrFail($student_id);

        // Verify the student belongs to a class taught by this teacher
        $class = $teacher->classes()->where('id', $student->class_id)->first();

        if (!$class) {
            return back()->with('error', 'You do not have permission to transfer this student.');
        }

        // Mark student as transferred
        $student->is_transferred = true;
        $student->save();

        // Disable the user account
        $student->user->update(['is_active' => false]);

        return back()->with('success', 'Student has been marked as transferred.');
    }

    /**
     * Display student assessment page for teachers.
     *
     * @return \Illuminate\Http\Response
     */
    public function assessment()
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            return redirect()->route('home')->with('error', 'Teacher profile not found.');
        }

        // Get classes taught by this teacher with student count
        $classes = $teacher->classes()->withCount('students')->get();
        $classIds = $classes->pluck('id')->toArray();

        // Get subjects taught by this teacher
        $subjects = $teacher->subjects()->get();

        // Assessment types to track
        $assessmentTypes = ['Quiz', 'Test', 'In Class Test', 'Monthly Test', 'Assignment', 'Exercise', 'Project', 'Exam', 'Vacation Exam', 'National Exam'];

        // Build performance data per subject
        $subjectPerformance = [];
        foreach ($subjects as $subject) {
            $typeStats = [];
            foreach ($assessmentTypes as $type) {
                $assessments = \App\Assessment::where('subject_id', $subject->id)
                    ->where('teacher_id', $teacher->id)
                    ->where('assessment_type', $type)
                    ->whereIn('class_id', $classIds)
                    ->get();

                $taken = $assessments->count();
                $totalMarks = 0;
                $obtainedMarks = 0;

                foreach ($assessments as $assessment) {
                    $marks = \App\AssessmentMark::where('assessment_id', $assessment->id)->get();
                    foreach ($marks as $mark) {
                        if ($mark->mark !== null && $mark->total_marks > 0) {
                            $totalMarks += $mark->total_marks;
                            $obtainedMarks += $mark->mark;
                        }
                    }
                }

                $performance = $totalMarks > 0 ? round(($obtainedMarks / $totalMarks) * 100, 1) : 0;

                $typeStats[] = [
                    'type' => $type,
                    'taken' => $taken,
                    'performance' => $performance
                ];
            }

            $subjectPerformance[] = [
                'subject' => $subject,
                'stats' => $typeStats
            ];
        }

        // Get recent assessments for this teacher (last 10)
        $recentAssessments = \App\Assessment::where('teacher_id', $teacher->id)
            ->with(['subject', 'class'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('backend.teacher.assessment', compact('classes', 'teacher', 'recentAssessments', 'subjectPerformance', 'assessmentTypes'));
    }

    /**
     * Display assessment list for a specific class.
     *
     * @param  int  $class_id
     * @return \Illuminate\Http\Response
     */
    public function assessmentList($class_id)
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            return redirect()->route('home')->with('error', 'Teacher profile not found.');
        }

        // Verify the class belongs to this teacher and load subjects
        $class = $teacher->classes()->with('subjects')->findOrFail($class_id);

        // Get assessments for this class and teacher
        $assessments = \App\Assessment::where('teacher_id', $teacher->id)
            ->where('class_id', $class_id)
            ->with('subject')
            ->orderBy('date', 'desc')
            ->get();

        // Get assessment comments for this class
        $assessmentComments = \App\AssessmentComment::where('class_id', $class_id)
            ->with('subject')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('backend.teacher.assessment-list', compact('class', 'assessments', 'assessmentComments', 'teacher'));
    }

    /**
     * Show form to create a new assessment.
     *
     * @param  int  $class_id
     * @return \Illuminate\Http\Response
     */
    public function createAssessment($class_id)
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            return redirect()->route('home')->with('error', 'Teacher profile not found.');
        }

        // Verify the class belongs to this teacher
        $class = $teacher->classes()->findOrFail($class_id);

        // Get subjects for this class
        $subjects = $class->subjects;

        return view('backend.teacher.assessment-create', compact('class', 'subjects', 'teacher'));
    }

    /**
     * Store a new assessment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeAssessment(Request $request)
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            return redirect()->route('home')->with('error', 'Teacher profile not found.');
        }

        // Validate the request with enhanced validation rules
        $validated = $request->validate([
            'class_id' => 'required|exists:grades,id',
            'subject_id' => 'required|exists:subjects,id',
            'topic' => 'required|string|min:3|max:255',
            'assessment_type' => 'required|string|in:Quiz,Test,In Class Test,Monthly Test,Assignment,Exercise,Project,Exam,Vacation Exam,National Exam',
            'date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:date',
            'exam' => 'nullable|string|max:255',
            'papers' => 'required|array|min:1|max:20',
            'papers.*.name' => 'required|string|min:2|max:100',
            'papers.*.total_marks' => 'required|integer|min:1|max:1000',
            'papers.*.weight' => 'required|integer|min:1|max:100'
        ], [
            'class_id.required' => 'Class selection is required.',
            'class_id.exists' => 'The selected class does not exist.',
            'subject_id.required' => 'Subject selection is required.',
            'subject_id.exists' => 'The selected subject does not exist.',
            'topic.required' => 'Topic is required.',
            'topic.min' => 'Topic must be at least 3 characters long.',
            'topic.max' => 'Topic cannot exceed 255 characters.',
            'assessment_type.required' => 'Assessment type is required.',
            'assessment_type.in' => 'Assessment type must be one of: Quiz, Test, Assignment, Exam, or Project.',
            'date.required' => 'Assessment date is required.',
            'date.before_or_equal' => 'Assessment date cannot be in the future.',
            'due_date.required' => 'Due date is required.',
            'due_date.after_or_equal' => 'Due date must be on or after the assessment date.',
            'exam.max' => 'Exam name cannot exceed 255 characters.',
            'papers.required' => 'At least one paper is required.',
            'papers.min' => 'At least one paper is required.',
            'papers.max' => 'You cannot add more than 20 papers.',
            'papers.*.name.required' => 'Paper name is required for all papers.',
            'papers.*.name.min' => 'Paper name must be at least 2 characters long.',
            'papers.*.name.max' => 'Paper name cannot exceed 100 characters.',
            'papers.*.total_marks.required' => 'Total marks is required for all papers.',
            'papers.*.total_marks.integer' => 'Total marks must be a valid number.',
            'papers.*.total_marks.min' => 'Total marks must be at least 1.',
            'papers.*.total_marks.max' => 'Total marks cannot exceed 1000.',
            'papers.*.weight.required' => 'Weight percentage is required for all papers.',
            'papers.*.weight.integer' => 'Weight must be a valid number.',
            'papers.*.weight.min' => 'Weight must be at least 1%.',
            'papers.*.weight.max' => 'Weight cannot exceed 100%.'
        ]);

        // Verify the class belongs to this teacher
        $class = $teacher->classes()->findOrFail($request->class_id);

        // Validate that paper weights add up to 100%
        $totalWeight = array_sum(array_column($request->papers, 'weight'));
        if ($totalWeight != 100) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['papers' => "Paper weights must add up to 100%. Current total: {$totalWeight}%"]);
        }

        // Check for duplicate paper names
        $paperNames = array_column($request->papers, 'name');
        if (count($paperNames) !== count(array_unique($paperNames))) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['papers' => 'Paper names must be unique. Please ensure each paper has a different name.']);
        }

        // Create the assessment
        $assessment = \App\Assessment::create([
            'teacher_id' => $teacher->id,
            'class_id' => $request->class_id,
            'subject_id' => $request->subject_id,
            'topic' => trim($request->topic),
            'assessment_type' => $request->assessment_type,
            'date' => $request->date,
            'due_date' => $request->due_date,
            'exam' => $request->exam ? trim($request->exam) : null,
            'papers' => $request->papers
        ]);

        return redirect()->route('teacher.assessment.list', $request->class_id)
            ->with('success', 'Assessment created successfully!');
    }

    /**
     * Store a new assessment comment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeAssessmentComment(Request $request)
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            return redirect()->route('home')->with('error', 'Teacher profile not found.');
        }

        // Validate the request with enhanced validation rules
        $validated = $request->validate([
            'class_id' => 'required|exists:grades,id',
            'entries' => 'required|array|min:1|max:50',
            'entries.*.comment' => 'required|string|min:10|max:500',
            'entries.*.subject_id' => 'required|exists:subjects,id',
            'entries.*.grade' => 'required|string|in:A,B,C,D,E,F'
        ], [
            'class_id.required' => 'Class selection is required.',
            'class_id.exists' => 'The selected class does not exist.',
            'entries.required' => 'At least one assessment comment entry is required.',
            'entries.min' => 'At least one assessment comment entry is required.',
            'entries.max' => 'You cannot add more than 50 entries at once.',
            'entries.*.comment.required' => 'Comment field is required for all entries.',
            'entries.*.comment.min' => 'Each comment must be at least 10 characters long.',
            'entries.*.comment.max' => 'Each comment cannot exceed 500 characters.',
            'entries.*.subject_id.required' => 'Subject selection is required for all entries.',
            'entries.*.subject_id.exists' => 'One or more selected subjects do not exist.',
            'entries.*.grade.required' => 'Grade selection is required for all entries.',
            'entries.*.grade.in' => 'Grade must be one of: A, B, C, D, E, or F.'
        ]);

        // Verify the class belongs to this teacher
        $class = $teacher->classes()->findOrFail($request->class_id);

        // Check for duplicate subject entries
        $subjectIds = array_column($request->entries, 'subject_id');
        if (count($subjectIds) !== count(array_unique($subjectIds))) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['entries' => 'You cannot add multiple comments for the same subject. Please ensure each subject is unique.']);
        }

        // Create multiple assessment comments
        foreach ($request->entries as $entry) {
            \App\AssessmentComment::create([
                'class_id' => $request->class_id,
                'subject_id' => $entry['subject_id'],
                'comment' => trim($entry['comment']),
                'grade' => strtoupper($entry['grade'])
            ]);
        }

        $count = count($request->entries);
        $message = $count > 1 
            ? "Successfully added {$count} assessment comments!" 
            : 'Assessment comment added successfully!';

        return redirect()->route('teacher.assessment.list', $request->class_id)
            ->with('success', $message);
    }

    /**
     * Delete an assessment comment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteAssessmentComment($id)
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            return redirect()->route('home')->with('error', 'Teacher profile not found.');
        }

        $comment = \App\AssessmentComment::findOrFail($id);

        // Verify the class belongs to this teacher
        $class = $teacher->classes()->findOrFail($comment->class_id);

        $comment->delete();

        return redirect()->route('teacher.assessment.list', $comment->class_id)
            ->with('success', 'Assessment comment deleted successfully!');
    }

    /**
     * Show form to add assessment marks for a class.
     *
     * @param  int  $class_id
     * @return \Illuminate\Http\Response
     */
    public function assessmentMarks($class_id)
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            return redirect()->route('home')->with('error', 'Teacher profile not found.');
        }

        // Verify the class belongs to this teacher
        $class = $teacher->classes()->findOrFail($class_id);

        // Get all assessments for this class
        $allAssessments = \App\Assessment::where('teacher_id', $teacher->id)
            ->where('class_id', $class_id)
            ->with('subject')
            ->orderBy('date', 'desc')
            ->get();

        // Get students count in the class
        $studentsCount = $class->students()->count();

        // Filter out assessments where all students have marks entered
        $assessments = $allAssessments->filter(function($assessment) use ($studentsCount) {
            // Count how many students have marks for this assessment
            $markedStudentsCount = \App\AssessmentMark::where('assessment_id', $assessment->id)
                ->distinct('student_id')
                ->count('student_id');
            
            // Only show assessment if not all students have been marked
            return $markedStudentsCount < $studentsCount;
        });

        // Get students in the class
        $students = $class->students()->with('user')->get();

        // Get assessment comments for this class
        $assessmentComments = \App\AssessmentComment::where('class_id', $class_id)
            ->with('subject')
            ->get();

        return view('backend.teacher.assessment-marks', compact('class', 'assessments', 'students', 'teacher', 'assessmentComments'));
    }

    /**
     * Save an individual assessment mark (auto-save).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveAssessmentMark(Request $request)
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            return response()->json(['success' => false, 'message' => 'Teacher profile not found.'], 403);
        }

        $validated = $request->validate([
            'assessment_id' => 'required|exists:assessments,id',
            'student_id' => 'required|exists:students,id',
            'paper_name' => 'required|string|max:100',
            'paper_index' => 'required|integer|min:0',
            'mark' => 'required|numeric|min:0',
            'total_marks' => 'required|numeric|min:0',
            'comment' => 'nullable|string|max:500'
        ]);

        // Verify the assessment belongs to this teacher
        $assessment = \App\Assessment::where('id', $request->assessment_id)
            ->where('teacher_id', $teacher->id)
            ->first();

        if (!$assessment) {
            return response()->json(['success' => false, 'message' => 'Assessment not found or access denied.'], 403);
        }

        // Create or update the assessment mark
        $assessmentMark = \App\AssessmentMark::updateOrCreate(
            [
                'assessment_id' => $request->assessment_id,
                'student_id' => $request->student_id,
                'paper_index' => $request->paper_index
            ],
            [
                'paper_name' => $request->paper_name,
                'mark' => $request->mark,
                'total_marks' => $request->total_marks,
                'comment' => $request->comment
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Mark saved successfully!',
            'data' => $assessmentMark
        ]);
    }

    /**
     * View assessment details.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function viewAssessment($id)
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            return response()->json(['success' => false, 'message' => 'Teacher profile not found.'], 403);
        }

        $assessment = \App\Assessment::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->with('subject')
            ->first();

        if (!$assessment) {
            return response()->json(['success' => false, 'message' => 'Assessment not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'assessment' => $assessment
        ]);
    }

    /**
     * Show form to edit an assessment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editAssessment($id)
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            return redirect()->route('home')->with('error', 'Teacher profile not found.');
        }

        $assessment = \App\Assessment::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->with('subject', 'class')
            ->first();

        if (!$assessment) {
            return redirect()->route('teacher.assessment')->with('error', 'Assessment not found.');
        }

        $class = $assessment->class;
        $subjects = $class->subjects;

        return view('backend.teacher.assessment-edit', compact('assessment', 'class', 'subjects', 'teacher'));
    }

    /**
     * Update an assessment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAssessment(Request $request, $id)
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            return redirect()->route('home')->with('error', 'Teacher profile not found.');
        }

        $assessment = \App\Assessment::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->first();

        if (!$assessment) {
            return redirect()->route('teacher.assessment')->with('error', 'Assessment not found.');
        }

        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'topic' => 'required|string|min:3|max:255',
            'assessment_type' => 'required|string|in:Quiz,Test,In Class Test,Monthly Test,Assignment,Exercise,Project,Exam,Vacation Exam,National Exam',
            'date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:date',
            'exam' => 'nullable|string|max:255',
            'papers' => 'required|array|min:1|max:20',
            'papers.*.name' => 'required|string|min:2|max:100',
            'papers.*.total_marks' => 'required|integer|min:1|max:1000',
            'papers.*.weight' => 'required|integer|min:1|max:100'
        ]);

        // Validate paper weights
        $totalWeight = array_sum(array_column($request->papers, 'weight'));
        if ($totalWeight != 100) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['papers' => "Paper weights must add up to 100%. Current total: {$totalWeight}%"]);
        }

        $assessment->update([
            'subject_id' => $request->subject_id,
            'topic' => trim($request->topic),
            'assessment_type' => $request->assessment_type,
            'date' => $request->date,
            'due_date' => $request->due_date,
            'exam' => $request->exam ? trim($request->exam) : null,
            'papers' => $request->papers
        ]);

        return redirect()->route('teacher.assessment.list', $assessment->class_id)
            ->with('success', 'Assessment updated successfully!');
    }

    /**
     * Delete an assessment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteAssessment($id)
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            return response()->json(['success' => false, 'message' => 'Teacher profile not found.'], 403);
        }

        $assessment = \App\Assessment::where('id', $id)
            ->where('teacher_id', $teacher->id)
            ->first();

        if (!$assessment) {
            return response()->json(['success' => false, 'message' => 'Assessment not found.'], 404);
        }

        $assessment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Assessment deleted successfully!'
        ]);
    }

    /**
     * Display marking scheme page with assessment marks.
     *
     * @param  int  $class_id
     * @return \Illuminate\Http\Response
     */
    public function markingScheme($class_id)
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            return redirect()->route('home')->with('error', 'Teacher profile not found.');
        }

        // Verify the class belongs to this teacher
        $class = $teacher->classes()->findOrFail($class_id);

        // Get assessments with marks for this class
        $assessments = \App\Assessment::where('teacher_id', $teacher->id)
            ->where('class_id', $class_id)
            ->with('subject')
            ->whereHas('marks')
            ->orderBy('date', 'desc')
            ->get();

        // Get students in the class
        $students = $class->students()->with('user')->get();

        return view('backend.teacher.marking-scheme', compact('class', 'assessments', 'students', 'teacher'));
    }

    /**
     * Export marking scheme to Excel.
     *
     * @param  int  $class_id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportMarkingScheme($class_id, Request $request)
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            return redirect()->route('home')->with('error', 'Teacher profile not found.');
        }

        $assessmentId = $request->query('assessment_id');

        if (!$assessmentId) {
            return redirect()->back()->with('error', 'Please select an assessment to export.');
        }

        // Verify the class belongs to this teacher
        $class = $teacher->classes()->findOrFail($class_id);

        // Get the assessment
        $assessment = \App\Assessment::where('id', $assessmentId)
            ->where('teacher_id', $teacher->id)
            ->where('class_id', $class_id)
            ->with('subject')
            ->firstOrFail();

        // Get marks for this assessment
        $marks = \App\AssessmentMark::where('assessment_id', $assessmentId)
            ->with('student.user')
            ->get();

        // Group marks by student
        $studentMarks = [];
        foreach ($marks as $mark) {
            $studentId = $mark->student_id;
            if (!isset($studentMarks[$studentId])) {
                $studentMarks[$studentId] = [
                    'name' => $mark->student->user->name,
                    'papers' => []
                ];
            }
            $studentMarks[$studentId]['papers'][$mark->paper_index] = [
                'paper_name' => $mark->paper_name,
                'mark' => $mark->mark,
                'total_marks' => $mark->total_marks,
                'comment' => $mark->comment
            ];
        }

        // Create Excel export
        return \Excel::download(new \App\Exports\MarkingSchemeExport($assessment, $studentMarks), 
            $assessment->subject->name . '_' . $assessment->topic . '_Marks.xlsx');
    }

    /**
     * Get assessment marks via API.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getAssessmentMarks($id)
    {
        $marks = \App\AssessmentMark::where('assessment_id', $id)
            ->with('student.user')
            ->get();

        // Group marks by student
        $studentMarks = [];
        foreach ($marks as $mark) {
            $studentId = $mark->student_id;
            if (!isset($studentMarks[$studentId])) {
                $studentMarks[$studentId] = [
                    'student_name' => $mark->student->user->name,
                    'papers' => []
                ];
            }
            $studentMarks[$studentId]['papers'][$mark->paper_index] = [
                'paper_name' => $mark->paper_name,
                'mark' => $mark->mark,
                'total_marks' => $mark->total_marks,
                'comment' => $mark->comment
            ];
        }

        return response()->json([
            'success' => true,
            'marks' => array_values($studentMarks)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Teacher  $teacher
     * @return \Illuminate\Http\Response
     */
    public function destroy(Teacher $teacher)
    {
        $user = User::findOrFail($teacher->user_id);

        $user->teacher()->delete();

        $user->removeRole('Teacher');

        if ($user->delete()) {
            if($user->profile_picture != 'avatar.png') {
                $image_path = public_path() . '/images/profile/' . $user->profile_picture;
                if (is_file($image_path) && file_exists($image_path)) {
                    unlink($image_path);
                }
            }
        }

        return back();
    }
}
