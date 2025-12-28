<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\PurchaseOrder;
use App\PurchaseOrderItem;
use App\Supplier;

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
}
