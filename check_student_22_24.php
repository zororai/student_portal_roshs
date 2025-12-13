<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Detailed Check for Students 22 and 24 ===\n\n";

$currentTerm = \App\ResultsStatus::orderBy('year', 'desc')->orderBy('result_period', 'desc')->first();
$totalFees = $currentTerm ? $currentTerm->total_fees : 0;

foreach ([22, 24] as $studentId) {
    $student = \App\Student::with('user')->find($studentId);
    
    if (!$student) {
        echo "Student $studentId not found\n";
        continue;
    }
    
    echo "Student: {$student->user->name} (ID: {$student->id})\n";
    echo "Total Fees: \${$totalFees}\n";
    
    $payments = \App\StudentPayment::where('student_id', $studentId)->get();
    echo "Number of payments: {$payments->count()}\n";
    
    $amountPaid = 0;
    foreach ($payments as $payment) {
        echo "  - Payment ID {$payment->id}: \${$payment->amount_paid}\n";
        $amountPaid += $payment->amount_paid;
    }
    
    echo "Total Amount Paid (calculated): \${$amountPaid}\n";
    
    $amountPaidSum = \App\StudentPayment::where('student_id', $studentId)->sum('amount_paid');
    echo "Total Amount Paid (sum query): \${$amountPaidSum}\n";
    
    $balance = $totalFees - $amountPaid;
    echo "Balance: \${$balance}\n";
    
    if ($balance == 0 && $totalFees > 0) {
        echo "Status: Fully Paid ✓\n";
    } elseif ($amountPaid > 0 && $balance > 0) {
        echo "Status: Partially Paid ⚠\n";
    } else {
        echo "Status: Unpaid ✗\n";
    }
    
    echo "\n---\n\n";
}
