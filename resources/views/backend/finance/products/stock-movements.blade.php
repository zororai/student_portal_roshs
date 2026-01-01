@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Stock Movements</h1>
            <p class="text-gray-600">Complete history of all inventory changes</p>
        </div>
        <a href="{{ route('finance.inventory.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Back to Inventory</a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                <select name="product_id" class="px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Products</option>
                    @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Movement Type</label>
                <select name="type" class="px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Types</option>
                    <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>Stock In</option>
                    <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>Stock Out</option>
                    <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Filter</button>
            <a href="{{ route('finance.inventory.movements') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">Reset</a>
        </form>
    </div>

    <!-- Movements Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date/Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Quantity</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stock Before</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Stock After</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">By</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($movements as $movement)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $movement->created_at->format('d M Y') }}<br>
                        <span class="text-xs text-gray-400">{{ $movement->created_at->format('H:i:s') }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('finance.products.show', $movement->product_id) }}" class="text-blue-600 hover:underline font-medium">
                            {{ $movement->product->name ?? 'Unknown' }}
                        </a>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2 py-1 text-xs rounded-full 
                            {{ $movement->type == 'in' ? 'bg-green-100 text-green-800' : ($movement->type == 'out' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                            {{ strtoupper($movement->type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="font-bold {{ $movement->type == 'in' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $movement->type == 'in' ? '+' : '-' }}{{ $movement->quantity }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center text-sm text-gray-600">{{ $movement->stock_before }}</td>
                    <td class="px-6 py-4 text-center text-sm font-semibold">{{ $movement->stock_after }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $movement->reason ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $movement->creator->name ?? 'System' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center text-gray-500">No stock movements found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $movements->withQueryString()->links() }}
    </div>
</div>
@endsection
