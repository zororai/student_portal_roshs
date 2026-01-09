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

    /**
     * Display master timetable with all classes on one sheet
     */
    public function master(Request $request)
    {
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        
        // Get current term info
        $currentTerm = \App\ResultsStatus::orderBy('year', 'desc')
            ->orderBy('result_period', 'desc')
            ->first();
        
        $academicYear = $request->get('year', $currentTerm ? $currentTerm->year : date('Y'));
        $term = $request->get('term', 1);
        if ($currentTerm && $currentTerm->result_period) {
            preg_match('/\d+/', $currentTerm->result_period, $matches);
            $term = $request->get('term', $matches[0] ?? 1);
        }
        
        // Get all classes with timetables
        $classes = Grade::with(['subjects', 'teacher'])
            ->orderBy('class_numeric')
            ->get();
        
        // Build timetable data for each class
        $classTimetables = [];
        $allTimeSlots = [];
        
        foreach ($classes as $class) {
            $timetable = Timetable::where('class_id', $class->id)
                ->where('academic_year', $academicYear)
                ->where('term', $term)
                ->with(['subject', 'teacher'])
                ->orderBy('slot_order')
                ->get();
            
            if ($timetable->count() > 0) {
                $classTimetables[$class->id] = [
                    'class' => $class,
                    'slots' => $timetable->groupBy('day'),
                ];
                
                // Collect all unique time slots
                foreach ($timetable as $slot) {
                    $timeKey = $slot->start_time . '-' . $slot->end_time;
                    if (!isset($allTimeSlots[$timeKey])) {
                        $allTimeSlots[$timeKey] = [
                            'start_time' => $slot->start_time,
                            'end_time' => $slot->end_time,
                            'slot_type' => $slot->slot_type,
                            'slot_order' => $slot->slot_order,
                        ];
                    }
                }
            }
        }
        
        // Sort time slots by start time
        uasort($allTimeSlots, function($a, $b) {
            return strcmp($a['start_time'], $b['start_time']);
        });
        
        return view('backend.admin.timetable.master', compact(
            'classes', 
            'classTimetables', 
            'days', 
            'allTimeSlots',
            'academicYear',
            'term',
            'currentTerm'
        ));
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
        
        // Build lesson pools by type (quad, triple, double, single)
        // Each entry represents ONE lesson block (not individual periods)
        $lessonPool = [];
        
        // Track which subjects have multi-period lessons configured
        // These subjects CAN have lessons back-to-back on the same day
        $subjectsWithMultiPeriod = [];
        foreach ($subjects as $subject) {
            $hasMulti = ($subject->double_lessons_per_week ?? 0) > 0 
                     || ($subject->triple_lessons_per_week ?? 0) > 0 
                     || ($subject->quad_lessons_per_week ?? 0) > 0;
            $subjectsWithMultiPeriod[$subject->id] = $hasMulti;
        }
        
        foreach ($subjects as $subject) {
            $subjectInfo = [
                'id' => $subject->id,
                'name' => $subject->name,
                'teacher_id' => $subject->teacher_id,
                'has_multi_period' => $subjectsWithMultiPeriod[$subject->id],
            ];
            
            // Add quad lessons (4 consecutive periods)
            $quadCount = $subject->quad_lessons_per_week ?? 0;
            for ($i = 0; $i < $quadCount; $i++) {
                $lessonPool[] = array_merge($subjectInfo, ['duration_periods' => 4, 'type' => 'quad']);
            }
            
            // Add triple lessons (3 consecutive periods)
            $tripleCount = $subject->triple_lessons_per_week ?? 0;
            for ($i = 0; $i < $tripleCount; $i++) {
                $lessonPool[] = array_merge($subjectInfo, ['duration_periods' => 3, 'type' => 'triple']);
            }
            
            // Add double lessons (2 consecutive periods)
            $doubleCount = $subject->double_lessons_per_week ?? 0;
            for ($i = 0; $i < $doubleCount; $i++) {
                $lessonPool[] = array_merge($subjectInfo, ['duration_periods' => 2, 'type' => 'double']);
            }
            
            // Add single lessons (1 period)
            $singleCount = $subject->single_lessons_per_week ?? 0;
            for ($i = 0; $i < $singleCount; $i++) {
                $lessonPool[] = array_merge($subjectInfo, ['duration_periods' => 1, 'type' => 'single']);
            }
        }
        
        // Sort pool: longer lessons first (harder to fit), then shuffle within each type
        usort($lessonPool, function($a, $b) {
            return $b['duration_periods'] - $a['duration_periods'];
        });
        
        // Track which lessons have been placed and on which days
        $placedLessons = []; // Array of placed lesson info
        
        // STRICT CONSTRAINT: Track subjects used per day
        // A subject may appear ONLY ONCE per day (as a single consecutive block)
        $usedSubjectsByDay = [
            'Monday'    => [],
            'Tuesday'   => [],
            'Wednesday' => [],
            'Thursday'  => [],
            'Friday'    => [],
        ];
        
        // First pass: Generate the time structure for each day
        $dayStructure = [];
        foreach ($days as $day) {
            $dayStructure[$day] = $this->generateDayStructure($settings, $day);
        }
        
        // Track how many lessons of each type have been placed for each subject
        $subjectLessonsPlaced = [];
        foreach ($subjects as $subject) {
            $subjectLessonsPlaced[$subject->id] = [
                'quad' => 0,
                'triple' => 0,
                'double' => 0,
                'single' => 0,
            ];
        }
        
        // Distribute lessons across days, spacing out multi-period lessons
        foreach ($lessonPool as $lessonIndex => $lesson) {
            $subjectId = $lesson['id'];
            $durationPeriods = $lesson['duration_periods'];
            $teacherId = $lesson['teacher_id'];
            $lessonType = $lesson['type'];
            $hasMultiPeriod = $lesson['has_multi_period'] ?? false;
            
            // For multi-period lessons, try to space them across different days
            $preferredDays = $this->getPreferredDays($days, $subjectId, $usedSubjectsByDay, $durationPeriods);
            
            // STRICT CONSTRAINT: Maximum 1 lesson block per subject per day
            // This applies to ALL lesson types (single, double, triple, quad)
            // A double lesson counts as 1 block, not 2 separate lessons
            $maxBlocksPerDay = 1;
            
            $placed = false;
            foreach ($preferredDays as $day) {
                // HARD CONSTRAINT: Check if subject already has a block on this day
                if (in_array($subjectId, $usedSubjectsByDay[$day])) {
                    continue; // Skip this day - subject already scheduled
                }
                
                // Find consecutive available slots for this lesson
                $availableSlot = $this->findConsecutiveSlots(
                    $dayStructure[$day], 
                    $durationPeriods, 
                    $settings->subject_duration,
                    $teacherId,
                    $day,
                    $settings,
                    $subjectId,
                    $lessonType,
                    $hasMultiPeriod
                );
                
                if ($availableSlot !== null) {
                    // Mark slots as used - $availableSlot is now an array of actual indices
                    foreach ($availableSlot as $i => $slotIndex) {
                        $dayStructure[$day][$slotIndex]['subject_id'] = $subjectId;
                        $dayStructure[$day][$slotIndex]['teacher_id'] = $teacherId;
                        $dayStructure[$day][$slotIndex]['lesson_type'] = $lessonType;
                        $dayStructure[$day][$slotIndex]['is_continuation'] = ($i > 0);
                    }
                    
                    // STRICT CONSTRAINT: Mark subject as used for this day
                    // This ensures the subject cannot appear again on this day
                    // (whether consecutive or non-consecutive)
                    $usedSubjectsByDay[$day][] = $subjectId;
                    
                    // Track lessons placed by type
                    $subjectLessonsPlaced[$subjectId][$lessonType]++;
                    
                    $placed = true;
                    break;
                }
            }
            
            // If couldn't place (all days at limit or no slots), lesson is NOT placed
            // The remaining empty slots will become free periods
            // This prevents subjects from appearing too many times on same day
        }
        
        // Now create timetable records from the day structure
        // IMPORTANT: Store every slot as a separate record to ensure all days align
        foreach ($days as $day) {
            $slots = $dayStructure[$day];
            
            foreach ($slots as $slotOrder => $slot) {
                // Create the timetable record for every slot
                Timetable::create([
                    'class_id' => $settings->class_id,
                    'subject_id' => $slot['subject_id'] ?? null,
                    'teacher_id' => $slot['teacher_id'] ?? null,
                    'day' => $day,
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                    'slot_type' => $slot['slot_type'],
                    'slot_order' => $slotOrder,
                    'academic_year' => $settings->academic_year,
                    'term' => $settings->term,
                ]);
            }
        }
    }
    
    /**
     * Generate the basic time structure for a day (breaks, lunch, subject slots)
     * Break and Lunch are FIXED at their configured times and never move
     */
    private function generateDayStructure(TimetableSetting $settings, $day)
    {
        $structure = [];
        
        // Parse all times as strings to avoid Carbon date issues
        $startTime = $settings->start_time;
        $endTime = $settings->end_time;
        $breakStart = $settings->break_start;
        $breakEnd = $settings->break_end;
        $lunchStart = $settings->lunch_start;
        $lunchEnd = $settings->lunch_end;
        $periodDuration = $settings->subject_duration;
        
        // Convert times to minutes from midnight for easier comparison
        $toMinutes = function($time) {
            $parts = explode(':', $time);
            return intval($parts[0]) * 60 + intval($parts[1]);
        };
        
        $fromMinutes = function($minutes) {
            $h = str_pad(floor($minutes / 60), 2, '0', STR_PAD_LEFT);
            $m = str_pad($minutes % 60, 2, '0', STR_PAD_LEFT);
            return "{$h}:{$m}:00";
        };
        
        $startMins = $toMinutes($startTime);
        $endMins = $toMinutes($endTime);
        $breakStartMins = $toMinutes($breakStart);
        $breakEndMins = $toMinutes($breakEnd);
        $lunchStartMins = $toMinutes($lunchStart);
        $lunchEndMins = $toMinutes($lunchEnd);
        
        $currentMins = $startMins;
        
        while ($currentMins < $endMins) {
            // Check if we're at break time
            if ($currentMins >= $breakStartMins && $currentMins < $breakEndMins) {
                $structure[] = [
                    'start_time' => $fromMinutes($breakStartMins),
                    'end_time' => $fromMinutes($breakEndMins),
                    'slot_type' => 'break',
                    'subject_id' => null,
                    'teacher_id' => null,
                ];
                $currentMins = $breakEndMins;
                continue;
            }
            
            // Check if we're at lunch time
            if ($currentMins >= $lunchStartMins && $currentMins < $lunchEndMins) {
                $structure[] = [
                    'start_time' => $fromMinutes($lunchStartMins),
                    'end_time' => $fromMinutes($lunchEndMins),
                    'slot_type' => 'lunch',
                    'subject_id' => null,
                    'teacher_id' => null,
                ];
                $currentMins = $lunchEndMins;
                continue;
            }
            
            // Calculate slot end time
            $slotEndMins = $currentMins + $periodDuration;
            
            // Check if slot would overlap with break
            if ($currentMins < $breakStartMins && $slotEndMins > $breakStartMins) {
                // Create shorter slot before break if there's meaningful time
                if ($breakStartMins - $currentMins >= 10) {
                    $structure[] = [
                        'start_time' => $fromMinutes($currentMins),
                        'end_time' => $fromMinutes($breakStartMins),
                        'slot_type' => 'subject',
                        'subject_id' => null,
                        'teacher_id' => null,
                        'is_gap' => true,
                    ];
                }
                $currentMins = $breakStartMins;
                continue;
            }
            
            // Check if slot would overlap with lunch
            if ($currentMins < $lunchStartMins && $slotEndMins > $lunchStartMins) {
                // Create shorter slot before lunch if there's meaningful time
                if ($lunchStartMins - $currentMins >= 10) {
                    $structure[] = [
                        'start_time' => $fromMinutes($currentMins),
                        'end_time' => $fromMinutes($lunchStartMins),
                        'slot_type' => 'subject',
                        'subject_id' => null,
                        'teacher_id' => null,
                        'is_gap' => true,
                    ];
                }
                $currentMins = $lunchStartMins;
                continue;
            }
            
            // Don't exceed end time
            if ($slotEndMins > $endMins) {
                if ($endMins - $currentMins >= 10) {
                    $structure[] = [
                        'start_time' => $fromMinutes($currentMins),
                        'end_time' => $fromMinutes($endMins),
                        'slot_type' => 'subject',
                        'subject_id' => null,
                        'teacher_id' => null,
                        'is_gap' => true,
                    ];
                }
                break;
            }
            
            // Create a normal subject slot
            $structure[] = [
                'start_time' => $fromMinutes($currentMins),
                'end_time' => $fromMinutes($slotEndMins),
                'slot_type' => 'subject',
                'subject_id' => null,
                'teacher_id' => null,
            ];
            
            $currentMins = $slotEndMins;
        }
        
        return $structure;
    }
    
    /**
     * Get preferred days for placing a lesson, prioritizing days where the subject hasn't been placed yet
     * STRICT CONSTRAINT: Only return days where the subject has NOT been scheduled
     */
    private function getPreferredDays($days, $subjectId, $usedSubjectsByDay, $durationPeriods)
    {
        // STRICT CONSTRAINT: Only consider days where this subject hasn't been placed
        $availableDays = [];
        
        foreach ($days as $day) {
            // Check if subject is already used on this day
            if (!in_array($subjectId, $usedSubjectsByDay[$day])) {
                $availableDays[] = $day;
            }
        }
        
        // Shuffle available days for variety in distribution
        shuffle($availableDays);
        
        return $availableDays;
    }
    
    /**
     * Find consecutive available slots that can fit a multi-period lesson
     * STRICT CONSTRAINT: Subject can only appear once per day as a consecutive block
     * Non-consecutive repetitions are strictly forbidden
     */
    private function findConsecutiveSlots($dayStructure, $periodsNeeded, $periodDuration, $teacherId, $day, $settings, $subjectId = null, $lessonType = 'single', $hasMultiPeriod = false, $forcePlace = false)
    {
        $subjectSlots = [];
        
        // Get indices of subject slots only (excluding gap slots which stay as free periods)
        foreach ($dayStructure as $index => $slot) {
            if ($slot['slot_type'] === 'subject' && $slot['subject_id'] === null) {
                // Skip gap slots - they should remain as free periods
                if (isset($slot['is_gap']) && $slot['is_gap']) {
                    continue;
                }
                $subjectSlots[] = $index;
            }
        }
        
        // STRICT CONSTRAINT: Check if this subject is ANYWHERE on this day already
        // This applies to ALL lesson types (single, double, triple, quad)
        // A subject may appear only ONCE per day as a single consecutive block
        if ($subjectId !== null) {
            foreach ($dayStructure as $slot) {
                if (isset($slot['subject_id']) && $slot['subject_id'] === $subjectId) {
                    // Subject already has a lesson block today - FORBIDDEN
                    // Return null to force trying another day
                    return null;
                }
            }
        }
        
        // Find consecutive available slots
        for ($i = 0; $i <= count($subjectSlots) - $periodsNeeded; $i++) {
            $consecutive = true;
            $startIndex = $subjectSlots[$i];
            
            // Check if the next N slots are consecutive and available
            for ($j = 0; $j < $periodsNeeded; $j++) {
                if ($i + $j >= count($subjectSlots)) {
                    $consecutive = false;
                    break;
                }
                
                $currentSlotIndex = $subjectSlots[$i + $j];
                
                // Check if this slot is already used
                if ($dayStructure[$currentSlotIndex]['subject_id'] !== null) {
                    $consecutive = false;
                    break;
                }
                
                // Check if slots are actually consecutive
                // Double lessons CAN span across break/lunch but NOT across other subjects
                if ($j > 0) {
                    $prevSlotIndex = $subjectSlots[$i + $j - 1];
                    $currSlotIndex = $subjectSlots[$i + $j];
                    
                    // If not directly adjacent, check what's in between
                    if ($currSlotIndex != $prevSlotIndex + 1) {
                        // Check if the gap contains ONLY break/lunch (which is allowed)
                        $gapIsOnlyBreakLunch = true;
                        for ($k = $prevSlotIndex + 1; $k < $currSlotIndex; $k++) {
                            if (!in_array($dayStructure[$k]['slot_type'], ['break', 'lunch'])) {
                                // There's something other than break/lunch in the gap
                                $gapIsOnlyBreakLunch = false;
                                break;
                            }
                        }
                        if (!$gapIsOnlyBreakLunch) {
                            // Gap contains subject slots - not truly consecutive
                            $consecutive = false;
                            break;
                        }
                        // If gap is only break/lunch, that's OK - continue checking
                    }
                }
            }
            
            if (!$consecutive) {
                continue;
            }
            
            // Check for teacher conflict across the entire time span
            if ($teacherId) {
                $startTime = $dayStructure[$startIndex]['start_time'];
                $endSlotIndex = $subjectSlots[$i + $periodsNeeded - 1];
                $endTime = Carbon::parse($dayStructure[$startIndex]['start_time'])
                    ->addMinutes($periodDuration * $periodsNeeded)
                    ->format('H:i:s');
                
                $teacherHasConflict = Timetable::where('teacher_id', $teacherId)
                    ->where('day', $day)
                    ->where('academic_year', $settings->academic_year)
                    ->where('term', $settings->term)
                    ->where(function($query) use ($startTime, $endTime) {
                        $query->where('start_time', '<', $endTime)
                              ->where('end_time', '>', $startTime);
                    })
                    ->exists();
                
                if ($teacherHasConflict) {
                    continue;
                }
            }
            
            // Return array of actual slot indices for multi-period lessons
            $slotIndices = [];
            for ($j = 0; $j < $periodsNeeded; $j++) {
                $slotIndices[] = $subjectSlots[$i + $j];
            }
            return $slotIndices;
        }
        
        return null;
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

    /**
     * Clear timetables for a specific term and academic year
     */
    public function clear(Request $request)
    {
        $validated = $request->validate([
            'academic_year' => 'required|integer',
            'term' => 'required|integer|between:1,3',
            'class_id' => 'nullable|exists:grades,id',
        ]);

        $query = Timetable::where('academic_year', $validated['academic_year'])
            ->where('term', $validated['term']);

        // If a specific class is selected, only delete for that class
        if (!empty($validated['class_id'])) {
            $query->where('class_id', $validated['class_id']);
            $className = Grade::find($validated['class_id'])->class_name;
            $deletedCount = $query->delete();
            
            // Also delete the timetable settings for this class
            TimetableSetting::where('class_id', $validated['class_id'])
                ->where('academic_year', $validated['academic_year'])
                ->where('term', $validated['term'])
                ->delete();
            
            return redirect()->route('admin.timetable.index')
                ->with('success', "Cleared {$deletedCount} timetable records for {$className} (Term {$validated['term']}, {$validated['academic_year']})");
        }

        // Delete all timetables for the term and year
        $deletedCount = $query->delete();
        
        // Also delete the timetable settings
        TimetableSetting::where('academic_year', $validated['academic_year'])
            ->where('term', $validated['term'])
            ->delete();

        return redirect()->route('admin.timetable.index')
            ->with('success', "Cleared {$deletedCount} timetable records for all classes (Term {$validated['term']}, {$validated['academic_year']})");
    }
}
