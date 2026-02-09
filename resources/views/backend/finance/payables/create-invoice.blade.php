@extends('layouts.app')

@section('content')
<!-- Page Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Create Supplier Invoice</h1>
        <p class="text-gray-500 text-sm">Record a new invoice from a supplier</p>
    </div>
    <a href="{{ route('finance.payables.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition duration-200">
        ← Back to Dashboard
    </a>
</div>

<!-- Form Card -->
<div class="bg-white shadow-md rounded-xl p-6">
    <form action="{{ route('finance.payables.invoices.store') }}" method="POST">
        @csrf

        <!-- Invoice Details Section -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Invoice Details</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Supplier -->
                <div>
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Supplier <span class="text-red-500">*</span>
                    </label>
                    <select name="supplier_id" id="supplier_id" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('supplier_id') border-red-500 @enderror">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Invoice Number -->
                <div>
                    <label for="invoice_number" class="block text-sm font-medium text-gray-700 mb-2">
                        Invoice Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="invoice_number" id="invoice_number" required
                           value="{{ old('invoice_number') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('invoice_number') border-red-500 @enderror"
                           placeholder="e.g., INV-2024-001">
                    @error('invoice_number')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Invoice Date -->
                <div>
                    <label for="invoice_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Invoice Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="invoice_date" id="invoice_date" required
                           value="{{ old('invoice_date', date('Y-m-d')) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('invoice_date') border-red-500 @enderror">
                    @error('invoice_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Due Date -->
                <div>
                    <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                        Due Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="due_date" id="due_date" required
                           value="{{ old('due_date') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('due_date') border-red-500 @enderror">
                    @error('due_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Amount -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                        Amount <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">$</span>
                        <input type="number" name="amount" id="amount" required step="0.01" min="0"
                               value="{{ old('amount') }}"
                               class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('amount') border-red-500 @enderror"
                               placeholder="0.00">
                    </div>
                    @error('amount')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Expense Account -->
                <div>
                    <label for="expense_account_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Expense Account <span class="text-red-500">*</span>
                    </label>
                    <select name="expense_account_id" id="expense_account_id" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('expense_account_id') border-red-500 @enderror">
                        <option value="">Select Account</option>
                        @foreach($expenseAccounts as $account)
                            <option value="{{ $account->id }}" {{ old('expense_account_id') == $account->id ? 'selected' : '' }}>
                                {{ $account->account_code }} - {{ $account->account_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('expense_account_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea name="description" id="description" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('description') border-red-500 @enderror"
                          placeholder="Enter invoice description or notes...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('finance.payables.index') }}" 
               class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200">
                Cancel
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-md transition duration-200">
                Create Invoice
            </button>
        </div>
    </form>
</div>

<!-- Help Card -->
<div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mt-6">
    <div class="flex items-start">
        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
        </svg>
        <div>
            <h4 class="text-sm font-semibold text-blue-800 mb-1">Accounting Note</h4>
            <p class="text-sm text-blue-700">
                Creating this invoice will post to the ledger: <strong>Dr</strong> Expense Account, <strong>Cr</strong> Accounts Payable.
                The invoice will remain unpaid until a payment is recorded.
            </p>
        </div>
    </div>
</div>
@endsection
