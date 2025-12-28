@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Expense Report</h1>
        <a href="{{ route('admin.finance.dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg">Back</a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <form method="GET" class="flex gap-4">
            <div><label class="text-sm text-gray-600">From</label><input type="date" name="from_date" value="{{ $fromDate }}" class="border rounded-lg px-3 py-2"></div>
            <div><label class="text-sm text-gray-600">To</label><input type="date" name="to_date" value="{{ $toDate }}" class="border rounded-lg px-3 py-2"></div>
            <div class="flex items-end"><button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Generate</button></div>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <div class="px-6 py-4 border-b"><h3 class="font-semibold">Expenses by Category</h3></div>
            <table class="min-w-full"><thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs">Category</th><th class="px-6 py-3 text-right text-xs">Count</th><th class="px-6 py-3 text-right text-xs">Total</th></tr></thead>
            <tbody class="divide-y">
                @foreach($expenses as $exp)
                <tr><td class="px-6 py-3 text-sm">{{ $exp->category->name ?? 'Uncategorized' }}</td><td class="px-6 py-3 text-sm text-right">{{ $exp->count }}</td><td class="px-6 py-3 text-sm text-right">${{ number_format($exp->total, 2) }}</td></tr>
                @endforeach
                <tr class="bg-gray-50 font-bold"><td class="px-6 py-3">Total</td><td class="px-6 py-3"></td><td class="px-6 py-3 text-right">${{ number_format($totalExpenses, 2) }}</td></tr>
            </tbody></table>
        </div>

        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <div class="px-6 py-4 border-b"><h3 class="font-semibold">Monthly Trend</h3></div>
            <table class="min-w-full"><thead class="bg-gray-50"><tr><th class="px-6 py-3 text-left text-xs">Month</th><th class="px-6 py-3 text-right text-xs">Total</th></tr></thead>
            <tbody class="divide-y">
                @foreach($monthlyTrend as $month)
                <tr><td class="px-6 py-3 text-sm">{{ $month->month }}</td><td class="px-6 py-3 text-sm text-right">${{ number_format($month->total, 2) }}</td></tr>
                @endforeach
            </tbody></table>
        </div>
    </div>
</div>
@endsection
