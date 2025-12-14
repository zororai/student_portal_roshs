<?php

namespace App\Http\Controllers;

use App\DisciplinaryRecord;
use App\Student;
use App\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisciplinaryController extends Controller
{
    /**
     * Display a listing of disciplinary records.
     */
    public function index()
    {
        // Get all disciplinary records with relationships
        $records = DisciplinaryRecord::with(['student.user', 'class', 'teacher'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Get all classes for the dropdown
        $classes = Grade::orderBy('class_name')->get();

        return view('backend.teacher.disciplinary-records', compact('records', 'classes'));
    }

    /**
     * Get students by class ID (for AJAX).
     */
    public function getStudentsByClass($classId)
    {
        try {
            $students = Student::with('user')
                ->where('class_id', $classId)
                ->where(function($query) {
                    $query->where('is_transferred', false)
                          ->orWhereNull('is_transferred');
                })
                ->get()
                ->map(function($student) {
                    return [
                        'id' => $student->id,
                        'name' => $student->user->name ?? 'Unknown'
                    ];
                })
                ->sortBy('name')
                ->values();

            return response()->json($students);
        } catch (\Exception $e) {
            \Log::error('Error loading students: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Store a newly created disciplinary record.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:grades,id',
            'offense_type' => 'required|string|max:255',
            'offense_status' => 'required|string|max:255',
            'offense_date' => 'required|date',
            'description' => 'required|string',
            'judgement' => 'nullable|string'
        ]);

        // Get teacher ID if user is a teacher, otherwise use 0 or first teacher for Admin
        $recordedBy = 1; // Default
        if (Auth::user()->teacher) {
            $recordedBy = Auth::user()->teacher->id;
        }

        DisciplinaryRecord::create([
            'student_id' => $request->student_id,
            'class_id' => $request->class_id,
            'recorded_by' => $recordedBy,
            'offense_type' => $request->offense_type,
            'offense_status' => $request->offense_status,
            'offense_date' => $request->offense_date,
            'description' => $request->description,
            'judgement' => $request->judgement
        ]);

        // Redirect based on user role
        if (Auth::user()->hasRole('Admin')) {
            return redirect()->route('admin.disciplinary.index')
                ->with('success', 'Disciplinary record added successfully.');
        }
        return redirect()->route('teacher.disciplinary.index')
            ->with('success', 'Disciplinary record added successfully.');
    }

    /**
     * Update the specified disciplinary record.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'offense_type' => 'required|string|max:255',
            'offense_status' => 'required|string|max:255',
            'offense_date' => 'required|date',
            'description' => 'required|string',
            'judgement' => 'nullable|string'
        ]);

        $record = DisciplinaryRecord::findOrFail($id);

        $record->update([
            'offense_type' => $request->offense_type,
            'offense_status' => $request->offense_status,
            'offense_date' => $request->offense_date,
            'description' => $request->description,
            'judgement' => $request->judgement
        ]);

        // Redirect based on user role
        if (Auth::user()->hasRole('Admin')) {
            return redirect()->route('admin.disciplinary.index')
                ->with('success', 'Disciplinary record updated successfully.');
        }
        return redirect()->route('teacher.disciplinary.index')
            ->with('success', 'Disciplinary record updated successfully.');
    }

    /**
     * Remove the specified disciplinary record.
     */
    public function destroy($id)
    {
        $record = DisciplinaryRecord::findOrFail($id);
        $record->delete();

        // Redirect based on user role
        if (Auth::user()->hasRole('Admin')) {
            return redirect()->route('admin.disciplinary.index')
                ->with('success', 'Disciplinary record deleted successfully.');
        }
        return redirect()->route('teacher.disciplinary.index')
            ->with('success', 'Disciplinary record deleted successfully.');
    }
}
