<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BudgetPeriod;
use App\BudgetItem;
use App\RevenueForecast;
use App\ExpenseForecast;
use App\ExpenseCategory;

class BudgetController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $budgets = BudgetPeriod::with('creator')
            ->orderBy('start_date', 'desc')
            ->paginate(10);
        
        return view('backend.admin.finance.budgets.index', compact('budgets'));
    }

    public function create()
    {
        return view('backend.admin.finance.budgets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'period_type' => 'required|in:annual,term,quarterly,monthly',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $budget = BudgetPeriod::create([
            'name' => $request->name,
            'period_type' => $request->period_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.finance.budgets.show', $budget->id)
            ->with('success', 'Budget period created. Add budget items now.');
    }

    public function show($id)
    {
        $budget = BudgetPeriod::with(['budgetItems', 'revenueForecasts', 'expenseForecasts'])->findOrFail($id);
        
        $incomeItems = $budget->budgetItems->where('type', 'income');
        $expenseItems = $budget->budgetItems->where('type', 'expense');
        
        return view('backend.admin.finance.budgets.show', compact('budget', 'incomeItems', 'expenseItems'));
    }

    public function addItem(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:income,expense',
            'category' => 'required|string|max:255',
            'budgeted_amount' => 'required|numeric|min:0',
        ]);

        BudgetItem::create([
            'budget_period_id' => $id,
            'type' => $request->type,
            'category' => $request->category,
            'description' => $request->description,
            'budgeted_amount' => $request->budgeted_amount,
        ]);

        return back()->with('success', 'Budget item added.');
    }

    public function updateActual(Request $request, $itemId)
    {
        $item = BudgetItem::findOrFail($itemId);
        $item->update([
            'actual_amount' => $request->actual_amount,
        ]);
        $item->updateVariance();

        return back()->with('success', 'Actual amount updated.');
    }

    public function activate($id)
    {
        $budget = BudgetPeriod::findOrFail($id);
        $budget->update(['status' => 'active']);
        return back()->with('success', 'Budget activated.');
    }

    public function close($id)
    {
        $budget = BudgetPeriod::findOrFail($id);
        $budget->update(['status' => 'closed']);
        return back()->with('success', 'Budget closed.');
    }

    public function revenueForecast($id)
    {
        $budget = BudgetPeriod::with('revenueForecasts')->findOrFail($id);
        return view('backend.admin.finance.budgets.revenue-forecast', compact('budget'));
    }

    public function storeRevenueForecast(Request $request, $id)
    {
        $request->validate([
            'source' => 'required|string|max:255',
            'expected_amount' => 'required|numeric|min:0',
        ]);

        RevenueForecast::create([
            'budget_period_id' => $id,
            'source' => $request->source,
            'expected_amount' => $request->expected_amount,
            'assumptions' => $request->assumptions,
        ]);

        return back()->with('success', 'Revenue forecast added.');
    }

    public function expenseForecast($id)
    {
        $budget = BudgetPeriod::with('expenseForecasts')->findOrFail($id);
        $categories = ExpenseCategory::where('is_active', true)->get();
        return view('backend.admin.finance.budgets.expense-forecast', compact('budget', 'categories'));
    }

    public function storeExpenseForecast(Request $request, $id)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'expected_amount' => 'required|numeric|min:0',
        ]);

        ExpenseForecast::create([
            'budget_period_id' => $id,
            'category_id' => $request->category_id,
            'category_name' => $request->category_name,
            'expected_amount' => $request->expected_amount,
            'assumptions' => $request->assumptions,
        ]);

        return back()->with('success', 'Expense forecast added.');
    }

    public function comparison($id)
    {
        $budget = BudgetPeriod::with(['budgetItems'])->findOrFail($id);
        
        $incomeComparison = $budget->budgetItems->where('type', 'income')->map(function($item) {
            return [
                'category' => $item->category,
                'budgeted' => $item->budgeted_amount,
                'actual' => $item->actual_amount,
                'variance' => $item->variance,
                'variance_pct' => $item->variance_percentage,
            ];
        });

        $expenseComparison = $budget->budgetItems->where('type', 'expense')->map(function($item) {
            return [
                'category' => $item->category,
                'budgeted' => $item->budgeted_amount,
                'actual' => $item->actual_amount,
                'variance' => $item->variance,
                'variance_pct' => $item->variance_percentage,
            ];
        });

        return view('backend.admin.finance.budgets.comparison', compact('budget', 'incomeComparison', 'expenseComparison'));
    }
}
