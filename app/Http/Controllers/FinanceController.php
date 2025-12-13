<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Student;
use App\Parents;
use App\SchoolIncome;
use App\SchoolExpense;
use App\Product;
use DB;

class FinanceController extends Controller
{
    public function studentPayments(Request $request)
    {
        $query = Student::with(['user', 'class', 'parent.user', 'payments.termFee.feeType']);
        
        // Apply class filter if provided
        if ($request->has('class_id') && $request->class_id != '') {
            $query->where('class_id', $request->class_id);
        }
        
        // Apply search filter if provided
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            })->orWhere('roll_number', 'like', '%' . $search . '%');
        }
        
        // Apply status filter if provided
        if ($request->has('status') && $request->status != '') {
            // This will be handled in the view since we need to calculate balance
        }
        
        $students = $query->orderBy('created_at', 'desc')->paginate(20);
        
        $currentTerm = \App\ResultsStatus::with('termFees.feeType')
            ->orderBy('year', 'desc')
            ->orderBy('result_period', 'desc')
            ->first();
        
        $classes = \App\Grade::orderBy('class_name')->get();
        
        return view('backend.finance.student-payments', compact('students', 'currentTerm', 'classes'));
    }

    public function storePayment(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'results_status_id' => 'required|exists:results_statuses,id',
            'fee_amounts' => 'required|array',
            'fee_amounts.*' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $totalPaid = 0;

        foreach ($validated['fee_amounts'] as $termFeeId => $amount) {
            // Skip if amount is 0 or empty
            if ($amount <= 0) {
                continue;
            }

            $termFee = \App\TermFee::findOrFail($termFeeId);
            
            // Validate amount doesn't exceed fee amount
            if ($amount > $termFee->amount) {
                return redirect()->back()
                    ->withErrors(['fee_amounts' => 'Payment amount cannot exceed the fee amount for ' . $termFee->feeType->name])
                    ->withInput();
            }
            
            \App\StudentPayment::create([
                'student_id' => $validated['student_id'],
                'results_status_id' => $validated['results_status_id'],
                'term_fee_id' => $termFeeId,
                'amount_paid' => $amount,
                'payment_date' => $validated['payment_date'],
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'],
                'notes' => $validated['notes'],
            ]);

            $totalPaid += $amount;
        }

        return redirect()->route('finance.student-payments')
            ->with('success', 'Payment of $' . number_format($totalPaid, 2) . ' recorded successfully!');
    }

    public function parentsArrears()
    {
        $parentsWithArrears = Parents::with(['user', 'students.user', 'students.class'])
            ->get()
            ->map(function($parent) {
                $totalFees = $parent->students->sum(function($student) {
                    return $student->total_fees ?? 0;
                });
                $totalPaid = $parent->students->sum(function($student) {
                    return $student->amount_paid ?? 0;
                });
                $arrears = $totalFees - $totalPaid;
                
                $parent->total_fees = $totalFees;
                $parent->total_paid = $totalPaid;
                $parent->arrears = $arrears;
                
                return $parent;
            })
            ->filter(function($parent) {
                return $parent->arrears > 0;
            })
            ->sortByDesc('arrears');
        
        return view('backend.finance.parents-arrears', compact('parentsWithArrears'));
    }

    public function schoolIncome()
    {
        $incomes = SchoolIncome::orderBy('date', 'desc')->paginate(20);
        $totalIncome = SchoolIncome::sum('amount');
        
        return view('backend.finance.school-income', compact('incomes', 'totalIncome'));
    }

    public function schoolExpenses()
    {
        $expenses = SchoolExpense::orderBy('date', 'desc')->paginate(20);
        $totalExpenses = SchoolExpense::sum('amount');
        
        return view('backend.finance.school-expenses', compact('expenses', 'totalExpenses'));
    }

    public function products()
    {
        $products = Product::orderBy('created_at', 'desc')->paginate(20);
        $totalRevenue = Product::sum(DB::raw('price * quantity_sold'));
        
        return view('backend.finance.products', compact('products', 'totalRevenue'));
    }

    public function financialStatements()
    {
        $totalIncome = SchoolIncome::sum('amount');
        $totalExpenses = SchoolExpense::sum('amount');
        $netProfit = $totalIncome - $totalExpenses;
        
        $incomeByCategory = SchoolIncome::select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();
        
        $expensesByCategory = SchoolExpense::select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();
        
        $monthlyIncome = SchoolIncome::select(
                DB::raw('MONTH(date) as month'),
                DB::raw('YEAR(date) as year'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();
        
        $monthlyExpenses = SchoolExpense::select(
                DB::raw('MONTH(date) as month'),
                DB::raw('YEAR(date) as year'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();
        
        return view('backend.finance.statements', compact(
            'totalIncome',
            'totalExpenses',
            'netProfit',
            'incomeByCategory',
            'expensesByCategory',
            'monthlyIncome',
            'monthlyExpenses'
        ));
    }
}
