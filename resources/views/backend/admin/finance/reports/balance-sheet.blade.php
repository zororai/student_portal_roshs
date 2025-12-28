@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Balance Sheet</h1>
        <a href="{{ route('admin.finance.dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg">Back</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <div class="px-6 py-4 border-b bg-blue-50"><h3 class="font-semibold text-blue-800">Assets</h3></div>
            <table class="min-w-full"><tbody class="divide-y">
                @foreach($assets as $account)
                <tr><td class="px-6 py-3 text-sm">{{ $account->account_name }}</td><td class="px-6 py-3 text-sm text-right">${{ number_format($account->current_balance, 2) }}</td></tr>
                @endforeach
                <tr class="bg-blue-50 font-bold"><td class="px-6 py-3">Total Assets</td><td class="px-6 py-3 text-right">${{ number_format($totalAssets, 2) }}</td></tr>
            </tbody></table>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
                <div class="px-6 py-4 border-b bg-red-50"><h3 class="font-semibold text-red-800">Liabilities</h3></div>
                <table class="min-w-full"><tbody class="divide-y">
                    @foreach($liabilities as $account)
                    <tr><td class="px-6 py-3 text-sm">{{ $account->account_name }}</td><td class="px-6 py-3 text-sm text-right">${{ number_format($account->current_balance, 2) }}</td></tr>
                    @endforeach
                    <tr class="bg-red-50 font-bold"><td class="px-6 py-3">Total Liabilities</td><td class="px-6 py-3 text-right">${{ number_format($totalLiabilities, 2) }}</td></tr>
                </tbody></table>
            </div>

            <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
                <div class="px-6 py-4 border-b bg-green-50"><h3 class="font-semibold text-green-800">Equity</h3></div>
                <table class="min-w-full"><tbody class="divide-y">
                    @foreach($equity as $account)
                    <tr><td class="px-6 py-3 text-sm">{{ $account->account_name }}</td><td class="px-6 py-3 text-sm text-right">${{ number_format($account->current_balance, 2) }}</td></tr>
                    @endforeach
                    <tr class="bg-green-50 font-bold"><td class="px-6 py-3">Total Equity</td><td class="px-6 py-3 text-right">${{ number_format($totalEquity, 2) }}</td></tr>
                </tbody></table>
            </div>
        </div>
    </div>

    <div class="mt-6 bg-gray-100 rounded-lg p-4">
        <div class="flex justify-between font-bold"><span>Total Liabilities + Equity</span><span>${{ number_format($totalLiabilities + $totalEquity, 2) }}</span></div>
    </div>
</div>
@endsection
