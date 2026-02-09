<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class SidebarLedgerPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create the sidebar-ledger permission if it doesn't exist
        Permission::firstOrCreate(
            ['name' => 'sidebar-ledger'],
            ['guard_name' => 'web']
        );
        
        $this->command->info('sidebar-ledger permission created successfully!');
    }
}
