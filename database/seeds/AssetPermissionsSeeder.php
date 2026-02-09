<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AssetPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Asset Management Permissions
        $permissions = [
            // Asset permissions
            'assets.view',
            'assets.create',
            'assets.edit',
            'assets.delete',
            'assets.assign',
            'assets.dispose',
            
            // Asset Category permissions
            'asset-categories.view',
            'asset-categories.create',
            'asset-categories.edit',
            'asset-categories.delete',
            
            // Asset Location permissions
            'asset-locations.view',
            'asset-locations.create',
            'asset-locations.edit',
            'asset-locations.delete',
            
            // Asset Maintenance permissions
            'asset-maintenance.view',
            'asset-maintenance.create',
            'asset-maintenance.edit',
            'asset-maintenance.complete',
            'asset-maintenance.approve',
            
            // Asset Depreciation permissions
            'asset-depreciation.view',
            'asset-depreciation.calculate',
            'asset-depreciation.post-to-ledger',
            
            // Asset Reports permissions
            'asset-reports.view',
            'asset-reports.export',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign permissions to roles
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        // Bursar - Financial & depreciation
        $bursarRole = Role::where('name', 'Bursar')->first();
        if ($bursarRole) {
            $bursarRole->givePermissionTo([
                'assets.view',
                'asset-categories.view',
                'asset-locations.view',
                'asset-maintenance.view',
                'asset-depreciation.view',
                'asset-depreciation.calculate',
                'asset-depreciation.post-to-ledger',
                'asset-reports.view',
                'asset-reports.export',
            ]);
        }

        // Storekeeper - Register & maintain
        $storekeeperRole = Role::where('name', 'Storekeeper')->first();
        if ($storekeeperRole) {
            $storekeeperRole->givePermissionTo([
                'assets.view',
                'assets.create',
                'assets.edit',
                'assets.assign',
                'asset-categories.view',
                'asset-locations.view',
                'asset-locations.create',
                'asset-locations.edit',
                'asset-maintenance.view',
                'asset-maintenance.create',
                'asset-maintenance.edit',
                'asset-maintenance.complete',
                'asset-reports.view',
            ]);
        }

        // Teacher - View assigned assets
        $teacherRole = Role::where('name', 'Teacher')->first();
        if ($teacherRole) {
            $teacherRole->givePermissionTo([
                'assets.view',
                'asset-maintenance.view',
                'asset-maintenance.create',
            ]);
        }

        // Auditor - Read-only
        $auditorRole = Role::where('name', 'Auditor')->first();
        if ($auditorRole) {
            $auditorRole->givePermissionTo([
                'assets.view',
                'asset-categories.view',
                'asset-locations.view',
                'asset-maintenance.view',
                'asset-depreciation.view',
                'asset-reports.view',
                'asset-reports.export',
            ]);
        }

        $this->command->info('Asset permissions seeded successfully!');
    }
}
