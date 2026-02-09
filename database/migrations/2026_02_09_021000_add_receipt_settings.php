<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddReceiptSettings extends Migration
{
    public function up()
    {
        $settings = [
            [
                'setting_key' => 'receipt_school_short_name',
                'setting_value' => 'ROSHS',
                'setting_type' => 'text',
                'description' => 'Short name displayed on receipts (e.g., ROSHS)'
            ],
            [
                'setting_key' => 'receipt_school_full_name',
                'setting_value' => 'Rose Of Sharon High School',
                'setting_type' => 'text',
                'description' => 'Full school name displayed on receipts'
            ],
            [
                'setting_key' => 'receipt_footer_message',
                'setting_value' => 'Thank You!',
                'setting_type' => 'text',
                'description' => 'Thank you message on receipt footer'
            ],
            [
                'setting_key' => 'receipt_footer_note',
                'setting_value' => 'This is a computer-generated receipt.',
                'setting_type' => 'text',
                'description' => 'Note displayed at bottom of receipt'
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('school_settings')->updateOrInsert(
                ['setting_key' => $setting['setting_key']],
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }
    }

    public function down()
    {
        $keys = [
            'receipt_school_short_name',
            'receipt_school_full_name',
            'receipt_footer_message',
            'receipt_footer_note'
        ];

        DB::table('school_settings')->whereIn('setting_key', $keys)->delete();
    }
}
