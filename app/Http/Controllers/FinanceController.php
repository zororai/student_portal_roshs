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
    public function studentPayments()
    {
        $students = Student::with(['user', 'class', 'parent.user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('backend.finance.student-payments', compact('students'));
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
