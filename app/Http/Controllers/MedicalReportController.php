<?php

namespace App\Http\Controllers;

use App\MedicalReport;
use App\Parents;
use App\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MedicalReportController extends Controller
{
    /**
     * Parent: Display list of medical reports
     */
    public function parentIndex()
    {
        $parent = Parents::where('user_id', Auth::id())->first();
        
        if (!$parent) {
            return view('backend.parent.medical-reports.index', [
                'reports' => collect(),
                'students' => collect(),
                'error' => 'Parent record not found.'
            ]);
        }

        $students = Student::where('parent_id', $parent->id)->with('user')->get();
        $reports = MedicalReport::where('parent_id', $parent->id)
            ->with(['student.user', 'acknowledgedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('backend.parent.medical-reports.index', compact('reports', 'students'));
    }

    /**
     * Parent: Show create form
     */
    public function parentCreate()
    {
        $parent = Parents::where('user_id', Auth::id())->first();
        
        if (!$parent) {
            return redirect()->route('parent.medical-reports.index')
                ->with('error', 'Parent record not found.');
        }

        $students = Student::where('parent_id', $parent->id)->with('user')->get();
        
        if ($students->isEmpty()) {
            return redirect()->route('parent.medical-reports.index')
                ->with('error', 'No students linked to your account.');
        }

        $conditionTypes = [
            'allergy' => 'Allergy',
            'chronic' => 'Chronic Condition',
            'disability' => 'Disability',
            'medication' => 'Ongoing Medication',
            'surgery' => 'Recent Surgery',
            'injury' => 'Injury',
            'mental_health' => 'Mental Health',
            'other' => 'Other'
        ];

        return view('backend.parent.medical-reports.create', compact('students', 'conditionTypes'));
    }

    /**
     * Parent: Store new medical report
     */
    public function parentStore(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'condition_type' => 'required|string',
            'condition_name' => 'required|string|max:255',
            'description' => 'required|string',
            'medications' => 'nullable|string',
            'emergency_instructions' => 'nullable|string',
            'diagnosis_date' => 'nullable|date',
            'doctor_name' => 'nullable|string|max:255',
            'doctor_contact' => 'nullable|string|max:255',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $parent = Parents::where('user_id', Auth::id())->first();
        
        if (!$parent) {
            return redirect()->back()->with('error', 'Parent record not found.');
        }

        // Verify student belongs to this parent
        $student = Student::where('id', $request->student_id)
            ->where('parent_id', $parent->id)
            ->first();
            
        if (!$student) {
            return redirect()->back()->with('error', 'Invalid student selected.');
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('medical-reports', 'public');
        }

        MedicalReport::create([
            'parent_id' => $parent->id,
            'student_id' => $request->student_id,
            'condition_type' => $request->condition_type,
            'condition_name' => $request->condition_name,
            'description' => $request->description,
            'medications' => $request->medications,
            'emergency_instructions' => $request->emergency_instructions,
            'diagnosis_date' => $request->diagnosis_date,
            'doctor_name' => $request->doctor_name,
            'doctor_contact' => $request->doctor_contact,
            'attachment_path' => $attachmentPath,
            'status' => 'pending',
        ]);

        return redirect()->route('parent.medical-reports.index')
            ->with('success', 'Medical report submitted successfully. Admin will review and acknowledge it.');
    }

    /**
     * Parent: View single report
     */
    public function parentShow($id)
    {
        $parent = Parents::where('user_id', Auth::id())->first();
        
        $report = MedicalReport::where('id', $id)
            ->where('parent_id', $parent->id)
            ->with(['student.user', 'acknowledgedBy'])
            ->firstOrFail();

        return view('backend.parent.medical-reports.show', compact('report'));
    }

    /**
     * Admin: Display all medical reports
     */
    public function adminIndex()
    {
        $reports = MedicalReport::with(['parent.user', 'student.user', 'student.class', 'acknowledgedBy'])
            ->orderBy('status', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        $pendingCount = $reports->where('status', 'pending')->count();
        $acknowledgedCount = $reports->where('status', 'acknowledged')->count();

        return view('backend.admin.medical-reports.index', compact('reports', 'pendingCount', 'acknowledgedCount'));
    }

    /**
     * Admin: View single report
     */
    public function adminShow($id)
    {
        $report = MedicalReport::with(['parent.user', 'student.user', 'student.class', 'acknowledgedBy'])
            ->findOrFail($id);

        return view('backend.admin.medical-reports.show', compact('report'));
    }

    /**
     * Admin: Acknowledge report
     */
    public function adminAcknowledge(Request $request, $id)
    {
        $request->validate([
            'admin_response' => 'nullable|string',
        ]);

        $report = MedicalReport::findOrFail($id);
        
        $report->update([
            'status' => 'acknowledged',
            'acknowledged_by' => Auth::id(),
            'acknowledged_at' => now(),
            'admin_response' => $request->admin_response,
        ]);

        return redirect()->route('admin.medical-reports.index')
            ->with('success', 'Medical report acknowledged successfully.');
    }

    /**
     * Admin: Mark as reviewed
     */
    public function adminReview(Request $request, $id)
    {
        $request->validate([
            'admin_response' => 'nullable|string',
        ]);

        $report = MedicalReport::findOrFail($id);
        
        $report->update([
            'status' => 'reviewed',
            'admin_response' => $request->admin_response ?? $report->admin_response,
        ]);

        return redirect()->route('admin.medical-reports.index')
            ->with('success', 'Medical report marked as reviewed.');
    }
}
