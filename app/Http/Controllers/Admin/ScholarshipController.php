<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Student;
use Illuminate\Http\Request;

class ScholarshipController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::whereNotNull('scholarship_percentage')
            ->where('scholarship_percentage', '>', 0)
            ->with(['class', 'parents', 'user']);
        
        // Filter by class
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        
        // Filter by curriculum
        if ($request->filled('curriculum_type')) {
            $query->where('curriculum_type', $request->curriculum_type);
        }
        
        // Filter by student type
        if ($request->filled('student_type')) {
            $query->where('student_type', $request->student_type);
        }
        
        // Search by name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }
        
        $students = $query->orderBy('scholarship_percentage', 'desc')->paginate(20);
        
        $classes = \App\Grade::orderBy('class_numeric')->get();
        
        return view('admin.scholarships.index', compact('students', 'classes'));
    }
    
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'student_type' => 'required|in:day,boarding',
            'curriculum_type' => 'required|in:zimsec,cambridge',
            'scholarship_percentage' => 'required|numeric|min:0|max:100',
        ]);
        
        $student->update($validated);
        
        return response()->json([
            'success' => true,
            'message' => 'Student updated successfully',
            'student' => $student->fresh()
        ]);
    }
    
    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'field' => 'required|in:student_type,curriculum_type',
            'value' => 'required|string',
        ]);
        
        $field = $validated['field'];
        $value = $validated['value'];
        
        // Validate value based on field
        if ($field === 'student_type' && !in_array($value, ['day', 'boarding'])) {
            return response()->json(['success' => false, 'message' => 'Invalid student type'], 422);
        }
        if ($field === 'curriculum_type' && !in_array($value, ['zimsec', 'cambridge'])) {
            return response()->json(['success' => false, 'message' => 'Invalid curriculum type'], 422);
        }
        
        Student::whereIn('id', $validated['student_ids'])->update([$field => $value]);
        
        return response()->json([
            'success' => true,
            'message' => count($validated['student_ids']) . ' students updated successfully'
        ]);
    }
}
