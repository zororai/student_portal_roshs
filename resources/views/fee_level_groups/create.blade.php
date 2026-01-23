@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
                <div class="flex items-center">
                    <a href="{{ route('fee-level-groups.index') }}" class="mr-3 text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                    </a>
                    <h2 class="text-2xl font-bold text-gray-800">Create Fee Level Groups</h2>
                </div>
                <div class="flex flex-wrap gap-2">
                    <form action="{{ route('fee-level-groups.apply-to-new-students') }}" method="POST" class="inline" onsubmit="return confirm('This will apply fee structures to all new students. Continue?');">
                        @csrf
                        <button type="submit" class="flex items-center px-3 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Apply to New Students
                        </button>
                    </form>
                    <button type="button" onclick="addGroupRow()" class="flex items-center px-3 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 text-sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add Another Group
                    </button>
                </div>
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

            <div class="bg-gray-50 p-4 rounded-lg mb-6">
                <h4 class="text-sm font-medium text-gray-700 mb-2">How to use:</h4>
                <ul class="text-xs text-gray-600 space-y-1">
                    <li>1. Enter a <strong>Category Name</strong> (e.g., "Form 1-4" or "Junior")</li>
                    <li>2. Select the <strong>classes</strong> that belong to this category</li>
                    <li>3. Add more groups if needed using the "Add Another Group" button</li>
                </ul>
            </div>

            <form action="{{ route('fee-level-groups.store') }}" method="POST">
                @csrf

                <div id="groups-container" class="space-y-4">
                    <!-- Group Row 0 -->
                    <div class="group-row bg-rose-50 p-4 rounded-lg border border-rose-200" data-index="0">
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="font-semibold text-rose-700 flex items-center">
                                <span class="w-6 h-6 rounded-full bg-rose-500 text-white flex items-center justify-center text-xs mr-2">1</span>
                                Level Group
                            </h4>
                            <button type="button" onclick="removeGroupRow(this)" class="text-red-500 hover:text-red-700 hidden remove-btn">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Category Name *</label>
                                <input type="text" name="groups[0][name]" required
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-rose-500"
                                    placeholder="e.g., Form 1-4">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                                <input type="text" name="groups[0][description]"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-rose-500"
                                    placeholder="Optional description">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Display Order</label>
                                <input type="number" name="groups[0][display_order]" value="0" min="0"
                                    class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-rose-500">
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-xs font-medium text-gray-700 mb-2">Select Classes in this Category *</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-2 bg-white p-3 rounded border border-gray-200">
                                @foreach($classes as $class)
                                <label class="flex items-center p-2 rounded hover:bg-rose-50 cursor-pointer border border-transparent hover:border-rose-200">
                                    <input type="checkbox" name="groups[0][classes][]" value="{{ $class->class_numeric }}"
                                        class="w-4 h-4 text-rose-500 border-gray-300 rounded focus:ring-rose-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ $class->class_name }}</span>
                                </label>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Select at least one class</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <a href="{{ route('fee-level-groups.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-rose-500 text-white rounded-lg hover:bg-rose-600">
                        Create Level Groups
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let groupIndex = 1;

const classCheckboxes = `@foreach($classes as $class)<label class="flex items-center p-2 rounded hover:bg-rose-50 cursor-pointer border border-transparent hover:border-rose-200"><input type="checkbox" name="groups[INDEX][classes][]" value="{{ $class->class_numeric }}" class="w-4 h-4 text-rose-500 border-gray-300 rounded focus:ring-rose-500"><span class="ml-2 text-sm text-gray-700">{{ $class->class_name }}</span></label>@endforeach`;

function addGroupRow() {
    const container = document.getElementById('groups-container');
    const rowNum = groupIndex + 1;
    const checkboxesHtml = classCheckboxes.replace(/INDEX/g, groupIndex);
    
    const html = `
        <div class="group-row bg-rose-50 p-4 rounded-lg border border-rose-200" data-index="${groupIndex}">
            <div class="flex justify-between items-center mb-3">
                <h4 class="font-semibold text-rose-700 flex items-center">
                    <span class="w-6 h-6 rounded-full bg-rose-500 text-white flex items-center justify-center text-xs mr-2">${rowNum}</span>
                    Level Group
                </h4>
                <button type="button" onclick="removeGroupRow(this)" class="text-red-500 hover:text-red-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Category Name *</label>
                    <input type="text" name="groups[${groupIndex}][name]" required
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-rose-500"
                        placeholder="e.g., Form 5-6">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                    <input type="text" name="groups[${groupIndex}][description]"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-rose-500"
                        placeholder="Optional description">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Display Order</label>
                    <input type="number" name="groups[${groupIndex}][display_order]" value="${groupIndex}" min="0"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-rose-500">
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-xs font-medium text-gray-700 mb-2">Select Classes in this Category *</label>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-2 bg-white p-3 rounded border border-gray-200">
                    ${checkboxesHtml}
                </div>
                <p class="text-xs text-gray-500 mt-1">Select at least one class</p>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', html);
    groupIndex++;
    updateRemoveButtons();
}

function removeGroupRow(btn) {
    const row = btn.closest('.group-row');
    row.remove();
    updateRowNumbers();
    updateRemoveButtons();
}

function updateRowNumbers() {
    const rows = document.querySelectorAll('.group-row');
    rows.forEach((row, idx) => {
        const badge = row.querySelector('.bg-rose-500');
        if (badge) badge.textContent = idx + 1;
    });
}

function updateRemoveButtons() {
    const rows = document.querySelectorAll('.group-row');
    rows.forEach((row, idx) => {
        const removeBtn = row.querySelector('.remove-btn, button[onclick*="removeGroupRow"]');
        if (removeBtn) {
            if (rows.length === 1) {
                removeBtn.classList.add('hidden');
            } else {
                removeBtn.classList.remove('hidden');
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', updateRemoveButtons);
</script>
@endpush
@endsection
