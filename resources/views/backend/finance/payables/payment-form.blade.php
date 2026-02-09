@extends('layouts.app')

@section('content')
<!-- Page Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Record Payment</h1>
        <p class="text-gray-500 text-sm">Record a payment for invoice {{ $invoice->invoice_number }}</p>
    </div>
    <a href="{{ route('finance.payables.invoices.show', $invoice->id) }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition duration-200">
        ← Back to Invoice
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Payment Form -->
    <div class="lg:col-span-2">
        <div class="bg-white shadow-md rounded-xl p-6">
            <form action="{{ route('finance.payables.payments.store', $invoice->id) }}" method="POST">
                @csrf

                <div class="space-y-6">
                    <!-- Payment Date -->
                    <div>
                        <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Payment Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="payment_date" id="payment_date" required
                               value="{{ old('payment_date', date('Y-m-d')) }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('payment_date') border-red-500 @enderror">
                        @error('payment_date')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment Amount -->
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                            Payment Amount <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">$</span>
                            <input type="number" name="amount" id="amount" required step="0.01" min="0.01"
                                   max="{{ $invoice->getOutstandingAmount() }}"
                                   value="{{ old('amount', $invoice->getOutstandingAmount()) }}"
                                   class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('amount') border-red-500 @enderror"
                                   placeholder="0.00">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Maximum: ${{ number_format($invoice->getOutstandingAmount(), 2) }}</p>
                        @error('amount')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Payment Method -->
                    <div>
                        <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-2">
                            Payment Method <span class="text-red-500">*</span>
                        </label>
                        <select name="payment_method" id="payment_method" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('payment_method') border-red-500 @enderror">
                            <option value="">Select Method</option>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="check" {{ old('payment_method') == 'check' ? 'selected' : '' }}>Check</option>
                            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="eft" {{ old('payment_method') == 'eft' ? 'selected' : '' }}>EFT</option>
                            <option value="mobile_money" {{ old('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                        </select>
                        @error('payment_method')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Reference Number -->
                    <div>
                        <label for="reference_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Reference Number
                        </label>
                        <input type="text" name="reference_number" id="reference_number"
                               value="{{ old('reference_number') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('reference_number') border-red-500 @enderror"
                               placeholder="Check number, transaction ID, etc.">
                        @error('reference_number')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes
                        </label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('notes') border-red-500 @enderror"
                                  placeholder="Additional payment notes...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 pt-6 border-t mt-6">
                    <a href="{{ route('finance.payables.invoices.show', $invoice->id) }}" 
                       class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg shadow-md transition duration-200">
                        Record Payment
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Invoice Summary Sidebar -->
    <div class="lg:col-span-1">
        <div class="bg-white shadow-md rounded-xl p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Invoice Summary</h3>
            
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Invoice Number</p>
                    <p class="font-semibold text-gray-900">{{ $invoice->invoice_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Supplier</p>
                    <p class="font-semibold text-gray-900">{{ $invoice->supplier->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Invoice Date</p>
                    <p class="text-gray-900">{{ $invoice->invoice_date->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Due Date</p>
                    <p class="{{ $invoice->isOverdue() ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                        {{ $invoice->due_date->format('M d, Y') }}
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Payment Summary</h3>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Invoice Amount</span>
                    <span class="font-bold text-gray-900">${{ number_format($invoice->amount, 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Amount Paid</span>
                    <span class="font-semibold text-green-600">${{ number_format($invoice->paid_amount, 2) }}</span>
                </div>
                <div class="pt-4 border-t">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-900 font-semibold">Balance Due</span>
                        <span class="font-bold text-xl text-red-600">${{ number_format($invoice->getOutstandingAmount(), 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accounting Note -->
        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mt-6">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h4 class="text-sm font-semibold text-blue-800 mb-1">Accounting Note</h4>
                    <p class="text-sm text-blue-700">
                        This payment will post to the ledger: <strong>Dr</strong> Accounts Payable, <strong>Cr</strong> Cash/Bank.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
