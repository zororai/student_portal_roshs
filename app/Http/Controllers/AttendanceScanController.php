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
     * Get session mode (single or dual).
     */
    public static function getSessionMode()
    {
        return \App\SchoolSetting::get('attendance_session_mode', 'single');
    }

    /**
     * Get standard check-in time from settings.
     * For dual mode, can specify 'morning' or 'afternoon' session.
     */
    public static function getStandardCheckIn($session = 'morning')
    {
        if ($session === 'afternoon') {
            return \App\SchoolSetting::get('attendance_afternoon_check_in_time', '12:30') . ':00';
        }
        return \App\SchoolSetting::get('attendance_check_in_time', '07:30') . ':00';
    }

    /**
     * Get standard check-out time from settings.
     * For dual mode, can specify 'morning' or 'afternoon' session.
     */
    public static function getStandardCheckOut($session = 'morning')
    {
        if ($session === 'afternoon') {
            return \App\SchoolSetting::get('attendance_afternoon_check_out_time', '17:30') . ':00';
        }
        return \App\SchoolSetting::get('attendance_check_out_time', '16:30') . ':00';
    }

    /**
     * Get late grace period in minutes from settings.
     */
    public static function getLateGraceMinutes()
    {
        return (int) \App\SchoolSetting::get('attendance_late_grace_minutes', '0');
    }

    /**
     * Detect which session a time belongs to (morning or afternoon).
     * Returns 'morning' or 'afternoon' based on the check-in time.
     */
    public static function detectSession($checkInTime)
    {
        if (self::getSessionMode() === 'single') {
            return 'morning'; // Single mode always uses morning times
        }

        $time = \Carbon\Carbon::parse($checkInTime);
        $morningEnd = \Carbon\Carbon::parse(self::getStandardCheckOut('morning'));
        
        // If check-in is after morning session ends, it's afternoon
        if ($time->gte($morningEnd)) {
            return 'afternoon';
        }
        
        return 'morning';
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

        // Get session mode and grace period
        $sessionMode = self::getSessionMode();
        $graceMinutes = self::getLateGraceMinutes();
        
        // Get standard times (for display - morning/single session)
        $standardCheckIn = self::getStandardCheckIn();
        $standardCheckOut = self::getStandardCheckOut();

        // Calculate late/overtime for each attendance record
        foreach ($attendances as $attendance) {
            $attendance->is_late = false;
            $attendance->late_minutes = 0;
            $attendance->has_overtime = false;
            $attendance->overtime_minutes = 0;
            $attendance->worked_minutes = 0;
            $attendance->session_type = 'morning';

            if ($attendance->check_in_time) {
                // Detect which session this attendance belongs to
                $attendance->session_type = self::detectSession($attendance->check_in_time);
                
                $checkIn = \Carbon\Carbon::parse($attendance->check_in_time);
                $expectedIn = \Carbon\Carbon::parse(self::getStandardCheckIn($attendance->session_type))->addMinutes($graceMinutes);
                
                if ($checkIn->gt($expectedIn)) {
                    $attendance->is_late = true;
                    $attendance->late_minutes = $checkIn->diffInMinutes($expectedIn);
                }
            }

            if ($attendance->check_in_time && $attendance->check_out_time) {
                $checkIn = \Carbon\Carbon::parse($attendance->check_in_time);
                $checkOut = \Carbon\Carbon::parse($attendance->check_out_time);
                $expectedOut = \Carbon\Carbon::parse(self::getStandardCheckOut($attendance->session_type));
                
                $attendance->worked_minutes = $checkIn->diffInMinutes($checkOut);
                
                // Overtime if checked out after expected time
                if ($checkOut->gt($expectedOut)) {
                    $attendance->has_overtime = true;
                    $attendance->overtime_minutes = $checkOut->diffInMinutes($expectedOut);
                }
            }
        }

        // Statistics
        $totalRecords = $attendances->count();
        $onTimeCount = $attendances->filter(fn($a) => !$a->is_late && $a->check_in_time)->count();
        $lateCount = $attendances->filter(fn($a) => $a->is_late)->count();
        $totalWorkedMinutes = $attendances->sum('worked_minutes');
        $totalOvertimeMinutes = $attendances->sum('overtime_minutes');
        $totalLateMinutes = $attendances->sum('late_minutes');

        // Calculate per-teacher summaries (weekly, monthly, termly)
        $teacherSummaries = $this->calculateTeacherSummaries($year, $month);
        
        // Get afternoon session times for dual mode display
        $afternoonCheckIn = self::getStandardCheckIn('afternoon');
        $afternoonCheckOut = self::getStandardCheckOut('afternoon');

        return view('backend.attendance.history', compact(
            'attendances', 'teachers', 'month', 'year', 'teacherId',
            'totalRecords', 'onTimeCount', 'lateCount',
            'totalWorkedMinutes', 'totalOvertimeMinutes', 'totalLateMinutes',
            'standardCheckIn', 'standardCheckOut', 'teacherSummaries',
            'sessionMode', 'afternoonCheckIn', 'afternoonCheckOut'
        ));
    }

    /**
     * Calculate weekly, monthly, and termly summaries per teacher.
     */
    private function calculateTeacherSummaries($year, $month)
    {
        $teachers = Teacher::with('user')->get();
        $summaries = [];

        // Get standard times from settings
        $standardCheckIn = self::getStandardCheckIn();
        $standardCheckOut = self::getStandardCheckOut();

        // Get current week start/end
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();

        // Get term dates (assuming 3 terms: Jan-Apr, May-Aug, Sep-Dec)
        $currentMonth = now()->month;
        if ($currentMonth >= 1 && $currentMonth <= 4) {
            $termStart = now()->setMonth(1)->startOfMonth();
            $termEnd = now()->setMonth(4)->endOfMonth();
        } elseif ($currentMonth >= 5 && $currentMonth <= 8) {
            $termStart = now()->setMonth(5)->startOfMonth();
            $termEnd = now()->setMonth(8)->endOfMonth();
        } else {
            $termStart = now()->setMonth(9)->startOfMonth();
            $termEnd = now()->setMonth(12)->endOfMonth();
        }

        foreach ($teachers as $teacher) {
            // Weekly totals
            $weeklyAttendances = TeacherAttendance::where('teacher_id', $teacher->id)
                ->whereBetween('date', [$weekStart, $weekEnd])
                ->get();

            $weeklyMinutes = 0;
            $weeklyLate = 0;
            $weeklyOvertime = 0;
            foreach ($weeklyAttendances as $att) {
                if ($att->check_in_time && $att->check_out_time) {
                    $weeklyMinutes += \Carbon\Carbon::parse($att->check_in_time)->diffInMinutes(\Carbon\Carbon::parse($att->check_out_time));
                }
                if ($att->check_in_time && $att->check_in_time > $standardCheckIn) {
                    $weeklyLate += \Carbon\Carbon::parse($att->check_in_time)->diffInMinutes(\Carbon\Carbon::parse($standardCheckIn));
                }
                if ($att->check_out_time && $att->check_out_time > $standardCheckOut) {
                    $weeklyOvertime += \Carbon\Carbon::parse($att->check_out_time)->diffInMinutes(\Carbon\Carbon::parse($standardCheckOut));
                }
            }

            // Monthly totals
            $monthlyAttendances = TeacherAttendance::where('teacher_id', $teacher->id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->get();

            $monthlyMinutes = 0;
            $monthlyLate = 0;
            $monthlyOvertime = 0;
            foreach ($monthlyAttendances as $att) {
                if ($att->check_in_time && $att->check_out_time) {
                    $monthlyMinutes += \Carbon\Carbon::parse($att->check_in_time)->diffInMinutes(\Carbon\Carbon::parse($att->check_out_time));
                }
                if ($att->check_in_time && $att->check_in_time > $standardCheckIn) {
                    $monthlyLate += \Carbon\Carbon::parse($att->check_in_time)->diffInMinutes(\Carbon\Carbon::parse($standardCheckIn));
                }
                if ($att->check_out_time && $att->check_out_time > $standardCheckOut) {
                    $monthlyOvertime += \Carbon\Carbon::parse($att->check_out_time)->diffInMinutes(\Carbon\Carbon::parse($standardCheckOut));
                }
            }

            // Termly totals
            $termlyAttendances = TeacherAttendance::where('teacher_id', $teacher->id)
                ->whereBetween('date', [$termStart, $termEnd])
                ->get();

            $termlyMinutes = 0;
            $termlyLate = 0;
            $termlyOvertime = 0;
            foreach ($termlyAttendances as $att) {
                if ($att->check_in_time && $att->check_out_time) {
                    $termlyMinutes += \Carbon\Carbon::parse($att->check_in_time)->diffInMinutes(\Carbon\Carbon::parse($att->check_out_time));
                }
                if ($att->check_in_time && $att->check_in_time > $standardCheckIn) {
                    $termlyLate += \Carbon\Carbon::parse($att->check_in_time)->diffInMinutes(\Carbon\Carbon::parse($standardCheckIn));
                }
                if ($att->check_out_time && $att->check_out_time > $standardCheckOut) {
                    $termlyOvertime += \Carbon\Carbon::parse($att->check_out_time)->diffInMinutes(\Carbon\Carbon::parse($standardCheckOut));
                }
            }

            $summaries[$teacher->id] = [
                'teacher' => $teacher,
                'weekly' => [
                    'total_minutes' => $weeklyMinutes,
                    'late_minutes' => $weeklyLate,
                    'overtime_minutes' => $weeklyOvertime,
                    'days' => $weeklyAttendances->count(),
                ],
                'monthly' => [
                    'total_minutes' => $monthlyMinutes,
                    'late_minutes' => $monthlyLate,
                    'overtime_minutes' => $monthlyOvertime,
                    'days' => $monthlyAttendances->count(),
                ],
                'termly' => [
                    'total_minutes' => $termlyMinutes,
                    'late_minutes' => $termlyLate,
                    'overtime_minutes' => $termlyOvertime,
                    'days' => $termlyAttendances->count(),
                ],
            ];
        }

        return $summaries;
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
