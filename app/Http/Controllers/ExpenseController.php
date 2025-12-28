<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Expense;
use App\ExpenseCategory;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Expense::with(['category', 'creator']);
        
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->status) {
            $query->where('payment_status', $request->status);
        }
        if ($request->from_date) {
            $query->whereDate('expense_date', '>=', $request->from_date);
        }
        if ($request->to_date) {
            $query->whereDate('expense_date', '<=', $request->to_date);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->paginate(20);
        $categories = ExpenseCategory::where('is_active', true)->get();
        
        $stats = [
            'total' => Expense::sum('amount'),
            'pending' => Expense::where('payment_status', 'pending')->sum('amount'),
            'paid' => Expense::where('payment_status', 'paid')->sum('amount'),
        ];

        return view('backend.admin.finance.expenses.index', compact('expenses', 'categories', 'stats'));
    }

    public function create()
    {
        $categories = ExpenseCategory::where('is_active', true)->get();
        return view('backend.admin.finance.expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_date' => 'required|date',
            'category_id' => 'required|exists:expense_categories,id',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'payment_status' => 'required|in:pending,paid,partial',
        ]);

        Expense::create([
            'expense_number' => Expense::generateExpenseNumber(),
            'expense_date' => $request->expense_date,
            'category_id' => $request->category_id,
            'vendor_name' => $request->vendor_name,
            'description' => $request->description,
            'amount' => $request->amount,
            'payment_status' => $request->payment_status,
            'payment_method' => $request->payment_method,
            'receipt_number' => $request->receipt_number,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.finance.expenses.index')
            ->with('success', 'Expense recorded successfully.');
    }

    public function show($id)
    {
        $expense = Expense::with(['category', 'creator', 'approver'])->findOrFail($id);
        return view('backend.admin.finance.expenses.show', compact('expense'));
    }

    public function edit($id)
    {
        $expense = Expense::findOrFail($id);
        $categories = ExpenseCategory::where('is_active', true)->get();
        return view('backend.admin.finance.expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $expense = Expense::findOrFail($id);
        
        $request->validate([
            'expense_date' => 'required|date',
            'category_id' => 'required|exists:expense_categories,id',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $expense->update($request->only([
            'expense_date', 'category_id', 'vendor_name', 'description',
            'amount', 'payment_status', 'payment_method', 'receipt_number', 'notes'
        ]));

        return redirect()->route('admin.finance.expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function approve($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->update([
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Expense approved.');
    }

    public function categories()
    {
        $categories = ExpenseCategory::withCount('expenses')->get();
        return view('backend.admin.finance.expenses.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:expense_categories,code',
        ]);

        ExpenseCategory::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('admin.finance.expenses.categories')
            ->with('success', 'Category created successfully.');
    }

    public function report(Request $request)
    {
        $fromDate = $request->from_date ?? now()->startOfMonth()->format('Y-m-d');
        $toDate = $request->to_date ?? now()->format('Y-m-d');

        $expensesByCategory = Expense::selectRaw('category_id, SUM(amount) as total')
            ->whereBetween('expense_date', [$fromDate, $toDate])
            ->groupBy('category_id')
            ->with('category')
            ->get();

        $totalExpenses = Expense::whereBetween('expense_date', [$fromDate, $toDate])->sum('amount');

        return view('backend.admin.finance.expenses.report', compact('expensesByCategory', 'totalExpenses', 'fromDate', 'toDate'));
    }
}
