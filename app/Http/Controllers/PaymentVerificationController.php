<?php

namespace App\Http\Controllers;

use App\PaymentVerification;
use App\Parents;
use App\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PaymentVerificationController extends Controller
{
    /**
     * Parent: Show payment verification form
     */
    public function create()
    {
        $parent = Parents::where('user_id', Auth::id())->first();
        
        if (!$parent) {
            return redirect()->back()->with('error', 'Parent record not found.');
        }

        $students = Student::where('parent_id', $parent->id)
            ->with('user', 'class')
            ->get();

        $existingVerifications = PaymentVerification::where('parent_id', $parent->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('backend.parent.payment-verification.create', compact('students', 'existingVerifications'));
    }

    /**
     * Parent: Submit payment verification request
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'receipt_number' => 'required|string|max:100',
            'receipt_file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'amount_paid' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'notes' => 'nullable|string|max:500'
        ]);

        $parent = Parents::where('user_id', Auth::id())->first();

        if (!$parent) {
            return redirect()->back()->with('error', 'Parent record not found.');
        }

        // Store the receipt file
        $receiptPath = null;
        if ($request->hasFile('receipt_file')) {
            $receiptPath = $request->file('receipt_file')->store('receipts', 'public');
        }

        PaymentVerification::create([
            'parent_id' => $parent->id,
            'student_id' => $request->student_id,
            'receipt_number' => $request->receipt_number,
            'receipt_file' => $receiptPath,
            'amount_paid' => $request->amount_paid,
            'payment_date' => $request->payment_date,
            'notes' => $request->notes,
            'status' => 'pending'
        ]);

        return redirect()->route('parent.payment-verification.create')
            ->with('success', 'Payment verification submitted successfully! Please wait for admin approval.');
    }

    /**
     * Admin: List all payment verifications
     */
    public function adminIndex()
    {
        $verifications = PaymentVerification::with(['parent.user', 'student.user', 'student.class', 'verifiedBy'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $pendingCount = PaymentVerification::where('status', 'pending')->count();
        $verifiedCount = PaymentVerification::where('status', 'verified')->count();
        $rejectedCount = PaymentVerification::where('status', 'rejected')->count();

        return view('backend.admin.payment-verification.index', compact('verifications', 'pendingCount', 'verifiedCount', 'rejectedCount'));
    }

    /**
     * Admin: Show verification details
     */
    public function show($id)
    {
        $verification = PaymentVerification::with(['parent.user', 'student.user', 'student.class', 'verifiedBy'])
            ->findOrFail($id);

        // Get current term for applying payment
        $currentTerm = \App\ResultsStatus::with('termFees.feeType')
            ->orderBy('year', 'desc')
            ->orderBy('result_period', 'desc')
            ->first();

        // Get all terms for dropdown
        $allTerms = \App\ResultsStatus::with('termFees.feeType')
            ->orderBy('year', 'desc')
            ->orderBy('result_period', 'desc')
            ->get();

        return view('backend.admin.payment-verification.show', compact('verification', 'currentTerm', 'allTerms'));
    }

    /**
     * Admin: Verify payment and optionally record as student payment
     */
    public function verify(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:500',
            'apply_payment' => 'nullable|boolean',
            'results_status_id' => 'nullable|exists:results_statuses,id',
            'fee_amounts' => 'nullable|array',
            'fee_amounts.*' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string'
        ]);

        $verification = PaymentVerification::with('student')->findOrFail($id);
        
        // Update verification status
        $verification->update([
            'status' => 'verified',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'admin_notes' => $request->admin_notes
        ]);

        // If admin chose to apply payment as student payment record
        if ($request->apply_payment && $request->results_status_id && $request->fee_amounts) {
            $totalPaid = 0;
            
            foreach ($request->fee_amounts as $termFeeId => $amount) {
                if ($amount <= 0) continue;
                
                \App\StudentPayment::create([
                    'student_id' => $verification->student_id,
                    'results_status_id' => $request->results_status_id,
                    'term_fee_id' => $termFeeId,
                    'amount_paid' => $amount,
                    'payment_date' => $verification->payment_date,
                    'payment_method' => $request->payment_method ?? 'Bank Transfer',
                    'reference_number' => $verification->receipt_number,
                    'notes' => 'Applied from parent payment verification #' . $verification->id,
                ]);
                
                $totalPaid += $amount;
            }

            // Create income record if payment was applied
            if ($totalPaid > 0) {
                $studentName = $verification->student->user->name ?? 'Unknown';
                $term = \App\ResultsStatus::find($request->results_status_id);
                $termName = $term ? ucfirst($term->result_period) . ' ' . $term->year : '';

                \App\SchoolIncome::create([
                    'date' => $verification->payment_date,
                    'category' => 'School Fees',
                    'description' => 'Fees Paid For: ' . $studentName . ' - ' . $termName . ' (Verified Receipt: ' . $verification->receipt_number . ')',
                    'amount' => $totalPaid,
                    'payment_method' => $request->payment_method ?? 'Bank Transfer',
                    'reference_number' => $verification->receipt_number,
                ]);
            }

            return redirect()->route('finance.student-payments')
                ->with('success', 'Payment verified and recorded! Amount: $' . number_format($totalPaid, 2));
        }

        return redirect()->route('admin.payment-verification.index')
            ->with('success', 'Payment verified successfully! Parent can now view student results.');
    }

    /**
     * Admin: Reject payment
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_notes' => 'required|string|max:500'
        ]);

        $verification = PaymentVerification::findOrFail($id);
        
        $verification->update([
            'status' => 'rejected',
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'admin_notes' => $request->admin_notes
        ]);

        return redirect()->route('admin.payment-verification.index')
            ->with('success', 'Payment verification rejected.');
    }

    /**
     * Check if parent has verified payment for viewing results
     */
    public static function hasVerifiedPayment($parentId, $studentId = null)
    {
        $query = PaymentVerification::where('parent_id', $parentId)
            ->where('status', 'verified');

        if ($studentId) {
            $query->where('student_id', $studentId);
        }

        return $query->exists();
    }
}
