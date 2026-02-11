<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

class AddMissingSidebarPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $permissions = [
            'sidebar-notifications',
            'sidebar-website-dashboard',
            'sidebar-website-general',
            'sidebar-website-colors',
            'sidebar-website-images',
            'sidebar-website-text',
            'sidebar-website-pages',
            'sidebar-website-homepage',
            'sidebar-website-banners',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web']
            );
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $permissions = [
            'sidebar-notifications',
            'sidebar-website-dashboard',
            'sidebar-website-general',
            'sidebar-website-colors',
            'sidebar-website-images',
            'sidebar-website-text',
            'sidebar-website-pages',
            'sidebar-website-homepage',
            'sidebar-website-banners',
        ];

        foreach ($permissions as $permission) {
            Permission::where('name', $permission)->where('guard_name', 'web')->delete();
        }
    }
}
