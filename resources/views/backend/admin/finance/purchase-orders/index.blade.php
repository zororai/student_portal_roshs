@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Purchase Orders</h1>
        <div class="space-x-2">
            <a href="{{ route('admin.finance.purchase-orders.suppliers') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Suppliers</a>
            <a href="{{ route('admin.finance.purchase-orders.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">+ New PO</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PO Number</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($orders as $order)
                <tr>
                    <td class="px-6 py-4 text-sm font-mono">{{ $order->po_number }}</td>
                    <td class="px-6 py-4 text-sm">{{ $order->order_date->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-sm">{{ $order->supplier->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm text-right font-semibold">${{ number_format($order->total_amount, 2) }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2 py-1 text-xs rounded-full
                            @if($order->status == 'received') bg-green-100 text-green-800
                            @elseif($order->status == 'approved') bg-blue-100 text-blue-800
                            @elseif($order->status == 'ordered') bg-purple-100 text-purple-800
                            @elseif($order->status == 'cancelled') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800 @endif">{{ ucfirst($order->status) }}</span>
                    </td>
                    <td class="px-6 py-4 text-right text-sm"><a href="{{ route('admin.finance.purchase-orders.show', $order->id) }}" class="text-blue-600 hover:underline">View</a></td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No purchase orders found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $orders->links() }}</div>
</div>
@endsection
