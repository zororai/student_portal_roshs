@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Product Categories</h1>
            <p class="text-gray-600">Manage product categories</p>
        </div>
        <a href="{{ route('finance.inventory.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Back to Inventory</a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Add Category Form -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Add New Category</h3>
            <form action="{{ route('finance.categories.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Category Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="e.g. Uniform, Books">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Optional description">{{ old('description') }}</textarea>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">Add Category</button>
                </div>
            </form>
        </div>

        <!-- Categories List -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border">
            <div class="px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-800">All Categories</h3>
            </div>
            <div class="divide-y">
                @forelse($categories as $category)
                <div class="px-6 py-4 flex justify-between items-center hover:bg-gray-50">
                    <div>
                        <div class="font-medium text-gray-800">{{ $category->name }}</div>
                        @if($category->description)
                        <div class="text-sm text-gray-500">{{ $category->description }}</div>
                        @endif
                        <div class="text-xs text-gray-400 mt-1">{{ $category->products_count ?? 0 }} products</div>
                    </div>
                    <form action="{{ route('finance.categories.delete', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-gray-500">
                    No categories yet. Add your first category above.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
