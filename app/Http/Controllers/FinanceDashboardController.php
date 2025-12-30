<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CashBookEntry;
use App\Expense;
use App\Payroll;
use App\BudgetPeriod;
use App\LedgerAccount;
use Carbon\Carbon;

class FinanceDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $currentYear = Carbon::now()->startOfYear();

        // Monthly Stats
        $monthlyIncome = CashBookEntry::where('transaction_type', 'receipt')
            ->whereMonth('entry_date', now()->month)
            ->whereYear('entry_date', now()->year)
            ->sum('amount');

        $monthlyExpenses = CashBookEntry::where('transaction_type', 'payment')
            ->whereMonth('entry_date', now()->month)
            ->whereYear('entry_date', now()->year)
            ->sum('amount');

        // Yearly Stats
        $yearlyIncome = CashBookEntry::where('transaction_type', 'receipt')
            ->whereYear('entry_date', now()->year)
            ->sum('amount');

        $yearlyExpenses = CashBookEntry::where('transaction_type', 'payment')
            ->whereYear('entry_date', now()->year)
            ->sum('amount');

        // Current Balance
        $lastEntry = CashBookEntry::orderBy('id', 'desc')->first();
        $currentBalance = $lastEntry ? $lastEntry->balance : 0;

        // Pending Payroll
        $pendingPayroll = Payroll::where('status', 'pending')->sum('net_salary');
        $pendingPayrollCount = Payroll::where('status', 'pending')->count();

        // Pending Expenses
        $pendingExpenses = Expense::where('payment_status', 'pending')->sum('amount');

        // Monthly Income/Expense Chart Data (last 6 months)
        $chartData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $chartData['labels'][] = $month->format('M Y');
            $chartData['income'][] = CashBookEntry::where('transaction_type', 'receipt')
                ->whereMonth('entry_date', $month->month)
                ->whereYear('entry_date', $month->year)
                ->sum('amount');
            $chartData['expenses'][] = CashBookEntry::where('transaction_type', 'payment')
                ->whereMonth('entry_date', $month->month)
                ->whereYear('entry_date', $month->year)
                ->sum('amount');
        }

        // Expense by Category (current month)
        $expenseByCategory = Expense::selectRaw('category_id, SUM(amount) as total')
            ->whereMonth('expense_date', now()->month)
            ->whereYear('expense_date', now()->year)
            ->groupBy('category_id')
            ->with('category')
            ->get();

        // Recent Transactions
        $recentTransactions = CashBookEntry::orderBy('entry_date', 'desc')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        // Active Budget
        $activeBudget = BudgetPeriod::where('status', 'active')->first();

        return view('backend.admin.finance.dashboard', compact(
            'monthlyIncome', 'monthlyExpenses', 'yearlyIncome', 'yearlyExpenses',
            'currentBalance', 'pendingPayroll', 'pendingPayrollCount', 'pendingExpenses',
            'chartData', 'expenseByCategory', 'recentTransactions', 'activeBudget'
        ));
    }

    public function incomeStatement(Request $request)
    {
        $fromDate = $request->from_date ?? now()->startOfMonth()->format('Y-m-d');
        $toDate = $request->to_date ?? now()->format('Y-m-d');

        $income = CashBookEntry::where('transaction_type', 'receipt')
            ->whereBetween('entry_date', [$fromDate, $toDate])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        $expenses = CashBookEntry::where('transaction_type', 'payment')
            ->whereBetween('entry_date', [$fromDate, $toDate])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        $totalIncome = $income->sum('total');
        $totalExpenses = $expenses->sum('total');
        $netIncome = $totalIncome - $totalExpenses;

        return view('backend.admin.finance.reports.income-statement', compact(
            'income', 'expenses', 'totalIncome', 'totalExpenses', 'netIncome', 'fromDate', 'toDate'
        ));
    }

    public function balanceSheet()
    {
        $assets = LedgerAccount::where('account_type', 'asset')->where('is_active', true)->get();
        $liabilities = LedgerAccount::where('account_type', 'liability')->where('is_active', true)->get();
        $equity = LedgerAccount::where('account_type', 'equity')->where('is_active', true)->get();

        $totalAssets = $assets->sum('current_balance');
        $totalLiabilities = $liabilities->sum('current_balance');
        $totalEquity = $equity->sum('current_balance');

        return view('backend.admin.finance.reports.balance-sheet', compact(
            'assets', 'liabilities', 'equity', 'totalAssets', 'totalLiabilities', 'totalEquity'
        ));
    }

    public function feeReport(Request $request)
    {
        $term = $request->term ?? 'all';
        
        $feeCollections = CashBookEntry::where('transaction_type', 'receipt')
            ->where('category', 'school_fees')
            ->selectRaw('DATE_FORMAT(entry_date, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->get();

        $totalFees = $feeCollections->sum('total');

        return view('backend.admin.finance.reports.fee-report', compact('feeCollections', 'totalFees'));
    }

    public function expenseReport(Request $request)
    {
        $fromDate = $request->from_date ?? now()->startOfYear()->format('Y-m-d');
        $toDate = $request->to_date ?? now()->format('Y-m-d');

        $expenses = Expense::with('category')
            ->whereBetween('expense_date', [$fromDate, $toDate])
            ->selectRaw('category_id, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('category_id')
            ->get();

        $totalExpenses = $expenses->sum('total');

        $monthlyTrend = Expense::whereBetween('expense_date', [$fromDate, $toDate])
            ->selectRaw('DATE_FORMAT(expense_date, "%Y-%m") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('backend.admin.finance.reports.expense-report', compact(
            'expenses', 'totalExpenses', 'monthlyTrend', 'fromDate', 'toDate'
        ));
    }
}
