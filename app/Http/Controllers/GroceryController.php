<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GroceryList;
use App\GroceryItem;
use App\GroceryResponse;
use App\Grade;
use App\Student;
use App\Parents;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GroceryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Admin: Show class cards with grocery lists
    public function index()
    {
        $classes = Grade::withCount('students')->get();
        $groceryLists = GroceryList::with(['classes', 'items', 'responses'])->orderBy('created_at', 'desc')->get();
        $allTerms = \App\ResultsStatus::orderBy('year', 'desc')->orderBy('result_period', 'desc')->get();

        // Get response stats per class
        foreach ($classes as $class) {
            $activeList = GroceryList::whereHas('classes', function($q) use ($class) {
                $q->where('class_id', $class->id);
            })->where('status', 'active')->first();

            if ($activeList) {
                $class->active_list = $activeList;
                $class->total_students = $class->students_count;
                $class->submitted_count = GroceryResponse::where('grocery_list_id', $activeList->id)
                    ->whereHas('student', function($q) use ($class) {
                        $q->where('class_id', $class->id);
                    })
                    ->where('submitted', true)
                    ->count();
                $class->acknowledged_count = GroceryResponse::where('grocery_list_id', $activeList->id)
                    ->whereHas('student', function($q) use ($class) {
                        $q->where('class_id', $class->id);
                    })
                    ->where('acknowledged', true)
                    ->count();
            } else {
                $class->active_list = null;
                $class->submitted_count = 0;
                $class->acknowledged_count = 0;
            }
        }

        return view('backend.finance.groceries.index', compact('classes', 'groceryLists', 'allTerms'));
    }

    // Admin: Store new grocery list
    public function store(Request $request)
    {
        $validated = $request->validate([
            'term' => 'required|string',
            'year' => 'required|string',
            'classes' => 'required|array|min:1',
            'classes.*' => 'exists:grades,id',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.quantity' => 'nullable|string|max:100',
        ]);

        DB::beginTransaction();
        try {
            $groceryList = GroceryList::create([
                'term' => $validated['term'],
                'year' => $validated['year'],
                'status' => 'active'
            ]);

            // Attach classes
            $groceryList->classes()->attach($validated['classes']);

            // Create items
            foreach ($validated['items'] as $item) {
                GroceryItem::create([
                    'grocery_list_id' => $groceryList->id,
                    'name' => $item['name'],
                    'quantity' => $item['quantity'] ?? null
                ]);
            }

            DB::commit();
            return redirect()->route('admin.groceries.index')->with('success', 'Grocery list created and sent to parents!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create grocery list: ' . $e->getMessage());
        }
    }

    // Admin: View students for a class
    public function showClass($classId)
    {
        $class = Grade::with('students.user', 'students.parent.user')->findOrFail($classId);

        $activeList = GroceryList::whereHas('classes', function($q) use ($classId) {
            $q->where('class_id', $classId);
        })->where('status', 'active')->with('items')->first();

        $students = Student::where('class_id', $classId)->with(['user', 'parent.user'])->get();

        foreach ($students as $student) {
            if ($activeList) {
                $response = GroceryResponse::where('grocery_list_id', $activeList->id)
                    ->where('student_id', $student->id)
                    ->first();
                $student->grocery_response = $response;
            } else {
                $student->grocery_response = null;
            }
        }

        return view('backend.finance.groceries.class-students', compact('class', 'students', 'activeList'));
    }

    // Admin: View student's grocery response
    public function viewResponse($responseId)
    {
        $response = GroceryResponse::with(['groceryList.items', 'student.user', 'parent.user'])->findOrFail($responseId);
        return view('backend.finance.groceries.view-response', compact('response'));
    }

    // Admin: Acknowledge receipt of goods
    public function acknowledge($responseId)
    {
        $response = GroceryResponse::findOrFail($responseId);
        $response->update([
            'acknowledged' => true,
            'acknowledged_at' => now()
        ]);

        return redirect()->back()->with('success', 'Goods acknowledged as received!');
    }

    // Admin: Update student grocery list
    public function updateStudentGrocery(Request $request)
    {
        $validated = $request->validate([
            'grocery_list_id' => 'required|exists:grocery_lists,id',
            'student_id' => 'required|exists:students,id',
            'response_id' => 'nullable|exists:grocery_responses,id',
            'items_bought' => 'nullable|array',
            'items_bought.*' => 'exists:grocery_items,id',
            'extra_items' => 'nullable|array',
            'extra_items.*.name' => 'required_with:extra_items|string|max:255',
            'extra_items.*.quantity' => 'nullable|string|max:100',
            'item_extra_qty' => 'nullable|array',
            'item_extra_qty.*' => 'nullable|integer|min:0',
            'item_short_qty' => 'nullable|array',
            'item_short_qty.*' => 'nullable|integer|min:0',
            'notes' => 'nullable|string'
        ]);

        $student = Student::findOrFail($validated['student_id']);

        // Filter out empty extra items
        $extraItems = collect($validated['extra_items'] ?? [])->filter(function($item) {
            return !empty($item['name']);
        })->values()->toArray();

        // Filter out zero extra quantities for list items
        $itemExtraQty = collect($validated['item_extra_qty'] ?? [])->filter(function($qty) {
            return $qty > 0;
        })->toArray();

        // Filter out zero short quantities for list items
        $itemShortQty = collect($validated['item_short_qty'] ?? [])->filter(function($qty) {
            return $qty > 0;
        })->toArray();

        // If response exists, update it; otherwise create new
        if (!empty($validated['response_id'])) {
            $response = GroceryResponse::findOrFail($validated['response_id']);
            $response->update([
                'items_bought' => $validated['items_bought'] ?? [],
                'extra_items' => $extraItems,
                'item_extra_qty' => $itemExtraQty,
                'item_short_qty' => $itemShortQty,
                'notes' => $validated['notes'] ?? null,
                'submitted' => true,
                'submitted_at' => $response->submitted_at ?? now()
            ]);
        } else {
            $response = GroceryResponse::create([
                'grocery_list_id' => $validated['grocery_list_id'],
                'student_id' => $validated['student_id'],
                'parent_id' => $student->parent_id,
                'items_bought' => $validated['items_bought'] ?? [],
                'extra_items' => $extraItems,
                'item_extra_qty' => $itemExtraQty,
                'item_short_qty' => $itemShortQty,
                'notes' => $validated['notes'] ?? null,
                'submitted' => true,
                'submitted_at' => now()
            ]);
        }

        return redirect()->back()->with('success', 'Student grocery list updated successfully!');
    }

    // Parent: View grocery lists for their children
    public function parentIndex()
    {
        $user = Auth::user();
        $parent = Parents::where('user_id', $user->id)->first();

        if (!$parent) {
            return redirect()->back()->with('error', 'Parent record not found.');
        }

        $children = Student::where('parent_id', $parent->id)->with(['user', 'class'])->get();

        $groceryData = [];
        foreach ($children as $child) {
            $activeList = GroceryList::whereHas('classes', function($q) use ($child) {
                $q->where('class_id', $child->class_id);
            })->where('status', 'active')->with('items')->first();

            if ($activeList) {
                $response = GroceryResponse::where('grocery_list_id', $activeList->id)
                    ->where('student_id', $child->id)
                    ->first();

                $groceryData[] = [
                    'child' => $child,
                    'list' => $activeList,
                    'response' => $response
                ];
            }
        }

        return view('backend.finance.groceries.parent-index', compact('groceryData', 'parent'));
    }

    // Parent: Submit grocery response
    public function parentSubmit(Request $request)
    {
        $validated = $request->validate([
            'grocery_list_id' => 'required|exists:grocery_lists,id',
            'student_id' => 'required|exists:students,id',
            'items_bought' => 'nullable|array',
            'items_bought.*' => 'exists:grocery_items,id',
            'notes' => 'nullable|string|max:500'
        ]);

        $user = Auth::user();
        $parent = Parents::where('user_id', $user->id)->first();

        if (!$parent) {
            return redirect()->back()->with('error', 'Parent record not found.');
        }

        // Check if student belongs to this parent
        $student = Student::where('id', $validated['student_id'])
            ->where('parent_id', $parent->id)
            ->first();

        if (!$student) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        GroceryResponse::updateOrCreate(
            [
                'grocery_list_id' => $validated['grocery_list_id'],
                'student_id' => $validated['student_id']
            ],
            [
                'parent_id' => $parent->id,
                'items_bought' => $validated['items_bought'] ?? [],
                'submitted' => true,
                'submitted_at' => now(),
                'notes' => $validated['notes']
            ]
        );

        return redirect()->route('parent.groceries.index')->with('success', 'Grocery list submitted successfully!');
    }

    // Admin: Close a grocery list
    public function close($id)
    {
        $groceryList = GroceryList::findOrFail($id);
        $groceryList->update(['status' => 'closed']);

        return redirect()->back()->with('success', 'Grocery list closed.');
    }

    // Admin: Edit grocery list form (only if not locked)
    public function edit($id)
    {
        $groceryList = GroceryList::with(['classes', 'items'])->findOrFail($id);

        if ($groceryList->locked) {
            return redirect()->back()->with('error', 'This grocery list is locked and cannot be edited.');
        }

        $classes = Grade::all();
        return view('backend.finance.groceries.edit', compact('groceryList', 'classes'));
    }

    // Admin: Update grocery list (only if not locked)
    public function update(Request $request, $id)
    {
        $groceryList = GroceryList::findOrFail($id);

        if ($groceryList->locked) {
            return redirect()->back()->with('error', 'This grocery list is locked and cannot be edited.');
        }

        $validated = $request->validate([
            'term' => 'required|string',
            'year' => 'required|string',
            'classes' => 'required|array|min:1',
            'classes.*' => 'exists:grades,id',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.quantity' => 'nullable|string|max:100',
            'items.*.price' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $groceryList->update([
                'term' => $validated['term'],
                'year' => $validated['year']
            ]);

            // Sync classes
            $groceryList->classes()->sync($validated['classes']);

            // Delete existing items and recreate
            $groceryList->items()->delete();
            foreach ($validated['items'] as $item) {
                GroceryItem::create([
                    'grocery_list_id' => $groceryList->id,
                    'name' => $item['name'],
                    'quantity' => $item['quantity'] ?? null,
                    'price' => $item['price'] ?? 0
                ]);
            }

            DB::commit();
            return redirect()->route('admin.groceries.index')->with('success', 'Grocery list updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update grocery list: ' . $e->getMessage());
        }
    }

    // Admin: Lock grocery list (prevents further editing)
    public function lock($id)
    {
        $groceryList = GroceryList::findOrFail($id);
        $groceryList->update([
            'locked' => true,
            'locked_at' => now()
        ]);

        return redirect()->back()->with('success', 'Grocery list locked. No further editing is allowed.');
    }

    // Admin: View student grocery history
    public function studentHistory($studentId)
    {
        $student = Student::with(['user', 'class'])->findOrFail($studentId);

        // Get all grocery responses for this student
        $responses = GroceryResponse::where('student_id', $studentId)
            ->with(['groceryList.items', 'parent.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate accumulative balance (item counts, not money)
        $totalOwedItems = 0;
        $historyData = [];

        foreach ($responses as $response) {
            $groceryList = $response->groceryList;
            $allItems = $groceryList->items;
            $boughtItemIds = $response->items_bought ?? [];

            $totalItemCount = count($allItems);
            $providedCount = 0;
            $owedCount = 0;
            $missingItems = [];

            foreach ($allItems as $item) {
                if (in_array($item->id, $boughtItemIds)) {
                    $providedCount++;
                } else {
                    $owedCount++;
                    $missingItems[] = $item->name . ($item->quantity ? ' (' . $item->quantity . ')' : '');
                }
            }

            $totalOwedItems += $owedCount;

            $historyData[] = [
                'response' => $response,
                'total_items' => $totalItemCount,
                'provided_count' => $providedCount,
                'owed_count' => $owedCount,
                'missing_items' => $missingItems,
                'term' => $groceryList->term,
                'year' => $groceryList->year
            ];
        }

        return view('backend.finance.groceries.student-history', compact('student', 'historyData', 'totalOwedItems'));
    }

    // Helper: Calculate grocery balance for a student
    public static function calculateGroceryBalance($studentId)
    {
        $responses = GroceryResponse::where('student_id', $studentId)
            ->with('groceryList.items')
            ->get();

        $totalOwed = 0;

        foreach ($responses as $response) {
            $groceryList = $response->groceryList;
            if (!$groceryList) continue;

            $allItems = $groceryList->items;
            $boughtItemIds = $response->items_bought ?? [];

            foreach ($allItems as $item) {
                if (!in_array($item->id, $boughtItemIds)) {
                    $totalOwed += floatval($item->price);
                }
            }
        }

        return $totalOwed;
    }

    // Admin: Delete a grocery list
    public function destroy($id)
    {
        $groceryList = GroceryList::findOrFail($id);
        $groceryList->delete();

        return redirect()->route('admin.groceries.index')->with('success', 'Grocery list deleted.');
    }

    // Admin: View grocery block settings
    public function blockSettings(Request $request)
    {
        $groceryBlockEnabled = \App\SchoolSetting::get('grocery_block_enabled', 'true') === 'true';
        $q = $request->query('q');

        // blocked types from settings (array of 'day'|'boarder')
        $blockedTypesRaw = \App\SchoolSetting::get('grocery_block_types', null);
        $blockedTypes = [];
        if ($blockedTypesRaw) {
            $decoded = json_decode($blockedTypesRaw, true);
            if (is_array($decoded)) $blockedTypes = $decoded;
            else $blockedTypes = array_filter(array_map('trim', explode(',', $blockedTypesRaw)));
        } else {
            // default: block both types
            $blockedTypes = ['day', 'boarder'];
        }

        // If a search query is provided, return matching students (by name or roll_number)
        if ($q) {
            $matched = Student::with(['user', 'class'])
                ->where('roll_number', 'like', "%{$q}%")
                ->orWhereHas('user', function($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%");
                })
                ->get();

            $studentsWithArrears = [];
            foreach ($matched as $student) {
                $arrears = self::calculateGroceryBalance($student->id);
                $studentsWithArrears[] = [
                    'student' => $student,
                    'arrears' => $arrears
                ];
            }
        } else {
            $studentsWithArrears = self::getStudentsWithArrears();
        }

        return view('backend.finance.groceries.block-settings', compact('groceryBlockEnabled', 'studentsWithArrears', 'q', 'blockedTypes'));
    }

    // Admin: Update grocery block settings
    public function updateBlockSettings(Request $request)
    {
        $enabled = $request->has('grocery_block_enabled') ? 'true' : 'false';

        \App\SchoolSetting::set(
            'grocery_block_enabled',
            $enabled,
            'boolean',
            'Controls whether grocery arrears block parents and students from viewing results'
        );

        // Save which student types should be subject to grocery blocking.
        // NOTE: once a type is enabled for blocking it cannot be removed via this form (add-only).
        $blockTypes = $request->input('block_types'); // expected as array
        $existingRaw = \App\SchoolSetting::get('grocery_block_types', null);
        $existing = [];
        if ($existingRaw) {
            $decoded = json_decode($existingRaw, true);
            if (is_array($decoded)) $existing = $decoded;
            else $existing = array_filter(array_map('trim', explode(',', $existingRaw)));
        }

        if (is_array($blockTypes)) {
            // Union existing and newly selected types so types cannot be unselected once added
            $merged = array_values(array_unique(array_merge($existing, $blockTypes)));
            \App\SchoolSetting::set('grocery_block_types', json_encode($merged), 'string', 'Student types subject to grocery blocking (day, boarder)');
        } else {
            // if no new types provided, keep existing
            if (!empty($existing)) {
                \App\SchoolSetting::set('grocery_block_types', json_encode(array_values($existing)), 'string', 'Student types subject to grocery blocking (day, boarder)');
            }
        }

        $status = $enabled === 'true' ? 'enabled' : 'disabled';
        // Handle apply-to-type actions (block/unblock by student_type)
        $applyType = $request->input('apply_type'); // expected: 'day' or 'boarder'
        $applyAction = $request->input('apply_action'); // expected: 'block' or 'exempt'

        if ($applyType && $applyAction) {
            $students = Student::where('student_type', $applyType)->get();
            foreach ($students as $student) {
                if ($applyAction === 'block') {
                    $student->grocery_exempt = false;
                } else {
                    $student->grocery_exempt = true;
                }
                $student->save();
            }

            $status .= "; applied '{$applyAction}' to all '{$applyType}' students";
        }

        return redirect()->route('admin.grocery-block-settings')
            ->with('success', "Grocery arrears blocking has been {$status}.");
    }

    // Helper: Check if grocery blocking is enabled
    public static function isGroceryBlockEnabled()
    {
        return \App\SchoolSetting::get('grocery_block_enabled', 'true') === 'true';
    }

    // Return array of student types that are subject to grocery blocking
    public static function getBlockedTypes()
    {
        $raw = \App\SchoolSetting::get('grocery_block_types', null);
        if (!$raw) return ['day', 'boarder'];
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) return $decoded;
        return array_filter(array_map('trim', explode(',', $raw)));
    }

    // Check whether a given student is in a type that should be blocked
    public static function isStudentTypeBlocked($student)
    {
        if (!$student) return false;
        $types = self::getBlockedTypes();
        return in_array($student->student_type, $types);
    }

    // Admin: Toggle student grocery exemption
    public function toggleExemption($studentId)
    {
        $student = Student::findOrFail($studentId);
        $student->grocery_exempt = !$student->grocery_exempt;
        $student->save();

        $status = $student->grocery_exempt ? 'exempted from' : 'no longer exempt from';
        return redirect()->back()->with('success', "{$student->user->name} is now {$status} grocery blocking.");
    }

    // Helper: Get students with grocery arrears
    public static function getStudentsWithArrears()
    {
        $students = Student::with(['user', 'class'])->get();
        $studentsWithArrears = [];

        foreach ($students as $student) {
            $arrears = self::calculateGroceryBalance($student->id);
            if ($arrears > 0) {
                $studentsWithArrears[] = [
                    'student' => $student,
                    'arrears' => $arrears
                ];
            }
        }

        return $studentsWithArrears;
    }

    /**
     * Admin: Display grocery arrears page with balance brought forward
     */
    public function groceryArrears(Request $request)
    {
        $classes = Grade::orderBy('class_name')->get();
        
        // Get all grocery lists ordered by date
        $allGroceryLists = GroceryList::with(['items', 'classes'])
            ->orderBy('year', 'asc')
            ->orderBy('term', 'asc')
            ->get();
        
        // Get current grocery list (most recent)
        $currentGroceryList = GroceryList::with(['items', 'classes'])
            ->orderBy('year', 'desc')
            ->orderBy('term', 'desc')
            ->first();
        
        // Build query for students
        $query = Student::with(['user', 'class', 'parent.user']);
        
        // Apply filters
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('student_type')) {
            $query->where('student_type', $request->student_type);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('roll_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($sub) use ($search) {
                      $sub->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        $students = $query->orderBy('created_at', 'desc')->get();
        
        // Calculate grocery arrears with balance brought forward for each student (item counts)
        $studentsWithArrears = [];
        $totalBalanceBf = 0;
        $totalCurrentTermOwed = 0;
        $totalGroceryItems = 0;
        $totalProvided = 0;
        
        foreach ($students as $student) {
            $arrearsData = $this->calculateGroceryArrearsWithBf($student->id, $allGroceryLists, $currentGroceryList);
            
            $student->balance_bf = $arrearsData['balance_bf'];
            $student->current_term_owed = $arrearsData['current_term_owed'];
            $student->total_owed = $arrearsData['total_owed'];
            $student->total_grocery_items = $arrearsData['total_grocery_items'];
            $student->total_provided = $arrearsData['total_provided'];
            $student->arrears_breakdown = $arrearsData['breakdown'];
            $student->missing_items = $arrearsData['missing_items'];
            
            $totalBalanceBf += $arrearsData['balance_bf'];
            $totalCurrentTermOwed += $arrearsData['current_term_owed'];
            $totalGroceryItems += $arrearsData['total_grocery_items'];
            $totalProvided += $arrearsData['total_provided'];
            
            if ($arrearsData['total_owed'] > 0) {
                $studentsWithArrears[] = $student;
            }
        }
        
        $totalOutstanding = $totalGroceryItems - $totalProvided;
        
        // Filter to show only students with arrears if requested
        if ($request->filled('show_arrears_only') && $request->show_arrears_only == '1') {
            $students = collect($studentsWithArrears);
        }
        
        return view('backend.finance.grocery-arrears', compact(
            'students', 'classes', 'currentGroceryList',
            'totalBalanceBf', 'totalCurrentTermOwed', 'totalGroceryItems', 'totalProvided', 'totalOutstanding'
        ));
    }

    /**
     * Export grocery arrears to CSV
     */
    public function exportGroceryArrears(Request $request)
    {
        $classes = Grade::orderBy('class_name')->get();
        
        $allGroceryLists = GroceryList::with(['items', 'classes'])
            ->orderBy('year', 'asc')
            ->orderBy('term', 'asc')
            ->get();
        
        $currentGroceryList = GroceryList::with(['items', 'classes'])
            ->orderBy('year', 'desc')
            ->orderBy('term', 'desc')
            ->first();
        
        $query = Student::with(['user', 'class', 'parent.user']);
        
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }
        if ($request->filled('student_type')) {
            $query->where('student_type', $request->student_type);
        }
        
        $students = $query->orderBy('created_at', 'desc')->get();
        
        $exportData = [];
        foreach ($students as $student) {
            $arrearsData = $this->calculateGroceryArrearsWithBf($student->id, $allGroceryLists, $currentGroceryList);
            
            if ($request->filled('show_arrears_only') && $request->show_arrears_only == '1' && $arrearsData['total_owed'] == 0) {
                continue;
            }
            
            $exportData[] = [
                'student' => $student,
                'balance_bf' => $arrearsData['balance_bf'],
                'current_term_owed' => $arrearsData['current_term_owed'],
                'total_owed' => $arrearsData['total_owed'],
                'missing_items' => $arrearsData['missing_items']
            ];
        }
        
        $filename = 'grocery_arrears_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($exportData) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['#', 'Roll Number', 'Student Name', 'Class', 'Type', 'Parent', 'Items B/F', 'Current Term', 'Total Owed', 'Missing Items']);
            
            $index = 1;
            foreach ($exportData as $data) {
                $student = $data['student'];
                fputcsv($file, [
                    $index++,
                    $student->roll_number ?? '',
                    $student->user->name ?? $student->name,
                    $student->class->class_name ?? 'N/A',
                    ucfirst($student->student_type ?? 'day'),
                    $student->parent->user->name ?? 'No Parent',
                    $data['balance_bf'],
                    $data['current_term_owed'],
                    $data['total_owed'],
                    implode(', ', $data['missing_items'] ?? [])
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Print student grocery history as PDF
     */
    public function printStudentHistory($studentId)
    {
        $student = Student::with(['user', 'class', 'parent.user'])->findOrFail($studentId);

        $responses = GroceryResponse::where('student_id', $studentId)
            ->with(['groceryList.items', 'parent.user'])
            ->orderBy('created_at', 'desc')
            ->get();

        $totalOwedItems = 0;
        $totalProvidedItems = 0;
        $historyData = [];

        foreach ($responses as $response) {
            $groceryList = $response->groceryList;
            $allItems = $groceryList->items;
            $boughtItemIds = $response->items_bought ?? [];

            $totalItemCount = count($allItems);
            $providedCount = 0;
            $owedCount = 0;
            $missingItems = [];

            foreach ($allItems as $item) {
                if (in_array($item->id, $boughtItemIds)) {
                    $providedCount++;
                } else {
                    $owedCount++;
                    $missingItems[] = $item->name . ($item->quantity ? ' (' . $item->quantity . ')' : '');
                }
            }

            $totalOwedItems += $owedCount;
            $totalProvidedItems += $providedCount;

            $historyData[] = [
                'response' => $response,
                'total_items' => $totalItemCount,
                'provided_count' => $providedCount,
                'owed_count' => $owedCount,
                'missing_items' => $missingItems,
                'term' => $groceryList->term,
                'year' => $groceryList->year
            ];
        }

        return view('backend.finance.groceries.print-history', compact('student', 'historyData', 'totalOwedItems', 'totalProvidedItems'));
    }

    /**
     * Calculate grocery arrears with balance brought forward (item counts, not money)
     */
    private function calculateGroceryArrearsWithBf($studentId, $allGroceryLists, $currentGroceryList)
    {
        $responses = GroceryResponse::where('student_id', $studentId)
            ->with('groceryList.items')
            ->get()
            ->keyBy('grocery_list_id');
        
        $balanceBfItems = 0;
        $currentTermOwedItems = 0;
        $totalGroceryItems = 0;
        $totalProvidedItems = 0;
        $breakdown = [];
        $missingItems = [];
        
        foreach ($allGroceryLists as $list) {
            $listItemCount = count($list->items);
            $providedCount = 0;
            $owedCount = 0;
            $termMissingItems = [];
            
            $response = $responses->get($list->id);
            $boughtItemIds = $response ? ($response->items_bought ?? []) : [];
            
            foreach ($list->items as $item) {
                if (in_array($item->id, $boughtItemIds)) {
                    $providedCount++;
                } else {
                    $owedCount++;
                    $termMissingItems[] = $item->name . ($item->quantity ? ' (' . $item->quantity . ')' : '');
                }
            }
            
            $totalGroceryItems += $listItemCount;
            $totalProvidedItems += $providedCount;
            
            // Track current term vs previous terms
            if ($currentGroceryList && $list->id === $currentGroceryList->id) {
                $currentTermOwedItems = $owedCount;
                if ($owedCount > 0) {
                    $missingItems = array_merge($missingItems, $termMissingItems);
                }
            } else if (!$currentGroceryList || $list->id < $currentGroceryList->id) {
                $balanceBfItems += $owedCount;
            }
            
            if ($owedCount > 0) {
                $breakdown[] = [
                    'term' => ucfirst($list->term) . ' ' . $list->year,
                    'total_items' => $listItemCount,
                    'provided' => $providedCount,
                    'owed' => $owedCount,
                    'missing_items' => $termMissingItems
                ];
            }
        }
        
        return [
            'balance_bf' => $balanceBfItems,
            'current_term_owed' => $currentTermOwedItems,
            'total_owed' => $balanceBfItems + $currentTermOwedItems,
            'total_grocery_items' => $totalGroceryItems,
            'total_provided' => $totalProvidedItems,
            'breakdown' => $breakdown,
            'missing_items' => $missingItems
        ];
    }
}
