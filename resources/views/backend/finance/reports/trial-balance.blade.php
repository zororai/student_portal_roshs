@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Trial Balance</h1>
                <p class="mt-2 text-sm text-gray-600">Verify ledger balance as of {{ \Carbon\Carbon::parse($asOfDate)->format('F d, Y') }}</p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('finance.reports.profit-loss') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    P&L Statement
                </a>
                <a href="{{ route('finance.reports.balance-sheet') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    Balance Sheet
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <form method="GET" action="{{ route('finance.reports.trial-balance') }}" class="flex items-end space-x-4">
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

    <!-- Balance Status -->
    @if($trialBalance['is_balanced'])
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-green-800 font-medium">Ledger is balanced</span>
            </div>
        </div>
    @else
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-red-800 font-medium">WARNING: Ledger is out of balance!</span>
            </div>
        </div>
    @endif

    <!-- Trial Balance Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account Code</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Account Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Debit</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Credit</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($trialBalance['accounts'] as $account)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $account['account_code'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $account['account_name'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    @if($account['account_type'] === 'asset') bg-blue-100 text-blue-800
                                    @elseif($account['account_type'] === 'liability') bg-red-100 text-red-800
                                    @elseif($account['account_type'] === 'equity') bg-purple-100 text-purple-800
                                    @elseif($account['account_type'] === 'income') bg-green-100 text-green-800
                                    @else bg-orange-100 text-orange-800
                                    @endif">
                                    {{ ucfirst($account['account_type']) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                                @if($account['debit'] > 0)
                                    ${{ number_format($account['debit'], 2) }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                                @if($account['credit'] > 0)
                                    ${{ number_format($account['credit'], 2) }}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                No accounts with balances found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                    <tr class="font-bold">
                        <td colspan="3" class="px-6 py-4 text-right text-sm text-gray-900">TOTAL:</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                            ${{ number_format($trialBalance['total_debits'], 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                            ${{ number_format($trialBalance['total_credits'], 2) }}
                        </td>
                    </tr>
                    @if(!$trialBalance['is_balanced'])
                        <tr class="bg-red-50">
                            <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-red-800">Difference:</td>
                            <td colspan="2" class="px-6 py-3 text-right text-sm font-bold text-red-800">
                                ${{ number_format(abs($trialBalance['total_debits'] - $trialBalance['total_credits']), 2) }}
                            </td>
                        </tr>
                    @endif
                </tfoot>
            </table>
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

<style>
    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>
@endsection
