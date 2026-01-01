<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PurchaseOrder;
use App\PurchaseOrderItem;
use App\Supplier;
use App\SchoolExpense;
use App\CashBookEntry;

class PurchaseOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['supplier', 'creator']);
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $orders = $query->orderBy('order_date', 'desc')->paginate(20);
        $suppliers = Supplier::where('is_active', true)->get();
        
        return view('backend.admin.finance.purchase-orders.index', compact('orders', 'suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->get();
        return view('backend.admin.finance.purchase-orders.create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $po = PurchaseOrder::create([
            'po_number' => PurchaseOrder::generatePONumber(),
            'order_date' => $request->order_date,
            'expected_delivery_date' => $request->expected_delivery_date,
            'supplier_id' => $request->supplier_id,
            'notes' => $request->notes,
            'created_by' => auth()->id(),
        ]);

        foreach ($request->items as $item) {
            PurchaseOrderItem::create([
                'purchase_order_id' => $po->id,
                'item_name' => $item['item_name'],
                'description' => $item['description'] ?? null,
                'quantity' => $item['quantity'],
                'unit' => $item['unit'] ?? 'pcs',
                'unit_price' => $item['unit_price'],
                'total_price' => $item['quantity'] * $item['unit_price'],
            ]);
        }

        $po->calculateTotals();

        return redirect()->route('admin.finance.purchase-orders.show', $po->id)
            ->with('success', 'Purchase order created successfully.');
    }

    public function show($id)
    {
        $order = PurchaseOrder::with(['supplier', 'items', 'creator', 'approver'])->findOrFail($id);
        return view('backend.admin.finance.purchase-orders.show', compact('order'));
    }

    public function approve($id)
    {
        $order = PurchaseOrder::findOrFail($id);
        $order->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Purchase order approved.');
    }

    public function markOrdered($id)
    {
        $order = PurchaseOrder::findOrFail($id);
        $order->update(['status' => 'ordered']);
        return back()->with('success', 'Purchase order marked as ordered.');
    }

    public function markReceived($id)
    {
        $order = PurchaseOrder::findOrFail($id);
        $order->update(['status' => 'received']);
        return back()->with('success', 'Purchase order marked as received.');
    }

    public function suppliers()
    {
        $suppliers = Supplier::withCount('purchaseOrders')->get();
        return view('backend.admin.finance.purchase-orders.suppliers', compact('suppliers'));
    }

    public function storeSupplier(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Supplier::create([
            'name' => $request->name,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'tax_number' => $request->tax_number,
        ]);

        return redirect()->route('admin.finance.purchase-orders.suppliers')
            ->with('success', 'Supplier created successfully.');
    }

    /**
     * Record invoice for a purchase order
     */
    public function recordInvoice(Request $request, $id)
    {
        $request->validate([
            'invoice_number' => 'required|string|max:100',
            'invoice_date' => 'required|date',
        ]);

        $order = PurchaseOrder::findOrFail($id);
        
        $order->update([
            'invoice_number' => $request->invoice_number,
            'invoice_date' => $request->invoice_date,
        ]);

        return back()->with('success', 'Invoice recorded successfully.');
    }

    /**
     * Record payment for a purchase order - auto-creates expense and cashbook entry
     */
    public function recordPayment(Request $request, $id)
    {
        $request->validate([
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'amount_paid' => 'required|numeric|min:0.01',
        ]);

        $order = PurchaseOrder::with('supplier')->findOrFail($id);
        
        // Calculate payment status
        $totalPaid = floatval($order->amount_paid) + floatval($request->amount_paid);
        $totalAmount = floatval($order->total_amount);
        
        if ($totalPaid >= $totalAmount) {
            $paymentStatus = 'paid';
        } elseif ($totalPaid > 0) {
            $paymentStatus = 'partial';
        } else {
            $paymentStatus = 'unpaid';
        }

        // Create expense record
        $expense = SchoolExpense::create([
            'date' => $request->payment_date,
            'category' => 'Purchase Order',
            'description' => 'PO #' . $order->po_number . ' - ' . $order->supplier->name . ' - ' . ($order->invoice_number ? 'Invoice: ' . $order->invoice_number : 'No Invoice'),
            'amount' => $request->amount_paid,
            'payment_method' => $request->payment_method,
            'reference_number' => $order->invoice_number ?? $order->po_number,
            'paid_to' => $order->supplier->name,
            'approved_by' => auth()->user()->name,
        ]);

        // Create cash book entry
        $lastEntry = CashBookEntry::orderBy('id', 'desc')->first();
        $currentBalance = $lastEntry ? floatval($lastEntry->balance) : 0;
        $newBalance = $currentBalance - floatval($request->amount_paid);

        $cashEntry = CashBookEntry::create([
            'entry_date' => $request->payment_date,
            'reference_number' => CashBookEntry::generateReferenceNumber(),
            'transaction_type' => 'payment',
            'category' => 'supplies',
            'description' => '[Purchase Order] ' . $order->po_number . ' - ' . $order->supplier->name,
            'amount' => $request->amount_paid,
            'balance' => $newBalance,
            'payment_method' => $request->payment_method,
            'payer_payee' => $order->supplier->name,
            'created_by' => auth()->id(),
            'notes' => 'Auto-generated from Purchase Order Payment - Invoice: ' . ($order->invoice_number ?? 'N/A'),
        ]);

        // Update purchase order
        $order->update([
            'payment_status' => $paymentStatus,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'amount_paid' => $totalPaid,
            'expense_id' => $expense->id,
            'cashbook_entry_id' => $cashEntry->id,
        ]);

        return back()->with('success', 'Payment of $' . number_format($request->amount_paid, 2) . ' recorded successfully. Expense and Cash Book entry created automatically.');
    }
}
