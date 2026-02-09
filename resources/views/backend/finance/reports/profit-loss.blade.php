@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Profit & Loss Statement</h1>
                <p class="mt-2 text-sm text-gray-600">For the period {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('finance.reports.trial-balance') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    Trial Balance
                </a>
                <a href="{{ route('finance.reports.balance-sheet') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    Balance Sheet
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('finance.reports.profit-loss') }}" class="flex items-end space-x-4">
            <div class="flex-1">
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="flex-1">
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                Generate Report
            </button>
        </form>
    </div>

    <!-- P&L Statement -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6">
            <!-- Income Section -->
            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4 pb-2 border-b-2 border-gray-200">INCOME</h2>
                @foreach($incomeByCategory as $category)
                    <div class="mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">{{ $category['category'] }}</h3>
                        @foreach($category['accounts'] as $account)
                            <div class="flex justify-between items-center py-1 pl-4">
                                <span class="text-sm text-gray-600">{{ $account['code'] }} - {{ $account['name'] }}</span>
                                <span class="text-sm text-gray-900">${{ number_format($account['amount'], 2) }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between items-center py-2 pl-4 border-t border-gray-200 mt-1">
                            <span class="text-sm font-medium text-gray-700">Total {{ $category['category'] }}</span>
                            <span class="text-sm font-bold text-gray-900">${{ number_format($category['total'], 2) }}</span>
                        </div>
                    </div>
                @endforeach
                <div class="flex justify-between items-center py-3 mt-4 border-t-2 border-gray-300">
                    <span class="text-base font-bold text-gray-900">TOTAL INCOME</span>
                    <span class="text-base font-bold text-green-600">${{ number_format($totalIncome, 2) }}</span>
                </div>
            </div>

            <!-- Expenses Section -->
            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4 pb-2 border-b-2 border-gray-200">EXPENSES</h2>
                @foreach($expensesByCategory as $category)
                    <div class="mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">{{ $category['category'] }}</h3>
                        @foreach($category['accounts'] as $account)
                            <div class="flex justify-between items-center py-1 pl-4">
                                <span class="text-sm text-gray-600">{{ $account['code'] }} - {{ $account['name'] }}</span>
                                <span class="text-sm text-gray-900">${{ number_format($account['amount'], 2) }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between items-center py-2 pl-4 border-t border-gray-200 mt-1">
                            <span class="text-sm font-medium text-gray-700">Total {{ $category['category'] }}</span>
                            <span class="text-sm font-bold text-gray-900">${{ number_format($category['total'], 2) }}</span>
                        </div>
                    </div>
                @endforeach
                <div class="flex justify-between items-center py-3 mt-4 border-t-2 border-gray-300">
                    <span class="text-base font-bold text-gray-900">TOTAL EXPENSES</span>
                    <span class="text-base font-bold text-red-600">${{ number_format($totalExpenses, 2) }}</span>
                </div>
            </div>

            <!-- Net Profit/Loss -->
            <div class="bg-gray-50 rounded-lg p-4 border-2 border-gray-300">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-gray-900">NET {{ $netProfit >= 0 ? 'PROFIT' : 'LOSS' }}</span>
                    <span class="text-lg font-bold {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        ${{ number_format(abs($netProfit), 2) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Button -->
    <div class="mt-6 flex justify-end">
        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print Report
        </button>
    </div>
</div>
@endsection
