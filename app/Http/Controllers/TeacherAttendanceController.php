<?php

namespace App\Http\Controllers;

use App\Teacher;
use App\TeacherLog;
use App\SchoolGeolocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherAttendanceController extends Controller
{
    /**
     * Display the attendance marking page for teachers.
     */
    public function index()
    {
        $user = Auth::user();
        $teacher = $user->teacher;
        
        if (!$teacher) {
            return redirect()->back()->with('error', 'Teacher profile not found.');
        }

        $today = today();
        $todayLog = TeacherLog::where('teacher_id', $teacher->id)
            ->where('log_date', $today)
            ->first();

        // Get active school geolocation boundary
        $schoolBoundary = SchoolGeolocation::where('is_active', true)->first();

        // Get this month's attendance history
        $monthLogs = TeacherLog::where('teacher_id', $teacher->id)
            ->whereMonth('log_date', now()->month)
            ->whereYear('log_date', now()->year)
            ->orderBy('log_date', 'desc')
            ->get();

        // Calculate statistics
        $totalDays = $monthLogs->count();
        $presentDays = $monthLogs->where('status', 'present')->count();
        $absentDays = $monthLogs->where('status', 'absent')->count();

        return view('backend.teacher.attendance', compact(
            'teacher',
            'todayLog',
            'schoolBoundary',
            'monthLogs',
            'totalDays',
            'presentDays',
            'absentDays'
        ));
    }

    /**
     * Mark attendance (check in/out) with location verification.
     */
    public function markAttendance(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'action' => 'required|in:check_in,check_out,absent',
            'location_skipped' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $teacher = $user->teacher;

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Teacher profile not found.',
            ], 404);
        }

        $lat = $request->latitude;
        $lng = $request->longitude;
        $action = $request->action;
        $locationSkipped = $request->boolean('location_skipped', false);
        $today = today();
        $now = now();

        // Check if within school boundary (skip if location was not provided)
        $schoolBoundary = SchoolGeolocation::where('is_active', true)->first();
        $withinBoundary = false;

        if ($schoolBoundary && !$locationSkipped) {
            $withinBoundary = $schoolBoundary->containsPoint($lat, $lng);
        }

        // Find or create today's log
        $log = TeacherLog::firstOrNew([
            'teacher_id' => $teacher->id,
            'log_date' => $today,
        ]);

        if ($action === 'absent') {
            // Mark as absent
            $log->status = 'absent';
            $log->notes = $request->notes ?? 'Marked absent by teacher';
            $log->save();

            return response()->json([
                'success' => true,
                'message' => 'You have been marked as absent for today.',
                'status' => 'absent',
            ]);
        }

        if ($action === 'check_in') {
            if ($log->time_in) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already checked in today at ' . \Carbon\Carbon::parse($log->time_in)->format('H:i'),
                ], 400);
            }

            // Only enforce boundary check if location was not skipped
            if (!$withinBoundary && $schoolBoundary && !$locationSkipped) {
                return response()->json([
                    'success' => false,
                    'message' => 'You must be within the school boundary to check in. Please move closer to the school.',
                    'within_boundary' => false,
                ], 400);
            }

            $log->time_in = $now->format('H:i:s');
            $log->check_in_lat = $locationSkipped ? null : $lat;
            $log->check_in_lng = $locationSkipped ? null : $lng;
            $log->status = 'present';
            $log->within_boundary = $locationSkipped ? null : $withinBoundary;
            $log->notes = $locationSkipped ? 'Location verification skipped' : $log->notes;
            $log->save();

            $message = $locationSkipped 
                ? 'Welcome! You have checked in at ' . $now->format('H:i') . ' (without location verification)'
                : 'Welcome! You have checked in at ' . $now->format('H:i');

            return response()->json([
                'success' => true,
                'message' => $message,
                'time_in' => $now->format('H:i'),
                'within_boundary' => $withinBoundary,
                'location_skipped' => $locationSkipped,
                'status' => 'checked_in',
            ]);
        }

        if ($action === 'check_out') {
            if (!$log->time_in) {
                return response()->json([
                    'success' => false,
                    'message' => 'You need to check in first before checking out.',
                ], 400);
            }

            if ($log->time_out) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already checked out today at ' . \Carbon\Carbon::parse($log->time_out)->format('H:i'),
                ], 400);
            }

            $log->time_out = $now->format('H:i:s');
            $log->check_out_lat = $lat;
            $log->check_out_lng = $lng;
            $log->save();

            $timeIn = \Carbon\Carbon::parse($log->time_in);
            $duration = $now->diffForHumans($timeIn, true);

            return response()->json([
                'success' => true,
                'message' => 'Goodbye! You have checked out at ' . $now->format('H:i') . '. Duration: ' . $duration,
                'time_out' => $now->format('H:i'),
                'duration' => $duration,
                'status' => 'checked_out',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid action.',
        ], 400);
    }

    /**
     * Get the school boundary for the map.
     */
    public function getSchoolBoundary()
    {
        $boundary = SchoolGeolocation::where('is_active', true)->first();

        if (!$boundary) {
            return response()->json([
                'success' => false,
                'message' => 'No school boundary has been set up.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'boundary' => $boundary,
        ]);
    }
}
