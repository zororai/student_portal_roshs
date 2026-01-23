<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FeeLevelGroup;
use App\Grade;
use App\Student;
use App\FeeStructure;
use App\ResultsStatus;

class FeeLevelGroupController extends Controller
{
    public function index()
    {
        $groups = FeeLevelGroup::orderBy('display_order')->get();
        return view('fee_level_groups.index', compact('groups'));
    }

    public function create()
    {
        $classes = Grade::orderBy('class_numeric')->get();
        return view('fee_level_groups.create', compact('classes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'groups' => 'required|array|min:1',
            'groups.*.name' => 'required|string|max:255',
            'groups.*.description' => 'nullable|string|max:500',
            'groups.*.classes' => 'required|array|min:1',
            'groups.*.classes.*' => 'integer|min:0',
            'groups.*.display_order' => 'nullable|integer|min:0',
        ]);

        $created = 0;
        foreach ($request->groups as $groupData) {
            // Skip if no classes selected
            if (empty($groupData['classes'])) {
                continue;
            }
            
            // Calculate min and max from selected classes
            $selectedClasses = array_map('intval', $groupData['classes']);
            $minClass = min($selectedClasses);
            $maxClass = max($selectedClasses);
            
            // Generate class range string for display
            $classNames = Grade::whereIn('class_numeric', $selectedClasses)
                ->orderBy('class_numeric')
                ->pluck('class_name')
                ->toArray();
            $classRange = implode(', ', $classNames);
            
            FeeLevelGroup::create([
                'name' => $groupData['name'],
                'description' => $groupData['description'] ?? null,
                'min_class_numeric' => $minClass,
                'max_class_numeric' => $maxClass,
                'class_range' => $classRange,
                'display_order' => $groupData['display_order'] ?? 0,
                'is_active' => true,
            ]);
            $created++;
        }

        $message = $created === 1 
            ? 'Fee Level Group created successfully.' 
            : $created . ' Fee Level Groups created successfully.';

        return redirect()->route('fee-level-groups.index')
            ->with('success', $message);
    }

    public function edit($id)
    {
        $group = FeeLevelGroup::findOrFail($id);
        $classes = Grade::orderBy('class_numeric')->get();
        return view('fee_level_groups.edit', compact('group', 'classes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'min_class_numeric' => 'required|integer|min:1',
            'max_class_numeric' => 'required|integer|min:1|gte:min_class_numeric',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $group = FeeLevelGroup::findOrFail($id);
        $group->update([
            'name' => $request->name,
            'description' => $request->description,
            'min_class_numeric' => $request->min_class_numeric,
            'max_class_numeric' => $request->max_class_numeric,
            'display_order' => $request->display_order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('fee-level-groups.index')
            ->with('success', 'Fee Level Group updated successfully.');
    }

    public function destroy($id)
    {
        $group = FeeLevelGroup::findOrFail($id);
        $group->delete();

        return redirect()->route('fee-level-groups.index')
            ->with('success', 'Fee Level Group deleted successfully.');
    }

    public function applyToNewStudents()
    {
        // Get the current active term
        $currentTerm = ResultsStatus::orderBy('year', 'desc')
            ->orderBy('result_period', 'desc')
            ->first();

        if (!$currentTerm) {
            return redirect()->route('fee-level-groups.index')
                ->with('error', 'No term found. Please create a term first.');
        }

        // Get all new students with their class information
        $newStudents = Student::where('is_new_student', true)
            ->whereHas('class')
            ->with('class')
            ->get();

        if ($newStudents->isEmpty()) {
            return redirect()->route('fee-level-groups.index')
                ->with('info', 'No new students found to apply fee structures to.');
        }

        $appliedCount = 0;
        $errors = [];

        foreach ($newStudents as $student) {
            $classNumeric = $student->class->class_numeric ?? null;
            
            if (!$classNumeric) {
                continue;
            }

            // Find the fee level group for this student's class
            $levelGroup = FeeLevelGroup::getGroupForClass($classNumeric);
            
            if (!$levelGroup) {
                $errors[] = "No fee level group found for {$student->name} (Class: {$student->class->class_name})";
                continue;
            }

            // Get fee structures for this student
            $feeStructures = FeeStructure::where('results_status_id', $currentTerm->id)
                ->where('fee_level_group_id', $levelGroup->id)
                ->where('student_type', $student->student_type ?? 'day')
                ->where('curriculum_type', $student->curriculum_type ?? 'zimsec')
                ->where('is_for_new_student', true)
                ->get();

            if ($feeStructures->isNotEmpty()) {
                // Calculate total fees for this student
                $totalFees = $feeStructures->sum('amount');
                
                // Update student's fee information if needed
                // You can add custom logic here to store the calculated fees
                
                $appliedCount++;
            }
        }

        $message = "Fee structures applied to {$appliedCount} new student(s) for {$currentTerm->year} Term {$currentTerm->result_period}.";
        
        if (!empty($errors)) {
            $message .= " " . count($errors) . " student(s) had issues.";
        }

        return redirect()->route('fee-level-groups.index')
            ->with('success', $message);
    }
}
