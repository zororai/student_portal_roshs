<?php

namespace App\Http\Controllers;

use App\Grade;
use App\Parents;
use App\Student;
use App\Teacher;
use App\Subject;
use App\Result;
use App\Assessment;
use App\AssessmentMark;
use App\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Str;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->hasRole('Admin')) {

            $parents = Parents::latest()->get();
            $teachers = Teacher::latest()->get();
            $students = Student::latest()->get();
            $subjects = Subject::latest()->get();
            $classes = Grade::latest()->get();

            // Get pass/fail results by gender (pass = marks >= 50)
            $resultsByGender = DB::table('results')
                ->join('students', 'results.student_id', '=', 'students.id')
                ->select(
                    'students.gender',
                    DB::raw('SUM(CASE WHEN results.marks >= 50 THEN 1 ELSE 0 END) as pass_count'),
                    DB::raw('SUM(CASE WHEN results.marks < 50 THEN 1 ELSE 0 END) as fail_count'),
                    DB::raw('COUNT(*) as total_count')
                )
                ->groupBy('students.gender')
                ->get();

            $malePass = 0;
            $maleFail = 0;
            $femalePass = 0;
            $femaleFail = 0;

            foreach ($resultsByGender as $row) {
                if (strtolower($row->gender) === 'male') {
                    $malePass = $row->pass_count;
                    $maleFail = $row->fail_count;
                } elseif (strtolower($row->gender) === 'female') {
                    $femalePass = $row->pass_count;
                    $femaleFail = $row->fail_count;
                }
            }

            $genderStats = [
                'malePass' => $malePass,
                'maleFail' => $maleFail,
                'femalePass' => $femalePass,
                'femaleFail' => $femaleFail,
            ];

            // Get classroom population data with gender breakdown
            $classroomPopulation = Grade::with(['students'])
                ->orderBy('class_numeric')
                ->get()
                ->map(function($class) {
                    $maleCount = $class->students->where('gender', 'Male')->count();
                    $femaleCount = $class->students->where('gender', 'Female')->count();
                    return [
                        'name' => $class->class_name,
                        'count' => $class->students->count(),
                        'male' => $maleCount,
                        'female' => $femaleCount
                    ];
                });

            // Get assessment statistics by type for admin
            $assessmentTypes = ['Quiz', 'Test', 'In Class Test', 'Monthly Test', 'Assignment', 'Exercise', 'Project', 'Exam', 'Vacation Exam', 'National Exam'];
            $assessmentStats = [];
            
            foreach ($assessmentTypes as $type) {
                $assessments = Assessment::where('assessment_type', $type)->get();
                $totalGiven = $assessments->count();
                
                $totalMarks = 0;
                $obtainedMarks = 0;
                $marksCount = 0;
                
                foreach ($assessments as $assessment) {
                    $marks = AssessmentMark::where('assessment_id', $assessment->id)->get();
                    foreach ($marks as $mark) {
                        if ($mark->mark !== null && $mark->total_marks > 0) {
                            $totalMarks += $mark->total_marks;
                            $obtainedMarks += $mark->mark;
                            $marksCount++;
                        }
                    }
                }
                
                $performance = $totalMarks > 0 ? round(($obtainedMarks / $totalMarks) * 100, 1) : 0;
                
                $assessmentStats[] = [
                    'type' => $type,
                    'given' => $totalGiven,
                    'performance' => $performance
                ];
            }

            return view('home', compact('parents','teachers','students','subjects','classes','genderStats','classroomPopulation','assessmentStats'));

        } elseif ($user->hasRole('Teacher')) {

            $teacher = Teacher::with(['user','subjects','classes','students'])->withCount('subjects','classes')->findOrFail($user->teacher->id);

            // Get assessment statistics for teacher's subjects
            $teacherSubjectIds = $teacher->subjects->pluck('id')->toArray();
            $teacherClassIds = $teacher->classes->pluck('id')->toArray();
            
            $assessmentTypes = ['Quiz', 'Test', 'In Class Test', 'Monthly Test', 'Assignment', 'Exercise', 'Project', 'Exam', 'Vacation Exam', 'National Exam'];
            $teacherAssessmentStats = [];
            
            foreach ($assessmentTypes as $type) {
                $assessments = Assessment::where('assessment_type', $type)
                    ->whereIn('subject_id', $teacherSubjectIds)
                    ->whereIn('class_id', $teacherClassIds)
                    ->get();
                    
                $totalGiven = $assessments->count();
                
                $totalMarks = 0;
                $obtainedMarks = 0;
                
                foreach ($assessments as $assessment) {
                    $marks = AssessmentMark::where('assessment_id', $assessment->id)->get();
                    foreach ($marks as $mark) {
                        if ($mark->mark !== null && $mark->total_marks > 0) {
                            $totalMarks += $mark->total_marks;
                            $obtainedMarks += $mark->mark;
                        }
                    }
                }
                
                $performance = $totalMarks > 0 ? round(($obtainedMarks / $totalMarks) * 100, 1) : 0;
                
                $teacherAssessmentStats[] = [
                    'type' => $type,
                    'given' => $totalGiven,
                    'performance' => $performance
                ];
            }
            
            // Get subject-wise assessment data for cards like in the image
            $subjectAssessmentData = [];
            foreach ($teacher->subjects as $subject) {
                $subjectClasses = $teacher->classes;
                foreach ($subjectClasses as $class) {
                    $subjectStats = [];
                    foreach ($assessmentTypes as $type) {
                        $assessments = Assessment::where('assessment_type', $type)
                            ->where('subject_id', $subject->id)
                            ->where('class_id', $class->id)
                            ->get();
                            
                        $given = $assessments->count();
                        $totalMarks = 0;
                        $obtainedMarks = 0;
                        
                        foreach ($assessments as $assessment) {
                            $marks = AssessmentMark::where('assessment_id', $assessment->id)->get();
                            foreach ($marks as $mark) {
                                if ($mark->mark !== null && $mark->total_marks > 0) {
                                    $totalMarks += $mark->total_marks;
                                    $obtainedMarks += $mark->mark;
                                }
                            }
                        }
                        
                        $performance = $totalMarks > 0 ? round(($obtainedMarks / $totalMarks) * 100, 1) : 0;
                        
                        $subjectStats[] = [
                            'type' => $type,
                            'given' => $given,
                            'performance' => $performance > 0 ? $performance . '%' : '--%'
                        ];
                    }
                    
                    $subjectAssessmentData[] = [
                        'subject' => $subject->name,
                        'class' => $class->class_name,
                        'stats' => $subjectStats
                    ];
                }
            }

            return view('home', compact('teacher', 'teacherAssessmentStats', 'subjectAssessmentData'));

        } elseif ($user->hasRole('Parent')) {
            
            $parents = Parents::with(['children.user', 'children.class'])->withCount('children')->findOrFail($user->parent->id);
            
            // Get children IDs
            $childrenIds = $parents->children->pluck('id')->toArray();
            
            // Get assessment marks for all children grouped by subject
            $assessmentData = [];
            $attendanceData = [];
            $recentAssessments = [];
            
            foreach ($parents->children as $child) {
                // Get assessment marks with subject info
                $childAssessments = AssessmentMark::with(['assessment.subject', 'assessment.class'])
                    ->where('student_id', $child->id)
                    ->whereHas('assessment')
                    ->get();
                
                // Group by subject for chart
                $subjectMarks = [];
                foreach ($childAssessments as $mark) {
                    if ($mark->assessment && $mark->assessment->subject) {
                        $subjectName = $mark->assessment->subject->name;
                        if (!isset($subjectMarks[$subjectName])) {
                            $subjectMarks[$subjectName] = [
                                'total_marks' => 0,
                                'obtained_marks' => 0,
                                'count' => 0,
                                'assessments' => []
                            ];
                        }
                        $subjectMarks[$subjectName]['total_marks'] += $mark->total_marks ?? 0;
                        $subjectMarks[$subjectName]['obtained_marks'] += $mark->mark ?? 0;
                        $subjectMarks[$subjectName]['count']++;
                        $subjectMarks[$subjectName]['assessments'][] = [
                            'topic' => $mark->assessment->topic,
                            'type' => $mark->assessment->assessment_type,
                            'mark' => $mark->mark,
                            'total' => $mark->total_marks,
                            'comment' => $mark->comment,
                            'date' => $mark->assessment->date
                        ];
                    }
                }
                
                // Calculate percentages per subject
                $subjectPercentages = [];
                foreach ($subjectMarks as $subject => $data) {
                    $percentage = $data['total_marks'] > 0 
                        ? round(($data['obtained_marks'] / $data['total_marks']) * 100, 1) 
                        : 0;
                    $subjectPercentages[$subject] = $percentage;
                }
                
                $assessmentData[$child->id] = [
                    'student_name' => $child->user->name ?? 'Unknown',
                    'class' => $child->class->class_name ?? 'N/A',
                    'subject_marks' => $subjectMarks,
                    'subject_percentages' => $subjectPercentages
                ];
                
                // Get attendance data
                $totalAttendance = Attendance::where('student_id', $child->id)->count();
                $presentCount = Attendance::where('student_id', $child->id)->where('attendence_status', 'present')->count();
                $absentCount = Attendance::where('student_id', $child->id)->where('attendence_status', 'absent')->count();
                $lateCount = Attendance::where('student_id', $child->id)->where('attendence_status', 'late')->count();
                
                $attendanceData[$child->id] = [
                    'student_name' => $child->user->name ?? 'Unknown',
                    'total' => $totalAttendance,
                    'present' => $presentCount,
                    'absent' => $absentCount,
                    'late' => $lateCount,
                    'percentage' => $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100, 1) : 0
                ];
                
                // Get recent assessments
                $recent = AssessmentMark::with(['assessment.subject'])
                    ->where('student_id', $child->id)
                    ->whereHas('assessment')
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get();
                    
                $recentAssessments[$child->id] = $recent;
            }

            return view('home', compact('parents', 'assessmentData', 'attendanceData', 'recentAssessments'));

        } elseif ($user->hasRole('Student')) {
            
            $student = Student::with(['user','parent','class','attendances'])->findOrFail($user->student->id); 

            return view('home', compact('student'));

        } else {
            return 'NO ROLE ASSIGNED YET!';
        }
        
    }

    /**
     * PROFILE
     */
    public function profile() 
    {
        return view('profile.index');
    }

    public function profileEdit() 
    {
        return view('profile.edit');
    }

    public function profileUpdate(Request $request) 
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.auth()->id()
        ]);

        if ($request->hasFile('profile_picture')) {
            $profile = Str::slug(auth()->user()->name).'-'.auth()->id().'.'.$request->profile_picture->getClientOriginalExtension();
            $request->profile_picture->move(public_path('images/profile'), $profile);
        } else {
            $profile = 'avatar.png';
        }

        $user = auth()->user();

        $user->update([
            'name'              => $request->name,
            'email'             => $request->email,
            'profile_picture'   => $profile
        ]);

        return redirect()->route('profile');
    }

    /**
     * CHANGE PASSWORD
     */
    public function changePasswordForm()
    {  
        return view('profile.changepassword');
    }

    public function changePassword(Request $request)
    {     
        if (!(Hash::check($request->get('currentpassword'), Auth::user()->password))) {
            return back()->with([
                'msg_currentpassword' => 'Your current password does not matches with the password you provided! Please try again.'
            ]);
        }
        if(strcmp($request->get('currentpassword'), $request->get('newpassword')) == 0){
            return back()->with([
                'msg_currentpassword' => 'New Password cannot be same as your current password! Please choose a different password.'
            ]);
        }

        $this->validate($request, [
            'currentpassword' => 'required',
            'newpassword'     => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        $user->password = bcrypt($request->get('newpassword'));
        $user->save();

        Auth::logout();
        return redirect()->route('login');
    }
}
