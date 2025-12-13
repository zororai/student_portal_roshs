<?php

// Run this file with: php assign_roles.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Assign Teacher role
$teacher = App\User::where('email', 'teacher@mail.com')->first();
if ($teacher) {
    $teacher->assignRole('Teacher');
    echo "✓ Teacher role assigned to teacher@mail.com\n";
    
    // Create teacher profile
    $teacherProfile = App\Teacher::firstOrCreate(['user_id' => $teacher->id]);
    echo "✓ Teacher profile created (ID: {$teacherProfile->id})\n";
} else {
    echo "✗ User teacher@mail.com not found\n";
}

// Assign Admin role
$admin = App\User::where('email', 'admin@mail.com')->first();
if ($admin) {
    $admin->assignRole('Admin');
    echo "✓ Admin role assigned to admin@mail.com\n";
} else {
    echo "✗ User admin@mail.com not found\n";
}

echo "\nDone! Please log out and log back in.\n";
