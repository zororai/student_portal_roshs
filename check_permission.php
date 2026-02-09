<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking sidebar-ledger permission:\n";
echo "=====================================\n\n";

$permission = DB::table('permissions')
    ->where('name', 'sidebar-ledger')
    ->where('guard_name', 'web')
    ->first();

if ($permission) {
    echo "✅ Permission EXISTS in database\n";
    echo "ID: {$permission->id}\n";
    echo "Name: {$permission->name}\n";
    echo "Guard: {$permission->guard_name}\n";
    echo "Created: {$permission->created_at}\n\n";
    
    // Check if it's in the Spatie cache
    echo "Checking Spatie Permission model:\n";
    try {
        $spatie = \Spatie\Permission\Models\Permission::findByName('sidebar-ledger', 'web');
        echo "✅ Found via Spatie: {$spatie->name}\n";
    } catch (\Exception $e) {
        echo "❌ Spatie Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "❌ Permission NOT FOUND in database\n";
}

echo "\nAll Finance permissions:\n";
echo "========================\n";
$allPerms = DB::table('permissions')
    ->whereIn('name', [
        'sidebar-ledger',
        'sidebar-financial-reports',
        'sidebar-journals',
        'sidebar-receivables',
        'sidebar-payables'
    ])
    ->get(['name', 'guard_name']);

foreach ($allPerms as $p) {
    echo "- {$p->name} ({$p->guard_name})\n";
}
