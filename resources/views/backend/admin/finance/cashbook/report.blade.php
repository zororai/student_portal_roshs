@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Cash Book Report</h1>
        <a href="{{ route('admin.finance.cashbook.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Back</a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <form method="GET" class="flex gap-4">
            <div><label class="block text-sm font-medium text-gray-700 mb-1">From</label><input type="date" name="date_from" value="{{ $dateFrom }}" class="border rounded-lg px-3 py-2"></div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">To</label><input type="date" name="date_to" value="{{ $dateTo }}" class="border rounded-lg px-3 py-2"></div>
            <div class="flex items-end"><button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Generate</button></div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gray-50 border rounded-lg p-4"><div class="text-sm text-gray-500">Opening Balance</div><div class="text-xl font-bold">${{ number_format($openingBalanceAmount, 2) }}</div></div>
        <div class="bg-green-50 border rounded-lg p-4"><div class="text-sm text-gray-500">Total Receipts</div><div class="text-xl font-bold text-green-600">${{ number_format($totalReceipts, 2) }}</div></div>
        <div class="bg-red-50 border rounded-lg p-4"><div class="text-sm text-gray-500">Total Payments</div><div class="text-xl font-bold text-red-600">${{ number_format($totalPayments, 2) }}</div></div>
        <div class="bg-blue-50 border rounded-lg p-4"><div class="text-sm text-gray-500">Closing Balance</div><div class="text-xl font-bold text-blue-600">${{ number_format($closingBalance, 2) }}</div></div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <h3 class="font-semibold mb-3">Receipts by Category</h3>
            @forelse($receiptsByCategory as $cat => $amount)
            <div class="flex justify-between py-1"><span>{{ ucfirst(str_replace('_', ' ', $cat)) }}</span><span class="text-green-600">${{ number_format($amount, 2) }}</span></div>
            @empty
            <p class="text-gray-500">No receipts</p>
            @endforelse
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <h3 class="font-semibold mb-3">Payments by Category</h3>
            @forelse($paymentsByCategory as $cat => $amount)
            <div class="flex justify-between py-1"><span>{{ ucfirst(str_replace('_', ' ', $cat)) }}</span><span class="text-red-600">${{ number_format($amount, 2) }}</span></div>
            @empty
            <p class="text-gray-500">No payments</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
