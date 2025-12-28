@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ $account->account_name }} - Transactions</h1>
        <div class="space-x-2">
            <a href="{{ route('admin.finance.reconciliation.reconcile', $account->id) }}" class="bg-green-600 text-white px-4 py-2 rounded-lg">Reconcile</a>
            <a href="{{ route('admin.finance.reconciliation.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg">Back</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs">Date</th><th class="px-6 py-3 text-left text-xs">Reference</th><th class="px-6 py-3 text-left text-xs">Description</th><th class="px-6 py-3 text-right text-xs">Amount</th><th class="px-6 py-3 text-right text-xs">Balance</th><th class="px-6 py-3 text-center text-xs">Status</th></tr></thead>
            <tbody class="divide-y">
                @forelse($transactions as $tx)
                <tr>
                    <td class="px-6 py-4 text-sm">{{ $tx->transaction_date->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-sm font-mono">{{ $tx->reference_number ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm">{{ $tx->description }}</td>
                    <td class="px-6 py-4 text-sm text-right {{ in_array($tx->transaction_type, ['deposit', 'interest']) ? 'text-green-600' : 'text-red-600' }}">{{ in_array($tx->transaction_type, ['deposit', 'interest']) ? '+' : '-' }}${{ number_format($tx->amount, 2) }}</td>
                    <td class="px-6 py-4 text-sm text-right">${{ number_format($tx->balance_after, 2) }}</td>
                    <td class="px-6 py-4 text-center">@if($tx->is_reconciled)<span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Reconciled</span>@else<span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Pending</span>@endif</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No transactions found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $transactions->links() }}</div>
</div>
@endsection
