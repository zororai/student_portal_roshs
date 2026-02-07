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
        
        // Store old values for audit trail
        $oldValues = [
            'student_type' => $student->student_type,
            'curriculum_type' => $student->curriculum_type,
            'scholarship_percentage' => $student->scholarship_percentage,
        ];
        
        // Check if scholarship percentage changed
        $scholarshipChanged = $oldValues['scholarship_percentage'] != $validated['scholarship_percentage'];
        
        // Update student
        $student->update($validated);
        
        // Log the change
        \App\AuditTrail::create([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'user_role' => auth()->user()->roles->first()->name ?? 'Admin',
            'action' => 'update',
            'description' => 'Updated scholarship and student details for ' . ($student->user->name ?? $student->name . ' ' . $student->surname),
            'old_values' => json_encode($oldValues),
            'new_values' => json_encode($validated),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        
        // Calculate new fee amounts for current term
        $currentTerm = \App\ResultsStatus::orderBy('year', 'desc')
            ->orderBy('result_period', 'desc')
            ->first();
        
        $message = 'Student updated successfully';
        
        if ($scholarshipChanged && $currentTerm) {
            // Get student's fee breakdown for current term
            $studentFees = $this->calculateStudentCurrentTermFees($student, $currentTerm);
            $totalPayments = \App\StudentPayment::where('student_id', $student->id)
                ->where('results_status_id', $currentTerm->id)
                ->sum('amount_paid');
            
            $newBalance = $studentFees - $totalPayments;
            
            $message .= sprintf(
                '. Scholarship changed from %s%% to %s%%. Current term fees recalculated: $%s (Paid: $%s, Balance: $%s)',
                number_format($oldValues['scholarship_percentage'], 1),
                number_format($validated['scholarship_percentage'], 1),
                number_format($studentFees, 2),
                number_format($totalPayments, 2),
                number_format($newBalance, 2)
            );
        }
        
        return response()->json([
            'success' => true,
            'message' => $message,
            'student' => $student->fresh(),
            'scholarship_changed' => $scholarshipChanged
        ]);
    }
    
    /**
     * Calculate student's fees for current term with scholarship applied
     */
    private function calculateStudentCurrentTermFees($student, $currentTerm)
    {
        $studentType = $student->student_type ?? 'day';
        $curriculumType = $student->curriculum_type ?? 'zimsec';
        $isNewStudent = $student->is_new_student ?? false;
        $scholarshipPercentage = floatval($student->scholarship_percentage ?? 0);
        
        // Get student's class numeric
        $classNumeric = optional($student->class)->class_numeric;
        if (!$classNumeric) {
            return 0;
        }
        
        // Find the appropriate fee level group
        $levelGroup = \App\FeeLevelGroup::where('is_active', true)
            ->where('min_class_numeric', '<=', $classNumeric)
            ->where('max_class_numeric', '>=', $classNumeric)
            ->first();
        
        if (!$levelGroup) {
            return 0;
        }
        
        // Get fee structures for this term
        $feeStructures = \App\FeeStructure::where('results_status_id', $currentTerm->id)
            ->where('fee_level_group_id', $levelGroup->id)
            ->where('student_type', $studentType)
            ->where('curriculum_type', $curriculumType)
            ->where('is_for_new_student', $isNewStudent)
            ->get();
        
        // If no fees found for new student status, try existing student fees
        if ($feeStructures->isEmpty() && $isNewStudent) {
            $feeStructures = \App\FeeStructure::where('results_status_id', $currentTerm->id)
                ->where('fee_level_group_id', $levelGroup->id)
                ->where('student_type', $studentType)
                ->where('curriculum_type', $curriculumType)
                ->where('is_for_new_student', false)
                ->get();
        }
        
        $totalFees = 0;
        foreach ($feeStructures as $feeStructure) {
            $amount = floatval($feeStructure->amount);
            
            // Apply scholarship discount
            if ($scholarshipPercentage > 0 && $scholarshipPercentage <= 100) {
                $discount = $amount * ($scholarshipPercentage / 100);
                $amount = $amount - $discount;
            }
            
            $totalFees += $amount;
        }
        
        return $totalFees;
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
