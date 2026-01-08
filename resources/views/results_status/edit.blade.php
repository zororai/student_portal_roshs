@extends('layouts.app')

@section('content')
    <div class="edit-results-status">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-gray-700 uppercase font-bold">Edit Term - {{ $resultStatus->year }} {{ ucfirst($resultStatus->result_period) }} Term</h2>
            </div>
            <div class="flex flex-wrap items-center">
                <a href="{{ route('results_status.index') }}" class="bg-gray-700 text-white text-sm uppercase py-2 px-4 flex items-center rounded">
                    <svg class="w-3 h-3 fill-current" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="long-arrow-alt-left" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                        <path fill="currentColor" d="M134.059 296H436c6.627 0 12-5.373 12-12v-56c0-6.627-5.373-12-12-12H134.059v-46.059c0-21.382-25.851-32.09-40.971-16.971L7.029 239.029c-9.373 9.373-9.373 24.569 0 33.941l86.059 86.059c15.119 15.119 40.971 4.411 40.971-16.971V296z"></path>
                    </svg>
                    <span class="ml-2 text-xs font-semibold">Back</span>
                </a>
            </div>
        </div>

        <form action="{{ route('results_status.update', $resultStatus->id) }}" method="POST" id="termForm">
            @csrf
            @method('PUT')
            <div class="mt-4 bg-white rounded border border-gray-300 p-6">
                <div class="form-group mb-4">
                    <label for="year" class="block text-sm font-semibold text-gray-700 mb-2">Year</label>
                    <select name="year" id="year" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select year</option>
                        @for($y = 2025; $y <= 2030; $y++)
                            <option value="{{ $y }}" {{ $resultStatus->year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                    @error('year')
                        <div class="text-danger text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="result_period" class="block text-gray-700 font-bold mb-2">Select Term:</label>
                    <select name="result_period" id="result_period" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select a Term</option>
                        <option value="first" {{ $resultStatus->result_period == 'first' ? 'selected' : '' }}>First Term</option>
                        <option value="second" {{ $resultStatus->result_period == 'second' ? 'selected' : '' }}>Second Term</option>
                        <option value="third" {{ $resultStatus->result_period == 'third' ? 'selected' : '' }}>Third Term</option>
                    </select>
                    @error('result_period')
                        <div class="text-danger text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Day Scholar Fees Section -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-700 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            Day Scholar Fees
                        </h3>
                    </div>

                    <div id="dayFeesContainer" class="space-y-4 bg-blue-50 p-4 rounded-lg border border-blue-200">
                        @if($feeTypes->count() > 0)
                            @forelse($dayFees as $index => $fee)
                                <div class="fee-type-row bg-white p-4 rounded border border-gray-200">
                                    <div class="grid grid-cols-12 gap-4">
                                        <div class="col-span-5">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Fee Type</label>
                                            <select name="day_fees[{{ $index }}][fee_type_id]" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500" required>
                                                <option value="">Select Fee Type</option>
                                                @foreach($feeTypes as $type)
                                                    <option value="{{ $type->id }}" {{ $fee->fee_type_id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-span-5">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Amount ($)</label>
                                            <input type="number" name="day_fees[{{ $index }}][amount]" step="0.01" min="0" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500 day-fee-amount" placeholder="0.00" value="{{ $fee->amount }}" required>
                                        </div>
                                        <div class="col-span-2 flex items-end gap-2">
                                            <button type="button" class="add-day-fee bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded flex items-center justify-center" title="Add another fee">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                            </button>
                                            <button type="button" class="remove-day-fee bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded flex items-center justify-center" title="Remove fee">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="fee-type-row bg-white p-4 rounded border border-gray-200">
                                    <div class="grid grid-cols-12 gap-4">
                                        <div class="col-span-5">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Fee Type</label>
                                            <select name="day_fees[0][fee_type_id]" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500" required>
                                                <option value="">Select Fee Type</option>
                                                @foreach($feeTypes as $type)
                                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-span-5">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Amount ($)</label>
                                            <input type="number" name="day_fees[0][amount]" step="0.01" min="0" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500 day-fee-amount" placeholder="0.00" required>
                                        </div>
                                        <div class="col-span-2 flex items-end gap-2">
                                            <button type="button" class="add-day-fee bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded flex items-center justify-center" title="Add another fee">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                            </button>
                                            <button type="button" class="remove-day-fee bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded flex items-center justify-center" title="Remove fee">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <p>No fee types available. Please add fee types first.</p>
                            </div>
                        @endif
                        <div class="text-right font-semibold text-blue-700">
                            Day Total: <span id="dayTotalDisplay">$0.00</span>
                        </div>
                    </div>

                    @error('day_fees')
                        <div class="text-danger text-red-500 text-sm mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Boarding Fees Section -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-700 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            Boarding Fees
                        </h3>
                    </div>

                    <div id="boardingFeesContainer" class="space-y-4 bg-green-50 p-4 rounded-lg border border-green-200">
                        @if($feeTypes->count() > 0)
                            @forelse($boardingFees as $index => $fee)
                                <div class="fee-type-row bg-white p-4 rounded border border-gray-200">
                                    <div class="grid grid-cols-12 gap-4">
                                        <div class="col-span-5">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Fee Type</label>
                                            <select name="boarding_fees[{{ $index }}][fee_type_id]" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500" required>
                                                <option value="">Select Fee Type</option>
                                                @foreach($feeTypes as $type)
                                                    <option value="{{ $type->id }}" {{ $fee->fee_type_id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-span-5">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Amount ($)</label>
                                            <input type="number" name="boarding_fees[{{ $index }}][amount]" step="0.01" min="0" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500 boarding-fee-amount" placeholder="0.00" value="{{ $fee->amount }}" required>
                                        </div>
                                        <div class="col-span-2 flex items-end gap-2">
                                            <button type="button" class="add-boarding-fee bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded flex items-center justify-center" title="Add another fee">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                            </button>
                                            <button type="button" class="remove-boarding-fee bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded flex items-center justify-center" title="Remove fee">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="fee-type-row bg-white p-4 rounded border border-gray-200">
                                    <div class="grid grid-cols-12 gap-4">
                                        <div class="col-span-5">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Fee Type</label>
                                            <select name="boarding_fees[0][fee_type_id]" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500" required>
                                                <option value="">Select Fee Type</option>
                                                @foreach($feeTypes as $type)
                                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-span-5">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Amount ($)</label>
                                            <input type="number" name="boarding_fees[0][amount]" step="0.01" min="0" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500 boarding-fee-amount" placeholder="0.00" required>
                                        </div>
                                        <div class="col-span-2 flex items-end gap-2">
                                            <button type="button" class="add-boarding-fee bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded flex items-center justify-center" title="Add another fee">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                            </button>
                                            <button type="button" class="remove-boarding-fee bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded flex items-center justify-center" title="Remove fee">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <p>No fee types available. Please add fee types first.</p>
                            </div>
                        @endif
                        <div class="text-right font-semibold text-green-700">
                            Boarding Total: <span id="boardingTotalDisplay">$0.00</span>
                        </div>
                    </div>

                    @error('boarding_fees')
                        <div class="text-danger text-red-500 text-sm mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Total Fees Summary -->
                <div class="mb-6 grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                        <h4 class="text-lg font-bold text-gray-800">Day Scholar Total</h4>
                        <p class="text-sm text-gray-600">Total fees for day students</p>
                        <div id="totalDayFeesDisplay" class="text-2xl font-bold text-blue-600 mt-2">$0.00</div>
                    </div>
                    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
                        <h4 class="text-lg font-bold text-gray-800">Boarding Total</h4>
                        <p class="text-sm text-gray-600">Total fees for boarding students</p>
                        <div id="totalBoardingFeesDisplay" class="text-2xl font-bold text-green-600 mt-2">$0.00</div>
                    </div>
                </div>

                <div class="flex justify-end space-x-2">
                    <a href="{{ route('results_status.index') }}" class="btn btn-secondary bg-gray-300 hover:bg-gray-400 text-gray-700 py-2 px-6 rounded">Cancel</a>
                    <button type="submit" class="btn btn-primary bg-blue-500 hover:bg-blue-600 text-white py-2 px-6 rounded">Update Term</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
<script>
let dayFeeIndex = {{ $dayFees->count() > 0 ? $dayFees->count() : 1 }};
let boardingFeeIndex = {{ $boardingFees->count() > 0 ? $boardingFees->count() : 1 }};

// Function to calculate totals
function updateTotals() {
    // Calculate Day fees total
    let dayTotal = 0;
    document.querySelectorAll('.day-fee-amount').forEach(input => {
        dayTotal += parseFloat(input.value) || 0;
    });
    document.getElementById('dayTotalDisplay').textContent = '$' + dayTotal.toFixed(2);
    document.getElementById('totalDayFeesDisplay').textContent = '$' + dayTotal.toFixed(2);
    
    // Calculate Boarding fees total
    let boardingTotal = 0;
    document.querySelectorAll('.boarding-fee-amount').forEach(input => {
        boardingTotal += parseFloat(input.value) || 0;
    });
    document.getElementById('boardingTotalDisplay').textContent = '$' + boardingTotal.toFixed(2);
    document.getElementById('totalBoardingFeesDisplay').textContent = '$' + boardingTotal.toFixed(2);
}

// Fee type options HTML
const feeTypeOptions = `
    <option value="">Select Fee Type</option>
    @foreach($feeTypes as $type)
        <option value="{{ $type->id }}">{{ $type->name }}</option>
    @endforeach
`;

document.addEventListener('DOMContentLoaded', function() {
    const dayFeesContainer = document.getElementById('dayFeesContainer');
    const boardingFeesContainer = document.getElementById('boardingFeesContainer');
    
    // Update totals on any input change
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('day-fee-amount') || e.target.classList.contains('boarding-fee-amount')) {
            updateTotals();
        }
    });
    
    // Initial calculation
    updateTotals();
    
    // Day Fees - Add row
    dayFeesContainer.addEventListener('click', function(e) {
        const addBtn = e.target.closest('.add-day-fee');
        if (addBtn) {
            const newRow = `
                <div class="fee-type-row bg-white p-4 rounded border border-gray-200">
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fee Type</label>
                            <select name="day_fees[${dayFeeIndex}][fee_type_id]" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500" required>
                                ${feeTypeOptions}
                            </select>
                        </div>
                        <div class="col-span-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Amount ($)</label>
                            <input type="number" name="day_fees[${dayFeeIndex}][amount]" step="0.01" min="0" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-blue-500 day-fee-amount" placeholder="0.00" required>
                        </div>
                        <div class="col-span-2 flex items-end gap-2">
                            <button type="button" class="add-day-fee bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded flex items-center justify-center" title="Add another fee">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                            <button type="button" class="remove-day-fee bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded flex items-center justify-center" title="Remove fee">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            const currentRow = addBtn.closest('.fee-type-row');
            currentRow.insertAdjacentHTML('afterend', newRow);
            dayFeeIndex++;
        }
        
        // Remove day fee row
        const removeBtn = e.target.closest('.remove-day-fee');
        if (removeBtn) {
            const row = removeBtn.closest('.fee-type-row');
            const allRows = dayFeesContainer.querySelectorAll('.fee-type-row');
            if (allRows.length > 1 && row) {
                row.remove();
                updateTotals();
            } else {
                alert('You must have at least one day fee type.');
            }
        }
    });
    
    // Boarding Fees - Add row
    boardingFeesContainer.addEventListener('click', function(e) {
        const addBtn = e.target.closest('.add-boarding-fee');
        if (addBtn) {
            const newRow = `
                <div class="fee-type-row bg-white p-4 rounded border border-gray-200">
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Fee Type</label>
                            <select name="boarding_fees[${boardingFeeIndex}][fee_type_id]" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500" required>
                                ${feeTypeOptions}
                            </select>
                        </div>
                        <div class="col-span-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Amount ($)</label>
                            <input type="number" name="boarding_fees[${boardingFeeIndex}][amount]" step="0.01" min="0" class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-green-500 boarding-fee-amount" placeholder="0.00" required>
                        </div>
                        <div class="col-span-2 flex items-end gap-2">
                            <button type="button" class="add-boarding-fee bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded flex items-center justify-center" title="Add another fee">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                            <button type="button" class="remove-boarding-fee bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded flex items-center justify-center" title="Remove fee">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            const currentRow = addBtn.closest('.fee-type-row');
            currentRow.insertAdjacentHTML('afterend', newRow);
            boardingFeeIndex++;
        }
        
        // Remove boarding fee row
        const removeBtn = e.target.closest('.remove-boarding-fee');
        if (removeBtn) {
            const row = removeBtn.closest('.fee-type-row');
            const allRows = boardingFeesContainer.querySelectorAll('.fee-type-row');
            if (allRows.length > 1 && row) {
                row.remove();
                updateTotals();
            } else {
                alert('You must have at least one boarding fee type.');
            }
        }
    });
});
</script>
@endpush