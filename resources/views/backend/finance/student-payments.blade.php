@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Student Payments</h2>
        <div class="flex space-x-2">
            <button onclick="openPaymentModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Record Payment
            </button>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search Student</label>
                <input type="text" id="searchStudent" placeholder="Search by name or roll number..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Class</label>
                <select id="filterClass" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Classes</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
                <select id="filterStatus" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="paid">Fully Paid</option>
                    <option value="partial">Partially Paid</option>
                    <option value="unpaid">Unpaid</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roll No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Fees</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Paid</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($students as $student)
                    @php
                        $totalFees = $student->total_fees ?? 0;
                        $amountPaid = $student->amount_paid ?? 0;
                        $balance = $totalFees - $amountPaid;
                        
                        if ($balance == 0 && $totalFees > 0) {
                            $status = 'paid';
                            $statusClass = 'bg-green-100 text-green-800';
                            $statusText = 'Fully Paid';
                        } elseif ($amountPaid > 0 && $balance > 0) {
                            $status = 'partial';
                            $statusClass = 'bg-yellow-100 text-yellow-800';
                            $statusText = 'Partially Paid';
                        } else {
                            $status = 'unpaid';
                            $statusClass = 'bg-red-100 text-red-800';
                            $statusText = 'Unpaid';
                        }
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $student->roll_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($student->name) }}&background=random" alt="">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $student->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $student->class->class_name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $student->parent->user->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            ${{ number_format($totalFees, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">
                            ${{ number_format($amountPaid, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold {{ $balance > 0 ? 'text-red-600' : 'text-gray-900' }}">
                            ${{ number_format($balance, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                {{ $statusText }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('student.show', $student->id) }}" class="text-gray-600 hover:text-gray-900" title="View Details">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                            No students found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $students->links() }}
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Record Payment</h3>
                    <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form id="paymentForm" method="POST" action="{{ route('finance.payments.store') }}">
                    @csrf
                    <input type="hidden" name="results_status_id" id="modal_results_status_id" value="{{ $currentTerm->id ?? '' }}">

                    <!-- Student Selection Filters -->
                    <div class="mb-4 bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-bold text-gray-700 mb-3">Find Student</label>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Filter by Class</label>
                                <select id="modal_class_filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" onchange="filterStudents()">
                                    <option value="">All Classes</option>
                                    @php
                                        $classes = $students->pluck('class')->unique()->filter();
                                    @endphp
                                    @foreach($classes as $class)
                                        @if($class)
                                            <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Search Student</label>
                                <input type="text" id="modal_student_search" placeholder="Name or Roll Number..." 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                                       oninput="filterStudents()">
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-xs text-gray-600 mb-1">Select Student</label>
                            <select name="student_id" id="modal_student_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required onchange="updateStudentBalance()">
                                <option value="">-- Select a student --</option>
                                @foreach($students as $student)
                                    @php
                                        $totalFees = $student->total_fees ?? 0;
                                        $amountPaid = $student->amount_paid ?? 0;
                                        $balance = $totalFees - $amountPaid;
                                    @endphp
                                    <option value="{{ $student->id }}" 
                                            data-name="{{ $student->name }}" 
                                            data-balance="{{ $balance }}"
                                            data-class="{{ $student->class->id ?? '' }}"
                                            data-roll="{{ $student->roll_number }}"
                                            class="student-option">
                                        {{ $student->name }} ({{ $student->roll_number }}) - {{ $student->class->class_name ?? 'N/A' }} - Balance: ${{ number_format($balance, 2) }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1" id="student_count">{{ $students->count() }} students available</p>
                        </div>
                    </div>

                    <!-- Student Info -->
                    <div class="bg-blue-50 p-4 rounded-lg mb-4" id="student_info_section" style="display: none;">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Student Name</p>
                                <p class="font-semibold text-gray-900" id="modal_student_name"></p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Outstanding Balance</p>
                                <p class="text-2xl font-bold text-red-600" id="modal_balance">$0.00</p>
                            </div>
                        </div>
                    </div>

                    <!-- Fee Types Selection -->
                    <div class="mb-4" id="fee_types_section" style="display: none;">
                        <label class="block text-sm font-bold text-gray-700 mb-3">Select Fee Types to Pay</label>
                        <div class="border border-gray-300 rounded-lg p-4 max-h-64 overflow-y-auto" id="feeTypesContainer">
                            @if(isset($currentTerm) && $currentTerm->termFees->count() > 0)
                                @foreach($currentTerm->termFees as $termFee)
                                <div class="flex items-center justify-between py-2 border-b border-gray-200 last:border-0">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="fee_types[]" value="{{ $termFee->id }}" 
                                               class="fee-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                               data-amount="{{ $termFee->amount }}">
                                        <label class="ml-3 text-sm font-medium text-gray-700">{{ $termFee->feeType->name }}</label>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">${{ number_format($termFee->amount, 2) }}</span>
                                </div>
                                @endforeach
                            @else
                                <p class="text-gray-500 text-center py-4">No fee types available for current term</p>
                            @endif
                        </div>
                    </div>

                    <!-- Payment Details -->
                    <div class="grid grid-cols-2 gap-4 mb-4" id="payment_details_section" style="display: none;">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Date</label>
                            <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                            <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Select Method</option>
                                <option value="Cash">Cash</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Mobile Money">Mobile Money</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Card">Card</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4" id="reference_section" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reference Number (Optional)</label>
                        <input type="text" name="reference_number" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                               placeholder="Transaction/Receipt number">
                    </div>

                    <div class="mb-4" id="notes_section" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                        <textarea name="notes" rows="2" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                                  placeholder="Additional notes about this payment"></textarea>
                    </div>

                    <!-- Payment Summary -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-4" id="payment_summary_section" style="display: none;">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">Selected Fees Total:</span>
                            <span class="text-lg font-bold text-gray-900" id="selected_total">$0.00</span>
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">Current Balance:</span>
                            <span class="text-lg font-semibold text-gray-700" id="current_balance">$0.00</span>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-gray-300">
                            <span class="text-sm font-bold text-gray-700">Remaining Balance:</span>
                            <span class="text-xl font-bold text-blue-600" id="remaining_balance">$0.00</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-3">
                        <button type="button" onclick="closePaymentModal()" 
                                class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-300">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="flex-1 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                            Record Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let currentBalance = 0;

    function openPaymentModal() {
        // Reset form and hide all sections
        document.getElementById('paymentForm').reset();
        document.getElementById('student_info_section').style.display = 'none';
        document.getElementById('fee_types_section').style.display = 'none';
        document.getElementById('payment_details_section').style.display = 'none';
        document.getElementById('reference_section').style.display = 'none';
        document.getElementById('notes_section').style.display = 'none';
        document.getElementById('payment_summary_section').style.display = 'none';
        
        document.getElementById('paymentModal').classList.remove('hidden');
    }

    function closePaymentModal() {
        document.getElementById('paymentModal').classList.add('hidden');
        document.getElementById('paymentForm').reset();
    }

    function filterStudents() {
        const classFilter = document.getElementById('modal_class_filter').value;
        const searchText = document.getElementById('modal_student_search').value.toLowerCase();
        const studentSelect = document.getElementById('modal_student_id');
        const options = studentSelect.querySelectorAll('.student-option');
        
        let visibleCount = 0;
        
        options.forEach(option => {
            const studentName = option.dataset.name.toLowerCase();
            const studentRoll = option.dataset.roll.toLowerCase();
            const studentClass = option.dataset.class;
            
            // Check class filter
            const classMatch = !classFilter || studentClass === classFilter;
            
            // Check search text
            const searchMatch = !searchText || 
                               studentName.includes(searchText) || 
                               studentRoll.includes(searchText);
            
            // Show or hide option
            if (classMatch && searchMatch) {
                option.style.display = '';
                visibleCount++;
            } else {
                option.style.display = 'none';
            }
        });
        
        // Update count
        document.getElementById('student_count').textContent = visibleCount + ' student' + (visibleCount !== 1 ? 's' : '') + ' found';
        
        // Reset selection if current selection is hidden
        if (studentSelect.value) {
            const selectedOption = studentSelect.options[studentSelect.selectedIndex];
            if (selectedOption.style.display === 'none') {
                studentSelect.value = '';
                updateStudentBalance();
            }
        }
    }

    function updateStudentBalance() {
        const studentSelect = document.getElementById('modal_student_id');
        const selectedOption = studentSelect.options[studentSelect.selectedIndex];
        
        if (selectedOption.value) {
            const studentName = selectedOption.dataset.name;
            const balance = parseFloat(selectedOption.dataset.balance);
            
            // Update student info
            document.getElementById('modal_student_name').textContent = studentName;
            document.getElementById('modal_balance').textContent = '$' + balance.toFixed(2);
            currentBalance = balance;
            
            // Show all sections
            document.getElementById('student_info_section').style.display = 'block';
            document.getElementById('fee_types_section').style.display = 'block';
            document.getElementById('payment_details_section').style.display = 'grid';
            document.getElementById('reference_section').style.display = 'block';
            document.getElementById('notes_section').style.display = 'block';
            document.getElementById('payment_summary_section').style.display = 'block';
            
            // Reset checkboxes and calculations
            document.querySelectorAll('.fee-checkbox').forEach(cb => cb.checked = false);
            updatePaymentCalculation();
        } else {
            // Hide all sections if no student selected
            document.getElementById('student_info_section').style.display = 'none';
            document.getElementById('fee_types_section').style.display = 'none';
            document.getElementById('payment_details_section').style.display = 'none';
            document.getElementById('reference_section').style.display = 'none';
            document.getElementById('notes_section').style.display = 'none';
            document.getElementById('payment_summary_section').style.display = 'none';
        }
    }

    function updatePaymentCalculation() {
        let selectedTotal = 0;
        
        document.querySelectorAll('.fee-checkbox:checked').forEach(checkbox => {
            selectedTotal += parseFloat(checkbox.dataset.amount);
        });
        
        const remainingBalance = currentBalance - selectedTotal;
        
        document.getElementById('selected_total').textContent = '$' + selectedTotal.toFixed(2);
        document.getElementById('current_balance').textContent = '$' + currentBalance.toFixed(2);
        document.getElementById('remaining_balance').textContent = '$' + remainingBalance.toFixed(2);
        
        // Change color based on remaining balance
        const remainingElement = document.getElementById('remaining_balance');
        if (remainingBalance < 0) {
            remainingElement.classList.remove('text-blue-600', 'text-green-600');
            remainingElement.classList.add('text-red-600');
        } else if (remainingBalance === 0) {
            remainingElement.classList.remove('text-blue-600', 'text-red-600');
            remainingElement.classList.add('text-green-600');
        } else {
            remainingElement.classList.remove('text-red-600', 'text-green-600');
            remainingElement.classList.add('text-blue-600');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchStudent');
        const filterClass = document.getElementById('filterClass');
        const filterStatus = document.getElementById('filterStatus');
        
        // Add event listeners for fee checkboxes
        document.querySelectorAll('.fee-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', updatePaymentCalculation);
        });
        
        // Close modal when clicking outside
        document.getElementById('paymentModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePaymentModal();
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePaymentModal();
            }
        });
        
        // Add event listeners for filtering (implement AJAX filtering if needed)
        searchInput.addEventListener('input', function() {
            // Implement search functionality
        });
        
        filterClass.addEventListener('change', function() {
            // Implement class filter
        });
        
        filterStatus.addEventListener('change', function() {
            // Implement status filter
        });
    });
</script>

@endsection
