<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\FeeType;

echo "Current fee types count: " . FeeType::count() . "\n\n";

// Delete existing fee types
FeeType::truncate();
echo "Cleared existing fee types.\n\n";

$feeTypes = [
    // Core / Mandatory Fees
    ['name' => 'Tuition Fee', 'description' => 'Core / Mandatory Fees - Tuition Fee', 'is_active' => true],
    ['name' => 'Levy (SDC / Development Levy)', 'description' => 'Core / Mandatory Fees - School Development Committee Levy', 'is_active' => true],
    ['name' => 'Examination Fee', 'description' => 'Core / Mandatory Fees - Examination Fee', 'is_active' => true],
    ['name' => 'Capital / Building Fee', 'description' => 'Core / Mandatory Fees - Capital / Building Fee', 'is_active' => true],
    
    // Academic & Learning Fees
    ['name' => 'Practical Subjects Fee', 'description' => 'Academic & Learning Fees - Practical Subjects Fee', 'is_active' => true],
    ['name' => 'ICT / Computer Fee', 'description' => 'Academic & Learning Fees - ICT / Computer Fee', 'is_active' => true],
    ['name' => 'Library Fee', 'description' => 'Academic & Learning Fees - Library Fee', 'is_active' => true],
    
    // Sports & Activities
    ['name' => 'Sports Fee', 'description' => 'Sports & Activities - Sports Fee', 'is_active' => true],
    ['name' => 'Clubs & Societies Fee', 'description' => 'Sports & Activities - Clubs & Societies Fee', 'is_active' => true],
    
    // Boarding Fees
    ['name' => 'Boarding Fee', 'description' => 'Boarding Fees - Boarding Fee', 'is_active' => true],
    ['name' => 'Boarding Maintenance Fee', 'description' => 'Boarding Fees - Boarding Maintenance Fee', 'is_active' => true],
    ['name' => 'Laundry Fee', 'description' => 'Boarding Fees - Laundry Fee', 'is_active' => true],
    
    // Transport & Services
    ['name' => 'Transport Fee', 'description' => 'Transport & Services - Transport Fee', 'is_active' => true],
    ['name' => 'Boarding Meals Fee', 'description' => 'Transport & Services - Boarding Meals Fee', 'is_active' => true],
    
    // Government & External Fees
    ['name' => 'ZIMSEC Examination Fee', 'description' => 'Government & External Fees - ZIMSEC Examination Fee', 'is_active' => true],
    ['name' => 'Ministry Registration / Approval Fee', 'description' => 'Government & External Fees - Ministry Registration / Approval Fee', 'is_active' => true],
    
    // Optional / Pay-as-You-Use Fees
    ['name' => 'Uniform Fee', 'description' => 'Optional / Pay-as-You-Use Fees - Uniform Fee', 'is_active' => true],
    ['name' => 'Textbook Fee', 'description' => 'Optional / Pay-as-You-Use Fees - Textbook Fee', 'is_active' => true],
    ['name' => 'Extra Lessons / Tutorials Fee', 'description' => 'Optional / Pay-as-You-Use Fees - Extra Lessons / Tutorials Fee', 'is_active' => true],
    ['name' => 'School Trip / Educational Tour Fee', 'description' => 'Optional / Pay-as-You-Use Fees - School Trip / Educational Tour Fee', 'is_active' => true],
    
    // Penalties & Adjustments
    ['name' => 'Late Payment Penalty', 'description' => 'Penalties & Adjustments - Late Payment Penalty', 'is_active' => true],
    ['name' => 'Damage / Breakage Fee', 'description' => 'Penalties & Adjustments - Damage / Breakage Fee', 'is_active' => true],
    ['name' => 'Refund / Credit Adjustment', 'description' => 'Penalties & Adjustments - Refund / Credit Adjustment', 'is_active' => true],
];

foreach ($feeTypes as $feeType) {
    FeeType::create($feeType);
    echo "Created: {$feeType['name']}\n";
}

echo "\n✓ Successfully inserted " . count($feeTypes) . " fee types!\n";
echo "Total fee types in database: " . FeeType::count() . "\n";
