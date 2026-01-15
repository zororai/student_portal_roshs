<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Timetable;
use App\TimetableSetting;
use App\Student;
use App\Teacher;
use App\Parents;
use App\Grade;
use Carbon\Carbon;

class TimetableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function studentView()
    {
        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->first();
        
        if (!$student) {
            $error = 'Student record not found. Please contact administrator.';
            return view('backend.timetable.view', ['error' => $error, 'class' => null, 'settings' => null, 'timetable' => [], 'days' => []]);
        }

        $classId = $student->class_id;
        
        if (!$classId) {
            $error = 'You have not been assigned to a class yet. Please contact administrator.';
            return view('backend.timetable.view', ['error' => $error, 'class' => null, 'settings' => null, 'timetable' => [], 'days' => []]);
        }
        
        $class = Grade::find($classId);
        
        if (!$class) {
            $error = 'Class not found. Please contact administrator.';
            return view('backend.timetable.view', ['error' => $error, 'class' => null, 'settings' => null, 'timetable' => [], 'days' => []]);
        }

        $settings = TimetableSetting::where('class_id', $classId)
            ->orderBy('academic_year', 'desc')
            ->orderBy('term', 'desc')
            ->first();
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $timetable = [];
        
        if ($settings) {
            foreach ($days as $day) {
                $timetable[$day] = Timetable::where('class_id', $classId)
                    ->where('academic_year', $settings->academic_year)
                    ->where('term', $settings->term)
                    ->where('day', $day)
                    ->with(['subject', 'teacher.user'])
                    ->orderBy('slot_order')
                    ->get();
            }
        }

        return view('backend.timetable.view', compact('class', 'settings', 'timetable', 'days'));
    }

    public function teacherView()
    {
        $user = auth()->user();
        $teacher = Teacher::where('user_id', $user->id)->first();
        
        if (!$teacher) {
            return redirect()->back()->with('error', 'Teacher record not found.');
        }

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        
        // Get all timetable entries where this teacher is assigned
        $teacherSlots = Timetable::where('teacher_id', $teacher->id)
            ->where('slot_type', 'subject')
            ->with(['subject', 'grade'])
            ->orderBy('start_time')
            ->get();
        
        if ($teacherSlots->isEmpty()) {
            return view('backend.timetable.teacher', [
                'teacher' => $teacher,
                'timetable' => [],
                'days' => $days,
                'hasSchedule' => false
            ]);
        }

        // Group by day
        $timetable = [];
        foreach ($days as $day) {
            $timetable[$day] = $teacherSlots->where('day', $day)->sortBy('start_time')->values();
        }

        return view('backend.timetable.teacher', [
            'teacher' => $teacher,
            'timetable' => $timetable,
            'days' => $days,
            'hasSchedule' => true
        ]);
    }

    public function parentView()
    {
        $user = auth()->user();
        $parent = Parents::where('user_id', $user->id)->first();
        
        if (!$parent) {
            return redirect()->back()->with('error', 'Parent record not found.');
        }

        // Get children of this parent
        $children = Student::where('parent_id', $parent->id)->get();
        
        if ($children->isEmpty()) {
            return view('backend.timetable.parent', [
                'class' => null,
                'children' => $children,
                'settings' => null,
                'timetable' => [],
                'days' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'selectedChild' => null
            ]);
        }

        // Get selected child or first child
        $selectedChildId = request('child_id') ?? $children->first()->id;
        $selectedChild = Student::find($selectedChildId);
        
        if (!$selectedChild) {
            return redirect()->back()->with('error', 'Student not found.');
        }

        $classId = $selectedChild->class_id;
        $class = Grade::find($classId);
        
        $settings = TimetableSetting::where('class_id', $classId)
            ->orderBy('academic_year', 'desc')
            ->orderBy('term', 'desc')
            ->first();
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $timetable = [];
        
        if ($settings) {
            foreach ($days as $day) {
                $timetable[$day] = Timetable::where('class_id', $classId)
                    ->where('academic_year', $settings->academic_year)
                    ->where('term', $settings->term)
                    ->where('day', $day)
                    ->with(['subject', 'teacher.user'])
                    ->orderBy('slot_order')
                    ->get();
            }
        }

        return view('backend.timetable.parent', compact('class', 'children', 'settings', 'timetable', 'days', 'selectedChild'));
    }
}
