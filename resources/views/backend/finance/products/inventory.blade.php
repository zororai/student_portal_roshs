@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Inventory Management</h1>
            <p class="text-gray-600">Track and manage product stock levels</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('finance.categories.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Manage Categories</a>
            <a href="{{ route('finance.products.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Product
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg p-4">
            <p class="text-blue-100 text-sm">Total Products</p>
            <p class="text-3xl font-bold">{{ $stats['total_products'] }}</p>
        </div>
        <div class="bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg p-4">
            <p class="text-green-100 text-sm">Total Stock Value</p>
            <p class="text-3xl font-bold">${{ number_format($stats['total_stock_value'], 2) }}</p>
        </div>
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-lg p-4">
            <p class="text-yellow-100 text-sm">Low Stock Items</p>
            <p class="text-3xl font-bold">{{ $stats['low_stock_count'] }}</p>
        </div>
        <div class="bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg p-4">
            <p class="text-red-100 text-sm">Out of Stock</p>
            <p class="text-3xl font-bold">{{ $stats['out_of_stock_count'] }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Product name, SKU, barcode..." class="px-3 py-2 border border-gray-300 rounded-lg w-64">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category" class="px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Stock Status</label>
                <select name="stock_status" class="px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">All</option>
                    <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                    <option value="low_stock" {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Filter</button>
            <a href="{{ route('finance.inventory.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">Reset</a>
        </form>
    </div>

    <!-- Inventory Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU / Barcode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">In Stock</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Min Level</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stock Value</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($products as $product)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-10 h-10 rounded-lg object-cover">
                            @else
                            <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            @endif
                            <div>
                                <a href="{{ route('finance.products.show', $product->id) }}" class="font-semibold text-blue-600 hover:underline">{{ $product->name }}</a>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm">
                            @if($product->sku)<span class="text-gray-600">SKU: {{ $product->sku }}</span><br>@endif
                            <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ $product->barcode }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $product->category ?? '-' }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="text-xl font-bold {{ $product->quantity <= 0 ? 'text-red-600' : ($product->quantity <= $product->min_stock_level ? 'text-yellow-600' : 'text-green-600') }}">
                            {{ $product->quantity }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center text-sm text-gray-600">{{ $product->min_stock_level }}</td>
                    <td class="px-6 py-4 text-right text-sm">${{ number_format($product->price, 2) }}</td>
                    <td class="px-6 py-4 text-right text-sm font-semibold">${{ number_format($product->price * $product->quantity, 2) }}</td>
                    <td class="px-6 py-4 text-center">
                        @if($product->quantity <= 0)
                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Out of Stock</span>
                        @elseif($product->quantity <= $product->min_stock_level)
                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Low Stock</span>
                        @else
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">In Stock</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button onclick="openAdjustModal({{ $product->id }}, '{{ $product->name }}', {{ $product->quantity }})" class="text-blue-600 hover:text-blue-800" title="Adjust Stock">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </button>
                            <a href="{{ route('finance.products.show', $product->id) }}" class="text-gray-600 hover:text-gray-800" title="View Details">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="{{ route('finance.products.edit', $product->id) }}" class="text-yellow-600 hover:text-yellow-800" title="Edit">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                        No products found. <a href="{{ route('finance.products.create') }}" class="text-blue-600 hover:underline">Add your first product</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $products->withQueryString()->links() }}
    </div>

    <!-- Stock Movement History -->
    <div class="mt-8 bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="font-semibold text-gray-800">Recent Stock Movements</h3>
            <a href="{{ route('finance.inventory.movements') }}" class="text-blue-600 hover:underline text-sm">View All</a>
        </div>
        <div class="divide-y">
            @forelse($recentMovements as $movement)
            <div class="px-6 py-3 flex justify-between items-center">
                <div class="flex items-center gap-4">
                    <span class="px-2 py-1 text-xs rounded-full 
                        {{ $movement->type == 'in' ? 'bg-green-100 text-green-800' : ($movement->type == 'out' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                        {{ strtoupper($movement->type) }}
                    </span>
                    <div>
                        <span class="font-medium">{{ $movement->product->name ?? 'Unknown Product' }}</span>
                        <span class="text-gray-500 text-sm ml-2">{{ $movement->reason }}</span>
                    </div>
                </div>
                <div class="text-right">
                    <span class="font-bold {{ $movement->type == 'in' ? 'text-green-600' : 'text-red-600' }}">
                        {{ $movement->type == 'in' ? '+' : '-' }}{{ $movement->quantity }}
                    </span>
                    <div class="text-xs text-gray-500">{{ $movement->created_at->format('d M Y H:i') }}</div>
                </div>
            </div>
            @empty
            <div class="px-6 py-8 text-center text-gray-500">No stock movements yet</div>
            @endforelse
        </div>
    </div>
</div>

<!-- Adjust Stock Modal -->
<div id="adjustModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="font-semibold text-gray-800">Adjust Stock</h3>
            <button onclick="closeAdjustModal()" class="text-gray-400 hover:text-gray-600">&times;</button>
        </div>
        <form id="adjustForm" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                    <p id="adjustProductName" class="font-semibold"></p>
                    <p class="text-sm text-gray-500">Current Stock: <span id="adjustCurrentStock" class="font-bold"></span></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adjustment Type</label>
                    <select name="adjustment_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="add">Add Stock (Received goods)</option>
                        <option value="remove">Remove Stock (Damaged/Lost)</option>
                        <option value="set">Set to Exact Amount (Stock count)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                    <input type="number" name="quantity" required min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                    <input type="text" name="reason" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="e.g., New shipment, Damaged goods, Stock count">
                </div>
            </div>
            <div class="px-6 py-4 border-t flex justify-end gap-3">
                <button type="button" onclick="closeAdjustModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update Stock</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAdjustModal(productId, productName, currentStock) {
    document.getElementById('adjustProductName').textContent = productName;
    document.getElementById('adjustCurrentStock').textContent = currentStock;
    document.getElementById('adjustForm').action = '/finance/products/' + productId + '/adjust-stock';
    document.getElementById('adjustModal').classList.remove('hidden');
}

function closeAdjustModal() {
    document.getElementById('adjustModal').classList.add('hidden');
}
</script>
@endsection
