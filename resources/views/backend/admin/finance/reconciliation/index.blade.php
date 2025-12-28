@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Bank Reconciliation</h1>
        <a href="{{ route('admin.finance.reconciliation.accounts') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Manage Accounts</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($accounts as $account)
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="font-semibold text-lg">{{ $account->account_name }}</h3>
            <p class="text-gray-500 text-sm">{{ $account->bank_name }} - {{ $account->account_number }}</p>
            <div class="mt-4">
                <div class="text-2xl font-bold {{ $account->current_balance >= 0 ? 'text-green-600' : 'text-red-600' }}">${{ number_format($account->current_balance, 2) }}</div>
            </div>
            <div class="mt-4 flex gap-2">
                <a href="{{ route('admin.finance.reconciliation.transactions', $account->id) }}" class="text-blue-600 hover:underline text-sm">Transactions</a>
                <a href="{{ route('admin.finance.reconciliation.reconcile', $account->id) }}" class="text-green-600 hover:underline text-sm">Reconcile</a>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-8 text-gray-500">No bank accounts configured. <a href="{{ route('admin.finance.reconciliation.accounts') }}" class="text-blue-600">Add one now</a>.</div>
        @endforelse
    </div>
</div>
@endsection
