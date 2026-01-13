<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SeatAssignmentPermissionSeeder extends Seeder
{
    public function run()
    {
        // Create the permission if it doesn't exist
        $permission = Permission::firstOrCreate(
            ['name' => 'sidebar-seat-assignment'],
            ['guard_name' => 'web']
        );

        // Get the Admin role (usually ID 1)
        $adminRole = Role::where('name', 'Admin')->first();
        
        if ($adminRole && !$adminRole->hasPermissionTo('sidebar-seat-assignment')) {
            $adminRole->givePermissionTo($permission);
            echo "Permission 'sidebar-seat-assignment' added to Admin role.\n";
        } else {
            echo "Admin role already has permission or not found.\n";
        }

        // Clear permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
