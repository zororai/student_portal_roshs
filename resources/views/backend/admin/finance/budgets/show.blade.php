@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">{{ $budget->name }}</h1>
        <div class="space-x-2">
            @if($budget->status == 'draft')
            <form action="{{ route('admin.finance.budgets.activate', $budget->id) }}" method="POST" class="inline">@csrf<button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">Activate</button></form>
            @elseif($budget->status == 'active')
            <form action="{{ route('admin.finance.budgets.close', $budget->id) }}" method="POST" class="inline">@csrf<button type="submit" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700">Close</button></form>
            @endif
            <a href="{{ route('admin.finance.budgets.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Back</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Income Items -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="px-6 py-4 border-b bg-green-50"><h3 class="font-semibold text-green-800">Income Budget</h3></div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50"><tr><th class="px-4 py-2 text-left text-xs text-gray-500">Category</th><th class="px-4 py-2 text-right text-xs text-gray-500">Budgeted</th><th class="px-4 py-2 text-right text-xs text-gray-500">Actual</th></tr></thead>
                <tbody class="divide-y">
                    @foreach($incomeItems as $item)
                    <tr><td class="px-4 py-2 text-sm">{{ $item->category }}</td><td class="px-4 py-2 text-sm text-right">${{ number_format($item->budgeted_amount, 2) }}</td><td class="px-4 py-2 text-sm text-right">${{ number_format($item->actual_amount, 2) }}</td></tr>
                    @endforeach
                </tbody>
            </table>
            @if($budget->status != 'closed')
            <div class="p-4 border-t">
                <form action="{{ route('admin.finance.budgets.add-item', $budget->id) }}" method="POST" class="flex gap-2">
                    @csrf<input type="hidden" name="type" value="income">
                    <input type="text" name="category" placeholder="Category" class="flex-1 border rounded px-2 py-1 text-sm" required>
                    <input type="number" name="budgeted_amount" placeholder="Amount" step="0.01" class="w-28 border rounded px-2 py-1 text-sm" required>
                    <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded text-sm">Add</button>
                </form>
            </div>
            @endif
        </div>

        <!-- Expense Items -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="px-6 py-4 border-b bg-red-50"><h3 class="font-semibold text-red-800">Expense Budget</h3></div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50"><tr><th class="px-4 py-2 text-left text-xs text-gray-500">Category</th><th class="px-4 py-2 text-right text-xs text-gray-500">Budgeted</th><th class="px-4 py-2 text-right text-xs text-gray-500">Actual</th></tr></thead>
                <tbody class="divide-y">
                    @foreach($expenseItems as $item)
                    <tr><td class="px-4 py-2 text-sm">{{ $item->category }}</td><td class="px-4 py-2 text-sm text-right">${{ number_format($item->budgeted_amount, 2) }}</td><td class="px-4 py-2 text-sm text-right">${{ number_format($item->actual_amount, 2) }}</td></tr>
                    @endforeach
                </tbody>
            </table>
            @if($budget->status != 'closed')
            <div class="p-4 border-t">
                <form action="{{ route('admin.finance.budgets.add-item', $budget->id) }}" method="POST" class="flex gap-2">
                    @csrf<input type="hidden" name="type" value="expense">
                    <input type="text" name="category" placeholder="Category" class="flex-1 border rounded px-2 py-1 text-sm" required>
                    <input type="number" name="budgeted_amount" placeholder="Amount" step="0.01" class="w-28 border rounded px-2 py-1 text-sm" required>
                    <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded text-sm">Add</button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
