@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Grocery List</h1>
            <p class="text-gray-500 mt-1">{{ ucfirst($groceryList->term) }} {{ $groceryList->year }}</p>
        </div>
        <a href="{{ route('admin.groceries.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back
        </a>
    </div>

    @if(session('error'))
    <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg">
        {{ session('error') }}
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('admin.groceries.update', $groceryList->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Term</label>
                    <select name="term" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="first" {{ $groceryList->term == 'first' ? 'selected' : '' }}>First Term</option>
                        <option value="second" {{ $groceryList->term == 'second' ? 'selected' : '' }}>Second Term</option>
                        <option value="third" {{ $groceryList->term == 'third' ? 'selected' : '' }}>Third Term</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <input type="text" name="year" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" value="{{ $groceryList->year }}">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Classes</label>
                <div class="grid grid-cols-3 gap-2 max-h-40 overflow-y-auto border border-gray-200 rounded-lg p-3">
                    @foreach($classes as $class)
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="classes[]" value="{{ $class->id }}" 
                            {{ $groceryList->classes->contains($class->id) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                        <span class="text-sm text-gray-700">{{ $class->class_name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Grocery Items</label>
                <div id="groceryItems" class="space-y-2">
                    @foreach($groceryList->items as $index => $item)
                    <div class="flex items-center space-x-2 grocery-item">
                        <input type="text" name="items[{{ $index }}][name]" required value="{{ $item->name }}" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Item name">
                        <input type="text" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" class="w-24 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Qty">
                        <input type="number" step="0.01" name="items[{{ $index }}][price]" value="{{ $item->price }}" class="w-28 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Price">
                        <button type="button" onclick="removeGroceryItem(this)" class="p-2 text-red-500 hover:text-red-700 {{ $loop->first && $groceryList->items->count() == 1 ? 'hidden' : '' }} remove-btn">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                    @endforeach
                </div>
                <button type="button" onclick="addGroceryItem()" class="mt-2 inline-flex items-center px-3 py-2 text-sm text-orange-600 hover:text-orange-800">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add More Items
                </button>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.groceries.index') }}" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">Update Grocery List</button>
            </div>
        </form>
    </div>
</div>

<script>
let itemIndex = {{ $groceryList->items->count() }};

function addGroceryItem() {
    const container = document.getElementById('groceryItems');
    const newItem = document.createElement('div');
    newItem.className = 'flex items-center space-x-2 grocery-item';
    newItem.innerHTML = `
        <input type="text" name="items[${itemIndex}][name]" required class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Item name">
        <input type="text" name="items[${itemIndex}][quantity]" class="w-24 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Qty">
        <input type="number" step="0.01" name="items[${itemIndex}][price]" class="w-28 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Price">
        <button type="button" onclick="removeGroceryItem(this)" class="p-2 text-red-500 hover:text-red-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </button>
    `;
    container.appendChild(newItem);
    itemIndex++;
    updateRemoveButtons();
}

function removeGroceryItem(btn) {
    btn.closest('.grocery-item').remove();
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const items = document.querySelectorAll('.grocery-item');
    items.forEach((item) => {
        const removeBtn = item.querySelector('.remove-btn');
        if (removeBtn) {
            removeBtn.classList.toggle('hidden', items.length === 1);
        }
    });
}
</script>
@endsection
