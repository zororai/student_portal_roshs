@extends('layouts.app')

@section('content')
    <div class="create-results-status">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-gray-700 uppercase font-bold">Create New Term</h2>
            </div>
        </div>

        <form action="{{ route('results_status.store') }}" method="POST" id="termForm">
            @csrf
            <div class="mt-4 bg-white rounded border border-gray-300 p-6">
                <div class="form-group mb-4">
                    <label for="year" class="block text-sm font-semibold text-gray-700 mb-2">Year</label>
                    <select name="year" id="year" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select year</option>
                        <option value="2025">2025</option>
                        <option value="2026">2026</option>
                        <option value="2027">2027</option>
                        <option value="2028">2028</option>
                        <option value="2029">2029</option>
                        <option value="2030">2030</option>
                    </select>
                    @error('year')
                        <div class="text-danger text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="result_period" class="block text-gray-700 font-bold mb-2">Select Term:</label>
                    <select name="result_period" id="result_period" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select a Term</option>
                        <option value="first">First Term</option>
                        <option value="second">Second Term</option>
                        <option value="third">Third Term</option>
                    </select>
                    @error('result_period')
                        <div class="text-danger text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Fee Payment Types Section -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-700">Fee Payment Types</h3>
                    </div>

                    <div id="feeTypesContainer" class="space-y-4">
                        @if($feeTypes->count() > 0)
                            <div class="fee-type-row bg-gray-50 p-4 rounded border border-gray-200">
                                <div class="grid grid-cols-12 gap-4">
                                    <div class="col-span-5">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Fee Type</label>
                                        <select name="fees[0][fee_type_id]" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500" required>
                                            <option value="">Select Fee Type</option>
                                            @foreach($feeTypes as $type)
                                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-span-5">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount ($)</label>
                                        <input type="number" name="fees[0][amount]" step="0.01" min="0" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500" placeholder="0.00" required>
                                    </div>
                                    <div class="col-span-2 flex items-end gap-2">
                                        <button type="button" class="add-fee-row bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded flex items-center justify-center" title="Add another fee">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </button>
                                        <button type="button" class="remove-fee-type bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded flex items-center justify-center" title="Remove fee">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <p>No fee types available. Please add fee types first.</p>
                            </div>
                        @endif
                    </div>

                    @error('fees')
                        <div class="text-danger text-red-500 text-sm mt-2">{{ $message }}</div>
                    @enderror
                </div>
             
                <div class="flex justify-end space-x-2">
                    <a href="{{ route('results_status.index') }}" class="btn btn-secondary bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-6 rounded">Cancel</a>
                    <button type="submit" class="btn btn-primary bg-blue-500 hover:bg-blue-600 text-white py-2 px-6 rounded">Create Term</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
let feeTypeIndex = 1;

document.addEventListener('DOMContentLoaded', function() {
    const feeTypesContainer = document.getElementById('feeTypesContainer');
    
    // Add new fee type row when '+' button is clicked
    feeTypesContainer.addEventListener('click', function(e) {
        // Check if the clicked element is the add button or inside it
        const addBtn = e.target.closest('.add-fee-row');
        if (addBtn) {
            const newRow = `
                <div class="fee-type-row bg-gray-50 p-4 rounded border border-gray-200">
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fee Type</label>
                            <select name="fees[${feeTypeIndex}][fee_type_id]" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Select Fee Type</option>
                                @foreach($feeTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Amount ($)</label>
                            <input type="number" name="fees[${feeTypeIndex}][amount]" step="0.01" min="0" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500" placeholder="0.00" required>
                        </div>
                        <div class="col-span-2 flex items-end gap-2">
                            <button type="button" class="add-fee-row bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded flex items-center justify-center" title="Add another fee">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                            <button type="button" class="remove-fee-type bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded flex items-center justify-center" title="Remove fee">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // Insert the new row after the current row
            const currentRow = addBtn.closest('.fee-type-row');
            currentRow.insertAdjacentHTML('afterend', newRow);
            feeTypeIndex++;
        }
        
        // Remove fee type row
        const removeBtn = e.target.closest('.remove-fee-type');
        if (removeBtn) {
            const row = removeBtn.closest('.fee-type-row');
            const allRows = feeTypesContainer.querySelectorAll('.fee-type-row');
            
            // Only allow removal if there's more than one row
            if (allRows.length > 1 && row) {
                row.remove();
            } else {
                alert('You must have at least one fee type.');
            }
        }
    });
});
</script>
@endpush