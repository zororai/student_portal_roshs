<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SchoolSetting;
use App\ClassFormat;
use App\Grade;

class SchoolSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function classFormats()
    {
        $classFormats = ClassFormat::ordered()->get();
        $existingClasses = Grade::orderBy('class_numeric')->get();
        
        return view('backend.admin.settings.class-formats', compact('classFormats', 'existingClasses'));
    }

    public function storeClassFormat(Request $request)
    {
        $validated = $request->validate([
            'format_name' => 'required|string|max:100',
            'numeric_value' => 'required|integer|min:0',
            'display_name' => 'required|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['sort_order'] = $validated['sort_order'] ?? ClassFormat::max('sort_order') + 1;
        
        ClassFormat::create($validated);

        return redirect()->route('admin.settings.class-formats')
            ->with('success', 'Class format added successfully!');
    }

    public function updateClassFormat(Request $request, $id)
    {
        $classFormat = ClassFormat::findOrFail($id);

        $validated = $request->validate([
            'format_name' => 'required|string|max:100',
            'numeric_value' => 'required|integer|min:0',
            'display_name' => 'required|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active') ? 1 : 0;
        
        $classFormat->update($validated);

        return redirect()->route('admin.settings.class-formats')
            ->with('success', 'Class format updated successfully!');
    }

    public function deleteClassFormat($id)
    {
        $classFormat = ClassFormat::findOrFail($id);
        $classFormat->delete();

        return redirect()->route('admin.settings.class-formats')
            ->with('success', 'Class format deleted successfully!');
    }

    public function upgradeDirection()
    {
        $upgradeDirection = SchoolSetting::get('upgrade_direction', 'ascending');
        $classes = Grade::orderBy('class_numeric')->get();
        
        return view('backend.admin.settings.upgrade-direction', compact('upgradeDirection', 'classes'));
    }

    public function updateUpgradeDirection(Request $request)
    {
        $validated = $request->validate([
            'upgrade_direction' => 'required|in:ascending,descending',
        ]);

        SchoolSetting::set(
            'upgrade_direction', 
            $validated['upgrade_direction'],
            'select',
            'Direction of class upgrade (ascending: 1->2->3 or descending: 3->2->1)'
        );

        return redirect()->route('admin.settings.upgrade-direction')
            ->with('success', 'Upgrade direction updated successfully!');
    }

    public function getUpgradePreview(Request $request)
    {
        $direction = SchoolSetting::get('upgrade_direction', 'ascending');
        $classes = Grade::orderBy('class_numeric', $direction === 'ascending' ? 'asc' : 'desc')->get();
        
        $upgradeMap = [];
        
        foreach ($classes as $index => $class) {
            if ($direction === 'ascending') {
                $nextClass = Grade::where('class_numeric', $class->class_numeric + 1)->first();
            } else {
                $nextClass = Grade::where('class_numeric', $class->class_numeric - 1)->first();
            }
            
            $upgradeMap[] = [
                'current' => $class->class_name,
                'current_numeric' => $class->class_numeric,
                'next' => $nextClass ? $nextClass->class_name : 'Graduated/Final',
                'next_numeric' => $nextClass ? $nextClass->class_numeric : null,
            ];
        }

        return response()->json([
            'success' => true,
            'direction' => $direction,
            'upgrade_map' => $upgradeMap,
        ]);
    }
}
