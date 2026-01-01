@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Products & Inventory</h1>
            <p class="text-gray-600">Manage products, stock levels, and barcodes</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('finance.products.pos') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Point of Sale
            </a>
            <a href="{{ route('finance.products.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Product
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, barcode, SKU..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="stock_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Stock Levels</option>
                    <option value="low" {{ request('stock_status') == 'low' ? 'selected' : '' }}>Low Stock</option>
                    <option value="out" {{ request('stock_status') == 'out' ? 'selected' : '' }}>Out of Stock</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Filter</button>
                <a href="{{ route('finance.products') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">Reset</a>
            </div>
        </form>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="text-gray-500 text-sm">Total Products</div>
            <div class="text-2xl font-bold text-gray-800">{{ \App\Product::count() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="text-gray-500 text-sm">Low Stock Items</div>
            <div class="text-2xl font-bold text-yellow-600">{{ \App\Product::whereRaw('quantity <= min_stock_level')->count() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="text-gray-500 text-sm">Out of Stock</div>
            <div class="text-2xl font-bold text-red-600">{{ \App\Product::where('quantity', 0)->count() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="text-gray-500 text-sm">Total Stock Value</div>
            <div class="text-2xl font-bold text-green-600">${{ number_format(\App\Product::selectRaw('SUM(price * quantity) as total')->first()->total ?? 0, 2) }}</div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barcode</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Stock</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($products as $product)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-12 h-12 rounded-lg object-cover mr-3">
                            @else
                            <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            @endif
                            <div>
                                <div class="font-medium text-gray-800">{{ $product->name }}</div>
                                <div class="text-sm text-gray-500">{{ $product->category ?? 'No category' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">{{ $product->barcode }}</div>
                        <div class="text-xs text-gray-500 mt-1">SKU: {{ $product->sku ?? 'N/A' }}</div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="font-semibold text-gray-800">${{ number_format($product->price, 2) }}</div>
                        @if($product->cost_price > 0)
                        <div class="text-xs text-gray-500">Cost: ${{ number_format($product->cost_price, 2) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="font-semibold {{ $product->quantity <= 0 ? 'text-red-600' : ($product->isLowStock() ? 'text-yellow-600' : 'text-green-600') }}">
                            {{ $product->quantity }}
                        </div>
                        <div class="text-xs text-gray-500">Min: {{ $product->min_stock_level }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($product->quantity <= 0)
                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Out of Stock</span>
                        @elseif($product->isLowStock())
                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Low Stock</span>
                        @else
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">In Stock</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('finance.products.show', $product->id) }}" class="text-blue-600 hover:text-blue-800" title="View">
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
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <p>No products found</p>
                        <a href="{{ route('finance.products.create') }}" class="text-blue-600 hover:underline mt-2 inline-block">Add your first product</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $products->withQueryString()->links() }}
    </div>
</div>
@endsection
