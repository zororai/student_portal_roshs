<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddSchoolNameToWebsiteSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add school_name setting for the header/navbar
        DB::table('website_settings')->insert([
            'key' => 'school_name',
            'value' => 'Rose of sharon student portal',
            'type' => 'text',
            'group' => 'general',
            'label' => 'School Name (Header)',
            'description' => 'The school name displayed in the header/navbar',
            'order' => 0,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('website_settings')->where('key', 'school_name')->delete();
    }
}
