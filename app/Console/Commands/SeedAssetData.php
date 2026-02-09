<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\AssetCategory;
use App\AssetLocation;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SeedAssetData extends Command
{
    protected $signature = 'assets:seed';
    protected $description = 'Seed asset categories, locations, and permissions';

    public function handle()
    {
        $this->info('Seeding Asset Categories...');
        $this->seedCategories();
        
        $this->info('Seeding Asset Locations...');
        $this->seedLocations();
        
        $this->info('Seeding Asset Permissions...');
        $this->seedPermissions();
        
        $this->info('Asset data seeded successfully!');
        return 0;
    }

    protected function seedCategories()
    {
        $categories = [
            ['name' => 'Furniture', 'code' => 'FUR001', 'description' => 'Desks, chairs, tables, cabinets', 'useful_life_years' => 10, 'depreciation_method' => 'straight_line'],
            ['name' => 'Computers & IT Equipment', 'code' => 'IT001', 'description' => 'Computers, laptops, printers, projectors', 'useful_life_years' => 5, 'depreciation_method' => 'straight_line'],
            ['name' => 'Laboratory Equipment', 'code' => 'LAB001', 'description' => 'Science lab equipment and apparatus', 'useful_life_years' => 8, 'depreciation_method' => 'straight_line'],
            ['name' => 'Sports Equipment', 'code' => 'SPT001', 'description' => 'Sports gear and equipment', 'useful_life_years' => 5, 'depreciation_method' => 'straight_line'],
            ['name' => 'Musical Instruments', 'code' => 'MUS001', 'description' => 'Musical instruments and audio equipment', 'useful_life_years' => 10, 'depreciation_method' => 'straight_line'],
            ['name' => 'Library Books', 'code' => 'LIB001', 'description' => 'Library books and reference materials', 'useful_life_years' => 5, 'depreciation_method' => 'straight_line'],
            ['name' => 'Vehicles', 'code' => 'VEH001', 'description' => 'School buses and vehicles', 'useful_life_years' => 10, 'depreciation_method' => 'reducing_balance'],
            ['name' => 'Kitchen Equipment', 'code' => 'KIT001', 'description' => 'Kitchen and catering equipment', 'useful_life_years' => 7, 'depreciation_method' => 'straight_line'],
            ['name' => 'Audio Visual', 'code' => 'AV001', 'description' => 'TVs, sound systems, cameras', 'useful_life_years' => 5, 'depreciation_method' => 'straight_line'],
            ['name' => 'Other Equipment', 'code' => 'OTH001', 'description' => 'Miscellaneous equipment', 'useful_life_years' => 5, 'depreciation_method' => 'straight_line'],
        ];

        foreach ($categories as $data) {
            AssetCategory::firstOrCreate(['code' => $data['code']], $data);
        }
    }

    protected function seedLocations()
    {
        $locations = [
            ['name' => 'Main Office', 'building' => 'Administration Block', 'floor' => 'Ground Floor'],
            ['name' => 'Principal Office', 'building' => 'Administration Block', 'floor' => 'Ground Floor'],
            ['name' => 'Staff Room', 'building' => 'Administration Block', 'floor' => 'First Floor'],
            ['name' => 'Computer Lab', 'building' => 'Science Block', 'floor' => 'Ground Floor'],
            ['name' => 'Science Lab', 'building' => 'Science Block', 'floor' => 'Ground Floor'],
            ['name' => 'Library', 'building' => 'Main Building', 'floor' => 'Ground Floor'],
            ['name' => 'Sports Store', 'building' => 'Sports Complex', 'floor' => 'Ground Floor'],
            ['name' => 'Kitchen', 'building' => 'Dining Hall', 'floor' => 'Ground Floor'],
            ['name' => 'Assembly Hall', 'building' => 'Main Building', 'floor' => 'Ground Floor'],
            ['name' => 'Music Room', 'building' => 'Arts Block', 'floor' => 'First Floor'],
        ];

        foreach ($locations as $data) {
            AssetLocation::firstOrCreate(['name' => $data['name']], $data);
        }
    }

    protected function seedPermissions()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'assets-view', 'assets-create', 'assets-edit', 'assets-delete', 'assets-assign',
            'assets-dispose', 'assets-maintenance', 'assets-depreciation', 'assets-reports',
            'asset-categories-manage', 'asset-locations-manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $rolePermissions = [
            'Admin' => $permissions,
            'Bursar' => ['assets-view', 'assets-create', 'assets-edit', 'assets-depreciation', 'assets-reports', 'asset-categories-manage', 'asset-locations-manage'],
            'Storekeeper' => ['assets-view', 'assets-create', 'assets-edit', 'assets-assign', 'assets-maintenance', 'asset-locations-manage'],
            'Teacher' => ['assets-view'],
            'Auditor' => ['assets-view', 'assets-reports'],
        ];

        foreach ($rolePermissions as $roleName => $perms) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($perms);
        }
    }
}
