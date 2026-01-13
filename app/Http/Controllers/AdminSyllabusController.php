<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SyllabusTopic;
use App\Subject;

class AdminSyllabusController extends Controller
{
    public function index()
    {
        $topics = SyllabusTopic::with('subject')
            ->orderBy('subject_id')
            ->orderBy('order_index')
            ->paginate(20);
        
        $subjects = Subject::orderBy('name')->get();
        
        return view('backend.admin.syllabus.index', compact('topics', 'subjects'));
    }

    public function create()
    {
        $subjects = Subject::orderBy('name')->get();
        $terms = ['Term 1', 'Term 2', 'Term 3'];
        $difficultyLevels = ['easy', 'medium', 'hard'];
        
        return view('backend.admin.syllabus.create', compact('subjects', 'terms', 'difficultyLevels'));
    }

    public function store(Request $request)
    {
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

        $validated['is_active'] = $request->has('is_active');
        
        SyllabusTopic::create($validated);

        return redirect()->route('admin.syllabus.index')
            ->with('success', 'Syllabus topic created successfully!');
    }

    public function edit($id)
    {
        $topic = SyllabusTopic::findOrFail($id);
        $subjects = Subject::orderBy('name')->get();
        $terms = ['Term 1', 'Term 2', 'Term 3'];
        $difficultyLevels = ['easy', 'medium', 'hard'];
        
        return view('backend.admin.syllabus.edit', compact('topic', 'subjects', 'terms', 'difficultyLevels'));
    }

    public function update(Request $request, $id)
    {
        $topic = SyllabusTopic::findOrFail($id);
        
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

        $validated['is_active'] = $request->has('is_active');
        
        $topic->update($validated);

        return redirect()->route('admin.syllabus.index')
            ->with('success', 'Syllabus topic updated successfully!');
    }

    public function destroy($id)
    {
        $topic = SyllabusTopic::findOrFail($id);
        $topic->delete();

        return redirect()->route('admin.syllabus.index')
            ->with('success', 'Syllabus topic deleted successfully!');
    }
}
