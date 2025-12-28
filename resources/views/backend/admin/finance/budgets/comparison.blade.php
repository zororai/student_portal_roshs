@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Budget vs Actual - {{ $budget->name }}</h1>
        <a href="{{ route('admin.finance.budgets.show', $budget->id) }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Back</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <div class="px-6 py-4 border-b bg-green-50"><h3 class="font-semibold text-green-800">Income Comparison</h3></div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50"><tr><th class="px-4 py-2 text-left text-xs">Category</th><th class="px-4 py-2 text-right text-xs">Budgeted</th><th class="px-4 py-2 text-right text-xs">Actual</th><th class="px-4 py-2 text-right text-xs">Variance</th></tr></thead>
                <tbody class="divide-y">
                    @foreach($incomeComparison as $item)
                    <tr>
                        <td class="px-4 py-2 text-sm">{{ $item['category'] }}</td>
                        <td class="px-4 py-2 text-sm text-right">${{ number_format($item['budgeted'], 2) }}</td>
                        <td class="px-4 py-2 text-sm text-right">${{ number_format($item['actual'], 2) }}</td>
                        <td class="px-4 py-2 text-sm text-right {{ $item['variance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">${{ number_format($item['variance'], 2) }} ({{ $item['variance_pct'] }}%)</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <div class="px-6 py-4 border-b bg-red-50"><h3 class="font-semibold text-red-800">Expense Comparison</h3></div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50"><tr><th class="px-4 py-2 text-left text-xs">Category</th><th class="px-4 py-2 text-right text-xs">Budgeted</th><th class="px-4 py-2 text-right text-xs">Actual</th><th class="px-4 py-2 text-right text-xs">Variance</th></tr></thead>
                <tbody class="divide-y">
                    @foreach($expenseComparison as $item)
                    <tr>
                        <td class="px-4 py-2 text-sm">{{ $item['category'] }}</td>
                        <td class="px-4 py-2 text-sm text-right">${{ number_format($item['budgeted'], 2) }}</td>
                        <td class="px-4 py-2 text-sm text-right">${{ number_format($item['actual'], 2) }}</td>
                        <td class="px-4 py-2 text-sm text-right {{ $item['variance'] >= 0 ? 'text-green-600' : 'text-red-600' }}">${{ number_format($item['variance'], 2) }} ({{ $item['variance_pct'] }}%)</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
