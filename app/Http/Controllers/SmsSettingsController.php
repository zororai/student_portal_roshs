<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SchoolSetting;

class SmsSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display SMS settings form.
     */
    public function index()
    {
        $countryCode = SchoolSetting::get('sms_country_code', '+263');
        $teacherCredentialsTemplate = SchoolSetting::get(
            'sms_teacher_credentials_template', 
            'RSH School: Teacher account created. Login: {phone}, Password: {password}. Complete profile on first login.'
        );
        $studentParentRegistrationTemplate = SchoolSetting::get(
            'sms_student_parent_registration_template',
            'RSH School: {student_name} registered. Complete parent registration: {url}'
        );
        $parentPasswordResetTemplate = SchoolSetting::get(
            'sms_parent_password_reset_template',
            "ROSHS Password Reset\nEmail: {email}\nTemp Password: {password}\nPlease login and change your password immediately."
        );
        $teacherPasswordResetTemplate = SchoolSetting::get(
            'sms_teacher_password_reset_template',
            "ROSHS Password Reset\nEmail: {email}\nTemp Password: {password}\nPlease login and change your password immediately."
        );
        $adminUserCredentialsTemplate = SchoolSetting::get(
            'sms_admin_user_credentials_template',
            'RSH School: Account created. Login: {email}, Password: {password}. Please change your password on first login.'
        );
        $smsSentCount = (int) SchoolSetting::get('sms_sent_count', 0);

        return view('backend.admin.settings.sms-settings', compact(
            'countryCode',
            'teacherCredentialsTemplate',
            'studentParentRegistrationTemplate',
            'parentPasswordResetTemplate',
            'teacherPasswordResetTemplate',
            'adminUserCredentialsTemplate',
            'smsSentCount'
        ));
    }

    /**
     * Reset SMS sent count.
     */
    public function resetCount()
    {
        SchoolSetting::set('sms_sent_count', 0, 'integer', 'Total number of SMS messages sent through the system');

        return redirect()->route('admin.settings.sms')
            ->with('success', 'SMS count has been reset to 0.');
    }

    /**
     * Update SMS settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'country_code' => 'required|string|max:10|regex:/^\+[0-9]{1,4}$/',
            'teacher_credentials_template' => 'required|string|max:500',
            'student_parent_registration_template' => 'required|string|max:500',
            'parent_password_reset_template' => 'required|string|max:500',
            'teacher_password_reset_template' => 'required|string|max:500',
            'admin_user_credentials_template' => 'required|string|max:500',
        ], [
            'country_code.regex' => 'Country code must start with + followed by 1-4 digits (e.g., +263)',
        ]);

        SchoolSetting::set(
            'sms_country_code',
            $request->country_code,
            'text',
            'Default country code for phone number formatting'
        );

        SchoolSetting::set(
            'sms_teacher_credentials_template',
            $request->teacher_credentials_template,
            'textarea',
            'SMS template for teacher account credentials. Placeholders: {name}, {phone}, {password}'
        );

        SchoolSetting::set(
            'sms_student_parent_registration_template',
            $request->student_parent_registration_template,
            'textarea',
            'SMS template for student/parent registration. Placeholders: {student_name}, {url}'
        );

        SchoolSetting::set(
            'sms_parent_password_reset_template',
            $request->parent_password_reset_template,
            'textarea',
            'SMS template for parent password reset. Placeholders: {name}, {email}, {password}'
        );

        SchoolSetting::set(
            'sms_teacher_password_reset_template',
            $request->teacher_password_reset_template,
            'textarea',
            'SMS template for teacher password reset. Placeholders: {name}, {email}, {password}'
        );

        SchoolSetting::set(
            'sms_admin_user_credentials_template',
            $request->admin_user_credentials_template,
            'textarea',
            'SMS template for admin user credentials. Placeholders: {name}, {email}, {password}'
        );

        return redirect()->route('admin.settings.sms')
            ->with('success', 'SMS settings updated successfully!');
    }

    /**
     * Preview SMS message with sample data.
     */
    public function preview(Request $request)
    {
        $template = $request->template;
        $sampleData = [
            '{name}' => 'John Doe',
            '{phone}' => '0771234567',
            '{password}' => '12345678',
        ];

        $preview = str_replace(
            array_keys($sampleData),
            array_values($sampleData),
            $template
        );

        return response()->json([
            'success' => true,
            'preview' => $preview,
            'character_count' => strlen($preview),
        ]);
    }
}
