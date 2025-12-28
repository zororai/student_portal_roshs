@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">New Cash Book Entry</h1>
        <a href="{{ route('admin.finance.cashbook.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6">
        <form action="{{ route('admin.finance.cashbook.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Entry Date *</label>
                    <input type="date" name="entry_date" value="{{ old('entry_date', date('Y-m-d')) }}" class="w-full border rounded-lg px-3 py-2" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Transaction Type *</label>
                    <select name="transaction_type" id="transaction_type" class="w-full border rounded-lg px-3 py-2" required>
                        <option value="receipt" {{ old('transaction_type') == 'receipt' ? 'selected' : '' }}>Receipt (Money In)</option>
                        <option value="payment" {{ old('transaction_type') == 'payment' ? 'selected' : '' }}>Payment (Money Out)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                    <select name="category" id="category" class="w-full border rounded-lg px-3 py-2" required>
                        <option value="">Select Category</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
                    <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" class="w-full border rounded-lg px-3 py-2" required>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
                    <input type="text" name="description" value="{{ old('description') }}" class="w-full border rounded-lg px-3 py-2" required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                    <select name="payment_method" class="w-full border rounded-lg px-3 py-2">
                        <option value="cash">Cash</option>
                        <option value="bank">Bank</option>
                        <option value="mobile_money">Mobile Money</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payer/Payee</label>
                    <input type="text" name="payer_payee" value="{{ old('payer_payee') }}" class="w-full border rounded-lg px-3 py-2" placeholder="Name of person/organization">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="3" class="w-full border rounded-lg px-3 py-2">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Save Entry
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const categories = @json($categories);

function updateCategories() {
    const type = document.getElementById('transaction_type').value;
    const categorySelect = document.getElementById('category');
    categorySelect.innerHTML = '<option value="">Select Category</option>';
    
    if (categories[type]) {
        Object.entries(categories[type]).forEach(([key, value]) => {
            const option = document.createElement('option');
            option.value = key;
            option.textContent = value;
            categorySelect.appendChild(option);
        });
    }
}

document.getElementById('transaction_type').addEventListener('change', updateCategories);
updateCategories();
</script>
@endsection
