<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Timetable;
use App\TimetableSetting;
use App\Grade;
use App\Subject;
use App\Teacher;
use Carbon\Carbon;

class AdminTimetableController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $classes = Grade::with(['subjects', 'teacher'])->orderBy('class_numeric')->get();
        return view('backend.admin.timetable.index', compact('classes'));
    }

    public function create()
    {
        $classes = Grade::orderBy('class_numeric')->get();
        $subjects = Subject::orderBy('name')->get();
        $teachers = Teacher::with('user')->get();
        
        return view('backend.admin.timetable.create', compact('classes', 'subjects', 'teachers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_id' => 'required|exists:grades,id',
            'start_time' => 'required|date_format:H:i',
            'break_start' => 'required|date_format:H:i',
            'break_end' => 'required|date_format:H:i',
            'lunch_start' => 'required|date_format:H:i',
            'lunch_end' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'subject_duration' => 'required|integer|min:20|max:120',
        ]);

        // Save or update timetable settings
        $settings = TimetableSetting::updateOrCreate(
            ['class_id' => $validated['class_id']],
            $validated
        );

        // Generate timetable
        $this->generateTimetable($settings);

        return redirect()->route('admin.timetable.show', $validated['class_id'])
            ->with('success', 'Timetable generated successfully!');
    }

    public function show($classId)
    {
        $class = Grade::with(['subjects', 'teacher'])->findOrFail($classId);
        $settings = TimetableSetting::where('class_id', $classId)->first();
        
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $timetable = [];
        
        foreach ($days as $day) {
            $timetable[$day] = Timetable::where('class_id', $classId)
                ->where('day', $day)
                ->with(['subject', 'teacher.user'])
                ->orderBy('slot_order')
                ->get();
        }

        return view('backend.admin.timetable.show', compact('class', 'settings', 'timetable', 'days'));
    }

    public function edit($classId)
    {
        $class = Grade::findOrFail($classId);
        $settings = TimetableSetting::where('class_id', $classId)->first();
        $subjects = Subject::orderBy('name')->get();
        $teachers = Teacher::with('user')->get();
        
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $timetable = [];
        
        foreach ($days as $day) {
            $timetable[$day] = Timetable::where('class_id', $classId)
                ->where('day', $day)
                ->orderBy('slot_order')
                ->get();
        }

        return view('backend.admin.timetable.edit', compact('class', 'settings', 'timetable', 'days', 'subjects', 'teachers'));
    }

    public function update(Request $request, $classId)
    {
        $validated = $request->validate([
            'slots' => 'required|array',
            'slots.*.id' => 'required|exists:timetables,id',
            'slots.*.subject_id' => 'nullable|exists:subjects,id',
            'slots.*.teacher_id' => 'nullable|exists:teachers,id',
        ]);

        $conflicts = [];

        foreach ($validated['slots'] as $slotData) {
            $slot = Timetable::find($slotData['id']);
            
            // Check for teacher conflicts
            if ($slotData['teacher_id']) {
                $hasConflict = Timetable::checkTeacherConflict(
                    $slotData['teacher_id'],
                    $slot->day,
                    $slot->start_time,
                    $slot->end_time,
                    $slot->id
                );
                
                if ($hasConflict) {
                    $teacher = Teacher::with('user')->find($slotData['teacher_id']);
                    $conflicts[] = "{$teacher->user->name} has a conflict on {$slot->day} at {$slot->start_time}";
                    continue;
                }
            }

            $slot->update([
                'subject_id' => $slotData['subject_id'],
                'teacher_id' => $slotData['teacher_id'],
            ]);
        }

        if (!empty($conflicts)) {
            return redirect()->back()->with('warning', 'Some slots had conflicts: ' . implode(', ', $conflicts));
        }

        return redirect()->route('admin.timetable.show', $classId)
            ->with('success', 'Timetable updated successfully!');
    }

    public function destroy($classId)
    {
        Timetable::where('class_id', $classId)->delete();
        TimetableSetting::where('class_id', $classId)->delete();

        return redirect()->route('admin.timetable.index')
            ->with('success', 'Timetable deleted successfully!');
    }

    private function generateTimetable(TimetableSetting $settings)
    {
        // Delete existing timetable for this class
        Timetable::where('class_id', $settings->class_id)->delete();

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $class = Grade::with('subjects')->find($settings->class_id);
        $subjects = $class->subjects->toArray();
        
        foreach ($days as $day) {
            $currentTime = Carbon::parse($settings->start_time);
            $endTime = Carbon::parse($settings->end_time);
            $breakStart = Carbon::parse($settings->break_start);
            $breakEnd = Carbon::parse($settings->break_end);
            $lunchStart = Carbon::parse($settings->lunch_start);
            $lunchEnd = Carbon::parse($settings->lunch_end);
            
            $slotOrder = 0;
            $subjectIndex = 0;

            while ($currentTime < $endTime) {
                $slotEndTime = (clone $currentTime)->addMinutes($settings->subject_duration);
                
                // Check if it's break time
                if ($currentTime >= $breakStart && $currentTime < $breakEnd) {
                    Timetable::create([
                        'class_id' => $settings->class_id,
                        'day' => $day,
                        'start_time' => $breakStart->format('H:i:s'),
                        'end_time' => $breakEnd->format('H:i:s'),
                        'slot_type' => 'break',
                        'slot_order' => $slotOrder++,
                    ]);
                    $currentTime = clone $breakEnd;
                    continue;
                }

                // Check if it's lunch time
                if ($currentTime >= $lunchStart && $currentTime < $lunchEnd) {
                    Timetable::create([
                        'class_id' => $settings->class_id,
                        'day' => $day,
                        'start_time' => $lunchStart->format('H:i:s'),
                        'end_time' => $lunchEnd->format('H:i:s'),
                        'slot_type' => 'lunch',
                        'slot_order' => $slotOrder++,
                    ]);
                    $currentTime = clone $lunchEnd;
                    continue;
                }

                // Check if slot would overlap with break
                if ($currentTime < $breakStart && $slotEndTime > $breakStart) {
                    $slotEndTime = clone $breakStart;
                }

                // Check if slot would overlap with lunch
                if ($currentTime < $lunchStart && $slotEndTime > $lunchStart) {
                    $slotEndTime = clone $lunchStart;
                }

                // Don't exceed end time
                if ($slotEndTime > $endTime) {
                    $slotEndTime = clone $endTime;
                }

                // Create subject slot
                if ($currentTime < $slotEndTime) {
                    $subjectId = null;
                    if (!empty($subjects) && isset($subjects[$subjectIndex % count($subjects)])) {
                        $subjectId = $subjects[$subjectIndex % count($subjects)]['id'];
                        $subjectIndex++;
                    }

                    Timetable::create([
                        'class_id' => $settings->class_id,
                        'subject_id' => $subjectId,
                        'day' => $day,
                        'start_time' => $currentTime->format('H:i:s'),
                        'end_time' => $slotEndTime->format('H:i:s'),
                        'slot_type' => 'subject',
                        'slot_order' => $slotOrder++,
                    ]);
                }

                $currentTime = $slotEndTime;
            }
        }
    }

    public function checkConflicts(Request $request)
    {
        $teacherId = $request->teacher_id;
        $day = $request->day;
        $startTime = $request->start_time;
        $endTime = $request->end_time;
        $excludeId = $request->exclude_id;

        $hasConflict = Timetable::checkTeacherConflict($teacherId, $day, $startTime, $endTime, $excludeId);

        return response()->json(['has_conflict' => $hasConflict]);
    }
}
