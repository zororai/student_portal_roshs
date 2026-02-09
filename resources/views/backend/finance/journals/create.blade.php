@extends('layouts.app')

@section('content')
<!-- Page Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Create Journal Entry</h1>
        <p class="text-gray-500 text-sm">Record manual journal entries for adjustments and corrections</p>
    </div>
    <a href="{{ route('finance.journals.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition duration-200">
        ← Back to List
    </a>
</div>

<!-- Error Messages -->
@if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-red-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div class="flex-1">
                <h4 class="text-sm font-semibold text-red-800 mb-1">Please fix the following errors:</h4>
                <ul class="list-disc list-inside text-sm text-red-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

<form action="{{ route('finance.journals.store') }}" method="POST" id="journalForm">
    @csrf
    
    <!-- Journal Batch Information -->
    <div class="bg-white shadow-md rounded-xl p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Journal Batch Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description <span class="text-red-500">*</span>
                </label>
                <textarea name="description" id="description" rows="3" required
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                          placeholder="Enter journal batch description...">{{ old('description') }}</textarea>
            </div>
            <div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-blue-800 mb-1">Important</h4>
                            <p class="text-sm text-blue-700">Journal entries must balance (Total Debits = Total Credits) before they can be approved.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Journal Entries Table -->
    <div class="bg-white shadow-md rounded-xl overflow-hidden mb-6">
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h3 class="font-semibold text-gray-800">Journal Entries</h3>
            <button type="button" onclick="addEntry()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-md transition duration-200 text-sm">
                + Add Entry
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full" id="entriesTable">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" width="35%">Account</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" width="15%">Debit</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider" width="15%">Credit</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" width="30%">Narration</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" width="5%">Action</th>
                    </tr>
                </thead>
                <tbody id="entriesBody" class="bg-white divide-y divide-gray-200">
                    <tr class="entry-row hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <select name="entries[0][ledger_account_id]" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="">Select Account</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}">
                                        {{ $account->account_code }} - {{ $account->account_name }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" name="entries[0][debit_amount]" step="0.01" min="0" value="0" onchange="calculateTotals()"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-right text-sm debit-input">
                        </td>
                        <td class="px-4 py-3">
                            <input type="number" name="entries[0][credit_amount]" step="0.01" min="0" value="0" onchange="calculateTotals()"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-right text-sm credit-input">
                        </td>
                        <td class="px-4 py-3">
                            <input type="text" name="entries[0][narration]" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                   placeholder="Enter narration">
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button type="button" onclick="removeEntry(this)" disabled
                                    class="px-3 py-1 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition duration-200 text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                ×
                            </button>
                        </td>
                    </tr>
                </tbody>
                <tfoot class="bg-gray-100 font-semibold">
                    <tr>
                        <td class="px-4 py-3 text-right text-gray-900">Totals:</td>
                        <td class="px-4 py-3 text-right">
                            <span id="totalDebit" class="text-gray-900">$0.00</span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <span id="totalCredit" class="text-gray-900">$0.00</span>
                        </td>
                        <td colspan="2" class="px-4 py-3">
                            <span id="balanceStatus" class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-200 text-gray-800">Not Balanced</span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex justify-end gap-3">
        <a href="{{ route('finance.journals.index') }}" 
           class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200">
            Cancel
        </a>
        <button type="submit" 
                class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-md transition duration-200">
            Save as Draft
        </button>
    </div>
</form>

@endsection

@push('scripts')
<script>
let entryCount = 1;

function addEntry() {
    const tbody = document.getElementById('entriesBody');
    const newRow = document.createElement('tr');
    newRow.className = 'entry-row hover:bg-gray-50';
    newRow.innerHTML = `
        <td class="px-4 py-3">
            <select name="entries[${entryCount}][ledger_account_id]" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                <option value="">Select Account</option>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}">
                        {{ $account->account_code }} - {{ $account->account_name }}
                    </option>
                @endforeach
            </select>
        </td>
        <td class="px-4 py-3">
            <input type="number" name="entries[${entryCount}][debit_amount]" step="0.01" min="0" value="0" onchange="calculateTotals()"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-right text-sm debit-input">
        </td>
        <td class="px-4 py-3">
            <input type="number" name="entries[${entryCount}][credit_amount]" step="0.01" min="0" value="0" onchange="calculateTotals()"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-right text-sm credit-input">
        </td>
        <td class="px-4 py-3">
            <input type="text" name="entries[${entryCount}][narration]" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                   placeholder="Enter narration">
        </td>
        <td class="px-4 py-3 text-center">
            <button type="button" onclick="removeEntry(this)"
                    class="px-3 py-1 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition duration-200 text-sm">
                ×
            </button>
        </td>
    `;
    tbody.appendChild(newRow);
    entryCount++;
    updateRemoveButtons();
}

function removeEntry(button) {
    button.closest('tr').remove();
    calculateTotals();
    updateRemoveButtons();
}

function updateRemoveButtons() {
    const rows = document.querySelectorAll('.entry-row');
    rows.forEach((row, index) => {
        const button = row.querySelector('button');
        button.disabled = rows.length === 1;
    });
}

function calculateTotals() {
    let totalDebit = 0;
    let totalCredit = 0;

    document.querySelectorAll('.debit-input').forEach(input => {
        totalDebit += parseFloat(input.value) || 0;
    });

    document.querySelectorAll('.credit-input').forEach(input => {
        totalCredit += parseFloat(input.value) || 0;
    });

    document.getElementById('totalDebit').textContent = '$' + totalDebit.toFixed(2);
    document.getElementById('totalCredit').textContent = '$' + totalCredit.toFixed(2);

    const balanceStatus = document.getElementById('balanceStatus');
    if (Math.abs(totalDebit - totalCredit) < 0.01 && totalDebit > 0) {
        balanceStatus.textContent = 'Balanced ✓';
        balanceStatus.className = 'px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800';
    } else {
        balanceStatus.textContent = 'Not Balanced';
        balanceStatus.className = 'px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateTotals();
});
</script>
@endpush
