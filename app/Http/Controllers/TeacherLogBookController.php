<?php

namespace App\Http\Controllers;

use App\Teacher;
use App\TeacherLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TeacherLogBookController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return redirect()->back()->with('error', 'Teacher profile not found.');
        }

        $month = $request->get('month', now()->format('Y-m'));
        $startDate = Carbon::parse($month)->startOfMonth();
        $endDate = Carbon::parse($month)->endOfMonth();

        $logs = TeacherLog::where('teacher_id', $teacher->id)
            ->whereBetween('log_date', [$startDate, $endDate])
            ->orderBy('log_date', 'desc')
            ->get();

        // Calculate statistics
        $totalDays = $logs->count();
        $presentDays = $logs->where('status', 'present')->count();
        $lateDays = $logs->where('status', 'late')->count();
        $absentDays = $logs->where('status', 'absent')->count();

        // Today's log
        $todayLog = TeacherLog::where('teacher_id', $teacher->id)
            ->where('log_date', today())
            ->first();

        return view('backend.teacher.logbook', compact(
            'teacher',
            'logs',
            'totalDays',
            'presentDays',
            'lateDays',
            'absentDays',
            'todayLog',
            'month'
        ));
    }

    public function scan(Request $request)
    {
        $user = auth()->user();
        $teacher = Teacher::where('user_id', $user->id)->first();

        if (!$teacher) {
            return response()->json(['success' => false, 'message' => 'Teacher not found'], 404);
        }

        $today = today();
        $now = now();

        $log = TeacherLog::firstOrNew([
            'teacher_id' => $teacher->id,
            'log_date' => $today,
        ]);

        if (!$log->exists) {
            $log->time_in = $now->format('H:i:s');
            $log->status = 'present';
            $log->save();

            return response()->json([
                'success' => true,
                'message' => 'Clocked in at ' . $now->format('H:i'),
                'action' => 'clock_in'
            ]);
        } elseif (!$log->time_out) {
            $log->time_out = $now->format('H:i:s');
            $log->save();

            return response()->json([
                'success' => true,
                'message' => 'Clocked out at ' . $now->format('H:i'),
                'action' => 'clock_out'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Already clocked in and out for today.'
        ]);
    }
}
