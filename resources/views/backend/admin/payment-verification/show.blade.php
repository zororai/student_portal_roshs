@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 -m-6 p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center space-x-4 mb-8">
            <a href="{{ route('admin.payment-verification.index') }}" class="w-10 h-10 bg-white rounded-xl flex items-center justify-center shadow-sm border border-gray-100 hover:bg-gray-50 transition-colors">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Review Payment</h1>
                <p class="text-gray-500 mt-1">Verify or reject this payment submission</p>
            </div>
        </div>

        <!-- Payment Details -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800">Payment Details</h2>
                {!! $verification->status_badge !!}
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Parent Name</p>
                        <p class="font-medium text-gray-800">{{ $verification->parent->user->name ?? 'Unknown' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Parent Email</p>
                        <p class="font-medium text-gray-800">{{ $verification->parent->user->email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Student Name</p>
                        <p class="font-medium text-gray-800">{{ $verification->student->user->name ?? 'Unknown' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Student Class</p>
                        <p class="font-medium text-gray-800">{{ $verification->student->class->class_name ?? 'No Class' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Receipt/Reference Number</p>
                        <p class="font-medium text-gray-800 text-lg">{{ $verification->receipt_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Amount Paid</p>
                        <p class="font-medium text-gray-800 text-lg">${{ number_format($verification->amount_paid, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Payment Date</p>
                        <p class="font-medium text-gray-800">{{ $verification->payment_date->format('d M Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Submitted On</p>
                        <p class="font-medium text-gray-800">{{ $verification->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>

                @if($verification->notes)
                <div class="mt-6 p-4 bg-gray-50 rounded-xl">
                    <p class="text-sm text-gray-500 mb-1">Parent Notes</p>
                    <p class="text-gray-700">{{ $verification->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Receipt Image -->
        @if($verification->receipt_file)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800">Receipt Document</h2>
            </div>
            <div class="p-6">
                @php
                    $extension = pathinfo($verification->receipt_file, PATHINFO_EXTENSION);
                @endphp
                @if(in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif']))
                    <img src="{{ asset('storage/' . $verification->receipt_file) }}" alt="Receipt" class="max-w-full h-auto rounded-xl border border-gray-200">
                @else
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                        <div class="flex items-center space-x-3">
                            <svg class="w-10 h-10 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-gray-700 font-medium">PDF Document</span>
                        </div>
                        <a href="{{ asset('storage/' . $verification->receipt_file) }}" target="_blank" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            View PDF
                        </a>
                    </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Verification Actions -->
        @if($verification->status === 'pending')
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800">Verify & Record Payment</h2>
                <p class="text-sm text-gray-500 mt-1">Verify the receipt and optionally record as student payment</p>
            </div>
            <form action="{{ route('admin.payment-verification.verify', $verification->id) }}" method="POST" class="p-6">
                @csrf
                
                <!-- Apply Payment Toggle -->
                <div class="mb-6 p-4 bg-blue-50 rounded-xl border border-blue-200">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="apply_payment" value="1" id="applyPaymentToggle" class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500" onchange="togglePaymentFields()">
                        <span class="ml-3 font-medium text-blue-800">Also record this as a student payment</span>
                    </label>
                    <p class="text-sm text-blue-600 mt-1 ml-8">Check this to automatically add this payment to the student's fee records</p>
                </div>

                <!-- Payment Application Fields (hidden by default) -->
                <div id="paymentFields" class="hidden mb-6 p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <h4 class="font-medium text-gray-800 mb-4">Payment Details</h4>
                    
                    @if(isset($allTerms) && $allTerms->count() > 0)
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Term</label>
                        <select name="results_status_id" id="termSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" onchange="updateFeeFields()">
                            @foreach($allTerms as $term)
                            <option value="{{ $term->id }}" data-fees='@json($term->termFees)' {{ isset($currentTerm) && $currentTerm->id == $term->id ? 'selected' : '' }}>
                                {{ ucfirst($term->result_period) }} {{ $term->year }} - Total: ${{ number_format($term->total_fees, 2) }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                        <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Cash">Cash</option>
                            <option value="Mobile Money">Mobile Money</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Allocate Payment to Fees</label>
                        <p class="text-xs text-gray-500 mb-2">Parent submitted: <strong>${{ number_format($verification->amount_paid, 2) }}</strong></p>
                        <div id="feeFieldsContainer" class="space-y-3">
                            @if(isset($currentTerm) && $currentTerm->termFees)
                            @foreach($currentTerm->termFees as $termFee)
                            <div class="flex items-center justify-between p-3 bg-white rounded-lg border">
                                <span class="text-sm font-medium text-gray-700">{{ $termFee->feeType->name ?? 'Fee' }}</span>
                                <div class="flex items-center">
                                    <span class="text-xs text-gray-500 mr-2">(Max: ${{ number_format($termFee->amount, 2) }})</span>
                                    <input type="number" name="fee_amounts[{{ $termFee->id }}]" value="0" min="0" max="{{ $termFee->amount }}" step="0.01" class="w-28 px-3 py-1 border border-gray-300 rounded-lg text-sm text-right">
                                </div>
                            </div>
                            @endforeach
                            @endif
                        </div>
                    </div>
                    @else
                    <p class="text-yellow-600 text-sm">No terms available. Please set up term fees first.</p>
                    @endif
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Admin Notes (Optional)</label>
                    <textarea name="admin_notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 text-sm" placeholder="Any notes about this verification..."></textarea>
                </div>

                <button type="submit" class="w-full py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Verify Payment
                </button>
            </form>
        </div>

        <!-- Reject Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800">Reject Payment</h2>
            </div>
            <form action="{{ route('admin.payment-verification.reject', $verification->id) }}" method="POST" class="p-6">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rejection Reason (Required)</label>
                    <textarea name="admin_notes" rows="2" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 text-sm" placeholder="Please provide a reason for rejection..."></textarea>
                </div>
                <button type="submit" class="w-full py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Reject Payment
                </button>
            </form>
        </div>

        <script>
        function togglePaymentFields() {
            const toggle = document.getElementById('applyPaymentToggle');
            const fields = document.getElementById('paymentFields');
            fields.classList.toggle('hidden', !toggle.checked);
        }
        </script>
        @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800">Verification Result</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Status</p>
                        <p class="font-medium">{{ ucfirst($verification->status) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Verified By</p>
                        <p class="font-medium text-gray-800">{{ $verification->verifiedBy->name ?? 'Unknown' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Verified At</p>
                        <p class="font-medium text-gray-800">{{ $verification->verified_at ? $verification->verified_at->format('d M Y, H:i') : 'N/A' }}</p>
                    </div>
                    @if($verification->admin_notes)
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500 mb-1">Admin Notes</p>
                        <p class="text-gray-700">{{ $verification->admin_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
