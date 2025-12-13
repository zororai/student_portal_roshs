<?php

// Run this file with: php setup_roles.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Spatie\Permission\Models\Role;

echo "Checking existing roles...\n";
$existingRoles = Role::all(['id', 'name']);
foreach ($existingRoles as $role) {
    echo "  - {$role->name} (ID: {$role->id})\n";
}

// Create roles if they don't exist
$rolesToCreate = ['Admin', 'Teacher', 'Parent', 'Student'];

foreach ($rolesToCreate as $roleName) {
    $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
    echo "✓ Role '{$roleName}' ready (ID: {$role->id})\n";
}

echo "\nAssigning roles to users...\n";

// Assign Teacher role
$teacher = App\User::where('email', 'teacher@mail.com')->first();
if ($teacher) {
    $teacher->syncRoles(['Teacher']);
    echo "✓ Teacher role assigned to teacher@mail.com\n";
    
    // Create teacher profile with all required fields
    $teacherProfile = App\Teacher::firstOrCreate(
        ['user_id' => $teacher->id],
        [
            'phone' => '0000000000',
            'dateofbirth' => '1990-01-01',
            'gender' => 'Male',
            'current_address' => 'N/A',
            'permanent_address' => 'N/A'
        ]
    );
    echo "✓ Teacher profile created (ID: {$teacherProfile->id})\n";
} else {
    echo "✗ User teacher@mail.com not found\n";
}

// Assign Admin role
$admin = App\User::where('email', 'admin@mail.com')->first();
if ($admin) {
    $admin->syncRoles(['Admin']);
    echo "✓ Admin role assigned to admin@mail.com\n";
} else {
    echo "✗ User admin@mail.com not found\n";
}

echo "\n✓ Setup complete! Please log out and log back in.\n";
