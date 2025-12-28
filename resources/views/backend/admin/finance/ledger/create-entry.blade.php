@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">New Ledger Entry</h1>
        <a href="{{ route('admin.finance.ledger.entries') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Back</a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6 max-w-2xl">
        <form action="{{ route('admin.finance.ledger.store-entry') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Date *</label><input type="date" name="entry_date" value="{{ date('Y-m-d') }}" class="w-full border rounded-lg px-3 py-2" required></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Account *</label>
                    <select name="account_id" class="w-full border rounded-lg px-3 py-2" required><option value="">Select Account</option>@foreach($accounts as $acc)<option value="{{ $acc->id }}">{{ $acc->account_code }} - {{ $acc->account_name }}</option>@endforeach</select></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Entry Type *</label>
                    <select name="entry_type" class="w-full border rounded-lg px-3 py-2" required><option value="debit">Debit</option><option value="credit">Credit</option></select></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label><input type="number" step="0.01" name="amount" class="w-full border rounded-lg px-3 py-2" required></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Description *</label><input type="text" name="description" class="w-full border rounded-lg px-3 py-2" required></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Notes</label><textarea name="notes" rows="3" class="w-full border rounded-lg px-3 py-2"></textarea></div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Create Entry</button>
            </div>
        </form>
    </div>
</div>
@endsection
