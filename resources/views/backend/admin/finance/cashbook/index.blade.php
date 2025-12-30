@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Cash Book</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.finance.cashbook.report') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                <i class="fas fa-file-alt mr-2"></i>Report
            </a>
            <a href="{{ route('admin.finance.cashbook.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>New Entry
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">{{ session('error') }}</div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="text-green-600 text-sm font-medium">Total Receipts</div>
            <div class="text-2xl font-bold text-green-800">${{ number_format($totalReceipts, 2) }}</div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="text-red-600 text-sm font-medium">Total Payments</div>
            <div class="text-2xl font-bold text-red-800">${{ number_format($totalPayments, 2) }}</div>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="text-blue-600 text-sm font-medium">Current Balance</div>
            <div class="text-2xl font-bold text-blue-800">${{ number_format($balance, 2) }}</div>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
            <div class="text-purple-600 text-sm font-medium">Today's Activity</div>
            <div class="text-sm text-purple-800">
                <span class="text-green-600">+${{ number_format($todayReceipts, 2) }}</span> /
                <span class="text-red-600">-${{ number_format($todayPayments, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                <select name="year" class="border rounded-lg px-3 py-2">
                    <option value="">All Years</option>
                    @foreach($years as $year)
                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Term</label>
                <select name="term" class="border rounded-lg px-3 py-2">
                    <option value="">All Terms</option>
                    @foreach($terms as $key => $label)
                    <option value="{{ $key }}" {{ $selectedTerm == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select name="transaction_type" class="border rounded-lg px-3 py-2">
                    <option value="">All Types</option>
                    <option value="receipt" {{ request('transaction_type') == 'receipt' ? 'selected' : '' }}>Receipts</option>
                    <option value="payment" {{ request('transaction_type') == 'payment' ? 'selected' : '' }}>Payments</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Filter</button>
            </div>
        </form>
    </div>

    <!-- Cash Book Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Receipt</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Balance</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($entries as $entry)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $entry->entry_date->format('d M Y') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $entry->reference_number }}</td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ $entry->description }}</div>
                        <div class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $entry->category)) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">
                        {{ $entry->isReceipt() ? '$' . number_format($entry->amount, 2) : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">
                        {{ $entry->isPayment() ? '$' . number_format($entry->amount, 2) : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${{ number_format($entry->balance, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <a href="{{ route('admin.finance.cashbook.show', $entry->id) }}" class="text-blue-600 hover:text-blue-800 mr-2">View</a>
                        @if(!$entry->related_payroll_id)
                            <a href="{{ route('admin.finance.cashbook.edit', $entry->id) }}" class="text-green-600 hover:text-green-800">Edit</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No entries found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $entries->links() }}
    </div>
</div>
@endsection
