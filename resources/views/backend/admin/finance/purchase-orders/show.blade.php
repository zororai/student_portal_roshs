@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ $order->po_number }}</h1>
        <div class="space-x-2">
            @if($order->status == 'draft')<form action="{{ route('admin.finance.purchase-orders.approve', $order->id) }}" method="POST" class="inline">@csrf<button class="bg-green-600 text-white px-4 py-2 rounded-lg">Approve</button></form>@endif
            @if($order->status == 'approved')<form action="{{ route('admin.finance.purchase-orders.mark-ordered', $order->id) }}" method="POST" class="inline">@csrf<button class="bg-purple-600 text-white px-4 py-2 rounded-lg">Mark Ordered</button></form>@endif
            @if($order->status == 'ordered')<form action="{{ route('admin.finance.purchase-orders.mark-received', $order->id) }}" method="POST" class="inline">@csrf<button class="bg-blue-600 text-white px-4 py-2 rounded-lg">Mark Received</button></form>@endif
            <a href="{{ route('admin.finance.purchase-orders.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg">Back</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div><span class="text-gray-500 text-sm">Supplier:</span><br><strong>{{ $order->supplier->name }}</strong></div>
            <div><span class="text-gray-500 text-sm">Order Date:</span><br><strong>{{ $order->order_date->format('d M Y') }}</strong></div>
            <div><span class="text-gray-500 text-sm">Status:</span><br><span class="px-2 py-1 text-xs rounded-full @if($order->status == 'received') bg-green-100 text-green-800 @else bg-yellow-100 text-yellow-800 @endif">{{ ucfirst($order->status) }}</span></div>
            <div><span class="text-gray-500 text-sm">Total:</span><br><strong class="text-xl">${{ number_format($order->total_amount, 2) }}</strong></div>
        </div>
    </div>

    <!-- Invoice & Payment Status -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Invoice Details (Optional) -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Invoice Details <span class="text-xs text-gray-400 ml-2">(Optional)</span>
            </h3>
            @if($order->invoice_number)
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-gray-500 text-sm">Invoice Number:</span>
                        <p class="font-semibold text-gray-800">{{ $order->invoice_number }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm">Invoice Date:</span>
                        <p class="font-semibold text-gray-800">{{ $order->invoice_date ? $order->invoice_date->format('d M Y') : 'N/A' }}</p>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                <p class="text-sm text-gray-600">No invoice recorded. You can record payment without an invoice.</p>
            </div>
            <form action="{{ route('admin.finance.purchase-orders.record-invoice', $order->id) }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Invoice Number</label>
                        <input type="text" name="invoice_number" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="INV-001">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Invoice Date</label>
                        <input type="date" name="invoice_date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Record Invoice (If Applicable)
                </button>
            </form>
            @endif
        </div>

        <!-- Payment Details -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Payment Status
            </h3>
            <div class="mb-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-600">Total Amount:</span>
                    <span class="font-bold text-lg">${{ number_format($order->total_amount, 2) }}</span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-600">Amount Paid:</span>
                    <span class="font-semibold text-green-600">${{ number_format($order->amount_paid ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between items-center border-t pt-2">
                    <span class="text-gray-600">Balance Due:</span>
                    <span class="font-bold text-red-600">${{ number_format(($order->total_amount ?? 0) - ($order->amount_paid ?? 0), 2) }}</span>
                </div>
                <div class="mt-2">
                    <span class="px-3 py-1 text-sm rounded-full 
                        @if(($order->payment_status ?? 'unpaid') == 'paid') bg-green-100 text-green-800
                        @elseif(($order->payment_status ?? 'unpaid') == 'partial') bg-yellow-100 text-yellow-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ ucfirst($order->payment_status ?? 'Unpaid') }}
                    </span>
                </div>
            </div>

            @if(($order->payment_status ?? 'unpaid') != 'paid')
            <form action="{{ route('admin.finance.purchase-orders.record-payment', $order->id) }}" method="POST" class="space-y-4 border-t pt-4">
                @csrf
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Date *</label>
                        <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
                        <input type="number" name="amount_paid" required step="0.01" min="0.01" max="{{ ($order->total_amount ?? 0) - ($order->amount_paid ?? 0) }}" value="{{ ($order->total_amount ?? 0) - ($order->amount_paid ?? 0) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method *</label>
                    <select name="payment_method" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Cash">Cash</option>
                        <option value="Cheque">Cheque</option>
                        <option value="Mobile Money">Mobile Money</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition-colors">
                    Record Payment & Create Expense
                </button>
                <p class="text-xs text-gray-500 text-center">This will auto-create an Expense record and Cash Book entry</p>
            </form>
            @else
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center">
                <svg class="w-8 h-8 text-green-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-green-700 font-medium">Fully Paid</p>
                <p class="text-sm text-green-600">Payment recorded on {{ $order->payment_date ? $order->payment_date->format('d M Y') : 'N/A' }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Items Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50">
            <h3 class="font-semibold text-gray-800">Order Items</h3>
        </div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs">Item</th><th class="px-6 py-3 text-right text-xs">Qty</th><th class="px-6 py-3 text-right text-xs">Unit Price</th><th class="px-6 py-3 text-right text-xs">Total</th></tr></thead>
            <tbody class="divide-y">
                @foreach($order->items as $item)
                <tr>
                    <td class="px-6 py-4 text-sm">{{ $item->item_name }}<br><span class="text-gray-500 text-xs">{{ $item->description }}</span></td>
                    <td class="px-6 py-4 text-sm text-right">{{ $item->quantity }} {{ $item->unit }}</td>
                    <td class="px-6 py-4 text-sm text-right">${{ number_format($item->unit_price, 2) }}</td>
                    <td class="px-6 py-4 text-sm text-right font-semibold">${{ number_format($item->total_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50"><tr><td colspan="3" class="px-6 py-3 text-right font-bold">Total:</td><td class="px-6 py-3 text-right font-bold">${{ number_format($order->total_amount, 2) }}</td></tr></tfoot>
        </table>
    </div>
</div>
@endsection
