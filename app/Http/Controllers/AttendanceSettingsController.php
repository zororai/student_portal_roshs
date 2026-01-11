<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SchoolSetting;

class AttendanceSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $settings = [
            'attendance_check_in_time' => SchoolSetting::get('attendance_check_in_time', '07:30'),
            'attendance_check_out_time' => SchoolSetting::get('attendance_check_out_time', '16:30'),
            'attendance_work_hours' => SchoolSetting::get('attendance_work_hours', '9'),
            'attendance_late_grace_minutes' => SchoolSetting::get('attendance_late_grace_minutes', '0'),
        ];

        return view('backend.admin.settings.attendance-settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'check_in_time' => 'required|date_format:H:i',
            'check_out_time' => 'required|date_format:H:i',
            'late_grace_minutes' => 'required|integer|min:0|max:60',
        ]);

        SchoolSetting::set('attendance_check_in_time', $request->check_in_time, 'time', 'Standard teacher check-in time');
        SchoolSetting::set('attendance_check_out_time', $request->check_out_time, 'time', 'Standard teacher check-out time');
        SchoolSetting::set('attendance_late_grace_minutes', $request->late_grace_minutes, 'number', 'Grace period in minutes before marking as late');

        // Calculate work hours
        $checkIn = \Carbon\Carbon::parse($request->check_in_time);
        $checkOut = \Carbon\Carbon::parse($request->check_out_time);
        $workHours = $checkIn->diffInHours($checkOut);
        SchoolSetting::set('attendance_work_hours', $workHours, 'number', 'Expected work hours per day');

        return redirect()->back()->with('success', 'Attendance settings updated successfully.');
    }
}
