@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Create Journal Entry</h2>
                <a href="{{ route('finance.journals.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('finance.journals.store') }}" method="POST" id="journalForm">
        @csrf
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Journal Batch Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="description">Description <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" class="form-control" rows="3" required>{{ old('description') }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <strong>Note:</strong> Journal entries must balance (Total Debits = Total Credits) before they can be approved.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Journal Entries</h5>
                <button type="button" class="btn btn-sm btn-success" onclick="addEntry()">
                    <i class="fas fa-plus"></i> Add Entry
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="entriesTable">
                        <thead>
                            <tr>
                                <th width="35%">Account</th>
                                <th width="15%">Debit</th>
                                <th width="15%">Credit</th>
                                <th width="30%">Narration</th>
                                <th width="5%">Action</th>
                            </tr>
                        </thead>
                        <tbody id="entriesBody">
                            <tr class="entry-row">
                                <td>
                                    <select name="entries[0][ledger_account_id]" class="form-control" required>
                                        <option value="">Select Account</option>
                                        @foreach($accounts as $account)
                                            <option value="{{ $account->id }}">
                                                {{ $account->account_code }} - {{ $account->account_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="number" name="entries[0][debit_amount]" class="form-control debit-input" step="0.01" min="0" value="0" onchange="calculateTotals()">
                                </td>
                                <td>
                                    <input type="number" name="entries[0][credit_amount]" class="form-control credit-input" step="0.01" min="0" value="0" onchange="calculateTotals()">
                                </td>
                                <td>
                                    <input type="text" name="entries[0][narration]" class="form-control" required>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="removeEntry(this)" disabled>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="font-weight-bold">
                                <td class="text-right">Totals:</td>
                                <td class="text-right">
                                    <span id="totalDebit">$0.00</span>
                                </td>
                                <td class="text-right">
                                    <span id="totalCredit">$0.00</span>
                                </td>
                                <td colspan="2">
                                    <span id="balanceStatus" class="badge badge-secondary">Not Balanced</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save as Draft
                </button>
                <a href="{{ route('finance.journals.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>

<script>
let entryCount = 1;

function addEntry() {
    const tbody = document.getElementById('entriesBody');
    const newRow = document.createElement('tr');
    newRow.className = 'entry-row';
    newRow.innerHTML = `
        <td>
            <select name="entries[${entryCount}][ledger_account_id]" class="form-control" required>
                <option value="">Select Account</option>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}">
                        {{ $account->account_code }} - {{ $account->account_name }}
                    </option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" name="entries[${entryCount}][debit_amount]" class="form-control debit-input" step="0.01" min="0" value="0" onchange="calculateTotals()">
        </td>
        <td>
            <input type="number" name="entries[${entryCount}][credit_amount]" class="form-control credit-input" step="0.01" min="0" value="0" onchange="calculateTotals()">
        </td>
        <td>
            <input type="text" name="entries[${entryCount}][narration]" class="form-control" required>
        </td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeEntry(this)">
                <i class="fas fa-trash"></i>
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
        balanceStatus.className = 'badge badge-success';
    } else {
        balanceStatus.textContent = 'Not Balanced';
        balanceStatus.className = 'badge badge-danger';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateTotals();
});
</script>
@endsection
