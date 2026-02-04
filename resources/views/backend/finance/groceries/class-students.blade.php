@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.groceries.index') }}" class="mr-4 p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $class->class_name }} - Grocery List</h1>
            <p class="text-gray-500 mt-1">View student grocery list responses</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @if($activeList)
    <!-- Active List Info -->
    <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-semibold text-orange-800">Active Grocery List: {{ ucfirst($activeList->term) }} {{ $activeList->year }}</h3>
                <p class="text-sm text-orange-600 mt-1">{{ $activeList->items->count() }} items in list</p>
            </div>
            <button onclick="document.getElementById('viewItemsModal').classList.remove('hidden')" class="px-3 py-1 text-sm bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                View Items
            </button>
        </div>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">#</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Parent</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Items Bought</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($students as $index => $student)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $index + 1 }}</td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                <span class="text-xs font-medium text-gray-600">{{ substr($student->user->name ?? 'S', 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-800">{{ $student->user->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">Roll: {{ $student->roll_number }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600">
                        {{ $student->parent->user->name ?? 'No Parent' }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($student->grocery_response)
                            @if($student->grocery_response->acknowledged)
                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Acknowledged</span>
                            @elseif($student->grocery_response->submitted)
                            <span class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full">Submitted</span>
                            @else
                            <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Pending</span>
                            @endif
                        @else
                        <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-500 rounded-full">Not Started</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center text-sm">
                        @if($student->grocery_response && $student->grocery_response->items_bought)
                        <span class="font-medium text-blue-600">{{ count($student->grocery_response->items_bought) }}/{{ $activeList->items->count() }}</span>
                        @if($student->grocery_response->extra_items && count($student->grocery_response->extra_items) > 0)
                        <span class="ml-1 px-1.5 py-0.5 text-xs bg-green-100 text-green-700 rounded">+{{ count($student->grocery_response->extra_items) }} extra</span>
                        @endif
                        @else
                        <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <button onclick="openEditModal({{ $student->id }}, '{{ addslashes($student->user->name ?? 'Unknown') }}', {{ $student->grocery_response ? $student->grocery_response->id : 'null' }}, {{ $student->grocery_response && $student->grocery_response->items_bought ? json_encode($student->grocery_response->items_bought) : '[]' }}, {{ $student->grocery_response && $student->grocery_response->extra_items ? json_encode($student->grocery_response->extra_items) : '[]' }}, '{{ addslashes($student->grocery_response->notes ?? '') }}', {{ $student->grocery_response && $student->grocery_response->item_actual_qty ? json_encode($student->grocery_response->item_actual_qty) : '{}' }})" class="inline-flex items-center px-3 py-1 text-xs bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 mr-1">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit List
                        </button>
                        @if($student->grocery_response && $student->grocery_response->submitted)
                        <a href="{{ route('admin.groceries.response', $student->grocery_response->id) }}" class="inline-flex items-center px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 mr-1">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View
                        </a>
                        @if(!$student->grocery_response->acknowledged)
                        <form action="{{ route('admin.groceries.acknowledge', $student->grocery_response->id) }}" method="POST" class="inline">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="inline-flex items-center px-3 py-1 text-xs bg-green-100 text-green-700 rounded-lg hover:bg-green-200">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Acknowledge
                            </button>
                        </form>
                        @endif
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">No students in this class</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @else
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
        <svg class="w-12 h-12 text-yellow-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <h3 class="text-lg font-semibold text-yellow-800 mb-2">No Active Grocery List</h3>
        <p class="text-yellow-600">There is no active grocery list for this class. Create one from the main groceries page.</p>
        <a href="{{ route('admin.groceries.index') }}" class="inline-block mt-4 px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
            Go to Groceries
        </a>
    </div>
    @endif
</div>

@if($activeList)
<!-- View Items Modal -->
<div id="viewItemsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Grocery Items</h3>
            <button onclick="document.getElementById('viewItemsModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="p-6 max-h-96 overflow-y-auto">
            <ul class="space-y-2">
                @foreach($activeList->items as $item)
                <li class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-gray-800">{{ $item->name }}</span>
                    @if($item->quantity)
                    <span class="text-sm text-gray-500">Qty: {{ $item->quantity }}</span>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

<!-- Edit Student Grocery List Modal -->
<div id="editGroceryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl mx-4">
        <form id="editGroceryForm" method="POST" action="{{ route('admin.groceries.update-student') }}">
            @csrf
            <input type="hidden" name="grocery_list_id" value="{{ $activeList->id }}">
            <input type="hidden" name="student_id" id="edit_student_id">
            <input type="hidden" name="response_id" id="edit_response_id">
            
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">Edit Grocery List - <span id="studentNameDisplay"></span></h3>
                <button type="button" onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <div class="p-6 max-h-96 overflow-y-auto">
                <p class="text-sm text-gray-600 mb-4">Check the items that the parent has brought:</p>
                <div class="space-y-2">
                    @foreach($activeList->items as $index => $item)
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100">
                        <label class="flex items-center flex-1 cursor-pointer">
                            <input type="checkbox" name="items_bought[]" value="{{ $item->id }}" class="item-checkbox w-5 h-5 text-blue-600 rounded focus:ring-blue-500 mr-3" data-item-id="{{ $item->id }}">
                            <div class="flex-1">
                                <span class="text-gray-800 font-medium">{{ $item->name }}</span>
                                @if($item->quantity)
                                <span class="text-sm text-gray-500 ml-2">(Required: {{ $item->quantity }})</span>
                                @endif
                            </div>
                        </label>
                        <div class="flex items-center ml-2">
                            <label class="text-xs text-blue-600 mr-1">Qty Brought:</label>
                            <input type="number" name="item_actual_qty[{{ $item->id }}]" min="0" value="" 
                                   class="w-16 px-2 py-1 text-sm border border-gray-300 rounded focus:ring-blue-500 focus:border-blue-500 item-actual-qty" 
                                   data-item-id="{{ $item->id }}" data-required="{{ $item->quantity ?? 1 }}" placeholder="{{ $item->quantity ?? 1 }}">
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Extra Items Section -->
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-sm font-medium text-gray-700">Extra Items Brought (Beyond Required List)</label>
                        <button type="button" onclick="addExtraItem()" class="px-3 py-1 text-xs bg-green-100 text-green-700 rounded-lg hover:bg-green-200">
                            + Add Extra Item
                        </button>
                    </div>
                    <div id="extraItemsContainer" class="space-y-2">
                        <!-- Extra items will be added here dynamically -->
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Use this section to record any additional items the parent has brought beyond the required grocery list.</p>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                    <textarea name="notes" id="edit_notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Add any notes about the grocery items..."></textarea>
                </div>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-100 flex justify-end gap-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let extraItemIndex = 0;

function openEditModal(studentId, studentName, responseId, itemsBought, extraItems, notes, itemActualQty) {
    document.getElementById('edit_student_id').value = studentId;
    document.getElementById('edit_response_id').value = responseId || '';
    document.getElementById('studentNameDisplay').textContent = studentName;
    document.getElementById('edit_notes').value = notes || '';
    
    // Uncheck all checkboxes and reset quantities
    document.querySelectorAll('input[name="items_bought[]"]').forEach(cb => cb.checked = false);
    document.querySelectorAll('.item-actual-qty').forEach(input => input.value = '');
    
    // Check the items that were bought
    if (itemsBought && itemsBought.length > 0) {
        itemsBought.forEach(itemId => {
            const checkbox = document.querySelector(`input[name="items_bought[]"][value="${itemId}"]`);
            if (checkbox) checkbox.checked = true;
        });
    }
    
    // Set actual quantities for list items
    if (itemActualQty && typeof itemActualQty === 'object') {
        Object.keys(itemActualQty).forEach(itemId => {
            const input = document.querySelector(`.item-actual-qty[data-item-id="${itemId}"]`);
            if (input) input.value = itemActualQty[itemId] || '';
        });
    }
    
    // Clear and populate extra items (items not on list)
    const container = document.getElementById('extraItemsContainer');
    container.innerHTML = '';
    extraItemIndex = 0;
    
    if (extraItems && extraItems.length > 0) {
        extraItems.forEach(item => {
            addExtraItem(item.name, item.quantity);
        });
    }
    
    document.getElementById('editGroceryModal').classList.remove('hidden');
}

function addExtraItem(name = '', quantity = '') {
    const container = document.getElementById('extraItemsContainer');
    const div = document.createElement('div');
    div.className = 'flex items-center gap-2 p-2 bg-green-50 rounded-lg';
    div.innerHTML = `
        <input type="text" name="extra_items[${extraItemIndex}][name]" value="${name}" 
               class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500" 
               placeholder="Item name">
        <input type="text" name="extra_items[${extraItemIndex}][quantity]" value="${quantity}" 
               class="w-24 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500" 
               placeholder="Qty">
        <button type="button" onclick="this.parentElement.remove()" class="p-2 text-red-500 hover:text-red-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
    container.appendChild(div);
    extraItemIndex++;
}

function closeEditModal() {
    document.getElementById('editGroceryModal').classList.add('hidden');
}
</script>
@endif
@endsection
