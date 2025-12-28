@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ $account->account_code }} - {{ $account->account_name }}</h1>
        <a href="{{ route('admin.finance.ledger.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Back</a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div><span class="text-gray-500">Type:</span> <strong>{{ ucfirst($account->account_type) }}</strong></div>
            <div><span class="text-gray-500">Category:</span> <strong>{{ $account->category ?? 'N/A' }}</strong></div>
            <div><span class="text-gray-500">Opening Balance:</span> <strong>${{ number_format($account->opening_balance, 2) }}</strong></div>
            <div><span class="text-gray-500">Current Balance:</span> <strong class="{{ $account->current_balance >= 0 ? 'text-green-600' : 'text-red-600' }}">${{ number_format($account->current_balance, 2) }}</strong></div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50"><h3 class="font-semibold">Transaction History</h3></div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Debit</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Credit</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($entries as $entry)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $entry->entry_date->format('d M Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $entry->reference_number }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $entry->description }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-blue-600">{{ $entry->isDebit() ? '$' . number_format($entry->amount, 2) : '-' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600">{{ $entry->isCredit() ? '$' . number_format($entry->amount, 2) : '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No entries found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $entries->links() }}</div>
</div>
@endsection
