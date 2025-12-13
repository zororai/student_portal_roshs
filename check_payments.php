<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Recent Payments ===\n\n";

$payments = \App\StudentPayment::with('student.user')
    ->latest()
    ->take(10)
    ->get();

foreach ($payments as $payment) {
    echo "Payment ID: {$payment->id}\n";
    echo "Student: {$payment->student->user->name} (ID: {$payment->student_id})\n";
    echo "Amount Paid: \${$payment->amount_paid}\n";
    echo "Date: {$payment->created_at}\n";
    echo "---\n";
}

echo "\n=== Student Balance Check ===\n\n";

$students = \App\Student::with('user')->take(5)->get();
$currentTerm = \App\ResultsStatus::orderBy('year', 'desc')->orderBy('result_period', 'desc')->first();

foreach ($students as $student) {
    $totalFees = $currentTerm ? $currentTerm->total_fees : 0;
    $amountPaid = \App\StudentPayment::where('student_id', $student->id)->sum('amount_paid');
    $balance = $totalFees - $amountPaid;
    
    echo "Student: {$student->user->name} (ID: {$student->id})\n";
    echo "Total Fees: \${$totalFees}\n";
    echo "Amount Paid: \${$amountPaid}\n";
    echo "Balance: \${$balance}\n";
    
    if ($balance == 0 && $totalFees > 0) {
        echo "Status: Fully Paid\n";
    } elseif ($amountPaid > 0 && $balance > 0) {
        echo "Status: Partially Paid\n";
    } else {
        echo "Status: Unpaid\n";
    }
    echo "---\n";
}
