@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Reconcile - {{ $account->account_name }}</h1>
        <a href="{{ route('admin.finance.reconciliation.transactions', $account->id) }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg">Back</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="px-6 py-4 border-b bg-blue-50"><h3 class="font-semibold text-blue-800">Bank Transactions (Unreconciled)</h3></div>
            <div class="max-h-96 overflow-y-auto">
                @forelse($unreconciledBank as $tx)
                <div class="px-4 py-3 border-b hover:bg-gray-50 cursor-pointer" data-bank-id="{{ $tx->id }}">
                    <div class="flex justify-between"><span class="text-sm">{{ $tx->transaction_date->format('d M') }}</span><span class="text-sm font-semibold {{ in_array($tx->transaction_type, ['deposit', 'interest']) ? 'text-green-600' : 'text-red-600' }}">${{ number_format($tx->amount, 2) }}</span></div>
                    <div class="text-xs text-gray-500">{{ Str::limit($tx->description, 40) }}</div>
                </div>
                @empty
                <div class="px-4 py-8 text-center text-gray-500">All transactions reconciled!</div>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border">
            <div class="px-6 py-4 border-b bg-green-50"><h3 class="font-semibold text-green-800">Cash Book Entries (Unmatched)</h3></div>
            <div class="max-h-96 overflow-y-auto">
                @forelse($unreconciledCashBook as $entry)
                <div class="px-4 py-3 border-b hover:bg-gray-50 cursor-pointer" data-cashbook-id="{{ $entry->id }}">
                    <div class="flex justify-between"><span class="text-sm">{{ $entry->entry_date->format('d M') }}</span><span class="text-sm font-semibold {{ $entry->transaction_type == 'receipt' ? 'text-green-600' : 'text-red-600' }}">${{ number_format($entry->amount, 2) }}</span></div>
                    <div class="text-xs text-gray-500">{{ Str::limit($entry->description, 40) }}</div>
                </div>
                @empty
                <div class="px-4 py-8 text-center text-gray-500">All entries matched!</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <p class="text-sm text-yellow-800"><strong>Tip:</strong> Select a bank transaction and a matching cash book entry, then click Match to reconcile them.</p>
    </div>
</div>
@endsection
