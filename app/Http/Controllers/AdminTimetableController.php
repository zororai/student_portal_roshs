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
        
        // Get current term from ResultsStatus
        $currentTerm = \App\ResultsStatus::orderBy('year', 'desc')
            ->orderBy('result_period', 'desc')
            ->first();
        
        return view('backend.admin.timetable.create', compact('classes', 'subjects', 'teachers', 'currentTerm'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'class_ids' => 'required|array|min:1',
            'class_ids.*' => 'exists:grades,id',
            'start_time' => 'required|date_format:H:i',
            'break_start' => 'required|date_format:H:i',
            'break_end' => 'required|date_format:H:i',
            'lunch_start' => 'required|date_format:H:i',
            'lunch_end' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'subject_duration' => 'required|integer|min:20|max:120',
            'academic_year' => 'required|string|max:10',
            'term' => 'required|integer|min:1|max:3',
        ]);

        $classIds = $validated['class_ids'];
        $generatedCount = 0;

        foreach ($classIds as $classId) {
            // Prepare settings data for this class
            $settingsData = $validated;
            $settingsData['class_id'] = $classId;
            unset($settingsData['class_ids']);

            // Save or update timetable settings
            $settings = TimetableSetting::updateOrCreate(
                [
                    'class_id' => $classId,
                    'academic_year' => $validated['academic_year'],
                    'term' => $validated['term']
                ],
                $settingsData
            );

            // Generate timetable
            $this->generateTimetable($settings);
            $generatedCount++;
        }

        // If only one class, redirect to show that class's timetable
        if (count($classIds) === 1) {
            return redirect()->route('admin.timetable.show', $classIds[0])
                ->with('success', 'Timetable generated successfully!');
        }

        // If multiple classes, redirect to index
        return redirect()->route('admin.timetable.index')
            ->with('success', "Timetables generated successfully for {$generatedCount} classes!");
    }

    public function show($classId)
    {
        $class = Grade::with(['subjects', 'teacher'])->findOrFail($classId);
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
                    ->with(['subject.teacher.user', 'teacher.user'])
                    ->orderBy('slot_order')
                    ->get();
            }
        }

        return view('backend.admin.timetable.show', compact('class', 'settings', 'timetable', 'days'));
    }

    public function edit($classId)
    {
        $class = Grade::with('subjects.teacher.user')->findOrFail($classId);
        $settings = TimetableSetting::where('class_id', $classId)
            ->orderBy('academic_year', 'desc')
            ->orderBy('term', 'desc')
            ->first();
        $classSubjects = $class->subjects()->with('teacher.user')->orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        $teachers = Teacher::with(['user', 'subjects'])->get();
        
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $timetable = [];
        
        if ($settings) {
            foreach ($days as $day) {
                $timetable[$day] = Timetable::where('class_id', $classId)
                    ->where('academic_year', $settings->academic_year)
                    ->where('term', $settings->term)
                    ->where('day', $day)
                    ->orderBy('slot_order')
                    ->get();
            }
        }

        return view('backend.admin.timetable.edit', compact('class', 'settings', 'timetable', 'days', 'subjects', 'teachers', 'classSubjects'));
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
        $class = Grade::with('subjects')->findOrFail($classId);
        $classSubjectIds = $class->subjects->pluck('id')->toArray();

        foreach ($validated['slots'] as $slotData) {
            $slot = Timetable::find($slotData['id']);
            
            // Check if subject is assigned to this class
            if ($slotData['subject_id'] && !in_array($slotData['subject_id'], $classSubjectIds)) {
                $subject = Subject::find($slotData['subject_id']);
                $conflicts[] = "Subject '{$subject->name}' is not assigned to this class";
                continue;
            }
            
            // Check if teacher is assigned to teach this subject
            if ($slotData['teacher_id'] && $slotData['subject_id']) {
                $subject = Subject::find($slotData['subject_id']);
                if ($subject->teacher_id != $slotData['teacher_id']) {
                    $teacher = Teacher::with('user')->find($slotData['teacher_id']);
                    $assignedTeacher = $subject->teacher ? $subject->teacher->user->name : 'None';
                    $conflicts[] = "{$teacher->user->name} does not teach {$subject->name} (assigned: {$assignedTeacher})";
                    continue;
                }
            }
            
            // Check for teacher time conflicts
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
                    $conflicts[] = "{$teacher->user->name} has a time conflict on {$slot->day} at {$slot->start_time}";
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
        // Delete existing timetable for this class, academic year, and term
        Timetable::where('class_id', $settings->class_id)
            ->where('academic_year', $settings->academic_year)
            ->where('term', $settings->term)
            ->delete();

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $class = Grade::with('subjects.teacher')->find($settings->class_id);
        $subjects = $class->subjects;
        
        // Build weighted subject pool based on periods_per_week
        // Each subject will appear in the pool according to its periods_per_week value
        $weightedSubjectPool = [];
        $subjectPeriodsRemaining = [];
        
        foreach ($subjects as $subject) {
            $periodsPerWeek = $subject->periods_per_week ?? 1;
            $subjectPeriodsRemaining[$subject->id] = $periodsPerWeek;
            
            // Add subject to pool based on periods_per_week
            for ($i = 0; $i < $periodsPerWeek; $i++) {
                $weightedSubjectPool[] = [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'teacher_id' => $subject->teacher_id,
                    'periods_per_week' => $periodsPerWeek
                ];
            }
        }
        
        // Shuffle the weighted pool to create variety
        shuffle($weightedSubjectPool);
        
        // Track how many periods each subject has been assigned per day (to distribute evenly)
        $subjectPerDay = [];
        foreach ($days as $day) {
            $subjectPerDay[$day] = [];
        }
        
        $poolIndex = 0;
        
        foreach ($days as $dayIndex => $day) {
            $currentTime = Carbon::parse($settings->start_time);
            $endTime = Carbon::parse($settings->end_time);
            $breakStart = Carbon::parse($settings->break_start);
            $breakEnd = Carbon::parse($settings->break_end);
            $lunchStart = Carbon::parse($settings->lunch_start);
            $lunchEnd = Carbon::parse($settings->lunch_end);
            
            $slotOrder = 0;

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
                        'academic_year' => $settings->academic_year,
                        'term' => $settings->term,
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
                        'academic_year' => $settings->academic_year,
                        'term' => $settings->term,
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
                    $teacherId = null;
                    
                    if (!empty($weightedSubjectPool)) {
                        // Find a subject that hasn't been used too many times today
                        // Try to distribute subjects evenly across days
                        $selectedSubject = null;
                        $attempts = 0;
                        $maxAttempts = count($weightedSubjectPool);
                        
                        while ($attempts < $maxAttempts) {
                            $candidateIndex = ($poolIndex + $attempts) % count($weightedSubjectPool);
                            $candidate = $weightedSubjectPool[$candidateIndex];
                            $candidateId = $candidate['id'];
                            
                            // Check how many times this subject appears today
                            $todayCount = $subjectPerDay[$day][$candidateId] ?? 0;
                            
                            // Calculate max periods per day for this subject
                            // Allow natural distribution: e.g., 10 periods = 2/day, 8 periods = up to 2/day
                            // Use ceiling to ensure all periods can be allocated across 5 days
                            $periodsPerWeek = $candidate['periods_per_week'];
                            $maxPerDay = max(1, ceil($periodsPerWeek / 5));
                            
                            // For subjects with many periods (6+), allow slightly more flexibility
                            // This ensures subjects like Math (10 periods) can have 2-3 periods some days
                            if ($periodsPerWeek >= 6) {
                                $maxPerDay = ceil($periodsPerWeek / 4); // More generous: allows clustering
                            }
                            if ($periodsPerWeek >= 10) {
                                $maxPerDay = ceil($periodsPerWeek / 3); // Even more generous for high-period subjects
                            }
                            
                            // If subject hasn't exceeded daily limit, use it
                            if ($todayCount < $maxPerDay) {
                                $selectedSubject = $candidate;
                                $poolIndex = ($candidateIndex + 1) % count($weightedSubjectPool);
                                break;
                            }
                            
                            $attempts++;
                        }
                        
                        // If no subject found within limits, just use next in pool
                        if (!$selectedSubject && !empty($weightedSubjectPool)) {
                            $selectedSubject = $weightedSubjectPool[$poolIndex % count($weightedSubjectPool)];
                            $poolIndex = ($poolIndex + 1) % count($weightedSubjectPool);
                        }
                        
                        if ($selectedSubject) {
                            $subjectId = $selectedSubject['id'];
                            
                            // Track subject usage per day
                            if (!isset($subjectPerDay[$day][$subjectId])) {
                                $subjectPerDay[$day][$subjectId] = 0;
                            }
                            $subjectPerDay[$day][$subjectId]++;
                            
                            // Only assign teacher if they exist in the database
                            $potentialTeacherId = $selectedSubject['teacher_id'] ?? null;
                            if ($potentialTeacherId && Teacher::find($potentialTeacherId)) {
                                $teacherId = $potentialTeacherId;
                            }
                        }
                    }

                    Timetable::create([
                        'class_id' => $settings->class_id,
                        'subject_id' => $subjectId,
                        'teacher_id' => $teacherId,
                        'day' => $day,
                        'start_time' => $currentTime->format('H:i:s'),
                        'end_time' => $slotEndTime->format('H:i:s'),
                        'slot_type' => 'subject',
                        'slot_order' => $slotOrder++,
                        'academic_year' => $settings->academic_year,
                        'term' => $settings->term,
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
