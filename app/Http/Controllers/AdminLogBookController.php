<?php

namespace App\Http\Controllers;

use App\Teacher;
use App\TeacherLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AdminLogBookController extends Controller
{
    /**
     * Display the log book with attendance statistics.
     */
    public function index(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $selectedDate = \Carbon\Carbon::parse($date);

        // Get all teachers with their today's log
        $teachers = Teacher::with(['user', 'logs' => function($query) use ($selectedDate) {
            $query->where('log_date', $selectedDate);
        }])->get();

        // Calculate statistics
        $totalTeachers = $teachers->count();
        $presentTeachers = 0;
        $absentTeachers = 0;
        $clockedInNow = 0;

        foreach ($teachers as $teacher) {
            $log = $teacher->logs->first();
            if ($log) {
                if ($log->time_in && !$log->time_out) {
                    $clockedInNow++;
                    $presentTeachers++;
                } elseif ($log->time_in && $log->time_out) {
                    $presentTeachers++;
                }
            } else {
                $absentTeachers++;
            }
        }

        // Get recent logs for the selected date
        $logs = TeacherLog::with('teacher.user')
            ->where('log_date', $selectedDate)
            ->orderBy('time_in', 'desc')
            ->get();

        return view('backend.logbook.index', compact(
            'teachers',
            'logs',
            'totalTeachers',
            'presentTeachers',
            'absentTeachers',
            'clockedInNow',
            'selectedDate'
        ));
    }

    /**
     * Handle QR code scan for time in/out.
     */
    public function scan(Request $request)
    {
        $request->validate([
            'qr_token' => 'required|string',
        ]);

        $token = $request->qr_token;

        // Find teacher by QR code token
        $teacher = Teacher::where('qr_code_token', $token)->with('user')->first();

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR code. Teacher not found.',
            ], 404);
        }

        $today = today();
        $now = now();

        // Find or create today's log entry
        $log = TeacherLog::firstOrNew([
            'teacher_id' => $teacher->id,
            'log_date' => $today,
        ]);

        if (!$log->exists) {
            // First scan - clock in
            $log->time_in = $now->format('H:i:s');
            $log->status = 'present';
            $log->save();

            return response()->json([
                'success' => true,
                'action' => 'time_in',
                'message' => "Welcome, {$teacher->user->name}! Clocked in at {$now->format('H:i')}",
                'teacher' => [
                    'name' => $teacher->user->name,
                    'time_in' => $now->format('H:i'),
                ],
            ]);
        } elseif ($log->time_in && !$log->time_out) {
            // Second scan - clock out
            $log->time_out = $now->format('H:i:s');
            $log->save();

            $timeIn = \Carbon\Carbon::parse($log->time_in);
            $duration = $now->diffForHumans($timeIn, true);

            return response()->json([
                'success' => true,
                'action' => 'time_out',
                'message' => "Goodbye, {$teacher->user->name}! Clocked out at {$now->format('H:i')}. Duration: {$duration}",
                'teacher' => [
                    'name' => $teacher->user->name,
                    'time_in' => $timeIn->format('H:i'),
                    'time_out' => $now->format('H:i'),
                    'duration' => $duration,
                ],
            ]);
        } else {
            // Already clocked out
            return response()->json([
                'success' => false,
                'message' => "{$teacher->user->name} has already clocked in and out today.",
                'teacher' => [
                    'name' => $teacher->user->name,
                    'time_in' => \Carbon\Carbon::parse($log->time_in)->format('H:i'),
                    'time_out' => \Carbon\Carbon::parse($log->time_out)->format('H:i'),
                ],
            ], 400);
        }
    }

    /**
     * Generate QR code for a teacher.
     */
    public function generateQrCode($teacherId)
    {
        $teacher = Teacher::with('user')->findOrFail($teacherId);

        // Generate unique token if not exists
        if (!$teacher->qr_code_token) {
            $teacher->qr_code_token = Str::uuid()->toString();
        }

        // Generate QR code as SVG
        $qrCodeSvg = QrCode::format('svg')
            ->size(300)
            ->errorCorrection('H')
            ->generate($teacher->qr_code_token);

        // Save QR code image
        $filename = 'qrcodes/teacher_' . $teacher->id . '_' . time() . '.svg';
        Storage::disk('public')->put($filename, $qrCodeSvg);

        $teacher->qr_code = $filename;
        $teacher->save();

        return response()->json([
            'success' => true,
            'message' => 'QR code generated successfully!',
            'qr_code_url' => asset('storage/' . $filename),
            'qr_code_token' => $teacher->qr_code_token,
        ]);
    }

    /**
     * Get QR code for a teacher (API endpoint).
     */
    public function getQrCode($teacherId)
    {
        $teacher = Teacher::with('user')->findOrFail($teacherId);

        if (!$teacher->qr_code || !Storage::disk('public')->exists($teacher->qr_code)) {
            // Generate QR code if it doesn't exist
            if (!$teacher->qr_code_token) {
                $teacher->qr_code_token = Str::uuid()->toString();
            }

            $qrCodeSvg = QrCode::format('svg')
                ->size(300)
                ->errorCorrection('H')
                ->generate($teacher->qr_code_token);

            $filename = 'qrcodes/teacher_' . $teacher->id . '_' . time() . '.svg';
            Storage::disk('public')->put($filename, $qrCodeSvg);

            $teacher->qr_code = $filename;
            $teacher->save();
        }

        return response()->json([
            'success' => true,
            'teacher' => [
                'id' => $teacher->id,
                'name' => $teacher->user->name,
                'email' => $teacher->user->email,
            ],
            'qr_code_url' => asset('storage/' . $teacher->qr_code),
            'qr_code_token' => $teacher->qr_code_token,
        ]);
    }

    /**
     * Get attendance history for a specific teacher.
     */
    public function teacherHistory($teacherId, Request $request)
    {
        $teacher = Teacher::with('user')->findOrFail($teacherId);

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $logs = TeacherLog::where('teacher_id', $teacherId)
            ->whereMonth('log_date', $month)
            ->whereYear('log_date', $year)
            ->orderBy('log_date', 'desc')
            ->get();

        $totalDays = $logs->count();
        $presentDays = $logs->where('status', 'present')->count();
        $avgTimeIn = $logs->whereNotNull('time_in')->avg(function($log) {
            return \Carbon\Carbon::parse($log->time_in)->secondsSinceMidnight();
        });
        $avgTimeOut = $logs->whereNotNull('time_out')->avg(function($log) {
            return \Carbon\Carbon::parse($log->time_out)->secondsSinceMidnight();
        });

        return response()->json([
            'success' => true,
            'teacher' => [
                'id' => $teacher->id,
                'name' => $teacher->user->name,
            ],
            'logs' => $logs,
            'statistics' => [
                'total_days' => $totalDays,
                'present_days' => $presentDays,
                'avg_time_in' => $avgTimeIn ? gmdate('H:i', $avgTimeIn) : null,
                'avg_time_out' => $avgTimeOut ? gmdate('H:i', $avgTimeOut) : null,
            ],
        ]);
    }

    /**
     * Get current availability status of all teachers.
     */
    public function availability()
    {
        $today = today();

        $teachers = Teacher::with(['user', 'logs' => function($query) use ($today) {
            $query->where('log_date', $today);
        }])->get();

        $available = [];
        $unavailable = [];

        foreach ($teachers as $teacher) {
            $log = $teacher->logs->first();
            $teacherData = [
                'id' => $teacher->id,
                'name' => $teacher->user->name,
                'email' => $teacher->user->email,
                'profile_picture' => $teacher->user->profile_picture,
            ];

            if ($log && $log->time_in && !$log->time_out) {
                $teacherData['time_in'] = \Carbon\Carbon::parse($log->time_in)->format('H:i');
                $available[] = $teacherData;
            } else {
                if ($log) {
                    $teacherData['time_in'] = \Carbon\Carbon::parse($log->time_in)->format('H:i');
                    $teacherData['time_out'] = \Carbon\Carbon::parse($log->time_out)->format('H:i');
                    $teacherData['status'] = 'left';
                } else {
                    $teacherData['status'] = 'not_arrived';
                }
                $unavailable[] = $teacherData;
            }
        }

        return response()->json([
            'success' => true,
            'available' => $available,
            'unavailable' => $unavailable,
            'statistics' => [
                'total' => count($teachers),
                'available_count' => count($available),
                'unavailable_count' => count($unavailable),
            ],
        ]);
    }
}
