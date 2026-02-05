@extends('layouts.app')

@section('title', 'Manage Stock Items')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Manage Stock Items</h1>
                <p class="mt-1 text-gray-500">Add and edit grocery stock items</p>
            </div>
            <a href="{{ route('admin.grocery-stock.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Stock
            </a>
        </div>

        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
        @endif

        <!-- Add New Item -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Add New Stock Item</h2>
            <form action="{{ route('admin.grocery-stock.store-item') }}" method="POST">
                @csrf
                <div class="flex flex-wrap items-end gap-4 mb-4">
                    <div class="flex-1 min-w-[200px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Item Name</label>
                        <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Sugar">
                    </div>
                    <div class="w-40">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                        <select name="unit" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <option value="kg">Kilograms (kg)</option>
                            <option value="litres">Litres</option>
                            <option value="packets">Packets</option>
                            <option value="bags">Bags</option>
                            <option value="boxes">Boxes</option>
                            <option value="units">Units</option>
                            <option value="bottles">Bottles</option>
                            <option value="tins">Tins</option>
                        </select>
                    </div>
                    <div class="w-32">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Initial Qty</label>
                        <input type="number" name="initial_quantity" step="0.01" min="0" value="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="0">
                    </div>
                </div>
                <div class="flex flex-wrap items-end gap-4">
                    <div class="flex-1 min-w-[300px]">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-gray-400 font-normal">(e.g. Donated by XYZ Company)</span></label>
                        <input type="text" name="description" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Donated by ABC Organization">
                    </div>
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Add Item
                    </button>
                </div>
            </form>
        </div>

        <!-- Items List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800">Stock Items ({{ $items->count() }})</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Item Name</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Unit</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Current Balance</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Source</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                {{ $item->name }}
                                @if($item->description)
                                <span class="block text-xs text-gray-500 font-normal">{{ $item->description }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $item->unit }}</td>
                            <td class="px-6 py-4 text-sm text-right font-medium {{ $item->current_balance < 0 ? 'text-red-600' : 'text-gray-800' }}">
                                {{ number_format($item->current_balance, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($item->is_manual)
                                <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Manual</span>
                                @else
                                <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Collected</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($item->is_active)
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Active</span>
                                @else
                                <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($item->is_manual)
                                <button onclick="openEditModal({{ $item->id }}, '{{ addslashes($item->name) }}', '{{ addslashes($item->description ?? '') }}', '{{ $item->unit }}', {{ $item->current_balance }}, {{ $item->is_active ? 'true' : 'false' }})" class="text-blue-600 hover:text-blue-800" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </button>
                                @else
                                <span class="text-xs text-gray-400" title="Auto-created from collected groceries">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">No stock items found. Add your first item above.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Edit Stock Item</h3>
        </div>
        <form id="editForm" method="POST" class="p-6">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Item Name</label>
                <input type="text" name="name" id="edit_name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <input type="text" name="description" id="edit_description" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Donated by ABC Organization">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                <select name="unit" id="edit_unit" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="kg">Kilograms (kg)</option>
                    <option value="litres">Litres</option>
                    <option value="packets">Packets</option>
                    <option value="bags">Bags</option>
                    <option value="boxes">Boxes</option>
                    <option value="units">Units</option>
                    <option value="bottles">Bottles</option>
                    <option value="tins">Tins</option>
                </select>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Adjust Quantity</label>
                <input type="number" name="quantity" id="edit_quantity" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <p class="text-xs text-gray-500 mt-1">Current balance. Change to adjust stock (creates adjustment transaction).</p>
            </div>
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" id="edit_is_active" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Active</span>
                </label>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(id, name, description, unit, quantity, isActive) {
    document.getElementById('editForm').action = '/admin/grocery-stock/items/' + id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_description').value = description;
    document.getElementById('edit_unit').value = unit;
    document.getElementById('edit_quantity').value = quantity;
    document.getElementById('edit_is_active').checked = isActive;
    document.getElementById('editModal').classList.remove('hidden');
}
function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}
</script>
@endsection
