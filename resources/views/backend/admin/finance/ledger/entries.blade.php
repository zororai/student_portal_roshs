@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Ledger Entries</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.finance.ledger.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Back</a>
            <a href="{{ route('admin.finance.ledger.create-entry') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">New Entry</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                <select name="year" class="border rounded-lg px-3 py-2">
                    <option value="">All Years</option>
                    @foreach($years as $year)
                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Term</label>
                <select name="term" class="border rounded-lg px-3 py-2">
                    <option value="">All Terms</option>
                    @foreach($terms as $key => $label)
                    <option value="{{ $key }}" {{ $selectedTerm == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Account</label>
                <select name="account_id" class="border rounded-lg px-3 py-2">
                    <option value="">All Accounts</option>
                    @foreach($accounts as $acc)
                    <option value="{{ $acc->id }}" {{ request('account_id') == $acc->id ? 'selected' : '' }}>{{ $acc->account_code }} - {{ $acc->account_name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="entry_type" class="border rounded-lg px-3 py-2">
                    <option value="">All</option>
                    <option value="debit" {{ request('entry_type') == 'debit' ? 'selected' : '' }}>Debit</option>
                    <option value="credit" {{ request('entry_type') == 'credit' ? 'selected' : '' }}>Credit</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Filter</button>
                <a href="{{ route('admin.finance.ledger.entries') }}" class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Debit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Credit</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($entries as $entry)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $entry->entry_date->format('d M Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $entry->reference_number }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $entry->account->account_name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $entry->description }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600">{{ $entry->isDebit() ? '$' . number_format($entry->amount, 2) : '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">{{ $entry->isCredit() ? '$' . number_format($entry->amount, 2) : '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No entries found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $entries->links() }}</div>
</div>
@endsection
