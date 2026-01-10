<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSmsSettingsToSchoolSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Insert default SMS settings
        DB::table('school_settings')->insert([
            [
                'setting_key' => 'sms_country_code',
                'setting_value' => '+263',
                'setting_type' => 'text',
                'description' => 'Default country code for phone number formatting',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'setting_key' => 'sms_teacher_credentials_template',
                'setting_value' => 'RSH School: Teacher account created. Login: {phone}, Password: {password}. Complete profile on first login.',
                'setting_type' => 'textarea',
                'description' => 'SMS template for teacher account credentials. Available placeholders: {name}, {phone}, {password}',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('school_settings')->whereIn('setting_key', [
            'sms_country_code',
            'sms_teacher_credentials_template',
        ])->delete();
    }
}
