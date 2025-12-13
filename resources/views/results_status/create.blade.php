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
                        <button type="button" id="addFeeType" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Fee Type
                        </button>
                    </div>

                    <div id="feeTypesContainer" class="space-y-4">
                        @if($feeTypes->count() > 0)
                            @foreach($feeTypes as $index => $feeType)
                            <div class="fee-type-row bg-gray-50 p-4 rounded border border-gray-200">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Fee Type</label>
                                        <select name="fees[{{ $index }}][fee_type_id]" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500" required>
                                            <option value="">Select Fee Type</option>
                                            @foreach($feeTypes as $type)
                                                <option value="{{ $type->id }}" {{ $type->id == $feeType->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount ($)</label>
                                        <input type="number" name="fees[{{ $index }}][amount]" step="0.01" min="0" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500" placeholder="0.00" required>
                                    </div>
                                    <div class="flex items-end">
                                        <button type="button" class="remove-fee-type bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded w-full">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
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
let feeTypeIndex = {{ $feeTypes->count() }};

document.addEventListener('DOMContentLoaded', function() {
    const addFeeTypeBtn = document.getElementById('addFeeType');
    const feeTypesContainer = document.getElementById('feeTypesContainer');
    
    // Add new fee type row
    addFeeTypeBtn.addEventListener('click', function() {
        const newRow = `
            <div class="fee-type-row bg-gray-50 p-4 rounded border border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Fee Type</label>
                        <select name="fees[${feeTypeIndex}][fee_type_id]" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500" required>
                            <option value="">Select Fee Type</option>
                            @foreach($feeTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount ($)</label>
                        <input type="number" name="fees[${feeTypeIndex}][amount]" step="0.01" min="0" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500" placeholder="0.00" required>
                    </div>
                    <div class="flex items-end">
                        <button type="button" class="remove-fee-type bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded w-full">
                            Remove
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        feeTypesContainer.insertAdjacentHTML('beforeend', newRow);
        feeTypeIndex++;
    });
    
    // Remove fee type row
    feeTypesContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-fee-type') || e.target.closest('.remove-fee-type')) {
            const row = e.target.closest('.fee-type-row');
            if (row) {
                row.remove();
            }
        }
    });
});
</script>
@endpush