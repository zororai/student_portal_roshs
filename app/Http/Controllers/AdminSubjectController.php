<?php

namespace App\Http\Controllers;

use App\Subject;
use App\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminSubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $subjects = Subject::with('teacher')
            ->withCount('readings')
            ->latest()
            ->paginate(10);
        
        return view('backend.subjectsadmin.index', compact('subjects'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Generate next subject code (auto-increment number)
        $lastSubject = Subject::orderBy('subject_code', 'desc')->first();
        $nextSubjectCode = $lastSubject ? (intval($lastSubject->subject_code) + 1) : 1001;

        return view('backend.subjectsadmin.create', compact('nextSubjectCode'));
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
            'name'          => 'required|string|max:255|unique:subjects',
            'subject_code'  => 'required|numeric',
            'single_lessons_per_week' => 'nullable|integer|min:0|max:20',
            'double_lessons_per_week' => 'nullable|integer|min:0|max:10',
            'triple_lessons_per_week' => 'nullable|integer|min:0|max:5',
            'quad_lessons_per_week'   => 'nullable|integer|min:0|max:5',
        ]);

        // Calculate total periods per week
        $totalPeriods = ($request->single_lessons_per_week ?? 0) * 1 
                      + ($request->double_lessons_per_week ?? 0) * 2 
                      + ($request->triple_lessons_per_week ?? 0) * 3 
                      + ($request->quad_lessons_per_week ?? 0) * 4;

        Subject::create([
            'name'          => $request->name,
            'slug'          => Str::slug($request->name),
            'subject_code'  => $request->subject_code,
            'single_lessons_per_week' => $request->single_lessons_per_week ?? 0,
            'double_lessons_per_week' => $request->double_lessons_per_week ?? 0,
            'triple_lessons_per_week' => $request->triple_lessons_per_week ?? 0,
            'quad_lessons_per_week'   => $request->quad_lessons_per_week ?? 0,
            'periods_per_week' => $totalPeriods,
        ]);

        return redirect()->route('admin.subjects.index')->with('success', 'Subject created successfully.');
    }

    /**
     * Show the assign subject to teacher form.
     */
    public function assignForm()
    {
        $teachers = Teacher::with('user')->latest()->get();
        $subjects = Subject::whereNull('teacher_id')->latest()->get();
        $assignedSubjects = Subject::with('teacher.user')->whereNotNull('teacher_id')->latest()->get();

        return view('backend.subjectsadmin.assign', compact('teachers', 'subjects', 'assignedSubjects'));
    }

    /**
     * Assign subjects to a teacher.
     */
    public function assign(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'subject_ids' => 'required|array|min:1',
            'subject_ids.*' => 'exists:subjects,id',
        ]);

        Subject::whereIn('id', $request->subject_ids)->update(['teacher_id' => $request->teacher_id]);

        $count = count($request->subject_ids);
        return redirect()->route('admin.subjects.assign')->with('success', $count . ' subject(s) assigned to teacher successfully.');
    }

    /**
     * Remove teacher assignment from a subject.
     */
    public function unassign($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->update(['teacher_id' => null]);

        return redirect()->route('admin.subjects.assign')->with('success', 'Teacher unassigned from subject.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function edit(Subject $subject)
    {
        $teachers = Teacher::latest()->get();

        return view('backend.subjectsadmin.edit', compact('subject','teachers'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subject $subject)
    {
        $request->validate([
            'name'          => 'required|string|max:255|unique:subjects,name,'.$subject->id,
            'subject_code'  => 'required|numeric',
            'teacher_id'    => 'required|numeric',
            'periods_per_week' => 'required|integer|min:1|max:10'
        ]);

        $subject->update([
            'name'          => $request->name,
            'slug'          => Str::slug($request->name),
            'subject_code'  => $request->subject_code,
            'teacher_id'    => $request->teacher_id,
            'periods_per_week' => $request->periods_per_week
        ]);

        return redirect()->route('admin.subjects.index')->with('success', 'Subject updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Subject  $subject
     * @return \Illuminate\Http\Response
     */
    public function destroy(Subject $subject)
    {
        $subject->delete();

        return back()->with('success', 'Subject deleted successfully.');
    }
}
