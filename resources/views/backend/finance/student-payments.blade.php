@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Student Payments</h2>
        <div class="flex space-x-2">
            <a href="{{ route('finance.student-payments.export', request()->query()) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export to Excel
            </a>
            <button onclick="openPaymentModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Record Payment
            </button>
        </div>
    </div>

    <!-- Filter -->
    <form method="GET" action="{{ route('finance.student-payments') }}" id="filterForm">
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Class</label>
                    <select name="class_id" id="filterClass" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Classes</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->class_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Status</label>
                    <select name="status" id="filterStatus" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">All Status</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Fully Paid</option>
                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partially Paid</option>
                        <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    </select>
                </div>
            </div>
            <div class="mt-3 flex justify-end gap-2">
                <a href="{{ route('finance.student-payments') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm">
                    Clear Filters
                </a>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                    Apply Filters
                </button>
            </div>
        </div>
    </form>

    <!-- Students Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto overflow-y-visible" style="max-width: 100%;">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roll No.</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Class</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parent</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fees Paid For</th>
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
                        <td class="px-6 py-4">
                            @if($student->payments && $student->payments->count() > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($student->payments->unique('term_fee_id') as $payment)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800" title="Paid on {{ $payment->payment_date->format('M d, Y') }}">
                                            {{ $payment->termFee->feeType->name ?? 'N/A' }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">No payments yet</span>
                            @endif
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
                        @php
                            $paymentHistory = $student->payments->map(function($p) {
                                return [
                                    'id' => $p->id,
                                    'amount' => $p->amount_paid,
                                    'date' => $p->payment_date->format('M d, Y'),
                                    'method' => $p->payment_method,
                                    'fee_type' => $p->termFee->feeType->name ?? 'N/A',
                                    'reference' => $p->reference_number,
                                    'term' => ($p->resultsStatus->result_period ?? '') . ' ' . ($p->resultsStatus->year ?? '')
                                ];
                            })->values()->toArray();
                        @endphp
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <button type="button" 
                                        onclick="showPaymentHistory({{ $student->id }}, '{{ addslashes($student->name) }}', {{ json_encode($paymentHistory) }})"
                                        class="text-blue-600 hover:text-blue-900" title="Payment History">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
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
                        <td colspan="10" class="px-6 py-4 text-center text-gray-500">
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

    <!-- Payment History Modal -->
    <div id="paymentHistoryModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-3xl shadow-lg rounded-md bg-white mb-10 max-h-[90vh] overflow-y-auto">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900">
                        <svg class="w-6 h-6 inline-block mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Payment History
                    </h3>
                    <button onclick="closePaymentHistoryModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Student Name -->
                <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                    <p class="text-sm text-gray-600">Student</p>
                    <p class="text-lg font-bold text-gray-900" id="history_student_name">-</p>
                </div>

                <!-- Payment History Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Term</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fee Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="payment_history_body" class="bg-white divide-y divide-gray-200">
                            <!-- Populated by JavaScript -->
                        </tbody>
                        <tfoot class="bg-gray-100">
                            <tr>
                                <td colspan="6" class="px-4 py-3 text-right text-sm font-bold text-gray-700">Total Paid:</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-green-600" id="history_total_paid">$0.00</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- No Payments Message -->
                <div id="no_payments_message" class="hidden text-center py-8">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-gray-500">No payment records found for this student.</p>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex justify-between">
                    <button onclick="printPaymentHistory()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Print
                    </button>
                    <button onclick="closePaymentHistoryModal()" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white mb-10 max-h-[90vh] overflow-y-auto">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Record Payment</h3>
                    <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Alert Container -->
                <div id="modal_alert" class="hidden mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <span id="modal_alert_message" class="font-medium"></span>
                    </div>
                    <button onclick="hideAlert()" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                        </svg>
                    </button>
                </div>

                <!-- Stepper -->
                <div class="mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center flex-1">
                            <div class="flex items-center text-blue-600 relative">
                                <div class="rounded-full transition duration-500 ease-in-out h-10 w-10 flex items-center justify-center border-2 border-blue-600 bg-blue-600 text-white font-bold" id="step1-circle">1</div>
                                <div class="absolute top-0 -ml-10 text-center mt-12 w-32 text-xs font-medium text-blue-600" id="step1-label">Select Student</div>
                            </div>
                            <div class="flex-auto border-t-2 transition duration-500 ease-in-out border-gray-300" id="line1"></div>
                        </div>
                        <div class="flex items-center flex-1">
                            <div class="flex items-center text-gray-500 relative">
                                <div class="rounded-full transition duration-500 ease-in-out h-10 w-10 flex items-center justify-center border-2 border-gray-300 bg-white font-bold" id="step2-circle">2</div>
                                <div class="absolute top-0 -ml-10 text-center mt-12 w-32 text-xs font-medium text-gray-500" id="step2-label">Select Fees</div>
                            </div>
                            <div class="flex-auto border-t-2 transition duration-500 ease-in-out border-gray-300" id="line2"></div>
                        </div>
                        <div class="flex items-center flex-1">
                            <div class="flex items-center text-gray-500 relative">
                                <div class="rounded-full transition duration-500 ease-in-out h-10 w-10 flex items-center justify-center border-2 border-gray-300 bg-white font-bold" id="step3-circle">3</div>
                                <div class="absolute top-0 -ml-10 text-center mt-12 w-32 text-xs font-medium text-gray-500" id="step3-label">Payment Details</div>
                            </div>
                            <div class="flex-auto border-t-2 transition duration-500 ease-in-out border-gray-300" id="line3"></div>
                        </div>
                        <div class="flex items-center">
                            <div class="flex items-center text-gray-500 relative">
                                <div class="rounded-full transition duration-500 ease-in-out h-10 w-10 flex items-center justify-center border-2 border-gray-300 bg-white font-bold" id="step4-circle">4</div>
                                <div class="absolute top-0 -ml-10 text-center mt-12 w-32 text-xs font-medium text-gray-500" id="step4-label">Review & Submit</div>
                            </div>
                        </div>
                    </div>
                </div>

                <form id="paymentForm" method="POST" action="{{ route('finance.payments.store') }}">
                    @csrf

                    <!-- Step 1: Student Selection -->
                    <div class="step-content" id="step1" style="display: block;">
                        <!-- Term/Year Selection -->
                        <div class="mb-4 bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                            <label class="block text-sm font-bold text-gray-700 mb-3">Select Term & Year</label>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Year</label>
                                    <select id="modal_year" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" onchange="updateTermOptions()">
                                        <option value="">-- Select Year --</option>
                                        @php
                                            $years = $allTerms->pluck('year')->unique()->sortDesc();
                                        @endphp
                                        @foreach($years as $year)
                                            <option value="{{ $year }}" {{ $currentTerm && $currentTerm->year == $year ? 'selected' : '' }}>{{ $year }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Term</label>
                                    <select name="results_status_id" id="modal_results_status_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" required onchange="updateFeeTypes()">
                                        <option value="">-- Select Term --</option>
                                        @foreach($allTerms as $term)
                                            <option value="{{ $term->id }}" 
                                                    data-year="{{ $term->year }}"
                                                    data-fees='@json($term->termFees)'
                                                    {{ $currentTerm && $currentTerm->id == $term->id ? 'selected' : '' }}>
                                                {{ ucfirst($term->result_period) }} Term {{ $term->year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4 bg-gray-50 p-4 rounded-lg">
                        <label class="block text-sm font-bold text-gray-700 mb-3">Find Student</label>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-xs text-gray-600 mb-1">Filter by Class</label>
                                <select id="modal_class_filter" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm" onchange="filterStudents()">
                                    <option value="">All Classes</option>
                                    @php
                                        $modalClasses = $allStudentsForModal->pluck('class')->unique()->filter();
                                    @endphp
                                    @foreach($modalClasses as $class)
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
                                @foreach($allStudentsForModal as $student)
                                    @php
                                        $totalFees = $student->total_fees ?? 0;
                                        $amountPaid = $student->amount_paid ?? 0;
                                        $balance = $totalFees - $amountPaid;
                                    @endphp
                                    <option value="{{ $student->id }}" 
                                            data-name="{{ $student->user->name ?? 'Unknown' }}" 
                                            data-total-fees="{{ $totalFees }}"
                                            data-amount-paid="{{ $amountPaid }}"
                                            data-balance="{{ $balance }}"
                                            data-class="{{ $student->class->id ?? '' }}"
                                            data-roll="{{ $student->roll_number }}"
                                            data-student-type="{{ $student->student_type ?? 'day' }}"
                                            class="student-option">
                                        {{ $student->user->name ?? 'Unknown' }} ({{ $student->roll_number }}) - {{ $student->class->class_name ?? 'N/A' }} - Balance: ${{ number_format($balance, 2) }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1" id="student_count">{{ $allStudentsForModal->count() }} students available</p>
                        </div>
                        </div>
                        <div class="flex justify-end mt-4">
                            <button type="button" onclick="nextStep(1)" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" id="step1-next">Next</button>
                        </div>
                    </div>

                    <!-- Step 2: Select Fees -->
                    <div class="step-content" id="step2" style="display: none;">
                        <!-- Student Info -->
                    <div class="bg-blue-50 p-4 rounded-lg mb-4" id="student_info_section" style="display: none;">
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Student Name</p>
                                <p class="font-semibold text-gray-900" id="modal_student_name"></p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-600">Original Balance</p>
                                <p class="text-lg font-semibold text-gray-700" id="modal_original_balance">$0.00</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Outstanding Balance</p>
                                <p class="text-2xl font-bold text-red-600" id="modal_balance">$0.00</p>
                                <p class="text-xs text-gray-500 mt-1" id="balance_change_indicator"></p>
                            </div>
                        </div>
                    </div>

                        <!-- Fee Types Selection -->
                        <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-700 mb-3">Select Fee Types to Pay</label>
                        <div class="border border-gray-300 rounded-lg p-4 max-h-96 overflow-y-auto" id="fee_types_container">
                            @if(isset($currentTerm) && $currentTerm->termFees->count() > 0)
                                @foreach($currentTerm->termFees as $termFee)
                                <div class="py-3 border-b border-gray-200 last:border-0 fee-item">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center flex-1">
                                            <input type="checkbox" 
                                                   id="fee_{{ $termFee->id }}"
                                                   class="fee-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                                   data-fee-id="{{ $termFee->id }}"
                                                   data-full-amount="{{ $termFee->amount }}"
                                                   onchange="toggleFeeAmount({{ $termFee->id }})">
                                            <label for="fee_{{ $termFee->id }}" class="ml-3 text-sm font-medium text-gray-700 flex-1">
                                                {{ $termFee->feeType->name }}
                                            </label>
                                            <span class="text-sm font-semibold text-gray-600">Full: ${{ number_format($termFee->amount, 2) }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-7 mt-2 hidden" id="amount_input_{{ $termFee->id }}">
                                        <div class="flex items-center gap-3">
                                            <div class="flex-1">
                                                <label class="block text-xs text-gray-600 mb-1">Amount to Pay</label>
                                                <input type="number" 
                                                       name="fee_amounts[{{ $termFee->id }}]"
                                                       id="amount_{{ $termFee->id }}"
                                                       class="fee-amount-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                                                       placeholder="Enter amount"
                                                       min="0"
                                                       max="{{ $termFee->amount }}"
                                                       step="0.01"
                                                       value="{{ $termFee->amount }}"
                                                       disabled
                                                       oninput="updatePaymentCalculation()">
                                            </div>
                                            <button type="button" 
                                                    onclick="setFullAmount({{ $termFee->id }}, {{ $termFee->amount }})"
                                                    class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-xs font-medium mt-5">
                                                Full Amount
                                            </button>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">Maximum: ${{ number_format($termFee->amount, 2) }}</p>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <p class="text-gray-500 text-center py-4">No fee types available for current term</p>
                            @endif
                        </div>
                        </div>
                        <div class="flex justify-between mt-4">
                            <button type="button" onclick="prevStep(2)" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Previous</button>
                            <button type="button" onclick="nextStep(2)" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700" id="step2-next">Next</button>
                        </div>
                    </div>

                    <!-- Step 3: Payment Details -->
                    <div class="step-content" id="step3" style="display: none;">
                        <div class="grid grid-cols-2 gap-4 mb-4">
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

                        <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reference Number (Optional)</label>
                        <input type="text" name="reference_number" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                               placeholder="Transaction/Receipt number">
                    </div>

                        <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                        <textarea name="notes" rows="2" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" 
                                  placeholder="Additional notes about this payment"></textarea>
                        </div>
                        <div class="flex justify-between mt-4">
                            <button type="button" onclick="prevStep(3)" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Previous</button>
                            <button type="button" onclick="nextStep(3)" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Next</button>
                        </div>
                    </div>

                    <!-- Step 4: Review & Submit -->
                    <div class="step-content" id="step4" style="display: none;">
                        <!-- Payment Summary -->
                        <div class="bg-gray-50 p-4 rounded-lg mb-4">
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
                            <span class="text-xl font-bold text-red-600" id="remaining_balance">$0.00</span>
                        </div>
                        </div>
                        <div class="flex justify-between mt-4">
                            <button type="button" onclick="prevStep(4)" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">Previous</button>
                            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
                                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Record Payment
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let currentBalance = 0;
    let currentStep = 1;

    function openPaymentModal() {
        // Reset form and stepper
        document.getElementById('paymentForm').reset();
        currentStep = 1;
        showStep(1);
        
        document.getElementById('paymentModal').classList.remove('hidden');
    }

    function showStep(step) {
        // Hide all steps
        document.querySelectorAll('.step-content').forEach(el => el.style.display = 'none');
        
        // Show current step
        document.getElementById('step' + step).style.display = 'block';
        
        // Update stepper UI
        for (let i = 1; i <= 4; i++) {
            const circle = document.getElementById('step' + i + '-circle');
            const label = document.getElementById('step' + i + '-label');
            const line = document.getElementById('line' + i);
            
            if (i < step) {
                // Completed steps
                circle.className = 'rounded-full transition duration-500 ease-in-out h-10 w-10 flex items-center justify-center border-2 border-green-600 bg-green-600 text-white font-bold';
                circle.innerHTML = '✓';
                label.className = 'absolute top-0 -ml-10 text-center mt-12 w-32 text-xs font-medium text-green-600';
                if (line) line.className = 'flex-auto border-t-2 transition duration-500 ease-in-out border-green-600';
            } else if (i === step) {
                // Current step
                circle.className = 'rounded-full transition duration-500 ease-in-out h-10 w-10 flex items-center justify-center border-2 border-blue-600 bg-blue-600 text-white font-bold';
                circle.innerHTML = i;
                label.className = 'absolute top-0 -ml-10 text-center mt-12 w-32 text-xs font-medium text-blue-600';
                if (line) line.className = 'flex-auto border-t-2 transition duration-500 ease-in-out border-gray-300';
            } else {
                // Future steps
                circle.className = 'rounded-full transition duration-500 ease-in-out h-10 w-10 flex items-center justify-center border-2 border-gray-300 bg-white font-bold';
                circle.innerHTML = i;
                label.className = 'absolute top-0 -ml-10 text-center mt-12 w-32 text-xs font-medium text-gray-500';
                if (line) line.className = 'flex-auto border-t-2 transition duration-500 ease-in-out border-gray-300';
            }
        }
        
        currentStep = step;
    }

    function showAlert(message) {
        const alertBox = document.getElementById('modal_alert');
        const alertMessage = document.getElementById('modal_alert_message');
        alertMessage.textContent = message;
        alertBox.classList.remove('hidden');
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            hideAlert();
        }, 5000);
        
        // Scroll to top of modal to show alert
        document.querySelector('#paymentModal > div').scrollTop = 0;
    }

    function hideAlert() {
        document.getElementById('modal_alert').classList.add('hidden');
    }

    function showPaymentHistory(studentId, studentName, payments) {
        document.getElementById('history_student_name').textContent = studentName;
        
        const tbody = document.getElementById('payment_history_body');
        const noPaymentsMsg = document.getElementById('no_payments_message');
        const table = tbody.closest('table');
        
        tbody.innerHTML = '';
        
        if (!payments || payments.length === 0) {
            table.classList.add('hidden');
            noPaymentsMsg.classList.remove('hidden');
            document.getElementById('history_total_paid').textContent = '$0.00';
        } else {
            table.classList.remove('hidden');
            noPaymentsMsg.classList.add('hidden');
            
            let totalPaid = 0;
            payments.forEach((payment, index) => {
                totalPaid += parseFloat(payment.amount) || 0;
                const row = `
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-sm text-gray-500">${index + 1}</td>
                        <td class="px-4 py-3 text-sm text-gray-900">${payment.date}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">${payment.term || '-'}</td>
                        <td class="px-4 py-3 text-sm text-gray-900 font-medium">${payment.fee_type}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">${payment.method || '-'}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">${payment.reference || '-'}</td>
                        <td class="px-4 py-3 text-sm text-green-600 font-semibold text-right">$${parseFloat(payment.amount).toFixed(2)}</td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', row);
            });
            
            document.getElementById('history_total_paid').textContent = '$' + totalPaid.toFixed(2);
        }
        
        document.getElementById('paymentHistoryModal').classList.remove('hidden');
    }

    function closePaymentHistoryModal() {
        document.getElementById('paymentHistoryModal').classList.add('hidden');
    }

    function printPaymentHistory() {
        const studentName = document.getElementById('history_student_name').textContent;
        const tableContent = document.getElementById('payment_history_body').innerHTML;
        const totalPaid = document.getElementById('history_total_paid').textContent;
        
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Payment History - ${studentName}</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; }
                    .header { text-align: center; margin-bottom: 30px; }
                    .header h1 { margin: 0; color: #1f2937; font-size: 24px; }
                    .header p { margin: 5px 0; color: #6b7280; }
                    .student-info { background: #eff6ff; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
                    .student-info p { margin: 0; }
                    .student-info .name { font-size: 18px; font-weight: bold; color: #1f2937; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th { background: #f3f4f6; padding: 12px 8px; text-align: left; font-size: 12px; text-transform: uppercase; color: #6b7280; border-bottom: 2px solid #e5e7eb; }
                    td { padding: 12px 8px; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
                    .amount { text-align: right; color: #059669; font-weight: 600; }
                    .total-row { background: #f3f4f6; font-weight: bold; }
                    .total-row td { border-top: 2px solid #e5e7eb; }
                    .footer { margin-top: 30px; text-align: center; color: #9ca3af; font-size: 12px; }
                    @media print { body { padding: 0; } }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>Payment History Report</h1>
                    <p>Generated on ${new Date().toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
                </div>
                <div class="student-info">
                    <p style="color: #6b7280; font-size: 12px;">Student</p>
                    <p class="name">${studentName}</p>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Term</th>
                            <th>Fee Type</th>
                            <th>Method</th>
                            <th>Reference</th>
                            <th style="text-align: right;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${tableContent}
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="6" style="text-align: right;">Total Paid:</td>
                            <td class="amount">${totalPaid}</td>
                        </tr>
                    </tfoot>
                </table>
                <div class="footer">
                    <p>This is a computer-generated document.</p>
                </div>
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.focus();
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 250);
    }

    function updateTermOptions() {
        const yearSelect = document.getElementById('modal_year');
        const termSelect = document.getElementById('modal_results_status_id');
        const selectedYear = yearSelect.value;
        
        // Show/hide term options based on selected year
        Array.from(termSelect.options).forEach(option => {
            if (option.value === '') {
                option.style.display = '';
            } else {
                const optionYear = option.dataset.year;
                option.style.display = (selectedYear === '' || optionYear === selectedYear) ? '' : 'none';
            }
        });
        
        // Reset term selection if current selection is hidden
        if (termSelect.value && termSelect.options[termSelect.selectedIndex].style.display === 'none') {
            termSelect.value = '';
        }
        
        // Auto-select first visible term if year is selected
        if (selectedYear && !termSelect.value) {
            for (let option of termSelect.options) {
                if (option.value && option.dataset.year === selectedYear && option.style.display !== 'none') {
                    termSelect.value = option.value;
                    break;
                }
            }
        }
        
        updateFeeTypes();
    }

    function updateFeeTypes() {
        const termSelect = document.getElementById('modal_results_status_id');
        const selectedOption = termSelect.options[termSelect.selectedIndex];
        const feeTypesContainer = document.getElementById('fee_types_container');
        
        if (!feeTypesContainer) return;
        
        // Get selected student's type
        const studentSelect = document.getElementById('modal_student_id');
        const selectedStudent = studentSelect.options[studentSelect.selectedIndex];
        const studentType = selectedStudent && selectedStudent.value ? (selectedStudent.dataset.studentType || 'day') : 'day';
        
        if (selectedOption.value && selectedOption.dataset.fees) {
            try {
                const fees = JSON.parse(selectedOption.dataset.fees);
                
                // Filter fees based on student type
                const filteredFees = fees.filter(fee => {
                    // If fee has no student_type specified, show it to all
                    if (!fee.student_type) return true;
                    
                    // If student is boarder, show boarding fees
                    if (studentType === 'boarding') {
                        return fee.student_type === 'boarding';
                    }
                    
                    // If student is day, show day fees
                    return fee.student_type === 'day';
                });
                
                // Clear existing fee checkboxes
                feeTypesContainer.innerHTML = '';
                
                if (filteredFees.length === 0) {
                    feeTypesContainer.innerHTML = '<p class="text-gray-500 text-center py-4">No fee types available for ' + (studentType === 'boarding' ? 'boarding' : 'day') + ' students in selected term</p>';
                    return;
                }
                
                // Create new fee checkboxes
                filteredFees.forEach(fee => {
                    const feeHtml = `
                        <div class="bg-white border border-gray-200 rounded-lg p-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <input type="checkbox" 
                                           id="fee_${fee.id}"
                                           class="fee-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                           data-fee-id="${fee.id}"
                                           data-full-amount="${fee.amount}"
                                           onchange="toggleFeeAmount(${fee.id})">
                                    <label for="fee_${fee.id}" class="ml-3 text-sm font-medium text-gray-700 flex-1">
                                        ${fee.fee_type ? fee.fee_type.name : 'Fee'}
                                    </label>
                                    <span class="text-sm font-semibold text-gray-600">Full: $${parseFloat(fee.amount).toFixed(2)}</span>
                                </div>
                            </div>
                            <div class="ml-7 mt-2 hidden" id="amount_input_${fee.id}">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1">
                                        <label class="block text-xs text-gray-600 mb-1">Amount to Pay</label>
                                        <input type="number" 
                                               name="fee_amounts[${fee.id}]"
                                               id="amount_${fee.id}"
                                               class="fee-amount-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
                                               placeholder="Enter amount"
                                               min="0"
                                               max="${fee.amount}"
                                               step="0.01"
                                               value="${fee.amount}"
                                               disabled
                                               oninput="updatePaymentCalculation()">
                                    </div>
                                    <button type="button" 
                                            onclick="setFullAmount(${fee.id}, ${fee.amount})"
                                            class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-xs font-medium mt-5">
                                        Full Amount
                                    </button>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Maximum: $${parseFloat(fee.amount).toFixed(2)}</p>
                            </div>
                        </div>
                    `;
                    feeTypesContainer.insertAdjacentHTML('beforeend', feeHtml);
                });
            } catch (e) {
                console.error('Error parsing fees:', e);
            }
        }
        
        updatePaymentCalculation();
    }

    function nextStep(step) {
        // Hide any existing alerts
        hideAlert();
        
        // Validate current step before proceeding
        if (step === 1) {
            const termId = document.getElementById('modal_results_status_id').value;
            if (!termId) {
                showAlert('Please select a term before proceeding.');
                return;
            }
            const studentId = document.getElementById('modal_student_id').value;
            if (!studentId) {
                showAlert('Please select a student before proceeding.');
                return;
            }
            // Ensure student info section is visible when moving to Step 2
            document.getElementById('student_info_section').style.display = 'block';
        } else if (step === 2) {
            const checkedFees = document.querySelectorAll('.fee-checkbox:checked');
            if (checkedFees.length === 0) {
                showAlert('Please select at least one fee type to proceed.');
                return;
            }
        } else if (step === 3) {
            const paymentMethod = document.querySelector('select[name="payment_method"]').value;
            if (!paymentMethod) {
                showAlert('Please select a payment method before proceeding.');
                return;
            }
        }
        
        showStep(step + 1);
    }

    function prevStep(step) {
        showStep(step - 1);
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

    let totalFees = 0;
    let amountAlreadyPaid = 0;

    function updateStudentBalance() {
        const studentSelect = document.getElementById('modal_student_id');
        const selectedOption = studentSelect.options[studentSelect.selectedIndex];
        
        if (selectedOption.value) {
            const studentName = selectedOption.dataset.name;
            totalFees = parseFloat(selectedOption.dataset.totalFees) || 0;
            amountAlreadyPaid = parseFloat(selectedOption.dataset.amountPaid) || 0;
            const balance = totalFees - amountAlreadyPaid;
            
            // Update student info
            document.getElementById('modal_student_name').textContent = studentName;
            document.getElementById('modal_balance').textContent = '$' + balance.toFixed(2);
            document.getElementById('modal_original_balance').textContent = '$' + balance.toFixed(2);
            currentBalance = balance;
            
            // Show student info section
            document.getElementById('student_info_section').style.display = 'block';
            
            // Update fee types based on student type
            updateFeeTypes();
            
            // Reset checkboxes and calculations
            document.querySelectorAll('.fee-checkbox').forEach(cb => {
                cb.checked = false;
                const feeId = cb.dataset.feeId;
                const amountInput = document.getElementById('amount_input_' + feeId);
                const amountField = document.getElementById('amount_' + feeId);
                if (amountInput) {
                    amountInput.classList.add('hidden');
                }
                if (amountField) {
                    amountField.disabled = true;
                    amountField.value = '';
                }
            });
            updatePaymentCalculation();
        } else {
            // Hide student info section
            document.getElementById('student_info_section').style.display = 'none';
        }
    }

    function toggleFeeAmount(feeId) {
        const checkbox = document.getElementById('fee_' + feeId);
        const amountInput = document.getElementById('amount_input_' + feeId);
        const amountField = document.getElementById('amount_' + feeId);
        
        if (checkbox.checked) {
            amountInput.classList.remove('hidden');
            amountField.disabled = false;
            // Set default to full amount
            const fullAmount = parseFloat(checkbox.dataset.fullAmount);
            amountField.value = fullAmount;
        } else {
            amountInput.classList.add('hidden');
            amountField.disabled = true;
            amountField.value = '';
        }
        
        updatePaymentCalculation();
    }

    function setFullAmount(feeId, amount) {
        document.getElementById('amount_' + feeId).value = amount;
        updatePaymentCalculation();
    }

    function updatePaymentCalculation() {
        let selectedTotal = 0;
        
        // Calculate total from checked fees with their custom amounts
        document.querySelectorAll('.fee-checkbox:checked').forEach(checkbox => {
            const feeId = checkbox.dataset.feeId;
            const amountInput = document.getElementById('amount_' + feeId);
            const amount = parseFloat(amountInput.value) || 0;
            selectedTotal += amount;
        });
        
        const remainingBalance = currentBalance - selectedTotal;
        
        // Update outstanding balance in Step 2 (student info section)
        const balanceElement = document.getElementById('modal_balance');
        const balanceIndicator = document.getElementById('balance_change_indicator');
        
        if (balanceElement) {
            balanceElement.textContent = '$' + remainingBalance.toFixed(2);
            
            // Update color based on remaining balance
            if (remainingBalance < 0) {
                balanceElement.className = 'text-2xl font-bold text-red-600';
                balanceIndicator.textContent = 'Overpayment!';
                balanceIndicator.className = 'text-xs text-red-600 mt-1 font-semibold';
            } else if (remainingBalance === 0) {
                balanceElement.className = 'text-2xl font-bold text-green-600';
                balanceIndicator.textContent = 'Fully Paid ✓';
                balanceIndicator.className = 'text-xs text-green-600 mt-1 font-semibold';
            } else if (selectedTotal > 0) {
                balanceElement.className = 'text-2xl font-bold text-orange-600';
                balanceIndicator.textContent = 'Paying $' + selectedTotal.toFixed(2);
                balanceIndicator.className = 'text-xs text-blue-600 mt-1 font-semibold';
            } else {
                balanceElement.className = 'text-2xl font-bold text-red-600';
                balanceIndicator.textContent = '';
            }
        }
        
        // Update Step 4 summary
        document.getElementById('selected_total').textContent = '$' + selectedTotal.toFixed(2);
        document.getElementById('current_balance').textContent = '$' + currentBalance.toFixed(2);
        document.getElementById('remaining_balance').textContent = '$' + remainingBalance.toFixed(2);
        
        // Change color based on remaining balance in Step 4
        const remainingElement = document.getElementById('remaining_balance');
        if (remainingBalance < 0) {
            remainingElement.classList.remove('text-blue-600', 'text-green-600');
            remainingElement.classList.add('text-red-600');
        } else if (remainingBalance === 0) {
            remainingElement.classList.remove('text-blue-600', 'text-red-600');
            remainingElement.classList.add('text-green-600');
        } else {
            remainingElement.classList.remove('text-blue-600', 'text-green-600');
            remainingElement.classList.add('text-red-600');
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
        
        // Search input - submit form on Enter key
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('filterForm').submit();
            }
        });
    });
</script>

@endsection
