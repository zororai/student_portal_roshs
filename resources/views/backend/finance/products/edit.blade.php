@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Product</h1>
            <p class="text-gray-600">{{ $product->name }}</p>
        </div>
        <a href="{{ route('finance.products') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Back</a>
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
        <form action="{{ route('finance.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Product Name *</label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('description', $product->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <input type="text" name="category" value="{{ old('category', $product->category) }}" list="categories" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <datalist id="categories">
                            @foreach($categories as $cat)
                            <option value="{{ $cat }}">
                            @endforeach
                        </datalist>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Product Image</label>
                        @if($product->image)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-24 h-24 object-cover rounded-lg">
                        </div>
                        @endif
                        <input type="file" name="image" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-2">Barcode (auto-generated)</p>
                        <p class="font-mono text-lg bg-white px-3 py-2 rounded border">{{ $product->barcode }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Selling Price</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">$</span>
                                <input type="number" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cost Price</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">$</span>
                                <input type="number" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" step="0.01" min="0" class="w-full pl-7 pr-3 py-2 border border-gray-300 rounded-lg">
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Min Stock Level</label>
                        <input type="number" name="min_stock_level" value="{{ old('min_stock_level', $product->min_stock_level) }}" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" {{ $product->is_active ? 'checked' : '' }} class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                        <label for="is_active" class="ml-2 text-sm text-gray-700">Product is active (available for sale)</label>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t flex justify-end gap-3">
                <a href="{{ route('finance.products') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update Product</button>
            </div>
        </form>
    </div>
</div>
@endsection
