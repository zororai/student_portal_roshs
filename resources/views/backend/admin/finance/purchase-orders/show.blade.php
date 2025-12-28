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

    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
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
