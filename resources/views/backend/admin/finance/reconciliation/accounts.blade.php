@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Bank Accounts</h1>
        <a href="{{ route('admin.finance.reconciliation.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg">Back</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs">Account</th><th class="px-6 py-3 text-left text-xs">Bank</th><th class="px-6 py-3 text-right text-xs">Balance</th></tr></thead>
                <tbody class="divide-y">
                    @foreach($accounts as $account)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium">{{ $account->account_name }}<br><span class="text-gray-500 text-xs">{{ $account->account_number }}</span></td>
                        <td class="px-6 py-4 text-sm">{{ $account->bank_name }}</td>
                        <td class="px-6 py-4 text-sm text-right font-semibold">${{ number_format($account->current_balance, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="font-semibold mb-4">Add Bank Account</h3>
            <form action="{{ route('admin.finance.reconciliation.store-account') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Account Name *</label><input type="text" name="account_name" class="w-full border rounded-lg px-3 py-2" required></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Bank Name *</label><input type="text" name="bank_name" class="w-full border rounded-lg px-3 py-2" required></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Account Number *</label><input type="text" name="account_number" class="w-full border rounded-lg px-3 py-2" required></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Opening Balance *</label><input type="number" name="opening_balance" step="0.01" class="w-full border rounded-lg px-3 py-2" required></div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Add Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
