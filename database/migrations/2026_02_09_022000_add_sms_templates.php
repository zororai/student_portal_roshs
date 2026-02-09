<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddSmsTemplates extends Migration
{
    public function up()
    {
        $settings = [
            [
                'setting_key' => 'sms_student_parent_registration_template',
                'setting_value' => 'RSH School: {student_name} registered. Complete parent registration: {url}',
                'setting_type' => 'textarea',
                'description' => 'SMS template for student/parent registration. Placeholders: {student_name}, {url}'
            ],
            [
                'setting_key' => 'sms_parent_password_reset_template',
                'setting_value' => "ROSHS Password Reset\nEmail: {email}\nTemp Password: {password}\nPlease login and change your password immediately.",
                'setting_type' => 'textarea',
                'description' => 'SMS template for parent password reset. Placeholders: {name}, {email}, {password}'
            ],
            [
                'setting_key' => 'sms_teacher_password_reset_template',
                'setting_value' => "ROSHS Password Reset\nEmail: {email}\nTemp Password: {password}\nPlease login and change your password immediately.",
                'setting_type' => 'textarea',
                'description' => 'SMS template for teacher password reset. Placeholders: {name}, {email}, {password}'
            ],
            [
                'setting_key' => 'sms_admin_user_credentials_template',
                'setting_value' => 'RSH School: Account created. Login: {email}, Password: {password}. Please change your password on first login.',
                'setting_type' => 'textarea',
                'description' => 'SMS template for admin user credentials. Placeholders: {name}, {email}, {password}'
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
            'sms_student_parent_registration_template',
            'sms_parent_password_reset_template',
            'sms_teacher_password_reset_template',
            'sms_admin_user_credentials_template'
        ];

        DB::table('school_settings')->whereIn('setting_key', $keys)->delete();
    }
}
