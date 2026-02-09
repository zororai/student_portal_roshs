@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Balance Sheet</h1>
                <p class="mt-2 text-sm text-gray-600">As of {{ \Carbon\Carbon::parse($asOfDate)->format('F d, Y') }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('finance.reports.trial-balance') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    Trial Balance
                </a>
                <a href="{{ route('finance.reports.profit-loss') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    P&L Statement
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('finance.reports.balance-sheet') }}" class="flex items-end space-x-4">
            <div class="flex-1">
                <label for="as_of_date" class="block text-sm font-medium text-gray-700 mb-2">As of Date</label>
                <input type="date" name="as_of_date" id="as_of_date" value="{{ $asOfDate }}" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                Generate Report
            </button>
        </form>
    </div>

    <!-- Balance Sheet -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Assets Column -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-blue-50 px-6 py-4 border-b border-blue-200">
                <h2 class="text-xl font-bold text-blue-900">ASSETS</h2>
            </div>
            <div class="p-6">
                @foreach($assetsByCategory as $category)
                    <div class="mb-4">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">{{ $category['category'] }}</h3>
                        @foreach($category['accounts'] as $account)
                            <div class="flex justify-between items-center py-1 pl-4">
                                <span class="text-sm text-gray-600">{{ $account['code'] }} - {{ $account['name'] }}</span>
                                <span class="text-sm text-gray-900">${{ number_format($account['balance'], 2) }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between items-center py-2 pl-4 border-t border-gray-200 mt-1">
                            <span class="text-sm font-medium text-gray-700">Total {{ $category['category'] }}</span>
                            <span class="text-sm font-bold text-gray-900">${{ number_format($category['total'], 2) }}</span>
                        </div>
                    </div>
                @endforeach
                <div class="flex justify-between items-center py-3 mt-4 border-t-2 border-blue-300 bg-blue-50 px-4 rounded">
                    <span class="text-base font-bold text-blue-900">TOTAL ASSETS</span>
                    <span class="text-base font-bold text-blue-900">${{ number_format($totalAssets, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Liabilities & Equity Column -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-red-50 px-6 py-4 border-b border-red-200">
                <h2 class="text-xl font-bold text-red-900">LIABILITIES & EQUITY</h2>
            </div>
            <div class="p-6">
                <!-- Liabilities -->
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Liabilities</h3>
                    @foreach($liabilitiesByCategory as $category)
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">{{ $category['category'] }}</h4>
                            @foreach($category['accounts'] as $account)
                                <div class="flex justify-between items-center py-1 pl-4">
                                    <span class="text-sm text-gray-600">{{ $account['code'] }} - {{ $account['name'] }}</span>
                                    <span class="text-sm text-gray-900">${{ number_format($account['balance'], 2) }}</span>
                                </div>
                            @endforeach
                            <div class="flex justify-between items-center py-2 pl-4 border-t border-gray-200 mt-1">
                                <span class="text-sm font-medium text-gray-700">Total {{ $category['category'] }}</span>
                                <span class="text-sm font-bold text-gray-900">${{ number_format($category['total'], 2) }}</span>
                            </div>
                        </div>
                    @endforeach
                    <div class="flex justify-between items-center py-2 mt-2 border-t border-gray-300">
                        <span class="text-sm font-bold text-gray-900">Total Liabilities</span>
                        <span class="text-sm font-bold text-gray-900">${{ number_format($totalLiabilities, 2) }}</span>
                    </div>
                </div>

                <!-- Equity -->
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-gray-900 mb-3">Equity</h3>
                    @foreach($equityByCategory as $category)
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-2">{{ $category['category'] }}</h4>
                            @foreach($category['accounts'] as $account)
                                <div class="flex justify-between items-center py-1 pl-4">
                                    <span class="text-sm text-gray-600">{{ $account['code'] }} - {{ $account['name'] }}</span>
                                    <span class="text-sm text-gray-900">${{ number_format($account['balance'], 2) }}</span>
                                </div>
                            @endforeach
                            <div class="flex justify-between items-center py-2 pl-4 border-t border-gray-200 mt-1">
                                <span class="text-sm font-medium text-gray-700">Total {{ $category['category'] }}</span>
                                <span class="text-sm font-bold text-gray-900">${{ number_format($category['total'], 2) }}</span>
                            </div>
                        </div>
                    @endforeach
                    <div class="flex justify-between items-center py-2 mt-2 border-t border-gray-300">
                        <span class="text-sm font-bold text-gray-900">Total Equity</span>
                        <span class="text-sm font-bold text-gray-900">${{ number_format($totalEquity, 2) }}</span>
                    </div>
                </div>

                <div class="flex justify-between items-center py-3 mt-4 border-t-2 border-red-300 bg-red-50 px-4 rounded">
                    <span class="text-base font-bold text-red-900">TOTAL LIABILITIES & EQUITY</span>
                    <span class="text-base font-bold text-red-900">${{ number_format($totalLiabilitiesAndEquity, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Balance Check -->
    <div class="mt-6">
        @if(round($totalAssets, 2) === round($totalLiabilitiesAndEquity, 2))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-green-800 font-medium">Balance Sheet is balanced (Assets = Liabilities + Equity)</span>
                </div>
            </div>
        @else
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-red-800 font-medium">WARNING: Balance Sheet is out of balance! Difference: ${{ number_format(abs($totalAssets - $totalLiabilitiesAndEquity), 2) }}</span>
                </div>
            </div>
        @endif
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
