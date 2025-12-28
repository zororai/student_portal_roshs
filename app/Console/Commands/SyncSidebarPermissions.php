<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class SyncSidebarPermissions extends Command
{
    protected $signature = 'permissions:sync-sidebar';
    protected $description = 'Scan sidebar for @can directives and create missing permissions';

    public function handle()
    {
        $sidebarPath = resource_path('views/layouts/sidebar.blade.php');
        
        if (!file_exists($sidebarPath)) {
            $this->error('Sidebar file not found!');
            return 1;
        }

        $content = file_get_contents($sidebarPath);
        
        // Find all @can('permission-name') directives
        preg_match_all("/@can\s*\(\s*['\"]([^'\"]+)['\"]\s*\)/", $content, $matches);
        
        $permissions = array_unique($matches[1]);
        $created = 0;
        $existing = 0;

        foreach ($permissions as $permissionName) {
            if (!Permission::where('name', $permissionName)->exists()) {
                Permission::create(['name' => $permissionName, 'guard_name' => 'web']);
                $this->info("Created permission: {$permissionName}");
                $created++;
            } else {
                $existing++;
            }
        }

        $this->info("Sync complete: {$created} created, {$existing} already existed");
        $this->info("Total sidebar permissions: " . count($permissions));
        
        return 0;
    }
}
