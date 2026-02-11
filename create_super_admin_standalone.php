<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

// Check if super admin already exists
$superAdmin = User::where('email', 'superadmin@roshs.ac.zw')->first();

if (!$superAdmin) {
    // Create super admin user
    $superAdmin = User::create([
        'name' => 'Super Administrator',
        'email' => 'superadmin@roshs.ac.zw',
        'password' => Hash::make('SuperAdmin@2026!'),
        'is_active' => true,
        'is_super_admin' => true,
    ]);

    // Assign Admin role
    $adminRole = Role::firstOrCreate(['name' => 'Admin']);
    $superAdmin->assignRole($adminRole);

    // Give all permissions
    $allPermissions = Permission::all();
    if ($allPermissions->count() > 0) {
        $superAdmin->givePermissionTo($allPermissions);
    }

    echo "✅ Super Admin created successfully!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📧 Email:    superadmin@roshs.ac.zw\n";
    echo "🔑 Password: SuperAdmin@2026!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "\n🛡️  This account is protected from:\n";
    echo "   - Editing by other admins\n";
    echo "   - Password changes by other admins\n";
    echo "   - Deletion\n";
    echo "   - Audit trail logging\n";
} else {
    echo "ℹ️  Super Admin already exists.\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "📧 Email:    superadmin@roshs.ac.zw\n";
    echo "🔑 Password: SuperAdmin@2026!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
}
