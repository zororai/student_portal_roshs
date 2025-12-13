<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\ResultsStatus;

echo "Updating existing terms...\n\n";

$terms = ResultsStatus::all();

foreach ($terms as $term) {
    // Calculate total from term fees
    $total = $term->termFees()->sum('amount');
    
    echo "Term: {$term->year} - {$term->result_period}\n";
    echo "Current Total: \${$term->total_fees}\n";
    echo "Calculated Total: \${$total}\n";
    
    // Update the total
    $term->total_fees = $total;
    $term->save();
    
    echo "✓ Updated!\n\n";
}

echo "All terms updated successfully!\n";
