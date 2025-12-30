<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Expense;
use App\ExpenseCategory;
use App\CashBookEntry;

class ExpenseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        // Year and term filter setup
        $years = range(date('Y'), date('Y') - 5);
        $terms = ['first' => 'First Term', 'second' => 'Second Term', 'third' => 'Third Term'];
        
        $selectedYear = $request->year;
        $selectedTerm = $request->term;
        
        $query = Expense::with(['category', 'creator']);
        
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->status) {
            $query->where('payment_status', $request->status);
        }
        
        // Apply year/term filter using term/year fields
        if ($selectedYear) {
            $query->where('year', $selectedYear);
        }
        if ($selectedTerm) {
            $query->where('term', $selectedTerm);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->paginate(20)->appends($request->query());
        $categories = ExpenseCategory::where('is_active', true)->get();
        
        // Calculate stats based on filtered query
        $statsQuery = Expense::query();
        if ($selectedYear) {
            $statsQuery->where('year', $selectedYear);
        }
        if ($selectedTerm) {
            $statsQuery->where('term', $selectedTerm);
        }
        
        $stats = [
            'total' => (clone $statsQuery)->sum('amount'),
            'pending' => (clone $statsQuery)->where('payment_status', 'pending')->sum('amount'),
            'paid' => (clone $statsQuery)->where('payment_status', 'paid')->sum('amount'),
        ];

        return view('backend.admin.finance.expenses.index', compact('expenses', 'categories', 'stats', 'years', 'terms', 'selectedYear', 'selectedTerm'));
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
            'term' => 'required|string|in:first,second,third',
            'year' => 'required|integer',
            'category_id' => 'required|exists:expense_categories,id',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'payment_status' => 'required|in:pending,paid,partial',
        ]);

        $expense = Expense::create([
            'expense_number' => Expense::generateExpenseNumber(),
            'expense_date' => $request->expense_date,
            'term' => $request->term,
            'year' => $request->year,
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

        // Auto-create CashBookEntry if expense is paid
        if ($request->payment_status === 'paid') {
            $lastEntry = CashBookEntry::orderBy('id', 'desc')->first();
            $currentBalance = $lastEntry ? $lastEntry->balance : 0;
            $newBalance = $currentBalance - $request->amount;

            $category = ExpenseCategory::find($request->category_id);
            $cashEntry = CashBookEntry::create([
                'entry_date' => $request->expense_date,
                'term' => $request->term,
                'year' => $request->year,
                'reference_number' => CashBookEntry::generateReferenceNumber(),
                'transaction_type' => 'payment',
                'category' => 'other_expense',
                'description' => '[Expense] ' . $request->description,
                'amount' => $request->amount,
                'balance' => $newBalance,
                'payment_method' => $request->payment_method ?? 'cash',
                'payer_payee' => $request->vendor_name ?? ($category ? $category->name : 'Expense'),
                'created_by' => auth()->id(),
                'notes' => 'Auto-generated from Expense #' . $expense->id,
            ]);
            $cashEntry->postToLedger();
        }

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
        $oldStatus = $expense->payment_status;
        
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

        // Auto-create CashBookEntry if status changed to paid and no entry exists
        if ($request->payment_status === 'paid' && $oldStatus !== 'paid') {
            $existingEntry = CashBookEntry::where('notes', 'Auto-generated from Expense #' . $id)->first();
            if (!$existingEntry) {
                $lastEntry = CashBookEntry::orderBy('id', 'desc')->first();
                $currentBalance = $lastEntry ? $lastEntry->balance : 0;
                $newBalance = $currentBalance - $request->amount;

                $category = ExpenseCategory::find($request->category_id);
                $cashEntry = CashBookEntry::create([
                    'entry_date' => $request->expense_date,
                    'reference_number' => CashBookEntry::generateReferenceNumber(),
                    'transaction_type' => 'payment',
                    'category' => 'other_expense',
                    'description' => '[Expense] ' . $request->description,
                    'amount' => $request->amount,
                    'balance' => $newBalance,
                    'payment_method' => $request->payment_method ?? 'cash',
                    'payer_payee' => $request->vendor_name ?? ($category ? $category->name : 'Expense'),
                    'created_by' => auth()->id(),
                    'notes' => 'Auto-generated from Expense #' . $expense->id,
                ]);
                $cashEntry->postToLedger();
            }
        }

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
