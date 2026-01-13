<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SyllabusTopic;
use App\Subject;

class TeacherSyllabusController extends Controller
{
    public function index()
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            return redirect()->route('home')->with('error', 'Teacher profile not found.');
        }

        // Get subjects taught by this teacher
        $subjectIds = $teacher->subjects->pluck('id');

        $topics = SyllabusTopic::whereIn('subject_id', $subjectIds)
            ->with('subject')
            ->orderBy('subject_id')
            ->orderBy('order_index')
            ->paginate(20);
        
        $subjects = $teacher->subjects;
        
        return view('backend.teacher.syllabus.index', compact('topics', 'subjects'));
    }

    public function create()
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            return redirect()->route('home')->with('error', 'Teacher profile not found.');
        }

        $subjects = $teacher->subjects;
        $terms = ['Term 1', 'Term 2', 'Term 3'];
        $difficultyLevels = ['easy', 'medium', 'hard'];
        
        return view('backend.teacher.syllabus.create', compact('subjects', 'terms', 'difficultyLevels'));
    }

    public function store(Request $request)
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            return redirect()->route('home')->with('error', 'Teacher profile not found.');
        }

        // Verify teacher teaches this subject
        $subjectIds = $teacher->subjects->pluck('id');

        // Handle multiple topics submission
        if ($request->has('multiple') && $request->has('topics')) {
            $request->validate([
                'subject_id' => 'required|exists:subjects,id',
                'term' => 'required|in:Term 1,Term 2,Term 3',
                'topics' => 'required|array|min:1',
                'topics.*.name' => 'required|string|max:255',
                'topics.*.description' => 'nullable|string',
                'topics.*.learning_objectives' => 'nullable|string',
                'topics.*.difficulty_level' => 'required|in:easy,medium,hard',
                'topics.*.suggested_periods' => 'required|integer|min:1|max:20',
                'topics.*.order_index' => 'nullable|integer|min:0',
            ]);

            if (!$subjectIds->contains($request->subject_id)) {
                return back()->withErrors(['subject_id' => 'You can only create topics for subjects you teach.']);
            }

            $createdCount = 0;
            foreach ($request->topics as $topicData) {
                SyllabusTopic::create([
                    'subject_id' => $request->subject_id,
                    'term' => $request->term,
                    'name' => $topicData['name'],
                    'description' => $topicData['description'] ?? null,
                    'learning_objectives' => $topicData['learning_objectives'] ?? null,
                    'difficulty_level' => $topicData['difficulty_level'],
                    'suggested_periods' => $topicData['suggested_periods'],
                    'order_index' => $topicData['order_index'] ?? 0,
                    'is_active' => isset($topicData['is_active']) ? true : false,
                ]);
                $createdCount++;
            }

            return redirect()->route('teacher.syllabus.index')
                ->with('success', "{$createdCount} syllabus topic(s) created successfully!");
        }
        
        // Handle single topic submission (legacy)
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'learning_objectives' => 'nullable|string',
            'term' => 'required|in:Term 1,Term 2,Term 3',
            'difficulty_level' => 'required|in:easy,medium,hard',
            'suggested_periods' => 'required|integer|min:1|max:20',
            'order_index' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        if (!$subjectIds->contains($validated['subject_id'])) {
            return back()->withErrors(['subject_id' => 'You can only create topics for subjects you teach.']);
        }

        $validated['is_active'] = $request->has('is_active');
        
        SyllabusTopic::create($validated);

        return redirect()->route('teacher.syllabus.index')
            ->with('success', 'Syllabus topic created successfully!');
    }

    public function edit($id)
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            return redirect()->route('home')->with('error', 'Teacher profile not found.');
        }

        $topic = SyllabusTopic::findOrFail($id);
        
        // Verify teacher teaches this subject
        $subjectIds = $teacher->subjects->pluck('id');
        if (!$subjectIds->contains($topic->subject_id)) {
            return redirect()->route('teacher.syllabus.index')
                ->with('error', 'You can only edit topics for subjects you teach.');
        }

        $subjects = $teacher->subjects;
        $terms = ['Term 1', 'Term 2', 'Term 3'];
        $difficultyLevels = ['easy', 'medium', 'hard'];
        
        return view('backend.teacher.syllabus.edit', compact('topic', 'subjects', 'terms', 'difficultyLevels'));
    }

    public function update(Request $request, $id)
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            return redirect()->route('home')->with('error', 'Teacher profile not found.');
        }

        $topic = SyllabusTopic::findOrFail($id);
        
        // Verify teacher teaches this subject
        $subjectIds = $teacher->subjects->pluck('id');
        if (!$subjectIds->contains($topic->subject_id)) {
            return redirect()->route('teacher.syllabus.index')
                ->with('error', 'You can only edit topics for subjects you teach.');
        }
        
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'learning_objectives' => 'nullable|string',
            'term' => 'required|in:Term 1,Term 2,Term 3',
            'difficulty_level' => 'required|in:easy,medium,hard',
            'suggested_periods' => 'required|integer|min:1|max:20',
            'order_index' => 'nullable|integer|min:0',
            'is_active' => 'boolean'
        ]);

        if (!$subjectIds->contains($validated['subject_id'])) {
            return back()->withErrors(['subject_id' => 'You can only update topics for subjects you teach.']);
        }

        $validated['is_active'] = $request->has('is_active');
        
        $topic->update($validated);

        return redirect()->route('teacher.syllabus.index')
            ->with('success', 'Syllabus topic updated successfully!');
    }

    public function destroy($id)
    {
        $teacher = auth()->user()->teacher;

        if (!$teacher) {
            return redirect()->route('home')->with('error', 'Teacher profile not found.');
        }

        $topic = SyllabusTopic::findOrFail($id);
        
        // Verify teacher teaches this subject
        $subjectIds = $teacher->subjects->pluck('id');
        if (!$subjectIds->contains($topic->subject_id)) {
            return redirect()->route('teacher.syllabus.index')
                ->with('error', 'You can only delete topics for subjects you teach.');
        }

        $topic->delete();

        return redirect()->route('teacher.syllabus.index')
            ->with('success', 'Syllabus topic deleted successfully!');
    }
}
