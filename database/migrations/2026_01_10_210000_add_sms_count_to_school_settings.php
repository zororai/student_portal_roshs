<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSmsCountToSchoolSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Insert SMS count tracking setting
        DB::table('school_settings')->insert([
            [
                'setting_key' => 'sms_sent_count',
                'setting_value' => '0',
                'setting_type' => 'integer',
                'description' => 'Total number of SMS messages sent through the system',
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
        DB::table('school_settings')->where('setting_key', 'sms_sent_count')->delete();
    }
}
