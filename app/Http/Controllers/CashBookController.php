<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CashBookEntry;
use Carbon\Carbon;

class CashBookController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin']);
    }

    public function index(Request $request)
    {
        // Year and term filter setup
        $years = range(date('Y'), date('Y') - 5);
        $terms = ['first' => 'First Term', 'second' => 'Second Term', 'third' => 'Third Term'];
        
        $selectedYear = $request->year;
        $selectedTerm = $request->term;
        
        $query = CashBookEntry::with(['creator', 'payroll']);

        // Apply year/term filter using term/year fields
        if ($selectedYear) {
            $query->where('year', $selectedYear);
        }
        if ($selectedTerm) {
            $query->where('term', $selectedTerm);
        }

        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $entries = $query->orderBy('entry_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->appends($request->query());

        // Calculate totals based on filter
        $statsQuery = CashBookEntry::query();
        if ($selectedYear) {
            $statsQuery->where('year', $selectedYear);
        }
        if ($selectedTerm) {
            $statsQuery->where('term', $selectedTerm);
        }
        
        $totalReceipts = (clone $statsQuery)->where('transaction_type', 'receipt')->sum('amount');
        $totalPayments = (clone $statsQuery)->where('transaction_type', 'payment')->sum('amount');
        $balance = $totalReceipts - $totalPayments;

        // Today's summary
        $todayReceipts = CashBookEntry::where('transaction_type', 'receipt')
            ->whereDate('entry_date', today())
            ->sum('amount');
        $todayPayments = CashBookEntry::where('transaction_type', 'payment')
            ->whereDate('entry_date', today())
            ->sum('amount');

        $categories = CashBookEntry::getCategories();

        // Calculate outstanding student fees with balance B/F
        $allTermsForCalc = \App\ResultsStatus::with(['termFees.feeType', 'feeStructures.feeType', 'feeStructures.feeLevelGroup'])
            ->orderBy('year', 'asc')
            ->orderBy('result_period', 'asc')
            ->get();
        
        $currentTerm = \App\ResultsStatus::orderBy('year', 'desc')->orderBy('result_period', 'desc')->first();
        
        $totalStudentFees = 0;
        $totalBalanceBf = 0;
        $totalCurrentTermFees = 0;
        $totalStudentPayments = 0;
        
        $students = \App\Student::with(['class', 'payments'])->get();
        foreach ($students as $student) {
            $feeBreakdown = $this->calculateCumulativeFees($student, $allTermsForCalc, $currentTerm);
            $totalBalanceBf += $feeBreakdown['balance_bf'];
            $totalCurrentTermFees += $feeBreakdown['current_term_fees'];
            $totalStudentFees += $feeBreakdown['total_fees'];
            $totalStudentPayments += floatval(\App\StudentPayment::where('student_id', $student->id)->sum('amount_paid'));
        }
        
        $totalOutstandingFees = $totalStudentFees - $totalStudentPayments;

        return view('backend.admin.finance.cashbook.index', compact(
            'entries', 'totalReceipts', 'totalPayments', 'balance',
            'todayReceipts', 'todayPayments', 'categories',
            'years', 'terms', 'selectedYear', 'selectedTerm',
            'totalStudentFees', 'totalBalanceBf', 'totalCurrentTermFees', 'totalStudentPayments', 'totalOutstandingFees'
        ));
    }

    public function create()
    {
        $categories = CashBookEntry::getCategories();
        return view('backend.admin.finance.cashbook.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'entry_date' => 'required|date',
            'term' => 'required|string|in:first,second,third',
            'year' => 'required|integer',
            'transaction_type' => 'required|in:receipt,payment',
            'category' => 'required|string',
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string',
            'payer_payee' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        // Calculate running balance
        $lastEntry = CashBookEntry::orderBy('id', 'desc')->first();
        $previousBalance = $lastEntry ? $lastEntry->balance : 0;

        if ($request->transaction_type === 'receipt') {
            $newBalance = $previousBalance + $request->amount;
        } else {
            $newBalance = $previousBalance - $request->amount;
        }

        $entry = CashBookEntry::create([
            'entry_date' => $request->entry_date,
            'term' => $request->term,
            'year' => $request->year,
            'reference_number' => CashBookEntry::generateReferenceNumber(),
            'transaction_type' => $request->transaction_type,
            'category' => $request->category,
            'description' => $request->description,
            'amount' => $request->amount,
            'balance' => $newBalance,
            'payment_method' => $request->payment_method,
            'payer_payee' => $request->payer_payee,
            'created_by' => auth()->id(),
            'notes' => $request->notes,
        ]);

        // Auto-post to General Ledger
        $entry->postToLedger();

        return redirect()->route('admin.finance.cashbook.index')
            ->with('success', 'Cash book entry created successfully.');
    }

    public function show($id)
    {
        $entry = CashBookEntry::with(['creator', 'payroll', 'ledgerEntries'])->findOrFail($id);
        return view('backend.admin.finance.cashbook.show', compact('entry'));
    }

    public function edit($id)
    {
        $entry = CashBookEntry::findOrFail($id);
        $categories = CashBookEntry::getCategories();
        return view('backend.admin.finance.cashbook.edit', compact('entry', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $entry = CashBookEntry::findOrFail($id);

        $request->validate([
            'entry_date' => 'required|date',
            'category' => 'required|string',
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string',
            'payer_payee' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $entry->update([
            'entry_date' => $request->entry_date,
            'category' => $request->category,
            'description' => $request->description,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'payer_payee' => $request->payer_payee,
            'notes' => $request->notes,
        ]);

        // Recalculate balances
        $this->recalculateBalances();

        return redirect()->route('admin.finance.cashbook.index')
            ->with('success', 'Cash book entry updated successfully.');
    }

    public function destroy($id)
    {
        $entry = CashBookEntry::findOrFail($id);

        if ($entry->related_payroll_id) {
            return back()->with('error', 'Cannot delete entries linked to payroll.');
        }

        $entry->delete();

        // Recalculate balances
        $this->recalculateBalances();

        return redirect()->route('admin.finance.cashbook.index')
            ->with('success', 'Cash book entry deleted successfully.');
    }

    private function recalculateBalances()
    {
        $entries = CashBookEntry::orderBy('entry_date')->orderBy('id')->get();
        $balance = 0;

        foreach ($entries as $entry) {
            if ($entry->transaction_type === 'receipt') {
                $balance += $entry->amount;
            } else {
                $balance -= $entry->amount;
            }
            $entry->update(['balance' => $balance]);
        }
    }

    public function report(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', Carbon::now()->toDateString());

        $entries = CashBookEntry::whereBetween('entry_date', [$dateFrom, $dateTo])
            ->orderBy('entry_date')
            ->orderBy('id')
            ->get();

        $openingBalance = CashBookEntry::where('entry_date', '<', $dateFrom)
            ->orderBy('id', 'desc')
            ->first();
        $openingBalanceAmount = $openingBalance ? $openingBalance->balance : 0;

        $totalReceipts = $entries->where('transaction_type', 'receipt')->sum('amount');
        $totalPayments = $entries->where('transaction_type', 'payment')->sum('amount');
        $closingBalance = $openingBalanceAmount + $totalReceipts - $totalPayments;

        // Group by category
        $receiptsByCategory = $entries->where('transaction_type', 'receipt')
            ->groupBy('category')
            ->map(function ($items) {
                return $items->sum('amount');
            });

        $paymentsByCategory = $entries->where('transaction_type', 'payment')
            ->groupBy('category')
            ->map(function ($items) {
                return $items->sum('amount');
            });

        return view('backend.admin.finance.cashbook.report', compact(
            'entries', 'dateFrom', 'dateTo', 'openingBalanceAmount',
            'totalReceipts', 'totalPayments', 'closingBalance',
            'receiptsByCategory', 'paymentsByCategory'
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
