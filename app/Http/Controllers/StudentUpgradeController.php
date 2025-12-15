<?php

namespace App\Http\Controllers;

use App\Student;
use App\Grade;
use App\AuditTrail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentUpgradeController extends Controller
{
    /**
     * Show the student upgrade page.
     */
    public function index()
    {
        $classes = Grade::withCount('students')
            ->orderBy('class_numeric', 'asc')
            ->get();

        $currentYear = date('Y');
        $newYear = $currentYear + 1;

        // Get statistics
        $totalStudents = Student::where('is_transferred', false)->count();
        $classesCount = Grade::count();

        return view('backend.admin.student-upgrade.index', compact('classes', 'currentYear', 'newYear', 'totalStudents', 'classesCount'));
    }

    /**
     * Preview the upgrade before executing.
     */
    public function preview(Request $request)
    {
        $classes = Grade::with(['students' => function($q) {
            $q->where('is_transferred', false);
        }, 'students.user', 'teacher.user'])
            ->withCount(['students' => function($q) {
                $q->where('is_transferred', false);
            }])
            ->orderBy('class_numeric', 'asc')
            ->get();

        $upgradeMap = [];

        foreach ($classes as $class) {
            // Find the next class (by class_numeric + 1)
            $nextClass = Grade::where('class_numeric', $class->class_numeric + 1)->first();
            
            $upgradeMap[] = [
                'current_class' => $class,
                'next_class' => $nextClass,
                'students_count' => $class->students_count,
                'can_upgrade' => $nextClass !== null,
                'is_final_class' => $nextClass === null,
            ];
        }

        return response()->json([
            'success' => true,
            'upgrade_map' => $upgradeMap,
            'total_students' => Student::where('is_transferred', false)->count(),
        ]);
    }

    /**
     * Execute the student upgrade.
     */
    public function execute(Request $request)
    {
        $request->validate([
            'confirm' => 'required|accepted',
        ]);

        try {
            DB::beginTransaction();

            $classes = Grade::orderBy('class_numeric', 'desc')->get();
            $upgradedCount = 0;
            $graduatedCount = 0;
            $upgradeDetails = [];

            foreach ($classes as $class) {
                $nextClass = Grade::where('class_numeric', $class->class_numeric + 1)->first();
                
                $studentsToUpgrade = Student::where('class_id', $class->id)
                    ->where('is_transferred', false)
                    ->get();

                if ($studentsToUpgrade->isEmpty()) {
                    continue;
                }

                if ($nextClass) {
                    // Upgrade students to next class
                    foreach ($studentsToUpgrade as $student) {
                        $oldClassId = $student->class_id;
                        $student->class_id = $nextClass->id;
                        $student->save();
                        $upgradedCount++;
                    }

                    $upgradeDetails[] = [
                        'from' => $class->class_name,
                        'to' => $nextClass->class_name,
                        'count' => $studentsToUpgrade->count(),
                    ];
                } else {
                    // Final class - mark students as graduated/transferred
                    foreach ($studentsToUpgrade as $student) {
                        $student->is_transferred = true;
                        $student->save();
                        $graduatedCount++;
                    }

                    $upgradeDetails[] = [
                        'from' => $class->class_name,
                        'to' => 'Graduated',
                        'count' => $studentsToUpgrade->count(),
                    ];
                }
            }

            DB::commit();

            // Log the upgrade action
            AuditTrail::create([
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name,
                'user_role' => auth()->user()->roles->first()->name ?? 'Admin',
                'action' => 'update',
                'description' => "Upgraded $upgradedCount students to next class level. $graduatedCount students graduated.",
                'old_values' => json_encode(['action' => 'student_class_upgrade']),
                'new_values' => json_encode($upgradeDetails),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully upgraded $upgradedCount students. $graduatedCount students have graduated.",
                'upgraded_count' => $upgradedCount,
                'graduated_count' => $graduatedCount,
                'details' => $upgradeDetails,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to upgrade students: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get upgrade history.
     */
    public function history()
    {
        $history = AuditTrail::where('description', 'like', '%Upgraded%students to next class level%')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'history' => $history,
        ]);
    }
}
