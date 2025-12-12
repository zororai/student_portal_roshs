<?php

namespace App\Http\Controllers;

use App\User;
use App\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
            'name'              => 'required|string|max:255',
            'email'             => 'required|string|email|max:255|unique:users',
            'password'          => 'required|string|min:8',
            'gender'            => 'required|string',
            'phone'             => 'required|string|max:255',
            'dateofbirth'       => 'required|date',
            'current_address'   => 'required|string|max:255',
            'permanent_address' => 'required|string|max:255'
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password)
        ]);

        if ($request->hasFile('profile_picture')) {
            $profile = Str::slug($user->name).'-'.$user->id.'.'.$request->profile_picture->getClientOriginalExtension();
            $request->profile_picture->move(public_path('images/profile'), $profile);
        } else {
            $profile = 'avatar.png';
        }
        $user->update([
            'profile_picture' => $profile
        ]);

        $user->teacher()->create([
            'gender'            => $request->gender,
            'phone'             => $request->phone,
            'dateofbirth'       => $request->dateofbirth,
            'current_address'   => $request->current_address,
            'permanent_address' => $request->permanent_address
        ]);

        $user->assignRole('Teacher');

        return redirect()->route('teacher.index');
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

        return view('backend.teacher.assessment', compact('classes', 'teacher'));
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

        // Verify the class belongs to this teacher
        $class = $teacher->classes()->findOrFail($class_id);

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

        // Validate the request
        $validated = $request->validate([
            'class_id' => 'required|exists:grades,id',
            'subject_id' => 'required|exists:subjects,id',
            'topic' => 'required|string|max:255',
            'assessment_type' => 'required|string',
            'date' => 'required|date',
            'due_date' => 'required|date',
            'exam' => 'nullable|string|max:255',
            'papers' => 'nullable|array'
        ]);

        // Verify the class belongs to this teacher
        $class = $teacher->classes()->findOrFail($request->class_id);

        // Create the assessment
        $assessment = \App\Assessment::create([
            'teacher_id' => $teacher->id,
            'class_id' => $request->class_id,
            'subject_id' => $request->subject_id,
            'topic' => $request->topic,
            'assessment_type' => $request->assessment_type,
            'date' => $request->date,
            'due_date' => $request->due_date,
            'exam' => $request->exam,
            'papers' => $request->papers ?? []
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

        // Validate the request
        $validated = $request->validate([
            'class_id' => 'required|exists:grades,id',
            'entries' => 'required|array|min:1',
            'entries.*.comment' => 'required|string',
            'entries.*.subject_id' => 'required|exists:subjects,id',
            'entries.*.grade' => 'required|string|max:10'
        ]);

        // Verify the class belongs to this teacher
        $class = $teacher->classes()->findOrFail($request->class_id);

        // Create multiple assessment comments
        foreach ($request->entries as $entry) {
            \App\AssessmentComment::create([
                'class_id' => $request->class_id,
                'subject_id' => $entry['subject_id'],
                'comment' => $entry['comment'],
                'grade' => $entry['grade']
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
