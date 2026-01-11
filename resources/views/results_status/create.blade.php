@extends('layouts.app')

@section('content')
    <div class="create-results-status">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-gray-700 uppercase font-bold">Create New Term</h2>
            </div>
        </div>

        <!-- Teacher Session Reminder Alert -->
        <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-6 rounded-r-lg shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-semibold text-amber-800">Remember to Update Teacher Sessions</h3>
                    <p class="text-sm text-amber-700 mt-1">
                        Before or after creating a new term, please ensure teacher work sessions (Morning, Afternoon, or Both) are correctly assigned.
                    </p>
                    <div class="mt-3">
                        <a href="{{ route('teacher.sessions') }}" class="inline-flex items-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Change Teacher Sessions
                        </a>
                    </div>
                </div>
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

                <!-- Teacher Attendance Settings Section -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-700 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Teacher Attendance Settings
                        </h3>
                    </div>

                    <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
                        <!-- Session Mode Toggle -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-3">School Session Mode</label>
                            <div class="flex space-x-4">
                                <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all border-purple-500 bg-white" id="single_mode_label">
                                    <input type="radio" name="session_mode" value="single" checked
                                           class="h-4 w-4 text-purple-600 focus:ring-purple-500"
                                           onchange="toggleAttendanceSessionMode()">
                                    <div class="ml-3">
                                        <span class="block font-medium text-gray-900">Single Session</span>
                                        <span class="block text-xs text-gray-500">Full day schedule</span>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition-all border-gray-200 hover:border-gray-300" id="dual_mode_label">
                                    <input type="radio" name="session_mode" value="dual"
                                           class="h-4 w-4 text-purple-600 focus:ring-purple-500"
                                           onchange="toggleAttendanceSessionMode()">
                                    <div class="ml-3">
                                        <span class="block font-medium text-gray-900">Dual Session</span>
                                        <span class="block text-xs text-gray-500">Morning & Afternoon</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Morning/Single Session Times -->
                        <div class="bg-white p-4 rounded border border-gray-200 mb-4">
                            <h4 class="font-medium text-amber-700 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <span id="morning_session_label">Work Hours</span>
                            </h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Check-In Time</label>
                                    <input type="time" name="check_in_time" id="check_in_time" value="07:30"
                                           class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-purple-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Check-Out Time</label>
                                    <input type="time" name="check_out_time" id="check_out_time" value="16:30"
                                           class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-purple-500">
                                </div>
                            </div>
                        </div>

                        <!-- Afternoon Session Times (hidden by default) -->
                        <div id="afternoon_session_section" class="bg-white p-4 rounded border border-gray-200 mb-4 hidden">
                            <h4 class="font-medium text-indigo-700 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                </svg>
                                Afternoon Session
                            </h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Check-In Time</label>
                                    <input type="time" name="afternoon_check_in_time" id="afternoon_check_in_time" value="12:30"
                                           class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-purple-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Check-Out Time</label>
                                    <input type="time" name="afternoon_check_out_time" id="afternoon_check_out_time" value="17:30"
                                           class="w-full border rounded px-4 py-2 focus:ring-2 focus:ring-purple-500">
                                </div>
                            </div>
                        </div>

                        <!-- Grace Period -->
                        <div class="bg-white p-4 rounded border border-gray-200">
                            <div class="flex items-center">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Late Grace Period (minutes)</label>
                                    <input type="number" name="late_grace_minutes" id="late_grace_minutes" value="0" min="0" max="60"
                                           class="w-24 border rounded px-4 py-2 focus:ring-2 focus:ring-purple-500">
                                </div>
                                <span class="ml-4 text-sm text-gray-500">Minutes after check-in time before marking as late</span>
                            </div>
                        </div>
                    </div>
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
let dayFeeIndex = 1;
let boardingFeeIndex = 1;

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

// Toggle attendance session mode
function toggleAttendanceSessionMode() {
    const isDual = document.querySelector('input[name="session_mode"]:checked').value === 'dual';
    document.getElementById('afternoon_session_section').classList.toggle('hidden', !isDual);
    document.getElementById('morning_session_label').textContent = isDual ? 'Morning Session' : 'Work Hours';
    
    // Update radio button styles
    const singleLabel = document.getElementById('single_mode_label');
    const dualLabel = document.getElementById('dual_mode_label');
    
    if (isDual) {
        singleLabel.classList.remove('border-purple-500', 'bg-white');
        singleLabel.classList.add('border-gray-200');
        dualLabel.classList.add('border-purple-500', 'bg-white');
        dualLabel.classList.remove('border-gray-200');
    } else {
        singleLabel.classList.add('border-purple-500', 'bg-white');
        singleLabel.classList.remove('border-gray-200');
        dualLabel.classList.remove('border-purple-500', 'bg-white');
        dualLabel.classList.add('border-gray-200');
    }
}

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