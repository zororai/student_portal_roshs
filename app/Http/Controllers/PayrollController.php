<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\EmployeeSalary;
use App\Payroll;
use App\CashBookEntry;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin']);
    }

    public function index(Request $request)
    {
        // Get current active term from ResultsStatus
        $currentTerm = \App\ResultsStatus::orderBy('year', 'desc')
            ->orderBy('result_period', 'desc')
            ->first();
        
        // Get all terms from database for filter
        $allTerms = \App\ResultsStatus::orderBy('year', 'desc')
            ->orderBy('result_period', 'desc')
            ->get();
        
        $query = Payroll::with(['user', 'salary', 'approver', 'payer']);

        // Default to current term's year if no filter specified
        $selectedYear = $request->has('year') ? $request->year : ($currentTerm ? $currentTerm->year : null);
        $selectedMonth = $request->month;
        $selectedTermId = $request->has('term_id') ? $request->term_id : ($currentTerm && !$request->hasAny(['year', 'month']) ? $currentTerm->id : null);

        // Filter by term (using term start/end dates)
        if ($selectedTermId) {
            $term = \App\ResultsStatus::find($selectedTermId);
            if ($term && $term->start_date && $term->end_date) {
                $query->whereBetween('pay_date', [$term->start_date, $term->end_date]);
            }
        } else {
            // Filter by year and month if no term selected
            if ($selectedYear) {
                $query->whereYear('pay_date', $selectedYear);
            }

            if ($selectedMonth) {
                $query->whereMonth('pay_date', $selectedMonth);
            }
        }

        // Filter by pay period (legacy support)
        if ($request->filled('pay_period')) {
            $query->where('pay_period', $request->pay_period);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payrolls = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get available pay periods
        $payPeriods = Payroll::select('pay_period')->distinct()->orderBy('pay_period', 'desc')->pluck('pay_period');

        // Get available years
        $years = Payroll::selectRaw('YEAR(pay_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Stats (apply same filters as main query)
        $statsQuery = Payroll::query();
        if ($selectedTermId) {
            $term = \App\ResultsStatus::find($selectedTermId);
            if ($term && $term->start_date && $term->end_date) {
                $statsQuery->whereBetween('pay_date', [$term->start_date, $term->end_date]);
            }
        } else {
            if ($selectedYear) {
                $statsQuery->whereYear('pay_date', $selectedYear);
            }
            if ($selectedMonth) {
                $statsQuery->whereMonth('pay_date', $selectedMonth);
            }
        }

        $stats = [
            'total_pending' => (clone $statsQuery)->where('status', 'pending')->sum('net_salary'),
            'total_approved' => (clone $statsQuery)->where('status', 'approved')->sum('net_salary'),
            'total_paid' => (clone $statsQuery)->where('status', 'paid')->sum('net_salary'),
            'pending_count' => (clone $statsQuery)->where('status', 'pending')->count(),
        ];

        // Monthly breakdown (for current year or selected year)
        $breakdownYear = $selectedYear ?? ($currentTerm ? $currentTerm->year : date('Y'));
        $monthlyBreakdown = Payroll::selectRaw('MONTH(pay_date) as month, SUM(net_salary) as total')
            ->whereYear('pay_date', $breakdownYear)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month')
            ->toArray();

        // Term-based breakdown (map payroll to terms by date)
        $termBreakdown = [];
        foreach ($allTerms as $term) {
            $termTotal = Payroll::where(function($q) use ($term) {
                if ($term->start_date && $term->end_date) {
                    $q->whereBetween('pay_date', [$term->start_date, $term->end_date]);
                }
            })->sum('net_salary');
            
            if ($termTotal > 0) {
                $termBreakdown[$term->result_period . ' ' . $term->year] = $termTotal;
            }
        }

        return view('backend.admin.finance.payroll.index', compact(
            'payrolls', 'payPeriods', 'years', 'stats', 'monthlyBreakdown', 
            'breakdownYear', 'termBreakdown', 'allTerms', 'currentTerm', 
            'selectedYear', 'selectedMonth', 'selectedTermId'
        ));
    }

    public function salaries()
    {
        $salaries = EmployeeSalary::with('user')->where('is_active', true)->paginate(20);
        return view('backend.admin.finance.payroll.salaries', compact('salaries'));
    }

    public function createSalary()
    {
        // Get users who don't have an active salary record
        $existingSalaryUserIds = EmployeeSalary::where('is_active', true)->pluck('user_id');
        
        // Get user IDs that are linked to students
        $studentUserIds = \App\Student::whereNotNull('user_id')->pluck('user_id');
        
        // Get user IDs that are linked to parents
        $parentUserIds = \App\Parents::whereNotNull('user_id')->pluck('user_id');
        
        // Combine all excluded user IDs
        $excludedUserIds = $existingSalaryUserIds->merge($studentUserIds)->merge($parentUserIds)->unique();
        
        // Get users who are not students or parents (by role AND by linked records)
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->whereIn('name', ['Student', 'Parent']);
        })->whereNotIn('id', $excludedUserIds)
          ->orderBy('name')
          ->get();

        return view('backend.admin.finance.payroll.create-salary', compact('users'));
    }

    public function storeSalary(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'basic_salary' => 'required|numeric|min:0',
            'housing_allowance' => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'medical_allowance' => 'nullable|numeric|min:0',
            'other_allowances' => 'nullable|numeric|min:0',
            'tax_deduction' => 'nullable|numeric|min:0',
            'pension_deduction' => 'nullable|numeric|min:0',
            'other_deductions' => 'nullable|numeric|min:0',
            'bank_name' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:255',
            'payment_method' => 'required|in:bank_transfer,cash,mobile_money',
        ]);

        EmployeeSalary::create([
            'user_id' => $request->user_id,
            'basic_salary' => $request->basic_salary,
            'housing_allowance' => $request->housing_allowance ?? 0,
            'transport_allowance' => $request->transport_allowance ?? 0,
            'medical_allowance' => $request->medical_allowance ?? 0,
            'other_allowances' => $request->other_allowances ?? 0,
            'tax_deduction' => $request->tax_deduction ?? 0,
            'pension_deduction' => $request->pension_deduction ?? 0,
            'other_deductions' => $request->other_deductions ?? 0,
            'bank_name' => $request->bank_name,
            'bank_account' => $request->bank_account,
            'payment_method' => $request->payment_method,
        ]);

        return redirect()->route('admin.finance.payroll.salaries')
            ->with('success', 'Employee salary configuration created successfully.');
    }

    public function editSalary($id)
    {
        $salary = EmployeeSalary::with('user')->findOrFail($id);
        return view('backend.admin.finance.payroll.edit-salary', compact('salary'));
    }

    public function updateSalary(Request $request, $id)
    {
        $salary = EmployeeSalary::findOrFail($id);

        $request->validate([
            'basic_salary' => 'required|numeric|min:0',
            'housing_allowance' => 'nullable|numeric|min:0',
            'transport_allowance' => 'nullable|numeric|min:0',
            'medical_allowance' => 'nullable|numeric|min:0',
            'other_allowances' => 'nullable|numeric|min:0',
            'tax_deduction' => 'nullable|numeric|min:0',
            'pension_deduction' => 'nullable|numeric|min:0',
            'other_deductions' => 'nullable|numeric|min:0',
            'bank_name' => 'nullable|string|max:255',
            'bank_account' => 'nullable|string|max:255',
            'payment_method' => 'required|in:bank_transfer,cash,mobile_money',
        ]);

        $salary->update([
            'basic_salary' => $request->basic_salary,
            'housing_allowance' => $request->housing_allowance ?? 0,
            'transport_allowance' => $request->transport_allowance ?? 0,
            'medical_allowance' => $request->medical_allowance ?? 0,
            'other_allowances' => $request->other_allowances ?? 0,
            'tax_deduction' => $request->tax_deduction ?? 0,
            'pension_deduction' => $request->pension_deduction ?? 0,
            'other_deductions' => $request->other_deductions ?? 0,
            'bank_name' => $request->bank_name,
            'bank_account' => $request->bank_account,
            'payment_method' => $request->payment_method,
        ]);

        return redirect()->route('admin.finance.payroll.salaries')
            ->with('success', 'Employee salary updated successfully.');
    }

    public function generate()
    {
        $currentPeriod = Carbon::now()->format('Y-m');
        $salaries = EmployeeSalary::with('user')->where('is_active', true)->get();
        
        return view('backend.admin.finance.payroll.generate', compact('salaries', 'currentPeriod'));
    }

    public function processGenerate(Request $request)
    {
        $request->validate([
            'pay_period' => 'required|date_format:Y-m',
            'pay_date' => 'required|date',
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:employee_salaries,id',
        ]);

        $payPeriod = $request->pay_period;
        $payDate = $request->pay_date;
        $created = 0;
        $skipped = 0;

        foreach ($request->employee_ids as $salaryId) {
            $salary = EmployeeSalary::find($salaryId);
            
            // Check if payroll already exists for this period
            $exists = Payroll::where('user_id', $salary->user_id)
                ->where('pay_period', $payPeriod)
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            $totalAllowances = $salary->housing_allowance + $salary->transport_allowance + 
                              $salary->medical_allowance + $salary->other_allowances;
            $totalDeductions = $salary->tax_deduction + $salary->pension_deduction + 
                              $salary->other_deductions;
            $grossSalary = $salary->basic_salary + $totalAllowances;
            $netSalary = $grossSalary - $totalDeductions;

            Payroll::create([
                'user_id' => $salary->user_id,
                'salary_id' => $salary->id,
                'pay_period' => $payPeriod,
                'pay_date' => $payDate,
                'basic_salary' => $salary->basic_salary,
                'total_allowances' => $totalAllowances,
                'total_deductions' => $totalDeductions,
                'gross_salary' => $grossSalary,
                'net_salary' => $netSalary,
                'days_worked' => 22, // Default working days
                'days_absent' => 0,
                'status' => 'pending',
            ]);

            $created++;
        }

        return redirect()->route('admin.finance.payroll.index')
            ->with('success', "Payroll generated: {$created} created, {$skipped} skipped (already exists).");
    }

    public function show($id)
    {
        $payroll = Payroll::with(['user', 'salary', 'approver', 'payer'])->findOrFail($id);
        return view('backend.admin.finance.payroll.show', compact('payroll'));
    }

    public function approve($id)
    {
        $payroll = Payroll::findOrFail($id);

        if ($payroll->status !== 'pending') {
            return back()->with('error', 'Only pending payrolls can be approved.');
        }

        $payroll->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Payroll approved successfully.');
    }

    public function markPaid($id)
    {
        $payroll = Payroll::findOrFail($id);

        if ($payroll->status !== 'approved') {
            return back()->with('error', 'Only approved payrolls can be marked as paid.');
        }

        $payroll->update([
            'status' => 'paid',
            'paid_by' => auth()->id(),
            'paid_at' => now(),
        ]);

        // Get current term for expense recording
        $currentTerm = \App\ResultsStatus::orderBy('year', 'desc')
            ->orderBy('result_period', 'desc')
            ->first();

        // Get or create Salaries/Payroll expense category
        $salaryCategory = \App\ExpenseCategory::firstOrCreate(
            ['code' => 'SAL'],
            [
                'name' => 'Salaries & Wages',
                'description' => 'Employee salaries and payroll expenses',
                'is_active' => true,
            ]
        );

        // Create expense record for payroll
        \App\Expense::create([
            'expense_number' => \App\Expense::generateExpenseNumber(),
            'expense_date' => now()->toDateString(),
            'term' => $currentTerm ? $currentTerm->result_period : 'first',
            'year' => $currentTerm ? $currentTerm->year : date('Y'),
            'category_id' => $salaryCategory->id,
            'vendor_name' => $payroll->user->name,
            'description' => 'Salary payment for ' . $payroll->user->name . ' - ' . $payroll->pay_period,
            'amount' => $payroll->net_salary,
            'payment_status' => 'paid',
            'payment_method' => $payroll->salary->payment_method ?? 'bank_transfer',
            'receipt_number' => 'PAY-' . $payroll->id,
            'created_by' => auth()->id(),
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'notes' => 'Auto-generated from Payroll #' . $payroll->id,
        ]);

        // Create cash book entry for the payment
        CashBookEntry::create([
            'entry_date' => now()->toDateString(),
            'term' => $currentTerm ? $currentTerm->result_period : 'first',
            'year' => $currentTerm ? $currentTerm->year : date('Y'),
            'reference_number' => CashBookEntry::generateReferenceNumber(),
            'transaction_type' => 'payment',
            'category' => 'salaries',
            'description' => 'Salary payment for ' . $payroll->user->name . ' - ' . $payroll->pay_period,
            'amount' => $payroll->net_salary,
            'payment_method' => $payroll->salary->payment_method ?? 'bank_transfer',
            'payer_payee' => $payroll->user->name,
            'related_payroll_id' => $payroll->id,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Payroll marked as paid and recorded in expenses.');
    }

    public function payslip($id)
    {
        $payroll = Payroll::with(['user', 'salary'])->findOrFail($id);
        return view('backend.admin.finance.payroll.payslip', compact('payroll'));
    }
}
