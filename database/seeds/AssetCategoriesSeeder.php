<?php

use Illuminate\Database\Seeder;
use App\AssetCategory;
use App\AssetLocation;

class AssetCategoriesSeeder extends Seeder
{
    public function run()
    {
        // Asset Categories
        $categories = [
            [
                'name' => 'Furniture',
                'code' => 'FUR001',
                'description' => 'Desks, chairs, tables, cabinets, and other furniture items',
                'useful_life_years' => 10,
                'depreciation_method' => 'straight_line',
            ],
            [
                'name' => 'ICT Equipment',
                'code' => 'ICT001',
                'description' => 'Computers, printers, projectors, and other ICT equipment',
                'useful_life_years' => 5,
                'depreciation_method' => 'straight_line',
            ],
            [
                'name' => 'Vehicles',
                'code' => 'VEH001',
                'description' => 'School buses, cars, and other vehicles',
                'useful_life_years' => 8,
                'depreciation_method' => 'reducing_balance',
            ],
            [
                'name' => 'Laboratory Equipment',
                'code' => 'LAB001',
                'description' => 'Science lab equipment, microscopes, and other lab items',
                'useful_life_years' => 7,
                'depreciation_method' => 'straight_line',
            ],
            [
                'name' => 'Sports Equipment',
                'code' => 'SPO001',
                'description' => 'Sports gear, gym equipment, and recreational items',
                'useful_life_years' => 5,
                'depreciation_method' => 'straight_line',
            ],
            [
                'name' => 'Musical Instruments',
                'code' => 'MUS001',
                'description' => 'Pianos, guitars, drums, and other musical instruments',
                'useful_life_years' => 10,
                'depreciation_method' => 'straight_line',
            ],
            [
                'name' => 'Kitchen Equipment',
                'code' => 'KIT001',
                'description' => 'Stoves, refrigerators, and other kitchen appliances',
                'useful_life_years' => 8,
                'depreciation_method' => 'straight_line',
            ],
            [
                'name' => 'Office Equipment',
                'code' => 'OFF001',
                'description' => 'Photocopiers, fax machines, and other office equipment',
                'useful_life_years' => 5,
                'depreciation_method' => 'straight_line',
            ],
            [
                'name' => 'Building Fixtures',
                'code' => 'BLD001',
                'description' => 'Air conditioners, water heaters, and building fixtures',
                'useful_life_years' => 15,
                'depreciation_method' => 'straight_line',
            ],
            [
                'name' => 'Library Resources',
                'code' => 'LIB001',
                'description' => 'Library furniture, shelving, and equipment (not books)',
                'useful_life_years' => 10,
                'depreciation_method' => 'straight_line',
            ],
        ];

        foreach ($categories as $category) {
            AssetCategory::updateOrCreate(
                ['code' => $category['code']],
                $category
            );
        }

        // Asset Locations
        $locations = [
            [
                'name' => 'Main Office',
                'building' => 'Administration Block',
                'floor' => 'Ground Floor',
                'description' => 'School main administration office',
            ],
            [
                'name' => 'Principal Office',
                'building' => 'Administration Block',
                'floor' => 'Ground Floor',
                'description' => 'Principal\'s office',
            ],
            [
                'name' => 'Staff Room',
                'building' => 'Administration Block',
                'floor' => 'First Floor',
                'description' => 'Teachers\' staff room',
            ],
            [
                'name' => 'Computer Lab',
                'building' => 'Science Block',
                'floor' => 'Ground Floor',
                'description' => 'Main computer laboratory',
            ],
            [
                'name' => 'Science Laboratory',
                'building' => 'Science Block',
                'floor' => 'Ground Floor',
                'description' => 'Science practical laboratory',
            ],
            [
                'name' => 'Library',
                'building' => 'Library Block',
                'floor' => 'Ground Floor',
                'description' => 'School library',
            ],
            [
                'name' => 'Sports Store',
                'building' => 'Sports Complex',
                'floor' => 'Ground Floor',
                'description' => 'Sports equipment storage',
            ],
            [
                'name' => 'Kitchen',
                'building' => 'Dining Hall',
                'floor' => 'Ground Floor',
                'description' => 'School kitchen',
            ],
            [
                'name' => 'Dining Hall',
                'building' => 'Dining Hall',
                'floor' => 'Ground Floor',
                'description' => 'Student dining area',
            ],
            [
                'name' => 'Assembly Hall',
                'building' => 'Main Hall',
                'floor' => 'Ground Floor',
                'description' => 'School assembly and events hall',
            ],
            [
                'name' => 'Classroom Block A',
                'building' => 'Classroom Block A',
                'floor' => null,
                'description' => 'Primary classrooms',
            ],
            [
                'name' => 'Classroom Block B',
                'building' => 'Classroom Block B',
                'floor' => null,
                'description' => 'Secondary classrooms',
            ],
            [
                'name' => 'Bursar Office',
                'building' => 'Administration Block',
                'floor' => 'Ground Floor',
                'description' => 'Bursar\'s office and accounts',
            ],
            [
                'name' => 'Store Room',
                'building' => 'Administration Block',
                'floor' => 'Ground Floor',
                'description' => 'General storage room',
            ],
            [
                'name' => 'Vehicle Garage',
                'building' => 'Garage',
                'floor' => null,
                'description' => 'School vehicle parking and storage',
            ],
        ];

        foreach ($locations as $location) {
            AssetLocation::updateOrCreate(
                ['name' => $location['name'], 'building' => $location['building']],
                $location
            );
        }

        $this->command->info('Asset categories and locations seeded successfully!');
    }
}
