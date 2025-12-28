@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">General Ledger</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.finance.ledger.trial-balance') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                <i class="fas fa-balance-scale mr-2"></i>Trial Balance
            </a>
            <a href="{{ route('admin.finance.ledger.entries') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700">
                <i class="fas fa-list mr-2"></i>All Entries
            </a>
            <a href="{{ route('admin.finance.ledger.create-account') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>New Account
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        @foreach($accountTypes as $type => $label)
        <div class="bg-white border rounded-lg p-4">
            <div class="text-gray-600 text-sm font-medium">{{ $label }}</div>
            <div class="text-xl font-bold text-gray-800">${{ number_format($summary[$type] ?? 0, 2) }}</div>
        </div>
        @endforeach
    </div>

    <!-- Accounts by Type -->
    @foreach($accountTypes as $type => $label)
        @if(isset($accounts[$type]) && $accounts[$type]->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border mb-6">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h2 class="text-lg font-semibold text-gray-800">{{ $label }} Accounts</h2>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Balance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($accounts[$type] as $account)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900">{{ $account->account_code }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $account->account_name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $account->category ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium {{ $account->current_balance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            ${{ number_format($account->current_balance, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <a href="{{ route('admin.finance.ledger.show-account', $account->id) }}" class="text-blue-600 hover:text-blue-800 mr-2">View</a>
                            <a href="{{ route('admin.finance.ledger.edit-account', $account->id) }}" class="text-green-600 hover:text-green-800">Edit</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    @endforeach
</div>
@endsection
