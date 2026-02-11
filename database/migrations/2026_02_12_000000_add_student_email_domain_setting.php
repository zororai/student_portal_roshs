<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddStudentEmailDomainSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add student email domain setting
        DB::table('website_settings')->insert([
            'key' => 'student_email_domain',
            'value' => 'roshs.co.zw',
            'type' => 'text',
            'group' => 'general',
            'label' => 'Student Email Domain',
            'description' => 'Domain used for auto-generated student emails (e.g., roshs.co.zw generates student@roshs.co.zw)',
            'order' => 10,
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
        DB::table('website_settings')->where('key', 'student_email_domain')->delete();
    }
}
