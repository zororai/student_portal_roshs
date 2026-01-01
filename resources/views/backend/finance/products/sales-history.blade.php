@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Sales History</h1>
            <p class="text-gray-600">View all product sales transactions</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('finance.products.pos') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">New Sale</a>
            <a href="{{ route('finance.products') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Back to Products</a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <form method="GET" class="flex gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Filter</button>
            <a href="{{ route('finance.products.sales-history') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">Reset</a>
        </form>
    </div>

    <!-- Sales Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sale #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Payment</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($sales as $sale)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <span class="font-mono font-semibold text-blue-600">{{ $sale->sale_number }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $sale->sale_date->format('d M Y') }}<br>
                        <span class="text-xs text-gray-400">{{ $sale->created_at->format('H:i') }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm">
                        {{ $sale->items->count() }} item(s)
                        <div class="text-xs text-gray-500">
                            {{ $sale->items->sum('quantity') }} units
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $sale->customer_name ?? 'Walk-in' }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="font-semibold text-green-600">${{ number_format($sale->total_amount, 2) }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">{{ ucfirst($sale->payment_method) }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('finance.products.sale-receipt', $sale->id) }}" class="text-blue-600 hover:text-blue-800" title="View Receipt">
                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        No sales found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $sales->withQueryString()->links() }}
    </div>
</div>
@endsection
