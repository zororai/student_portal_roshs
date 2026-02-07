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

        // Calculate outstanding student fees with balance B/F
        $allTermsForCalc = \App\ResultsStatus::with(['termFees.feeType', 'feeStructures.feeType', 'feeStructures.feeLevelGroup'])
            ->orderBy('year', 'asc')
            ->orderBy('result_period', 'asc')
            ->get();
        
        $currentTerm = \App\ResultsStatus::orderBy('year', 'desc')->orderBy('result_period', 'desc')->first();
        
        // Active students calculations
        $totalStudentFees = 0;
        $totalBalanceBf = 0;
        $totalCurrentTermFees = 0;
        $totalStudentPayments = 0;
        
        $activeStudents = \App\Student::with(['class', 'payments'])
            ->where('is_transferred', false)
            ->get();
        foreach ($activeStudents as $student) {
            $feeBreakdown = $this->calculateCumulativeFees($student, $allTermsForCalc, $currentTerm);
            $totalBalanceBf += $feeBreakdown['balance_bf'];
            $totalCurrentTermFees += $feeBreakdown['current_term_fees'];
            $totalStudentFees += $feeBreakdown['total_fees'];
            $totalStudentPayments += floatval(\App\StudentPayment::where('student_id', $student->id)->sum('amount_paid'));
        }
        
        $totalOutstandingFees = $totalStudentFees - $totalStudentPayments;

        // Graduated/Transferred students calculations
        $graduatedTotalFees = 0;
        $graduatedTotalPayments = 0;
        $graduatedStudentsCount = 0;
        $graduatedStudentsWithDebt = 0;
        $graduatedStudentsWithCredit = 0;
        
        $graduatedStudents = \App\Student::with(['class', 'payments'])
            ->where('is_transferred', true)
            ->get();
        
        foreach ($graduatedStudents as $student) {
            $feeBreakdown = $this->calculateCumulativeFees($student, $allTermsForCalc, $currentTerm);
            $graduatedTotalFees += $feeBreakdown['total_fees'];
            $studentPayments = floatval(\App\StudentPayment::where('student_id', $student->id)->sum('amount_paid'));
            $graduatedTotalPayments += $studentPayments;
            
            $balance = $feeBreakdown['total_fees'] - $studentPayments;
            if ($balance > 0) {
                $graduatedStudentsWithDebt++;
            } elseif ($balance < 0) {
                $graduatedStudentsWithCredit++;
            }
        }
        
        $graduatedStudentsCount = $graduatedStudents->count();
        $graduatedOutstanding = $graduatedTotalFees - $graduatedTotalPayments;

        return view('backend.admin.finance.dashboard', compact(
            'monthlyIncome', 'monthlyExpenses', 'yearlyIncome', 'yearlyExpenses',
            'currentBalance', 'pendingPayroll', 'pendingPayrollCount', 'pendingExpenses',
            'chartData', 'expenseByCategory', 'recentTransactions', 'activeBudget',
            'totalStudentFees', 'totalBalanceBf', 'totalCurrentTermFees', 'totalStudentPayments', 'totalOutstandingFees',
            'graduatedStudentsCount', 'graduatedTotalFees', 'graduatedTotalPayments', 'graduatedOutstanding',
            'graduatedStudentsWithDebt', 'graduatedStudentsWithCredit'
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

    /**
     * Calculate cumulative fees for a student across all terms
     */
    private function calculateCumulativeFees($student, $allTerms, $currentTerm)
    {
        $currentTermFees = 0;
        $previousTermsFees = 0;
        
        if (!$currentTerm) {
            return ['current_term_fees' => 0, 'balance_bf' => 0, 'total_fees' => 0];
        }

        $studentCreatedAt = $student->created_at;
        $studentType = $student->student_type ?? 'day';
        $curriculumType = $student->curriculum_type ?? 'zimsec';
        $scholarshipPercentage = floatval($student->scholarship_percentage ?? 0);

        foreach ($allTerms as $term) {
            if ($studentCreatedAt && $term->created_at < $studentCreatedAt) {
                continue;
            }

            $baseFee = 0;
            if ($curriculumType === 'cambridge') {
                $baseFee = $studentType === 'boarding' 
                    ? floatval($term->cambridge_boarding_fees ?? 0) 
                    : floatval($term->cambridge_day_fees ?? 0);
            } else {
                $baseFee = $studentType === 'boarding' 
                    ? floatval($term->zimsec_boarding_fees ?? $term->total_boarding_fees ?? 0) 
                    : floatval($term->zimsec_day_fees ?? $term->total_day_fees ?? 0);
            }
            
            if ($scholarshipPercentage > 0 && $scholarshipPercentage <= 100) {
                $baseFee = $baseFee - ($baseFee * ($scholarshipPercentage / 100));
            }
            
            if ($term->id === $currentTerm->id) {
                $currentTermFees = $baseFee;
            } else if ($term->id < $currentTerm->id) {
                $previousTermsFees += $baseFee;
            }
        }

        return [
            'current_term_fees' => $currentTermFees,
            'balance_bf' => $previousTermsFees,
            'total_fees' => $previousTermsFees + $currentTermFees,
        ];
    }
}
