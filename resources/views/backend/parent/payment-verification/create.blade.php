@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 -m-6 p-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center space-x-4 mb-8">
            <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center shadow-lg">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Payment Verification</h1>
                <p class="text-gray-500 mt-1">Submit proof of payment to view student results</p>
            </div>
        </div>

        @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-emerald-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <p class="text-emerald-700">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-red-700">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
            <ul class="text-red-700 text-sm list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Info Card -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-blue-800 font-medium">How it works:</p>
                    <ol class="text-blue-700 text-sm mt-1 list-decimal list-inside space-y-1">
                        <li>Upload a copy of your payment receipt</li>
                        <li>Enter the receipt number from your bank/payment provider</li>
                        <li>Wait for admin verification (usually within 24-48 hours)</li>
                        <li>Once verified, you can view your child's results</li>
                    </ol>
                </div>
            </div>
        </div>

        <!-- Submission Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800">Submit Payment Proof</h2>
            </div>
            <form action="{{ route('parent.payment-verification.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf
                
                <!-- Student Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Child *</label>
                    @if($students->count() == 1)
                        {{-- Single child - show as info card with hidden input --}}
                        @php $student = $students->first(); @endphp
                        <input type="hidden" name="student_id" value="{{ $student->id }}">
                        <div class="flex items-center p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
                            <div class="w-12 h-12 bg-emerald-500 rounded-full flex items-center justify-center text-white font-bold text-lg mr-4">
                                {{ strtoupper(substr($student->user->name ?? 'S', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800">{{ $student->user->name ?? 'Unknown' }}</p>
                                <p class="text-sm text-gray-600">{{ $student->class->class_name ?? 'No Class Assigned' }}</p>
                            </div>
                            <svg class="w-6 h-6 text-emerald-500 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    @else
                        {{-- Multiple children - show as selectable cards --}}
                        <div class="space-y-3" id="student-cards">
                            @foreach($students as $index => $student)
                            <label class="block cursor-pointer">
                                <input type="radio" name="student_id" value="{{ $student->id }}" class="hidden peer" {{ old('student_id') == $student->id ? 'checked' : '' }} {{ $index == 0 && !old('student_id') ? 'checked' : '' }} required>
                                <div class="flex items-center p-4 border-2 border-gray-200 rounded-xl peer-checked:border-emerald-500 peer-checked:bg-emerald-50 hover:border-emerald-300 transition-all">
                                    <div class="w-12 h-12 bg-gray-200 peer-checked:bg-emerald-500 rounded-full flex items-center justify-center text-gray-600 peer-checked:text-white font-bold text-lg mr-4">
                                        {{ strtoupper(substr($student->user->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-semibold text-gray-800">{{ $student->user->name ?? 'Unknown' }}</p>
                                        <p class="text-sm text-gray-600">{{ $student->class->class_name ?? 'No Class Assigned' }}</p>
                                    </div>
                                    <div class="w-6 h-6 border-2 border-gray-300 rounded-full flex items-center justify-center peer-checked:border-emerald-500 peer-checked:bg-emerald-500">
                                        <svg class="w-4 h-4 text-white hidden peer-checked:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Select which child this payment is for</p>
                    @endif
                </div>

                <!-- Receipt Number -->
                <div>
                    <label for="receipt_number" class="block text-sm font-medium text-gray-700 mb-2">Receipt/Reference Number *</label>
                    <input type="text" name="receipt_number" id="receipt_number" value="{{ old('receipt_number') }}" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                        placeholder="e.g., TXN123456789">
                    <p class="text-xs text-gray-500 mt-1">Enter the transaction or receipt number from your bank/payment confirmation</p>
                </div>

                <!-- Amount Paid -->
                <div>
                    <label for="amount_paid" class="block text-sm font-medium text-gray-700 mb-2">Amount Paid (USD) *</label>
                    <input type="number" name="amount_paid" id="amount_paid" value="{{ old('amount_paid') }}" required step="0.01" min="0"
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all"
                        placeholder="e.g., 150.00">
                </div>

                <!-- Payment Date -->
                <div>
                    <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-2">Payment Date *</label>
                    <input type="date" name="payment_date" id="payment_date" value="{{ old('payment_date') }}" required
                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                </div>

                <!-- Receipt File Upload -->
                <div>
                    <label for="receipt_file" class="block text-sm font-medium text-gray-700 mb-2">Upload Receipt *</label>
                    <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center hover:border-emerald-400 transition-colors">
                        <input type="file" name="receipt_file" id="receipt_file" accept=".jpg,.jpeg,.png,.pdf" required class="hidden" onchange="showFileName(this)">
                        <label for="receipt_file" class="cursor-pointer">
                            <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            <p class="text-gray-600">Click to upload receipt</p>
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG or PDF (Max 5MB)</p>
                        </label>
                        <div id="file-name" class="mt-3 text-sm text-emerald-600 font-medium hidden"></div>
                    </div>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Additional Notes (Optional)</label>
                    <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all resize-none" placeholder="Any additional information about this payment...">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="w-full py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl hover:from-emerald-600 hover:to-teal-700 transition-all duration-200 font-medium shadow-lg">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Submit for Verification
                </button>
            </form>
        </div>

        <!-- Previous Submissions -->
        @if($existingVerifications->count() > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800">Previous Submissions</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($existingVerifications as $verification)
                <div class="p-5">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="flex items-center space-x-3 mb-2">
                                <span class="font-medium text-gray-800">{{ $verification->student->user->name ?? 'Unknown' }}</span>
                                {!! $verification->status_badge !!}
                            </div>
                            <p class="text-sm text-gray-600">Receipt #: {{ $verification->receipt_number }}</p>
                            <p class="text-sm text-gray-600">Amount: ${{ number_format($verification->amount_paid, 2) }}</p>
                            <p class="text-sm text-gray-500">Submitted: {{ $verification->created_at->format('d M Y') }}</p>
                            @if($verification->status === 'rejected' && $verification->admin_notes)
                            <div class="mt-2 p-2 bg-red-50 rounded-lg">
                                <p class="text-sm text-red-700"><strong>Rejection Reason:</strong> {{ $verification->admin_notes }}</p>
                            </div>
                            @endif
                        </div>
                        @if($verification->receipt_file)
                        <a href="{{ asset('storage/' . $verification->receipt_file) }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function showFileName(input) {
    const fileName = input.files[0]?.name;
    const fileNameDiv = document.getElementById('file-name');
    if (fileName) {
        fileNameDiv.textContent = 'Selected: ' + fileName;
        fileNameDiv.classList.remove('hidden');
    } else {
        fileNameDiv.classList.add('hidden');
    }
}
</script>
@endsection
