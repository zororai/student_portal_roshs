<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Teacher;
use App\TeacherDevice;
use App\User;

class TeacherDeviceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-teacher-device-info', ['only' => ['index', 'show']]);
        $this->middleware('permission:manage-teacher-devices', ['only' => ['enableRegistration', 'revokeDevice']]);
        $this->middleware('permission:allow-teacher-phone-change', ['only' => ['allowPhoneChange', 'resetDevice']]);
    }

    /**
     * Display a listing of teachers with their device status.
     */
    public function index()
    {
        $teachers = Teacher::with(['user', 'devices' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])->get();

        return view('backend.teacher_devices.index', compact('teachers'));
    }

    /**
     * Show device details for a specific teacher.
     */
    public function show($id)
    {
        $teacher = Teacher::with(['user', 'devices' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])->findOrFail($id);

        return view('backend.teacher_devices.show', compact('teacher'));
    }

    /**
     * Enable device registration requirement for a teacher.
     */
    public function enableRegistration($id)
    {
        $teacher = Teacher::findOrFail($id);
        
        $teacher->update([
            'device_registration_status' => 'pending'
        ]);

        // Log the action
        \Log::info('Teacher device registration enabled', [
            'teacher_id' => $teacher->id,
            'teacher_name' => $teacher->user->name,
            'admin_id' => auth()->id(),
            'admin_name' => auth()->user()->name,
        ]);

        return redirect()->back()->with('success', 'Device registration enabled for ' . $teacher->user->name . '. They will be prompted to register their device on next login.');
    }

    /**
     * Allow teacher to change/re-register their phone.
     */
    public function allowPhoneChange($id)
    {
        $teacher = Teacher::findOrFail($id);

        // Revoke all active devices
        TeacherDevice::where('teacher_id', $teacher->id)
            ->where('status', 'active')
            ->update([
                'status' => 'revoked',
                'revoked_at' => now(),
                'revoked_by' => auth()->id(),
                'revoke_reason' => 'Admin allowed phone change'
            ]);

        // Set teacher to pending registration
        $teacher->update([
            'device_registration_status' => 'pending'
        ]);

        // Log the action
        \Log::info('Teacher phone change allowed', [
            'teacher_id' => $teacher->id,
            'teacher_name' => $teacher->user->name,
            'admin_id' => auth()->id(),
            'admin_name' => auth()->user()->name,
        ]);

        return redirect()->back()->with('success', 'Phone change allowed for ' . $teacher->user->name . '. Their old device has been revoked and they can register a new device.');
    }

    /**
     * Revoke a specific device.
     */
    public function revokeDevice(Request $request, $deviceId)
    {
        $device = TeacherDevice::findOrFail($deviceId);
        $teacher = $device->teacher;

        $device->update([
            'status' => 'revoked',
            'revoked_at' => now(),
            'revoked_by' => auth()->id(),
            'revoke_reason' => $request->input('reason', 'Revoked by admin')
        ]);

        // Log the action
        \Log::info('Teacher device revoked', [
            'device_id' => $device->id,
            'teacher_id' => $teacher->id,
            'teacher_name' => $teacher->user->name,
            'admin_id' => auth()->id(),
            'admin_name' => auth()->user()->name,
            'reason' => $request->input('reason', 'Revoked by admin'),
        ]);

        return redirect()->back()->with('success', 'Device revoked successfully.');
    }

    /**
     * Reset device status (remove requirement).
     */
    public function resetDevice($id)
    {
        $teacher = Teacher::findOrFail($id);

        // Revoke all devices
        TeacherDevice::where('teacher_id', $teacher->id)
            ->update([
                'status' => 'revoked',
                'revoked_at' => now(),
                'revoked_by' => auth()->id(),
                'revoke_reason' => 'Device requirement reset by admin'
            ]);

        // Set to not required
        $teacher->update([
            'device_registration_status' => 'not_required'
        ]);

        // Log the action
        \Log::info('Teacher device requirement reset', [
            'teacher_id' => $teacher->id,
            'teacher_name' => $teacher->user->name,
            'admin_id' => auth()->id(),
            'admin_name' => auth()->user()->name,
        ]);

        return redirect()->back()->with('success', 'Device requirement removed for ' . $teacher->user->name . '.');
    }

    /**
     * Bulk enable device registration for all teachers.
     */
    public function bulkEnableRegistration()
    {
        Teacher::where('device_registration_status', 'not_required')
            ->update(['device_registration_status' => 'pending']);

        \Log::info('Bulk device registration enabled', [
            'admin_id' => auth()->id(),
            'admin_name' => auth()->user()->name,
        ]);

        return redirect()->back()->with('success', 'Device registration enabled for all teachers.');
    }

    /**
     * Register device for teacher (called via AJAX from teacher attendance page).
     */
    public function registerDevice(Request $request)
    {
        $request->validate([
            'device_id' => 'required|string',
            'device_name' => 'nullable|string|max:255',
            'browser' => 'nullable|string|max:255',
        ]);

        $user = auth()->user();
        
        if (!$user->hasRole('Teacher') || !$user->teacher) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $teacher = $user->teacher;

        // Check if device registration is pending
        if ($teacher->device_registration_status !== 'pending') {
            return response()->json(['error' => 'Device registration not required or already completed'], 400);
        }

        // Check if this device is already registered to another teacher
        $existingDevice = TeacherDevice::where('device_id', $request->device_id)
            ->where('status', 'active')
            ->where('teacher_id', '!=', $teacher->id)
            ->first();

        if ($existingDevice) {
            return response()->json(['error' => 'This device is already registered to another teacher'], 400);
        }

        // Create new device registration
        $device = TeacherDevice::create([
            'teacher_id' => $teacher->id,
            'device_id' => $request->device_id,
            'device_name' => $request->device_name,
            'browser' => $request->browser,
            'ip_address' => $request->ip(),
            'status' => 'active',
            'registered_at' => now(),
        ]);

        // Update teacher status
        $teacher->update([
            'device_registration_status' => 'registered'
        ]);

        // Log the action
        \Log::info('Teacher device registered', [
            'device_id' => $device->id,
            'teacher_id' => $teacher->id,
            'teacher_name' => $user->name,
            'fingerprint' => $request->device_id,
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Device registered successfully'
        ]);
    }

    /**
     * Get device status for current teacher (AJAX).
     */
    public function getDeviceStatus()
    {
        $user = auth()->user();
        
        if (!$user->hasRole('Teacher') || !$user->teacher) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $teacher = $user->teacher;
        $activeDevice = $teacher->activeDevice();

        return response()->json([
            'registration_status' => $teacher->device_registration_status,
            'has_active_device' => $activeDevice !== null,
            'device_info' => $activeDevice ? [
                'device_name' => $activeDevice->device_name,
                'registered_at' => $activeDevice->registered_at->format('Y-m-d H:i:s'),
            ] : null
        ]);
    }
}
