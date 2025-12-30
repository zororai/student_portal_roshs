<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Student;
use App\Parents;
use App\SchoolIncome;
use App\SchoolExpense;
use App\Product;
use App\StudentPayment;
use App\CashBookEntry;
use DB;

class FinanceController extends Controller
{
    public function studentPayments(Request $request)
    {
        $currentTerm = \App\ResultsStatus::with('termFees.feeType')
            ->orderBy('year', 'desc')
            ->orderBy('result_period', 'desc')
            ->first();
        
        // Get ALL students for the Record Payment modal (unfiltered)
        $allStudentsForModal = Student::with(['user', 'class', 'parent.user', 'payments.termFee.feeType'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Calculate total fees and amount paid for modal students
        foreach ($allStudentsForModal as $student) {
            $student->total_fees = $currentTerm ? floatval($currentTerm->total_fees) : 0;
            $student->amount_paid = floatval(\App\StudentPayment::where('student_id', $student->id)
                ->sum('amount_paid'));
            $student->balance = $student->total_fees - $student->amount_paid;
        }
        
        // Build query for table (with filters)
        $query = Student::with(['user', 'class', 'parent.user', 'payments.termFee.feeType']);
        
        // Apply class filter if provided
        if ($request->has('class_id') && $request->class_id != '') {
            $query->where('class_id', $request->class_id);
        }
        
        $filteredStudents = $query->orderBy('created_at', 'desc')->get();
        
        // Calculate total fees and amount paid for filtered students
        foreach ($filteredStudents as $student) {
            $student->total_fees = $currentTerm ? floatval($currentTerm->total_fees) : 0;
            $student->amount_paid = floatval(\App\StudentPayment::where('student_id', $student->id)
                ->sum('amount_paid'));
            $student->balance = $student->total_fees - $student->amount_paid;
        }
        
        // Apply status filter after calculating balances
        if ($request->has('status') && $request->status != '') {
            $status = $request->status;
            $filteredStudents = $filteredStudents->filter(function($student) use ($status) {
                $balance = $student->balance;
                $totalFees = $student->total_fees;
                $amountPaid = $student->amount_paid;
                
                if ($status === 'paid') {
                    return $balance == 0 && $totalFees > 0;
                } elseif ($status === 'partial') {
                    return $amountPaid > 0 && $balance > 0;
                } elseif ($status === 'unpaid') {
                    return $amountPaid == 0 || $balance == $totalFees;
                }
                return true;
            });
        }
        
        // Manual pagination for table
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        
        $students = new \Illuminate\Pagination\LengthAwarePaginator(
            $filteredStudents->slice($offset, $perPage)->values(),
            $filteredStudents->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        $classes = \App\Grade::orderBy('class_name')->get();
        
        // Get all available terms for the dropdown
        $allTerms = \App\ResultsStatus::with('termFees.feeType')
            ->orderBy('year', 'desc')
            ->orderBy('result_period', 'desc')
            ->get();
        
        return view('backend.finance.student-payments', compact('students', 'currentTerm', 'classes', 'allTerms', 'allStudentsForModal'));
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
        $feesPaidFor = [];

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
            $feesPaidFor[] = $termFee->feeType->name . ' ($' . number_format($amount, 2) . ')';
        }

        // Calculate student's new balance for success message
        $student = Student::find($validated['student_id']);
        $currentTerm = \App\ResultsStatus::find($validated['results_status_id']);
        $totalFees = $currentTerm ? $currentTerm->total_fees : 0;
        $totalAmountPaid = \App\StudentPayment::where('student_id', $validated['student_id'])->sum('amount_paid');
        $remainingBalance = $totalFees - $totalAmountPaid;

        // Auto-create School Income and CashBookEntry if payment was made
        if ($totalPaid > 0) {
            $studentName = $student->user->name ?? ($student->name . ' ' . $student->surname);
            $feeDescription = implode(', ', $feesPaidFor);
            $termName = $currentTerm ? ucfirst($currentTerm->result_period) . ' ' . $currentTerm->year : '';
            
            // Create School Income record
            SchoolIncome::create([
                'date' => $validated['payment_date'],
                'category' => 'School Fees',
                'description' => 'Fees Paid For: ' . $studentName . ' - ' . $termName . ' | ' . $feeDescription,
                'amount' => $totalPaid,
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'],
            ]);

            // Auto-create CashBookEntry
            $lastEntry = CashBookEntry::orderBy('id', 'desc')->first();
            $currentBalance = $lastEntry ? $lastEntry->balance : 0;
            $newBalance = $currentBalance + $totalPaid;

            $cashEntry = CashBookEntry::create([
                'entry_date' => $validated['payment_date'],
                'reference_number' => CashBookEntry::generateReferenceNumber(),
                'transaction_type' => 'receipt',
                'category' => 'school_fees',
                'description' => '[Fees Paid For] ' . $studentName . ' - ' . $feeDescription,
                'amount' => $totalPaid,
                'balance' => $newBalance,
                'payment_method' => $validated['payment_method'],
                'payer_payee' => $studentName,
                'created_by' => auth()->id(),
                'notes' => 'Auto-generated from Student Payment - ' . $studentName . ' | Term: ' . $termName,
            ]);
            $cashEntry->postToLedger();
        }

        return redirect()->route('finance.student-payments')
            ->with('success', 'Payment of $' . number_format($totalPaid, 2) . ' recorded successfully! Remaining balance: $' . number_format($remainingBalance, 2));
    }

    public function parentsArrears(Request $request)
    {
        // Get all terms for cumulative calculation
        $allTerms = \App\ResultsStatus::with('termFees')->get();
        
        // Get classes for filter
        $classes = \App\Grade::orderBy('class_name')->get();
        
        $parentsWithArrears = Parents::with(['user', 'students.user', 'students.class', 'students.payments.termFee.feeType', 'students.payments.resultsStatus'])
            ->get()
            ->map(function($parent) use ($allTerms, $request) {
                // Filter students by class if provided
                $students = $parent->students;
                if ($request->has('class_id') && $request->class_id != '') {
                    $students = $students->filter(function($student) use ($request) {
                        return $student->class_id == $request->class_id;
                    });
                }
                
                // Calculate cumulative fees from ALL terms
                $totalFees = 0;
                foreach ($allTerms as $term) {
                    $totalFees += floatval($term->total_fees) * $students->count();
                }
                
                // Calculate total paid across all terms
                $totalPaid = 0;
                foreach ($students as $student) {
                    $totalPaid += floatval(\App\StudentPayment::where('student_id', $student->id)->sum('amount_paid'));
                }
                
                $arrears = $totalFees - $totalPaid;
                
                $parent->filtered_students = $students;
                $parent->total_fees = $totalFees;
                $parent->total_paid = $totalPaid;
                $parent->arrears = $arrears;
                
                // Get arrears breakdown by term for each student
                $arrearsBreakdown = [];
                foreach ($students as $student) {
                    $studentArrears = [];
                    foreach ($allTerms as $term) {
                        $termFees = floatval($term->total_fees);
                        $termPaid = floatval(\App\StudentPayment::where('student_id', $student->id)
                            ->where('results_status_id', $term->id)
                            ->sum('amount_paid'));
                        $termArrears = $termFees - $termPaid;
                        
                        if ($termArrears > 0) {
                            $studentArrears[] = [
                                'term' => ucfirst($term->result_period) . ' ' . $term->year,
                                'term_id' => $term->id,
                                'fees' => $termFees,
                                'paid' => $termPaid,
                                'arrears' => $termArrears
                            ];
                        }
                    }
                    if (!empty($studentArrears)) {
                        $arrearsBreakdown[$student->id] = [
                            'student_name' => $student->user->name ?? $student->name,
                            'class' => $student->class->class_name ?? 'N/A',
                            'terms' => $studentArrears,
                            'total_arrears' => array_sum(array_column($studentArrears, 'arrears'))
                        ];
                    }
                }
                $parent->arrears_breakdown = $arrearsBreakdown;
                
                return $parent;
            })
            ->filter(function($parent) {
                return $parent->arrears > 0 && $parent->filtered_students->count() > 0;
            })
            ->sortByDesc('arrears');
        
        return view('backend.finance.parents-arrears', compact('parentsWithArrears', 'classes'));
    }

    public function exportParentsArrears(Request $request)
    {
        $allTerms = \App\ResultsStatus::with('termFees')->get();
        
        $parentsWithArrears = Parents::with(['user', 'students.user', 'students.class'])
            ->get()
            ->map(function($parent) use ($allTerms, $request) {
                $students = $parent->students;
                if ($request->has('class_id') && $request->class_id != '') {
                    $students = $students->filter(function($student) use ($request) {
                        return $student->class_id == $request->class_id;
                    });
                }
                
                $totalFees = 0;
                foreach ($allTerms as $term) {
                    $totalFees += floatval($term->total_fees) * $students->count();
                }
                
                $totalPaid = 0;
                foreach ($students as $student) {
                    $totalPaid += floatval(\App\StudentPayment::where('student_id', $student->id)->sum('amount_paid'));
                }
                
                $parent->filtered_students = $students;
                $parent->total_fees = $totalFees;
                $parent->total_paid = $totalPaid;
                $parent->arrears = $totalFees - $totalPaid;
                
                return $parent;
            })
            ->filter(function($parent) {
                return $parent->arrears > 0 && $parent->filtered_students->count() > 0;
            })
            ->sortByDesc('arrears');

        $filename = 'parents_arrears_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($parentsWithArrears) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, ['#', 'Parent Name', 'Email', 'Phone', 'Students', 'Total Fees', 'Amount Paid', 'Arrears']);
            
            $index = 1;
            foreach ($parentsWithArrears as $parent) {
                $studentNames = $parent->filtered_students->map(function($s) {
                    return ($s->user->name ?? $s->name) . ' (' . ($s->class->class_name ?? 'N/A') . ')';
                })->implode(', ');
                
                fputcsv($file, [
                    $index++,
                    $parent->user->name ?? 'N/A',
                    $parent->user->email ?? 'N/A',
                    $parent->phone ?? $parent->user->phone ?? 'N/A',
                    $studentNames,
                    number_format($parent->total_fees, 2),
                    number_format($parent->total_paid, 2),
                    number_format($parent->arrears, 2)
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportStudentPayments(Request $request)
    {
        $currentTerm = \App\ResultsStatus::with('termFees.feeType')
            ->orderBy('year', 'desc')
            ->orderBy('result_period', 'desc')
            ->first();
        
        $query = Student::with(['user', 'class', 'payments.termFee.feeType']);
        
        if ($request->has('class_id') && $request->class_id != '') {
            $query->where('class_id', $request->class_id);
        }
        
        $students = $query->orderBy('created_at', 'desc')->get();
        
        foreach ($students as $student) {
            $student->total_fees = $currentTerm ? floatval($currentTerm->total_fees) : 0;
            $student->amount_paid = floatval(\App\StudentPayment::where('student_id', $student->id)->sum('amount_paid'));
            $student->balance = $student->total_fees - $student->amount_paid;
            
            if ($student->balance == 0 && $student->total_fees > 0) {
                $student->status = 'Fully Paid';
            } elseif ($student->amount_paid > 0 && $student->balance > 0) {
                $student->status = 'Partially Paid';
            } else {
                $student->status = 'Unpaid';
            }
        }
        
        if ($request->has('status') && $request->status != '') {
            $status = $request->status;
            $students = $students->filter(function($student) use ($status) {
                if ($status === 'paid') return $student->status === 'Fully Paid';
                if ($status === 'partial') return $student->status === 'Partially Paid';
                if ($status === 'unpaid') return $student->status === 'Unpaid';
                return true;
            });
        }

        $filename = 'student_payments_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($students) {
            $file = fopen('php://output', 'w');
            
            // CSV Header
            fputcsv($file, ['#', 'Roll Number', 'Student Name', 'Class', 'Total Fees', 'Amount Paid', 'Balance', 'Status']);
            
            $index = 1;
            foreach ($students as $student) {
                fputcsv($file, [
                    $index++,
                    $student->roll_number,
                    $student->user->name ?? $student->name,
                    $student->class->class_name ?? 'N/A',
                    number_format($student->total_fees, 2),
                    number_format($student->amount_paid, 2),
                    number_format($student->balance, 2),
                    $student->status
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function schoolIncome(Request $request)
    {
        // Get available years and terms for filter
        $years = range(date('Y'), date('Y') - 5);
        $terms = ['first' => 'First Term', 'second' => 'Second Term', 'third' => 'Third Term'];
        
        // Term date ranges
        $termDateRanges = [
            'first' => ['01-01', '04-30'],
            'second' => ['05-01', '08-31'],
            'third' => ['09-01', '12-31'],
        ];
        
        $selectedYear = $request->year;
        $selectedTerm = $request->term;
        
        // Build date range filter
        $dateFrom = null;
        $dateTo = null;
        if ($selectedYear && $selectedTerm && isset($termDateRanges[$selectedTerm])) {
            $dateFrom = $selectedYear . '-' . $termDateRanges[$selectedTerm][0];
            $dateTo = $selectedYear . '-' . $termDateRanges[$selectedTerm][1];
        } elseif ($selectedYear) {
            $dateFrom = $selectedYear . '-01-01';
            $dateTo = $selectedYear . '-12-31';
        }
        
        // Get manual income records
        $manualQuery = SchoolIncome::orderBy('date', 'desc');
        if ($dateFrom && $dateTo) {
            $manualQuery->whereBetween('date', [$dateFrom, $dateTo]);
        }
        $manualIncomes = $manualQuery->get()->map(function($income) {
            return [
                'id' => $income->id,
                'date' => $income->date,
                'description' => $income->description,
                'category' => $income->category ?? 'General',
                'amount' => $income->amount,
                'source' => 'manual',
                'deletable' => true
            ];
        });

        // Get student payments as income
        $paymentQuery = StudentPayment::with('student')->orderBy('payment_date', 'desc');
        if ($dateFrom && $dateTo) {
            $paymentQuery->whereBetween('payment_date', [$dateFrom, $dateTo]);
        }
        $studentPayments = $paymentQuery->get()->map(function($payment) {
                $studentName = $payment->student ? $payment->student->name . ' ' . $payment->student->surname : 'Unknown Student';
                return [
                    'id' => 'sp_' . $payment->id,
                    'date' => $payment->payment_date,
                    'description' => 'Student Payment - ' . $studentName,
                    'category' => 'Student Fees',
                    'amount' => $payment->amount_paid,
                    'source' => 'student_payment',
                    'deletable' => false
                ];
            });

        // Get product sales as income
        $productQuery = Product::where('quantity_sold', '>', 0);
        if ($dateFrom && $dateTo) {
            $productQuery->whereBetween('updated_at', [$dateFrom, $dateTo]);
        }
        $productSales = $productQuery->get()->map(function($product) {
                return [
                    'id' => 'prod_' . $product->id,
                    'date' => $product->updated_at,
                    'description' => 'Product Sale - ' . $product->name . ' (' . $product->quantity_sold . ' units)',
                    'category' => 'Products Sold',
                    'amount' => $product->price * $product->quantity_sold,
                    'source' => 'product',
                    'deletable' => false
                ];
            });

        // Combine all income sources
        $allIncomes = $manualIncomes->concat($studentPayments)->concat($productSales);
        
        // Sort by date descending
        $allIncomes = $allIncomes->sortByDesc('date')->values();
        
        // Calculate totals (filtered)
        $totalManualIncome = $manualIncomes->sum('amount');
        $totalStudentPayments = $studentPayments->sum('amount');
        $totalProductSales = $productSales->sum('amount');
        $totalIncome = $totalManualIncome + $totalStudentPayments + $totalProductSales;

        // Paginate the combined collection
        $page = request()->get('page', 1);
        $perPage = 20;
        $incomes = new \Illuminate\Pagination\LengthAwarePaginator(
            $allIncomes->forPage($page, $perPage),
            $allIncomes->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
        
        return view('backend.finance.school-income', compact('incomes', 'totalIncome', 'totalManualIncome', 'totalStudentPayments', 'totalProductSales', 'years', 'terms', 'selectedYear', 'selectedTerm'));
    }

    public function schoolExpenses()
    {
        $expenses = SchoolExpense::orderBy('date', 'desc')->paginate(20);
        $totalExpenses = SchoolExpense::sum('amount');
        
        return view('backend.finance.school-expenses', compact('expenses', 'totalExpenses'));
    }

    public function products(Request $request)
    {
        // Year and term filter setup
        $years = range(date('Y'), date('Y') - 5);
        $terms = ['first' => 'First Term', 'second' => 'Second Term', 'third' => 'Third Term'];
        $termDateRanges = [
            'first' => ['01-01', '04-30'],
            'second' => ['05-01', '08-31'],
            'third' => ['09-01', '12-31'],
        ];
        
        $selectedYear = $request->year;
        $selectedTerm = $request->term;
        
        $query = Product::orderBy('created_at', 'desc');
        
        if ($selectedYear && $selectedTerm && isset($termDateRanges[$selectedTerm])) {
            $dateFrom = $selectedYear . '-' . $termDateRanges[$selectedTerm][0];
            $dateTo = $selectedYear . '-' . $termDateRanges[$selectedTerm][1];
            $query->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59']);
        } elseif ($selectedYear) {
            $query->whereYear('created_at', $selectedYear);
        }
        
        $products = $query->paginate(20)->appends($request->query());
        $totalRevenue = (clone $query)->sum(\DB::raw('price * quantity_sold'));
        
        return view('backend.finance.products', compact('products', 'totalRevenue', 'years', 'terms', 'selectedYear', 'selectedTerm'));
    }

    public function storeIncome(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $income = SchoolIncome::create($validated);

        // Auto-create CashBookEntry
        $lastEntry = CashBookEntry::orderBy('id', 'desc')->first();
        $currentBalance = $lastEntry ? $lastEntry->balance : 0;
        $newBalance = $currentBalance + $validated['amount'];

        $cashEntry = CashBookEntry::create([
            'entry_date' => $validated['date'],
            'reference_number' => CashBookEntry::generateReferenceNumber(),
            'transaction_type' => 'receipt',
            'category' => 'other_income',
            'description' => '[School Income] ' . $validated['description'],
            'amount' => $validated['amount'],
            'balance' => $newBalance,
            'payment_method' => 'cash',
            'payer_payee' => $validated['category'],
            'created_by' => auth()->id(),
            'notes' => 'Auto-generated from School Income #' . $income->id,
        ]);
        $cashEntry->postToLedger();

        return redirect()->route('finance.school-income')->with('success', 'Income recorded successfully!');
    }

    public function destroyIncome($id)
    {
        $income = SchoolIncome::findOrFail($id);
        
        // Delete related CashBookEntry
        CashBookEntry::where('notes', 'Auto-generated from School Income #' . $id)->delete();
        
        $income->delete();

        return redirect()->route('finance.school-income')->with('success', 'Income record deleted!');
    }

    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'description' => 'required|string|max:255',
            'category' => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $expense = SchoolExpense::create($validated);

        // Auto-create CashBookEntry
        $lastEntry = CashBookEntry::orderBy('id', 'desc')->first();
        $currentBalance = $lastEntry ? $lastEntry->balance : 0;
        $newBalance = $currentBalance - $validated['amount'];

        $cashEntry = CashBookEntry::create([
            'entry_date' => $validated['date'],
            'reference_number' => CashBookEntry::generateReferenceNumber(),
            'transaction_type' => 'payment',
            'category' => 'other_expense',
            'description' => '[School Expense] ' . $validated['description'],
            'amount' => $validated['amount'],
            'balance' => $newBalance,
            'payment_method' => 'cash',
            'payer_payee' => $validated['category'],
            'created_by' => auth()->id(),
            'notes' => 'Auto-generated from School Expense #' . $expense->id,
        ]);
        $cashEntry->postToLedger();

        return redirect()->route('finance.school-expenses')->with('success', 'Expense recorded successfully!');
    }

    public function destroyExpense($id)
    {
        $expense = SchoolExpense::findOrFail($id);
        
        // Delete related CashBookEntry
        CashBookEntry::where('notes', 'Auto-generated from School Expense #' . $id)->delete();
        
        $expense->delete();

        return redirect()->route('finance.school-expenses')->with('success', 'Expense record deleted!');
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'price' => 'required|numeric|min:0',
            'quantity_sold' => 'required|integer|min:0',
        ]);

        $product = Product::create($validated);

        // Auto-create School Income and CashBookEntry if quantity sold > 0
        $totalSale = $validated['price'] * $validated['quantity_sold'];
        if ($totalSale > 0) {
            // Create School Income record
            $income = SchoolIncome::create([
                'date' => now()->format('Y-m-d'),
                'category' => 'Products Sold',
                'description' => 'Product Sale: ' . $validated['name'] . ' (' . $validated['quantity_sold'] . ' units @ $' . number_format($validated['price'], 2) . ')',
                'amount' => $totalSale,
            ]);

            // Auto-create CashBookEntry
            $lastEntry = CashBookEntry::orderBy('id', 'desc')->first();
            $currentBalance = $lastEntry ? $lastEntry->balance : 0;
            $newBalance = $currentBalance + $totalSale;

            $cashEntry = CashBookEntry::create([
                'entry_date' => now()->format('Y-m-d'),
                'reference_number' => CashBookEntry::generateReferenceNumber(),
                'transaction_type' => 'receipt',
                'category' => 'other_income',
                'description' => '[Product Sale] ' . $validated['name'] . ' (' . $validated['quantity_sold'] . ' units)',
                'amount' => $totalSale,
                'balance' => $newBalance,
                'payment_method' => 'cash',
                'payer_payee' => 'Product Sales',
                'created_by' => auth()->id(),
                'notes' => 'Auto-generated from Product #' . $product->id,
            ]);
            $cashEntry->postToLedger();
        }

        return redirect()->route('finance.products')->with('success', 'Product added successfully!');
    }

    public function destroyProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('finance.products')->with('success', 'Product deleted!');
    }

    public function financialStatements(Request $request)
    {
        // Year and term filter setup
        $years = range(date('Y'), date('Y') - 5);
        $terms = ['first' => 'First Term', 'second' => 'Second Term', 'third' => 'Third Term'];
        $termDateRanges = [
            'first' => ['01-01', '04-30'],
            'second' => ['05-01', '08-31'],
            'third' => ['09-01', '12-31'],
        ];
        
        $selectedYear = $request->year;
        $selectedTerm = $request->term;
        
        // Build base query with date filter
        $incomeQuery = CashBookEntry::where('transaction_type', 'receipt');
        $expenseQuery = CashBookEntry::where('transaction_type', 'payment');
        
        if ($selectedYear && $selectedTerm && isset($termDateRanges[$selectedTerm])) {
            $dateFrom = $selectedYear . '-' . $termDateRanges[$selectedTerm][0];
            $dateTo = $selectedYear . '-' . $termDateRanges[$selectedTerm][1];
            $incomeQuery->whereBetween('entry_date', [$dateFrom, $dateTo]);
            $expenseQuery->whereBetween('entry_date', [$dateFrom, $dateTo]);
        } elseif ($selectedYear) {
            $incomeQuery->whereYear('entry_date', $selectedYear);
            $expenseQuery->whereYear('entry_date', $selectedYear);
        }
        
        // Pull from CashBookEntry for complete financial picture
        $totalIncome = (clone $incomeQuery)->sum('amount');
        $totalExpenses = (clone $expenseQuery)->sum('amount');
        $netProfit = $totalIncome - $totalExpenses;
        
        $incomeByCategory = (clone $incomeQuery)
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();
        
        $expensesByCategory = (clone $expenseQuery)
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->get();
        
        $monthlyIncome = (clone $incomeQuery)
            ->select(
                DB::raw('MONTH(entry_date) as month'),
                DB::raw('YEAR(entry_date) as year'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();
        
        $monthlyExpenses = (clone $expenseQuery)
            ->select(
                DB::raw('MONTH(entry_date) as month'),
                DB::raw('YEAR(entry_date) as year'),
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
            'monthlyExpenses',
            'years',
            'terms',
            'selectedYear',
            'selectedTerm'
        ));
    }
}
