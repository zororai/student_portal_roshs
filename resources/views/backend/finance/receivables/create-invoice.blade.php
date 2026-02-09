@extends('layouts.app')

@section('content')
<!-- Page Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Create Student Invoice</h1>
        <p class="text-gray-500 text-sm">Generate a new invoice for student fees</p>
    </div>
    <a href="{{ route('finance.receivables.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition duration-200">
        ← Back to Dashboard
    </a>
</div>

<!-- Form Card -->
<div class="bg-white shadow-md rounded-xl p-6">
    <form action="{{ route('finance.receivables.invoices.store') }}" method="POST">
        @csrf

        <!-- Invoice Details Section -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Invoice Details</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Student -->
                <div>
                    <label for="student_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Student <span class="text-red-500">*</span>
                    </label>
                    <select name="student_id" id="student_id" required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('student_id') border-red-500 @enderror">
                        <option value="">Select Student</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                {{ $student->user->name ?? 'N/A' }} - {{ $student->roll_number }}
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Term -->
                <div>
                    <label for="term" class="block text-sm font-medium text-gray-700 mb-2">
                        Term <span class="text-red-500">*</span>
                    </label>
                    <select name="term" id="term" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('term') border-red-500 @enderror">
                        <option value="">Select Term</option>
                        <option value="Term 1" {{ old('term') == 'Term 1' ? 'selected' : '' }}>Term 1</option>
                        <option value="Term 2" {{ old('term') == 'Term 2' ? 'selected' : '' }}>Term 2</option>
                        <option value="Term 3" {{ old('term') == 'Term 3' ? 'selected' : '' }}>Term 3</option>
                    </select>
                    @error('term')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Year -->
                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700 mb-2">
                        Year <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="year" id="year" required
                           value="{{ old('year', date('Y')) }}"
                           min="2020" max="2050"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('year') border-red-500 @enderror">
                    @error('year')
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

                <!-- Total Amount -->
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                        Total Amount <span class="text-red-500">*</span>
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
            </div>
        </div>

        <!-- Invoice Items Section -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Invoice Items</h3>
            
            <div id="invoice-items">
                <div class="invoice-item grid grid-cols-1 md:grid-cols-12 gap-4 mb-4 p-4 bg-gray-50 rounded-lg">
                    <div class="md:col-span-5">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <input type="text" name="items[0][description]" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                               placeholder="e.g., Tuition Fee">
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Income Account</label>
                        <select name="items[0][income_account_id]" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            <option value="">Select Account</option>
                            @foreach($incomeAccounts as $account)
                                <option value="{{ $account->id }}">{{ $account->account_code }} - {{ $account->account_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">$</span>
                            <input type="number" name="items[0][amount]" required step="0.01" min="0"
                                   class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 item-amount"
                                   placeholder="0.00">
                        </div>
                    </div>
                    <div class="md:col-span-1 flex items-end">
                        <button type="button" class="remove-item w-full px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition duration-200" style="display: none;">
                            ×
                        </button>
                    </div>
                </div>
            </div>

            <button type="button" id="add-item" class="mt-2 px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition duration-200">
                + Add Item
            </button>
        </div>

        <!-- Notes -->
        <div class="mb-6">
            <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                Notes
            </label>
            <textarea name="notes" id="notes" rows="3"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                      placeholder="Additional notes or payment instructions...">{{ old('notes') }}</textarea>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end gap-3 pt-4 border-t">
            <a href="{{ route('finance.receivables.index') }}" 
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
                Creating this invoice will post to the ledger: <strong>Dr</strong> Accounts Receivable, <strong>Cr</strong> Income Accounts (based on items).
                The invoice will remain unpaid until payment is received.
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let itemIndex = 1;

document.getElementById('add-item').addEventListener('click', function() {
    const container = document.getElementById('invoice-items');
    const firstItem = container.querySelector('.invoice-item');
    const newItem = firstItem.cloneNode(true);
    
    // Update input names and clear values
    newItem.querySelectorAll('input, select').forEach(input => {
        const name = input.getAttribute('name');
        if (name) {
            input.setAttribute('name', name.replace('[0]', `[${itemIndex}]`));
            if (input.type !== 'hidden') {
                input.value = '';
            }
        }
    });
    
    // Show remove button
    newItem.querySelector('.remove-item').style.display = 'block';
    
    container.appendChild(newItem);
    itemIndex++;
    
    // Update total
    updateTotal();
});

document.getElementById('invoice-items').addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-item') || e.target.closest('.remove-item')) {
        const item = e.target.closest('.invoice-item');
        if (document.querySelectorAll('.invoice-item').length > 1) {
            item.remove();
            updateTotal();
        }
    }
});

document.getElementById('invoice-items').addEventListener('input', function(e) {
    if (e.target.classList.contains('item-amount')) {
        updateTotal();
    }
});

function updateTotal() {
    let total = 0;
    document.querySelectorAll('.item-amount').forEach(input => {
        const value = parseFloat(input.value) || 0;
        total += value;
    });
    
    const amountInput = document.getElementById('amount');
    if (amountInput) {
        amountInput.value = total.toFixed(2);
    }
}
</script>
@endpush
