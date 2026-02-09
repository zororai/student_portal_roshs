<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Supplier;
use App\SupplierInvoice;
use App\SupplierPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountsPayableController extends Controller
{
    /**
     * Display A/P dashboard
     */
    public function index()
    {
        $totalPayables = SupplierInvoice::whereIn('status', ['unpaid', 'partial'])->sum('amount') 
                       - SupplierInvoice::whereIn('status', ['unpaid', 'partial'])->sum('paid_amount');
        
        $overdueInvoices = SupplierInvoice::where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->count();
        
        $recentInvoices = SupplierInvoice::with('supplier')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        $recentPayments = SupplierPayment::with('invoice.supplier')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('backend.finance.payables.index', compact(
            'totalPayables',
            'overdueInvoices',
            'recentInvoices',
            'recentPayments'
        ));
    }

    /**
     * Display list of supplier invoices
     */
    public function invoices(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('search');
        
        $query = SupplierInvoice::with('supplier');
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('supplier_invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        $invoices = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('backend.finance.payables.invoices', compact('invoices', 'status', 'search'));
    }

    /**
     * Show form for creating new supplier invoice
     */
    public function createInvoice()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        
        $expenseCategories = [
            'salaries' => 'Salaries & Wages',
            'utilities' => 'Utilities',
            'electricity' => 'Electricity',
            'water' => 'Water',
            'maintenance' => 'Maintenance & Repairs',
            'teaching_materials' => 'Teaching Materials',
            'office_supplies' => 'Office Supplies',
            'transport' => 'Transport & Fuel',
            'food' => 'Food & Catering',
        ];
        
        return view('backend.finance.payables.create-invoice', compact('suppliers', 'expenseCategories'));
    }

    /**
     * Store a new supplier invoice
     */
    public function storeInvoice(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'supplier_invoice_number' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'description' => 'required|string',
            'expense_type' => 'required|in:expense,asset',
            'expense_category' => 'required_if:expense_type,expense',
        ]);

        DB::beginTransaction();
        
        try {
            // Create invoice
            $invoice = SupplierInvoice::create([
                'invoice_number' => SupplierInvoice::generateInvoiceNumber(),
                'supplier_id' => $request->supplier_id,
                'supplier_invoice_number' => $request->supplier_invoice_number,
                'amount' => $request->amount,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'description' => $request->description,
                'expense_type' => $request->expense_type,
                'expense_category' => $request->expense_category,
                'status' => 'unpaid',
                'created_by' => Auth::id(),
            ]);

            // Post to ledger
            $invoice->postToLedger();

            DB::commit();

            return redirect()->route('finance.payables.invoices.show', $invoice->id)
                ->with('success', 'Supplier invoice created and posted to ledger successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to create invoice: ' . $e->getMessage());
        }
    }

    /**
     * Display supplier invoice details
     */
    public function showInvoice($id)
    {
        $invoice = SupplierInvoice::with(['supplier', 'payments', 'creator'])->findOrFail($id);
        
        return view('backend.finance.payables.show-invoice', compact('invoice'));
    }

    /**
     * Show payment form
     */
    public function showPaymentForm($id)
    {
        $invoice = SupplierInvoice::with('supplier')->findOrFail($id);
        
        if ($invoice->status === 'paid') {
            return redirect()->route('finance.payables.invoices.show', $invoice->id)
                ->with('error', 'This invoice is already fully paid');
        }
        
        $outstandingAmount = $invoice->getOutstandingAmount();
        
        return view('backend.finance.payables.payment-form', compact('invoice', 'outstandingAmount'));
    }

    /**
     * Record payment against supplier invoice
     */
    public function recordPayment(Request $request, $id)
    {
        $invoice = SupplierInvoice::findOrFail($id);
        
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->getOutstandingAmount(),
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank,cheque',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        
        try {
            // Create payment record
            $payment = SupplierPayment::create([
                'payment_number' => SupplierPayment::generatePaymentNumber(),
                'supplier_invoice_id' => $invoice->id,
                'amount' => $request->amount,
                'payment_date' => $request->payment_date,
                'payment_method' => $request->payment_method,
                'reference' => $request->reference,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
            ]);

            // Post to ledger and update invoice
            $payment->postToLedger();

            DB::commit();

            return redirect()->route('finance.payables.invoices.show', $invoice->id)
                ->with('success', 'Payment recorded and posted to ledger successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Failed to record payment: ' . $e->getMessage());
        }
    }

    /**
     * Display A/P aging report
     */
    public function aging(Request $request)
    {
        $asOfDate = $request->input('as_of_date', now()->toDateString());
        
        $invoices = SupplierInvoice::with('supplier')
            ->whereIn('status', ['unpaid', 'partial'])
            ->get();
        
        $agingData = [];
        
        foreach ($invoices as $invoice) {
            $daysOverdue = now()->diffInDays($invoice->due_date, false);
            $outstanding = $invoice->getOutstandingAmount();
            
            $supplierId = $invoice->supplier_id;
            
            if (!isset($agingData[$supplierId])) {
                $agingData[$supplierId] = [
                    'supplier' => $invoice->supplier,
                    'current' => 0,
                    '30_days' => 0,
                    '60_days' => 0,
                    '90_plus_days' => 0,
                    'total' => 0,
                ];
            }
            
            if ($daysOverdue >= 0) {
                $agingData[$supplierId]['current'] += $outstanding;
            } elseif ($daysOverdue >= -30) {
                $agingData[$supplierId]['30_days'] += $outstanding;
            } elseif ($daysOverdue >= -60) {
                $agingData[$supplierId]['60_days'] += $outstanding;
            } else {
                $agingData[$supplierId]['90_plus_days'] += $outstanding;
            }
            
            $agingData[$supplierId]['total'] += $outstanding;
        }
        
        // Convert to array and sort by total
        $agingData = array_values($agingData);
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
        
        return view('backend.finance.payables.aging', compact('agingData', 'totals', 'asOfDate'));
    }
}
