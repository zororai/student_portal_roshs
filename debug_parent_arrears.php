<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Parent 1's Children ===\n\n";

$parent = \App\Parents::with(['user', 'students.user', 'students.class'])->where('id', 1)->first();

if (!$parent) {
    echo "Parent 1 not found\n";
    exit;
}

echo "Parent: " . ($parent->user->name ?? 'N/A') . "\n";
echo "Email: " . ($parent->user->email ?? 'N/A') . "\n";
echo "Total Children: " . $parent->students->count() . "\n\n";

$allTerms = \App\ResultsStatus::all();
echo "Number of Terms: " . $allTerms->count() . "\n\n";

echo "=== All Children Details ===\n\n";

foreach ($parent->students as $student) {
    echo "Student ID: {$student->id}\n";
    echo "Name: " . ($student->user->name ?? $student->name) . "\n";
    echo "Class: " . ($student->class->class_name ?? 'N/A') . "\n";
    
    // Calculate fees for this student
    $totalFees = 0;
    foreach ($allTerms as $term) {
        $totalFees += floatval($term->total_fees);
    }
    
    $totalPaid = floatval(\App\StudentPayment::where('student_id', $student->id)->sum('amount_paid'));
    $arrears = $totalFees - $totalPaid;
    
    echo "Total Fees (all terms): \${$totalFees}\n";
    echo "Total Paid: \${$totalPaid}\n";
    echo "Arrears: \${$arrears}\n";
    echo "Has Arrears: " . ($arrears > 0 ? 'YES' : 'NO') . "\n";
    echo "---\n";
}
