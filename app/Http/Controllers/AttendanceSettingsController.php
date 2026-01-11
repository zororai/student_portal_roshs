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
            // Session mode: 'single' or 'dual'
            'attendance_session_mode' => SchoolSetting::get('attendance_session_mode', 'single'),
            
            // Single session / Morning session times
            'attendance_check_in_time' => SchoolSetting::get('attendance_check_in_time', '07:30'),
            'attendance_check_out_time' => SchoolSetting::get('attendance_check_out_time', '16:30'),
            'attendance_work_hours' => SchoolSetting::get('attendance_work_hours', '9'),
            
            // Afternoon session times
            'attendance_afternoon_check_in_time' => SchoolSetting::get('attendance_afternoon_check_in_time', '12:30'),
            'attendance_afternoon_check_out_time' => SchoolSetting::get('attendance_afternoon_check_out_time', '17:30'),
            'attendance_afternoon_work_hours' => SchoolSetting::get('attendance_afternoon_work_hours', '5'),
            
            // Grace period (applies to both sessions)
            'attendance_late_grace_minutes' => SchoolSetting::get('attendance_late_grace_minutes', '0'),
        ];

        return view('backend.admin.settings.attendance-settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $sessionMode = $request->session_mode ?? 'single';
        
        $rules = [
            'session_mode' => 'required|in:single,dual',
            'check_in_time' => 'required|date_format:H:i',
            'check_out_time' => 'required|date_format:H:i',
            'late_grace_minutes' => 'required|integer|min:0|max:60',
        ];
        
        // Add afternoon session validation if dual mode
        if ($sessionMode === 'dual') {
            $rules['afternoon_check_in_time'] = 'required|date_format:H:i';
            $rules['afternoon_check_out_time'] = 'required|date_format:H:i';
        }
        
        $request->validate($rules);

        // Save session mode
        SchoolSetting::set('attendance_session_mode', $sessionMode, 'text', 'Attendance session mode (single or dual)');
        
        // Save morning/single session times
        SchoolSetting::set('attendance_check_in_time', $request->check_in_time, 'time', 'Morning session check-in time');
        SchoolSetting::set('attendance_check_out_time', $request->check_out_time, 'time', 'Morning session check-out time');
        SchoolSetting::set('attendance_late_grace_minutes', $request->late_grace_minutes, 'number', 'Grace period in minutes before marking as late');

        // Calculate morning work hours
        $checkIn = \Carbon\Carbon::parse($request->check_in_time);
        $checkOut = \Carbon\Carbon::parse($request->check_out_time);
        $workHours = $checkIn->diffInHours($checkOut);
        SchoolSetting::set('attendance_work_hours', $workHours, 'number', 'Morning session work hours');
        
        // Save afternoon session times if dual mode
        if ($sessionMode === 'dual') {
            SchoolSetting::set('attendance_afternoon_check_in_time', $request->afternoon_check_in_time, 'time', 'Afternoon session check-in time');
            SchoolSetting::set('attendance_afternoon_check_out_time', $request->afternoon_check_out_time, 'time', 'Afternoon session check-out time');
            
            // Calculate afternoon work hours
            $afternoonIn = \Carbon\Carbon::parse($request->afternoon_check_in_time);
            $afternoonOut = \Carbon\Carbon::parse($request->afternoon_check_out_time);
            $afternoonHours = $afternoonIn->diffInHours($afternoonOut);
            SchoolSetting::set('attendance_afternoon_work_hours', $afternoonHours, 'number', 'Afternoon session work hours');
        }

        return redirect()->back()->with('success', 'Attendance settings updated successfully.');
    }
}
