<?php

namespace App\Http\Controllers;

use App\Payment;
use App\Student;
use App\FeeCategory;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF; // Ensure this import is correct

class PaymentController extends Controller
{
    // Show payment form
    public function create($studentId)
    {
        $student = Student::findOrFail($studentId);
        $feeCategories = FeeCategory::all();
        return view('payments.create', compact('student', 'feeCategories'));
    }

    // Store payment
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'fee_category_id' => 'required|exists:fee_categories,id',
            'amount' => 'required|numeric',
        ]);

        $payment = Payment::create([
            'student_id' => $request->student_id,
            'fee_category_id' => $request->fee_category_id,
            'amount' => $request->amount,
            'status' => 'pending', // Initially set to pending
        ]);

        // Simulate payment processing here
        // Update status based on payment gateway response
        // For now, we will assume it is completed
        $payment->status = 'completed'; 
        $payment->save();

        return redirect()->route('payments.index')->with('success', 'Payment successful!');
    }
    

    public function downloadReceipt($id)
    {
        $payment = Payment::with(['student', 'feeCategory'])->findOrFail($id);
    
        // Load the view and pass the payment data
        $pdf = PDF::loadView('payments.receipt', compact('payment'));
    
        // Download the generated PDF
        return $pdf->download('receipt_' . $payment->id . '.pdf');
    }
    // List payments
    public function index()
    {
        $payments = Payment::with(['student', 'feeCategory'])->get();
        return view('payments.index', compact('payments'));
    }

    // Generate receipt
    public function receipt($id)
    {
        $payment = Payment::with(['student', 'feeCategory'])->findOrFail($id);
        return view('payments.receipt', compact('payment'));
    }
}