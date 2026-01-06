@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Student Groceries</h1>
            <p class="text-gray-500 mt-1">Manage grocery lists for students by class</p>
        </div>
        <button onclick="document.getElementById('addGroceryModal').classList.remove('hidden')" class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Groceries
        </button>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg">
        {{ session('error') }}
    </div>
    @endif

    <!-- Class Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($classes as $class)
        <a href="{{ route('admin.groceries.class', $class->id) }}" class="block bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    @if($class->active_list)
                    <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Active</span>
                    @else
                    <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-500 rounded-full">No List</span>
                    @endif
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-1">{{ $class->class_name }}</h3>
                <p class="text-sm text-gray-500 mb-4">{{ $class->students_count }} Students</p>
                
                @if($class->active_list)
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Submitted:</span>
                        <span class="font-medium text-blue-600">{{ $class->submitted_count }}/{{ $class->students_count }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Acknowledged:</span>
                        <span class="font-medium text-green-600">{{ $class->acknowledged_count }}/{{ $class->students_count }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                        @php $progress = $class->students_count > 0 ? ($class->acknowledged_count / $class->students_count) * 100 : 0; @endphp
                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                    </div>
                </div>
                @else
                <p class="text-sm text-gray-400 italic">No active grocery list</p>
                @endif
            </div>
        </a>
        @endforeach
    </div>

    <!-- Recent Grocery Lists -->
    @if($groceryLists->count() > 0)
    <div class="mt-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Recent Grocery Lists</h2>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Term/Year</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Classes</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Items</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Responses</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($groceryLists as $list)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ ucfirst($list->term) }} {{ $list->year }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @foreach($list->classes as $cls)
                            <span class="inline-block px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded mr-1 mb-1">{{ $cls->class_name }}</span>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $list->items->count() }} items</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $list->responses->where('submitted', true)->count() }} submitted</td>
                        <td class="px-6 py-4">
                            @if($list->status === 'active')
                            <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Active</span>
                            @else
                            <span class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-500 rounded-full">Closed</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if(!$list->locked)
                                <a href="{{ route('admin.groceries.edit', $list->id) }}" class="text-blue-600 hover:text-blue-800 mr-2" title="Edit List">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.groceries.lock', $list->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to lock this list? No further editing will be allowed.')">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="text-orange-600 hover:text-orange-800 mr-2" title="Lock List">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </button>
                                </form>
                            @else
                                <span class="text-gray-400 mr-2" title="Locked">
                                    <svg class="w-5 h-5 inline" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 1C8.676 1 6 3.676 6 7v2H4v14h16V9h-2V7c0-3.324-2.676-6-6-6zm0 2c2.276 0 4 1.724 4 4v2H8V7c0-2.276 1.724-4 4-4zm0 10c1.1 0 2 .9 2 2s-.9 2-2 2-2-.9-2-2 .9-2 2-2z"/>
                                    </svg>
                                </span>
                            @endif
                            @if($list->status === 'active' && !$list->locked)
                            <form action="{{ route('admin.groceries.close', $list->id) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="text-yellow-600 hover:text-yellow-800 mr-2" title="Close List">
                                    <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                            <button type="button" onclick="openDeleteModal({{ $list->id }}, '{{ ucfirst($list->term) }} {{ $list->year }}')" class="text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<!-- Add Grocery Modal -->
<div id="addGroceryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center overflow-y-auto">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl mx-4 my-8">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Create Grocery List</h3>
            <button onclick="document.getElementById('addGroceryModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form action="{{ route('admin.groceries.store') }}" method="POST" class="p-6">
            @csrf
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Term</label>
                    <select name="term" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="first">First Term</option>
                        <option value="second">Second Term</option>
                        <option value="third">Third Term</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <input type="text" name="year" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" value="{{ date('Y') }}" placeholder="2025">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Select Classes</label>
                <div class="grid grid-cols-3 gap-2 max-h-40 overflow-y-auto border border-gray-200 rounded-lg p-3">
                    @foreach($classes as $class)
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="classes[]" value="{{ $class->id }}" class="rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                        <span class="text-sm text-gray-700">{{ $class->class_name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Grocery Items</label>
                <div id="groceryItems" class="space-y-2">
                    <div class="flex items-center space-x-2 grocery-item">
                        <input type="text" name="items[0][name]" required class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Item name (e.g., Exercise Books)">
                        <input type="text" name="items[0][quantity]" class="w-32 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Qty (e.g., 5)">
                        <button type="button" onclick="removeGroceryItem(this)" class="p-2 text-red-500 hover:text-red-700 hidden remove-btn">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <button type="button" onclick="addGroceryItem()" class="mt-2 inline-flex items-center px-3 py-2 text-sm text-orange-600 hover:text-orange-800">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add More Items
                </button>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                <button type="button" onclick="document.getElementById('addGroceryModal').classList.add('hidden')" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">Send to Parents</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4">
        <div class="p-6 text-center">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">Delete Grocery List</h3>
            <p class="text-gray-600 mb-2">Are you sure you want to delete this grocery list?</p>
            <p id="deleteListName" class="font-medium text-gray-800 mb-6"></p>
            <p class="text-sm text-red-600 mb-6">This action cannot be undone. All items and responses will be permanently removed.</p>
            
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-center space-x-3">
                    <button type="button" onclick="closeDeleteModal()" class="px-6 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 font-medium">Cancel</button>
                    <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">Delete</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let itemIndex = 1;
let deleteListId = null;

function openDeleteModal(listId, listName) {
    deleteListId = listId;
    document.getElementById('deleteListName').textContent = listName;
    document.getElementById('deleteForm').action = '/admin/groceries/' + listId;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    deleteListId = null;
}

function addGroceryItem() {
    const container = document.getElementById('groceryItems');
    const newItem = document.createElement('div');
    newItem.className = 'flex items-center space-x-2 grocery-item';
    newItem.innerHTML = `
        <input type="text" name="items[${itemIndex}][name]" required class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Item name">
        <input type="text" name="items[${itemIndex}][quantity]" class="w-32 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Qty">
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
    items.forEach((item, index) => {
        const removeBtn = item.querySelector('.remove-btn');
        if (removeBtn) {
            removeBtn.classList.toggle('hidden', items.length === 1);
        }
    });
}
</script>
@endsection
