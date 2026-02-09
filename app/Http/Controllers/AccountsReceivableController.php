<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Student;
use App\StudentAccount;
use App\StudentInvoice;
use App\StudentInvoiceItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountsReceivableController extends Controller
{
    /**
     * Display A/R dashboard
     */
    public function index()
    {
        $totalReceivables = StudentAccount::sum('current_balance');
        $overdueInvoices = StudentInvoice::where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->count();
        
        $recentInvoices = StudentInvoice::with('student')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        // Get aging summary
        $agingSummary = [
            'current' => 0,
            '30_days' => 0,
            '60_days' => 0,
            '90_plus_days' => 0,
        ];
        
        $accounts = StudentAccount::where('current_balance', '>', 0)->get();
        foreach ($accounts as $account) {
            $aging = $account->getAgingBreakdown();
            $agingSummary['current'] += $aging['current'];
            $agingSummary['30_days'] += $aging['30_days'];
            $agingSummary['60_days'] += $aging['60_days'];
            $agingSummary['90_plus_days'] += $aging['90_plus_days'];
        }
        
        return view('backend.finance.receivables.index', compact(
            'totalReceivables',
            'overdueInvoices',
            'recentInvoices',
            'agingSummary'
        ));
    }

    /**
     * Display list of invoices
     */
    public function invoices(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('search');
        
        $query = StudentInvoice::with('student');
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('student', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        $invoices = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('backend.finance.receivables.invoices', compact('invoices', 'status', 'search'));
    }

    /**
     * Show form for creating new invoice
     */
    public function createInvoice()
    {
        $students = Student::orderBy('name')->get();
        
        return view('backend.finance.receivables.create-invoice', compact('students'));
    }

    /**
     * Store a new invoice
     */
    public function storeInvoice(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'term' => 'nullable|string',
            'year' => 'required|integer',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'description' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.amount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        
        try {
            // Calculate total amount
            $totalAmount = array_sum(array_column($request->items, 'amount'));
            
            // Create invoice
            $invoice = StudentInvoice::create([
                'invoice_number' => StudentInvoice::generateInvoiceNumber(),
                'student_id' => $request->student_id,
                'term' => $request->term,
                'year' => $request->year,
                'amount' => $totalAmount,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'description' => $request->description,
                'status' => 'unpaid',
                'created_by' => Auth::id(),
            ]);

            // Create invoice items
            foreach ($request->items as $item) {
                StudentInvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'amount' => $item['amount'],
                ]);
            }

            // Post to ledger
            $invoice->postToLedger();

            // Ensure student account exists and update balance
            $studentAccount = StudentAccount::firstOrCreate(
                ['student_id' => $request->student_id],
                ['opening_balance' => 0, 'current_balance' => 0]
            );
            $studentAccount->updateBalance();

            DB::commit();

            return redirect()->route('finance.receivables.invoices.show', $invoice->id)
                ->with('success', 'Invoice created and posted to ledger successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create invoice: ' . $e->getMessage());
        }
    }

    /**
     * Display invoice details
     */
    public function showInvoice($id)
    {
        $invoice = StudentInvoice::with(['student', 'items', 'creator'])->findOrFail($id);
        
        return view('backend.finance.receivables.show-invoice', compact('invoice'));
    }

    /**
     * Display A/R aging report
     */
    public function aging(Request $request)
    {
        $asOfDate = $request->input('as_of_date', now()->toDateString());
        
        $accounts = StudentAccount::with('student')
            ->where('current_balance', '>', 0)
            ->get();
        
        $agingData = [];
        
        foreach ($accounts as $account) {
            $aging = $account->getAgingBreakdown();
            
            if ($aging['total'] > 0) {
                $agingData[] = [
                    'student' => $account->student,
                    'current' => $aging['current'],
                    '30_days' => $aging['30_days'],
                    '60_days' => $aging['60_days'],
                    '90_plus_days' => $aging['90_plus_days'],
                    'total' => $aging['total'],
                ];
            }
        }
        
        // Sort by total descending
        usort($agingData, function($a, $b) {
            return $b['total'] <=> $a['total'];
        });
        
        // Calculate totals
        $totals = [
            'current' => array_sum(array_column($agingData, 'current')),
            '30_days' => array_sum(array_column($agingData, '30_days')),
            '60_days' => array_sum(array_column($agingData, '60_days')),
            '90_plus_days' => array_sum(array_column($agingData, '90_plus_days')),
            'total' => array_sum(array_column($agingData, 'total')),
        ];
        
        return view('backend.finance.receivables.aging', compact('agingData', 'totals', 'asOfDate'));
    }

    /**
     * Display student statement
     */
    public function studentStatement($studentId)
    {
        $student = Student::findOrFail($studentId);
        
        $account = StudentAccount::firstOrCreate(
            ['student_id' => $studentId],
            ['opening_balance' => 0, 'current_balance' => 0]
        );
        
        $invoices = StudentInvoice::where('student_id', $studentId)
            ->with('items')
            ->orderBy('invoice_date', 'desc')
            ->get();
        
        return view('backend.finance.receivables.statement', compact('student', 'account', 'invoices'));
    }
}
