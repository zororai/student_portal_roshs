@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Expense Management</h1>
        <div class="space-x-2">
            <a href="{{ route('admin.finance.expenses.categories') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Categories</a>
            <a href="{{ route('admin.finance.expenses.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">+ New Expense</a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Total Expenses</div>
            <div class="text-xl font-bold text-gray-800">${{ number_format($stats['total'], 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Pending Payment</div>
            <div class="text-xl font-bold text-yellow-600">${{ number_format($stats['pending'], 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="text-sm text-gray-500">Paid</div>
            <div class="text-xl font-bold text-green-600">${{ number_format($stats['paid'], 2) }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <select name="category_id" class="border rounded-lg px-3 py-2">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="status" class="border rounded-lg px-3 py-2">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
            </select>
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="border rounded-lg px-3 py-2">
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="border rounded-lg px-3 py-2">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Filter</button>
        </form>
    </div>

    <!-- Expenses Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Number</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($expenses as $expense)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono">{{ $expense->expense_number }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $expense->expense_date->format('d M Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $expense->category->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-sm">{{ Str::limit($expense->description, 40) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-semibold">${{ number_format($expense->amount, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if($expense->payment_status == 'paid')
                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Paid</span>
                        @elseif($expense->payment_status == 'partial')
                        <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">Partial</span>
                        @else
                        <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Pending</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                        <a href="{{ route('admin.finance.expenses.show', $expense->id) }}" class="text-blue-600 hover:underline">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No expenses found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $expenses->links() }}</div>
</div>
@endsection
