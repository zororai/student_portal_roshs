<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Investigating Payment Status Issue ===\n\n";

$currentTerm = \App\ResultsStatus::orderBy('year', 'desc')->orderBy('result_period', 'desc')->first();
echo "Current Term: {$currentTerm->year} - {$currentTerm->result_period}\n";
echo "Total Fees for Term: \${$currentTerm->total_fees}\n\n";

// Get all students with payments
$studentsWithPayments = \App\Student::with('user')
    ->whereHas('payments')
    ->get();

echo "Students with payments: {$studentsWithPayments->count()}\n\n";

foreach ($studentsWithPayments as $student) {
    $totalFees = $currentTerm->total_fees;
    $amountPaid = \App\StudentPayment::where('student_id', $student->id)->sum('amount_paid');
    $balance = $totalFees - $amountPaid;
    
    // Determine status
    if ($balance == 0 && $totalFees > 0) {
        $status = 'Fully Paid';
        $statusSymbol = '✓';
    } elseif ($amountPaid > 0 && $balance > 0) {
        $status = 'Partially Paid';
        $statusSymbol = '⚠';
    } else {
        $status = 'Unpaid';
        $statusSymbol = '✗';
    }
    
    echo "Student: {$student->user->name} (ID: {$student->id})\n";
    echo "  Total Fees: \${$totalFees}\n";
    echo "  Amount Paid: \${$amountPaid}\n";
    echo "  Balance: \${$balance}\n";
    echo "  Status: {$status} {$statusSymbol}\n";
    
    // Show individual payments
    $payments = \App\StudentPayment::where('student_id', $student->id)->get();
    echo "  Payments breakdown:\n";
    foreach ($payments as $payment) {
        echo "    - \${$payment->amount_paid} on {$payment->payment_date->format('Y-m-d')}\n";
    }
    
    // Check if status is wrong
    if ($status == 'Fully Paid' && $balance > 0) {
        echo "  ⚠️ ERROR: Marked as Fully Paid but balance is \${$balance}\n";
    } elseif ($status == 'Partially Paid' && $balance == 0) {
        echo "  ⚠️ ERROR: Marked as Partially Paid but balance is \$0\n";
    }
    
    echo "\n";
}

// Check for any data type issues
echo "=== Data Type Check ===\n";
$testStudent = $studentsWithPayments->first();
if ($testStudent) {
    $totalFees = $currentTerm->total_fees;
    $amountPaid = \App\StudentPayment::where('student_id', $testStudent->id)->sum('amount_paid');
    $balance = $totalFees - $amountPaid;
    
    echo "Total Fees type: " . gettype($totalFees) . " = {$totalFees}\n";
    echo "Amount Paid type: " . gettype($amountPaid) . " = {$amountPaid}\n";
    echo "Balance type: " . gettype($balance) . " = {$balance}\n";
    echo "Balance == 0: " . ($balance == 0 ? 'true' : 'false') . "\n";
    echo "Balance > 0: " . ($balance > 0 ? 'true' : 'false') . "\n";
}
