<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\ResultsStatus;

echo "Checking Terms in Database:\n\n";

$terms = ResultsStatus::with('termFees.feeType')->get();

if ($terms->count() === 0) {
    echo "No terms found in database.\n";
} else {
    foreach ($terms as $term) {
        echo "Year: {$term->year}\n";
        echo "Period: {$term->result_period}\n";
        echo "Total Fees: \${$term->total_fees}\n";
        echo "Number of Fee Types: {$term->termFees->count()}\n";
        
        if ($term->termFees->count() > 0) {
            echo "Fee Breakdown:\n";
            foreach ($term->termFees as $termFee) {
                echo "  - {$termFee->feeType->name}: \${$termFee->amount}\n";
            }
        }
        echo "\n---\n\n";
    }
}
