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
        $teacher = Auth::user()->teacher;

        // Get all disciplinary records recorded by this teacher with relationships
        $records = DisciplinaryRecord::with(['student', 'class', 'teacher'])
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
            'description' => 'required|string'
        ]);

        $teacher = Auth::user()->teacher;

        DisciplinaryRecord::create([
            'student_id' => $request->student_id,
            'class_id' => $request->class_id,
            'recorded_by' => $teacher->id,
            'offense_type' => $request->offense_type,
            'offense_status' => $request->offense_status,
            'offense_date' => $request->offense_date,
            'description' => $request->description
        ]);

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
            'description' => 'required|string'
        ]);

        $record = DisciplinaryRecord::findOrFail($id);

        $record->update([
            'offense_type' => $request->offense_type,
            'offense_status' => $request->offense_status,
            'offense_date' => $request->offense_date,
            'description' => $request->description
        ]);

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

        return redirect()->route('teacher.disciplinary.index')
            ->with('success', 'Disciplinary record deleted successfully.');
    }
}
