<?php

namespace App\Http\Controllers;

use App\Subject;
use App\Teacher;
use App\Grade;
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
        $classes = Grade::orderBy('class_name')->get();

        return view('backend.subjectsadmin.create', compact('classes'));
    }

    /**
     * Generate subject code from subject name and class name
     * Format: [First letter of subject][Last letter of subject][First char of class][Last char of class]
     */
    private function generateSubjectCode($subjectName, $className)
    {
        $subjectName = strtoupper(trim($subjectName));
        $className = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $className));
        
        $firstLetterSubject = substr($subjectName, 0, 1);
        $lastLetterSubject = substr($subjectName, -1);
        $firstCharClass = substr($className, 0, 1);
        $lastCharClass = substr($className, -1);
        
        return $firstLetterSubject . $lastLetterSubject . $firstCharClass . $lastCharClass;
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
            'class_id' => 'required|exists:grades,id',
            'subjects' => 'required|array|min:1',
            'subjects.*.name' => 'required|string|max:255',
            'subjects.*.subject_code' => 'required|string|max:20',
            'subjects.*.single_lessons_per_week' => 'nullable|numeric|min:0|max:20',
            'subjects.*.double_lessons_per_week' => 'nullable|numeric|min:0|max:10',
            'subjects.*.triple_lessons_per_week' => 'nullable|numeric|min:0|max:5',
            'subjects.*.quad_lessons_per_week'   => 'nullable|numeric|min:0|max:5',
        ]);

        // Convert empty strings to 0 for lesson fields
        $subjects = $request->subjects;
        foreach ($subjects as $key => $subject) {
            $subjects[$key]['single_lessons_per_week'] = intval($subject['single_lessons_per_week'] ?? 0);
            $subjects[$key]['double_lessons_per_week'] = intval($subject['double_lessons_per_week'] ?? 0);
            $subjects[$key]['triple_lessons_per_week'] = intval($subject['triple_lessons_per_week'] ?? 0);
            $subjects[$key]['quad_lessons_per_week'] = intval($subject['quad_lessons_per_week'] ?? 0);
        }
        $request->merge(['subjects' => $subjects]);

        $class = Grade::findOrFail($request->class_id);
        $createdCount = 0;

        foreach ($request->subjects as $subjectData) {
            // Check if subject with same name already exists
            $existingSubject = Subject::where('name', $subjectData['name'])->first();
            
            if ($existingSubject) {
                // Just attach to class if not already attached
                if (!$existingSubject->grades()->where('grade_id', $class->id)->exists()) {
                    $existingSubject->grades()->attach($class->id);
                }
                continue;
            }

            // Calculate total periods per week
            $totalPeriods = ($subjectData['single_lessons_per_week'] ?? 0) * 1 
                          + ($subjectData['double_lessons_per_week'] ?? 0) * 2 
                          + ($subjectData['triple_lessons_per_week'] ?? 0) * 3 
                          + ($subjectData['quad_lessons_per_week'] ?? 0) * 4;

            $subject = Subject::create([
                'name'          => $subjectData['name'],
                'slug'          => Str::slug($subjectData['name']),
                'subject_code'  => $subjectData['subject_code'],
                'single_lessons_per_week' => $subjectData['single_lessons_per_week'] ?? 0,
                'double_lessons_per_week' => $subjectData['double_lessons_per_week'] ?? 0,
                'triple_lessons_per_week' => $subjectData['triple_lessons_per_week'] ?? 0,
                'quad_lessons_per_week'   => $subjectData['quad_lessons_per_week'] ?? 0,
                'periods_per_week' => $totalPeriods,
            ]);

            // Attach subject to the selected class
            $subject->grades()->attach($class->id);
            $createdCount++;
        }

        return redirect()->route('admin.subjects.index')->with('success', $createdCount . ' subject(s) created and assigned to ' . $class->class_name . '.');
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
