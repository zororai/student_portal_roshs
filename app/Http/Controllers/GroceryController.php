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
            'notes' => 'nullable|string'
        ]);

        $student = Student::findOrFail($validated['student_id']);
        
        // If response exists, update it; otherwise create new
        if (!empty($validated['response_id'])) {
            $response = GroceryResponse::findOrFail($validated['response_id']);
            $response->update([
                'items_bought' => $validated['items_bought'] ?? [],
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
        
        // Calculate accumulative balance
        $totalOwed = 0;
        $historyData = [];
        
        foreach ($responses as $response) {
            $groceryList = $response->groceryList;
            $allItems = $groceryList->items;
            $boughtItemIds = $response->items_bought ?? [];
            
            $listTotal = 0;
            $paidTotal = 0;
            $owedTotal = 0;
            
            foreach ($allItems as $item) {
                $itemPrice = floatval($item->price);
                $listTotal += $itemPrice;
                
                if (in_array($item->id, $boughtItemIds)) {
                    $paidTotal += $itemPrice;
                } else {
                    $owedTotal += $itemPrice;
                }
            }
            
            $totalOwed += $owedTotal;
            
            $historyData[] = [
                'response' => $response,
                'list_total' => $listTotal,
                'paid_total' => $paidTotal,
                'owed_total' => $owedTotal,
                'term' => $groceryList->term,
                'year' => $groceryList->year
            ];
        }
        
        return view('backend.finance.groceries.student-history', compact('student', 'historyData', 'totalOwed'));
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
}
