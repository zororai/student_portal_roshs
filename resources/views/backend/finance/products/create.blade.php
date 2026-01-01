@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Add New Product</h1>
            <p class="text-gray-600">Create a product with auto-generated barcode</p>
        </div>
        <a href="{{ route('finance.products') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Back to Products</a>
    </div>

    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border p-6">
        <form action="{{ route('finance.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column -->
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Product Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Enter product name">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Product description (optional)">{{ old('description') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <input type="text" name="category" value="{{ old('category') }}" list="categories" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Select or type category">
                        <datalist id="categories">
                            @foreach($categories as $cat)
                            <option value="{{ $cat }}">
                            @endforeach
                            <option value="Uniform">
                            <option value="Books">
                            <option value="Stationery">
                            <option value="Food">
                            <option value="Other">
                        </datalist>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Product Image (Optional)</label>
                        <input type="file" name="image" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Max 2MB. Supported: JPEG, PNG, GIF</p>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Selling Price</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">$</span>
                                <input type="number" name="price" value="{{ old('price', '0') }}" step="0.01" min="0" class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="0.00">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cost Price</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">$</span>
                                <input type="number" name="cost_price" value="{{ old('cost_price', '0') }}" step="0.01" min="0" class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Initial Quantity</label>
                            <input type="number" name="quantity" value="{{ old('quantity', '0') }}" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="0">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Min Stock Level</label>
                            <input type="number" name="min_stock_level" value="{{ old('min_stock_level', '5') }}" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="5">
                            <p class="text-xs text-gray-500 mt-1">Alert when stock falls below this</p>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-medium text-blue-800">Auto-Generated</span>
                        </div>
                        <p class="text-sm text-blue-700">A unique barcode and SKU will be automatically generated when you save this product.</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t flex justify-end gap-3">
                <a href="{{ route('finance.products') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Create Product</button>
            </div>
        </form>
    </div>
</div>
@endsection
