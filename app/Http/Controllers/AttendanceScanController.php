<?php

namespace App\Http\Controllers;

use App\Teacher;
use App\TeacherAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceScanController extends Controller
{
    /**
     * Display the attendance scanner page (Admin only).
     */
    public function index(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $selectedDate = \Carbon\Carbon::parse($date);

        // Get all teachers with their attendance for the selected date
        $teachers = Teacher::with(['user', 'attendances' => function($query) use ($selectedDate) {
            $query->whereDate('date', $selectedDate);
        }])->get();

        // Calculate statistics
        $totalTeachers = $teachers->count();
        $presentTeachers = 0;
        $absentTeachers = 0;
        $currentlyIn = 0;

        foreach ($teachers as $teacher) {
            $attendance = $teacher->attendances->first();
            if ($attendance) {
                if ($attendance->check_in_time && !$attendance->check_out_time) {
                    $currentlyIn++;
                    $presentTeachers++;
                } elseif ($attendance->check_in_time && $attendance->check_out_time) {
                    $presentTeachers++;
                }
            } else {
                $absentTeachers++;
            }
        }

        // Get attendance records for the selected date
        $attendances = TeacherAttendance::with('teacher.user')
            ->whereDate('date', $selectedDate)
            ->orderBy('check_in_time', 'desc')
            ->get();

        return view('backend.attendance.scanner', compact(
            'teachers',
            'attendances',
            'totalTeachers',
            'presentTeachers',
            'absentTeachers',
            'currentlyIn',
            'selectedDate'
        ));
    }

    /**
     * Handle QR code scan for attendance.
     */
    public function scan(Request $request)
    {
        $request->validate([
            'qr_code_token' => 'required|string',
        ]);

        $token = $request->qr_code_token;

        // Find teacher by QR code token
        $teacher = Teacher::where('qr_code_token', $token)->with('user')->first();

        if (!$teacher) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid QR Code',
                'type' => 'error',
            ]);
        }

        $today = today();
        $now = now();

        // Find today's attendance record
        $attendance = TeacherAttendance::where('teacher_id', $teacher->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance) {
            // First scan - Check In
            TeacherAttendance::create([
                'teacher_id' => $teacher->id,
                'date' => $today,
                'check_in_time' => $now,
                'status' => 'IN',
                'device_id' => $request->header('User-Agent'),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Checked In - Welcome, {$teacher->user->name}!",
                'action' => 'check_in',
                'time' => $now->format('H:i'),
                'type' => 'success',
                'teacher' => [
                    'name' => $teacher->user->name,
                    'profile_picture' => $teacher->user->profile_picture,
                ],
            ]);
        }

        if ($attendance->check_out_time === null) {
            // Second scan - Check Out
            $attendance->update([
                'check_out_time' => $now,
                'status' => 'OUT',
            ]);

            $checkIn = \Carbon\Carbon::parse($attendance->check_in_time);
            $duration = $now->diffForHumans($checkIn, true);

            return response()->json([
                'success' => true,
                'message' => "Checked Out - Goodbye, {$teacher->user->name}! Duration: {$duration}",
                'action' => 'check_out',
                'time' => $now->format('H:i'),
                'duration' => $duration,
                'type' => 'info',
                'teacher' => [
                    'name' => $teacher->user->name,
                    'profile_picture' => $teacher->user->profile_picture,
                ],
            ]);
        }

        // Already checked out
        return response()->json([
            'success' => false,
            'message' => "Already Checked Out - {$teacher->user->name} has completed attendance for today.",
            'type' => 'warning',
            'teacher' => [
                'name' => $teacher->user->name,
                'check_in' => \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i'),
                'check_out' => \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i'),
            ],
        ]);
    }

    /**
     * Generate QR code for a teacher.
     */
    public function generateQrCode($teacherId)
    {
        $teacher = Teacher::with('user')->findOrFail($teacherId);

        // Generate new unique token
        $qrToken = Str::uuid()->toString();

        // Generate QR code as SVG (no imagick required)
        $qrImage = QrCode::format('svg')
            ->size(300)
            ->margin(2)
            ->generate($qrToken);

        $fileName = 'teacher_qr_' . $teacher->id . '.svg';
        $filePath = 'qrcodes/teachers/' . $fileName;

        Storage::disk('public')->put($filePath, $qrImage);

        $teacher->update([
            'qr_code_token' => $qrToken,
            'qr_code' => $filePath,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'QR code generated successfully!',
            'qr_code_url' => asset('storage/' . $filePath),
            'qr_code_token' => $qrToken,
        ]);
    }

    /**
     * Regenerate QR code for a teacher.
     */
    public function regenerateQrCode($teacherId)
    {
        return $this->generateQrCode($teacherId);
    }

    /**
     * Get QR code for a teacher.
     */
    public function getQrCode($teacherId)
    {
        $teacher = Teacher::with('user')->findOrFail($teacherId);

        if (!$teacher->qr_code || !Storage::disk('public')->exists($teacher->qr_code)) {
            // Generate QR code if it doesn't exist
            $qrToken = $teacher->qr_code_token ?: Str::uuid()->toString();

            $qrImage = QrCode::format('svg')
                ->size(300)
                ->margin(2)
                ->generate($qrToken);

            $fileName = 'teacher_qr_' . $teacher->id . '.svg';
            $filePath = 'qrcodes/teachers/' . $fileName;

            Storage::disk('public')->put($filePath, $qrImage);

            $teacher->update([
                'qr_code_token' => $qrToken,
                'qr_code' => $filePath,
            ]);
        }

        return response()->json([
            'success' => true,
            'teacher' => [
                'id' => $teacher->id,
                'name' => $teacher->user->name,
            ],
            'qr_code_url' => asset('storage/' . $teacher->qr_code),
            'qr_code_token' => $teacher->qr_code_token,
        ]);
    }

    /**
     * Print QR code for a teacher.
     */
    public function printQrCode($teacherId)
    {
        $teacher = Teacher::with('user')->findOrFail($teacherId);

        // Ensure QR code exists
        if (!$teacher->qr_code || !Storage::disk('public')->exists($teacher->qr_code)) {
            $qrToken = $teacher->qr_code_token ?: Str::uuid()->toString();

            $qrImage = QrCode::format('svg')
                ->size(300)
                ->margin(2)
                ->generate($qrToken);

            $fileName = 'teacher_qr_' . $teacher->id . '.svg';
            $filePath = 'qrcodes/teachers/' . $fileName;

            Storage::disk('public')->put($filePath, $qrImage);

            $teacher->update([
                'qr_code_token' => $qrToken,
                'qr_code' => $filePath,
            ]);
        }

        return view('backend.attendance.print-qr', compact('teacher'));
    }

    /**
     * Get attendance history for a specific teacher.
     */
    public function teacherHistory($teacherId, Request $request)
    {
        $teacher = Teacher::with('user')->findOrFail($teacherId);

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $attendances = TeacherAttendance::where('teacher_id', $teacherId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'desc')
            ->get();

        $totalDays = $attendances->count();
        $presentDays = $attendances->whereNotNull('check_in_time')->count();

        return response()->json([
            'success' => true,
            'teacher' => [
                'id' => $teacher->id,
                'name' => $teacher->user->name,
            ],
            'attendances' => $attendances,
            'statistics' => [
                'total_days' => $totalDays,
                'present_days' => $presentDays,
            ],
        ]);
    }

    /**
     * Get current availability status of all teachers.
     */
    public function availability()
    {
        $today = today();

        $teachers = Teacher::with(['user', 'attendances' => function($query) use ($today) {
            $query->whereDate('date', $today);
        }])->get();

        $available = [];
        $unavailable = [];

        foreach ($teachers as $teacher) {
            $attendance = $teacher->attendances->first();
            $teacherData = [
                'id' => $teacher->id,
                'name' => $teacher->user->name,
                'profile_picture' => $teacher->user->profile_picture,
            ];

            if ($attendance && $attendance->check_in_time && !$attendance->check_out_time) {
                $teacherData['check_in'] = \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i');
                $available[] = $teacherData;
            } else {
                if ($attendance) {
                    $teacherData['check_in'] = \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i');
                    $teacherData['check_out'] = \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i');
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

    /**
     * Display teacher attendance history for admin.
     */
    public function attendanceHistory(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $teacherId = $request->get('teacher_id');

        $query = TeacherAttendance::with('teacher.user')
            ->whereMonth('date', $month)
            ->whereYear('date', $year);

        if ($teacherId) {
            $query->where('teacher_id', $teacherId);
        }

        $attendances = $query->orderBy('date', 'desc')->orderBy('check_in_time', 'desc')->get();

        $teachers = Teacher::with('user')->orderBy('id')->get();

        // Statistics
        $totalRecords = $attendances->count();
        $onTimeCount = $attendances->filter(fn($a) => $a->check_in_time && $a->check_in_time <= '08:00:00')->count();
        $lateCount = $attendances->filter(fn($a) => $a->check_in_time && $a->check_in_time > '08:00:00')->count();

        return view('backend.attendance.history', compact(
            'attendances', 'teachers', 'month', 'year', 'teacherId',
            'totalRecords', 'onTimeCount', 'lateCount'
        ));
    }

    /**
     * Update attendance record (check-in/out times).
     */
    public function updateAttendance(Request $request, $id)
    {
        $request->validate([
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
        ]);

        $attendance = TeacherAttendance::findOrFail($id);

        $attendance->update([
            'check_in_time' => $request->check_in_time ? $request->check_in_time . ':00' : null,
            'check_out_time' => $request->check_out_time ? $request->check_out_time . ':00' : null,
            'status' => $request->check_out_time ? 'OUT' : ($request->check_in_time ? 'IN' : null),
        ]);

        return redirect()->back()->with('success', 'Attendance updated successfully.');
    }

    /**
     * Manually add attendance record.
     */
    public function storeAttendance(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
            'date' => 'required|date',
            'check_in_time' => 'nullable|date_format:H:i',
            'check_out_time' => 'nullable|date_format:H:i',
        ]);

        // Check if record already exists
        $existing = TeacherAttendance::where('teacher_id', $request->teacher_id)
            ->whereDate('date', $request->date)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'Attendance record already exists for this teacher on this date.');
        }

        TeacherAttendance::create([
            'teacher_id' => $request->teacher_id,
            'date' => $request->date,
            'check_in_time' => $request->check_in_time ? $request->check_in_time . ':00' : null,
            'check_out_time' => $request->check_out_time ? $request->check_out_time . ':00' : null,
            'status' => $request->check_out_time ? 'OUT' : ($request->check_in_time ? 'IN' : null),
        ]);

        return redirect()->back()->with('success', 'Attendance record added successfully.');
    }

    /**
     * Delete attendance record.
     */
    public function deleteAttendance($id)
    {
        $attendance = TeacherAttendance::findOrFail($id);
        $attendance->delete();

        return redirect()->back()->with('success', 'Attendance record deleted successfully.');
    }
}
