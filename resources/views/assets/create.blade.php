@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('finance.assets.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Add New Asset</h1>
            <p class="text-gray-600">Register a new school asset</p>
        </div>
    </div>

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border p-6">
        <form action="{{ route('finance.assets.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Asset Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                    <select name="category_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('category_id') border-red-500 @enderror">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }} ({{ $category->useful_life_years }} years)
                        </option>
                        @endforeach
                    </select>
                    @error('category_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Serial Number</label>
                    <input type="text" name="serial_number" value="{{ old('serial_number') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Date *</label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('purchase_date') border-red-500 @enderror">
                    @error('purchase_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Cost ($) *</label>
                    <input type="number" name="purchase_cost" value="{{ old('purchase_cost') }}" step="0.01" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 @error('purchase_cost') border-red-500 @enderror">
                    @error('purchase_cost')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Residual Value ($)</label>
                    <input type="number" name="residual_value" value="{{ old('residual_value', 0) }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <p class="text-gray-500 text-xs mt-1">Value at end of useful life</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Condition *</label>
                    <select name="condition" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="new" {{ old('condition') == 'new' ? 'selected' : '' }}>New</option>
                        <option value="good" {{ old('condition') == 'good' ? 'selected' : '' }}>Good</option>
                        <option value="fair" {{ old('condition') == 'fair' ? 'selected' : '' }}>Fair</option>
                        <option value="damaged" {{ old('condition') == 'damaged' ? 'selected' : '' }}>Damaged</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                    <select name="location_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Location</option>
                        @foreach($locations as $location)
                        <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                            {{ $location->full_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-4">
                <a href="{{ route('finance.assets.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Create Asset</button>
            </div>
        </form>
    </div>
</div>
@endsection
