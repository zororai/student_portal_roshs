@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Income Statement</h1>
        <a href="{{ route('admin.finance.dashboard') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg">Back</a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <form method="GET" class="flex gap-4">
            <div><label class="text-sm text-gray-600">From</label><input type="date" name="from_date" value="{{ $fromDate }}" class="border rounded-lg px-3 py-2"></div>
            <div><label class="text-sm text-gray-600">To</label><input type="date" name="to_date" value="{{ $toDate }}" class="border rounded-lg px-3 py-2"></div>
            <div class="flex items-end"><button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Generate</button></div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="px-6 py-4 border-b bg-green-50"><h3 class="font-semibold text-green-800">Revenue</h3></div>
        <table class="min-w-full"><tbody class="divide-y">
            @foreach($income as $item)
            <tr><td class="px-6 py-3 text-sm">{{ ucfirst(str_replace('_', ' ', $item->category)) }}</td><td class="px-6 py-3 text-sm text-right">${{ number_format($item->total, 2) }}</td></tr>
            @endforeach
            <tr class="bg-green-50 font-bold"><td class="px-6 py-3">Total Revenue</td><td class="px-6 py-3 text-right">${{ number_format($totalIncome, 2) }}</td></tr>
        </tbody></table>

        <div class="px-6 py-4 border-b border-t bg-red-50"><h3 class="font-semibold text-red-800">Expenses</h3></div>
        <table class="min-w-full"><tbody class="divide-y">
            @foreach($expenses as $item)
            <tr><td class="px-6 py-3 text-sm">{{ ucfirst(str_replace('_', ' ', $item->category)) }}</td><td class="px-6 py-3 text-sm text-right">${{ number_format($item->total, 2) }}</td></tr>
            @endforeach
            <tr class="bg-red-50 font-bold"><td class="px-6 py-3">Total Expenses</td><td class="px-6 py-3 text-right">${{ number_format($totalExpenses, 2) }}</td></tr>
        </tbody></table>

        <div class="px-6 py-4 border-t {{ $netIncome >= 0 ? 'bg-blue-100' : 'bg-red-100' }}">
            <div class="flex justify-between font-bold text-lg">
                <span>Net Income</span>
                <span class="{{ $netIncome >= 0 ? 'text-green-600' : 'text-red-600' }}">${{ number_format($netIncome, 2) }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
