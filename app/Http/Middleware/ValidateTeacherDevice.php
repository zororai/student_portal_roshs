<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\TeacherDevice;

class ValidateTeacherDevice
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // Only apply to teachers
        if (!$user || !$user->hasRole('Teacher')) {
            return $next($request);
        }

        $teacher = $user->teacher;

        if (!$teacher) {
            return $next($request);
        }

        // If device registration is not required, allow access
        if ($teacher->device_registration_status === 'not_required') {
            return $next($request);
        }

        // Get device ID from request (sent via JavaScript fingerprint)
        $deviceId = $request->cookie('device_fingerprint') ?? $request->header('X-Device-Fingerprint');

        // If teacher needs to register a device (pending status)
        if ($teacher->device_registration_status === 'pending') {
            // Allow access but registration will be prompted on the page
            return $next($request);
        }

        // If teacher has registered status, validate device
        if ($teacher->device_registration_status === 'registered') {
            if (!$deviceId) {
                return response()->view('errors.device-not-authorized', [
                    'message' => 'Device fingerprint not detected. Please enable cookies and JavaScript.',
                    'teacher' => $teacher
                ], 403);
            }

            $isValidDevice = TeacherDevice::validateDevice($teacher->id, $deviceId);

            if (!$isValidDevice) {
                return response()->view('errors.device-not-authorized', [
                    'message' => 'This device is not authorized for attendance. Please contact the administrator.',
                    'teacher' => $teacher
                ], 403);
            }
        }

        return $next($request);
    }
}
