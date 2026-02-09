@extends('layouts.app')

@section('content')
<!-- Page Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Accounts Receivable Aging Report</h1>
        <p class="text-gray-500 text-sm">Track outstanding balances by aging period</p>
    </div>
    <div class="flex gap-2">
        <button onclick="window.print()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition duration-200">
            Print Report
        </button>
        <a href="{{ route('finance.receivables.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow-md transition duration-200">
            ← Back to Dashboard
        </a>
    </div>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white shadow-md rounded-xl p-5 hover:shadow-lg transition duration-200">
        <p class="text-sm text-gray-500">Current (0-30)</p>
        <h2 class="text-2xl font-bold text-blue-600">${{ number_format($totals['current'], 2) }}</h2>
    </div>

    <div class="bg-white shadow-md rounded-xl p-5 hover:shadow-lg transition duration-200">
        <p class="text-sm text-gray-500">31-60 Days</p>
        <h2 class="text-2xl font-bold text-yellow-600">${{ number_format($totals['30_days'], 2) }}</h2>
    </div>

    <div class="bg-white shadow-md rounded-xl p-5 hover:shadow-lg transition duration-200">
        <p class="text-sm text-gray-500">61-90 Days</p>
        <h2 class="text-2xl font-bold text-orange-600">${{ number_format($totals['60_days'], 2) }}</h2>
    </div>

    <div class="bg-white shadow-md rounded-xl p-5 hover:shadow-lg transition duration-200">
        <p class="text-sm text-gray-500">90+ Days</p>
        <h2 class="text-2xl font-bold text-red-600">${{ number_format($totals['90_plus_days'], 2) }}</h2>
    </div>
</div>

<!-- Aging Report Table -->
<div class="bg-white shadow-md rounded-xl overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h3 class="font-semibold text-gray-800">Aging Details - As of {{ $asOfDate }}</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Current</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">31-60 Days</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">61-90 Days</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">90+ Days</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($agingData as $data)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $data['student']->user->name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $data['student']->roll_number ?? 'N/A' }}</div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($data['current'] > 0)
                                <span class="text-sm font-semibold text-blue-600">${{ number_format($data['current'], 2) }}</span>
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($data['30_days'] > 0)
                                <span class="text-sm font-semibold text-yellow-600">${{ number_format($data['30_days'], 2) }}</span>
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($data['60_days'] > 0)
                                <span class="text-sm font-semibold text-orange-600">${{ number_format($data['60_days'], 2) }}</span>
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($data['90_plus_days'] > 0)
                                <span class="text-sm font-semibold text-red-600">${{ number_format($data['90_plus_days'], 2) }}</span>
                            @else
                                <span class="text-sm text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-sm font-bold text-gray-900">${{ number_format($data['total'], 2) }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('finance.receivables.statement', $data['student']->id) }}" 
                               class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition duration-150">
                                Statement
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-12 h-12 mb-4 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <p class="text-lg font-medium">No outstanding receivables!</p>
                                <p class="text-sm">All student invoices have been paid.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if(count($agingData) > 0)
                <tfoot class="bg-gray-100 font-bold">
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">TOTAL</td>
                        <td class="px-6 py-4 text-right text-sm text-blue-600">${{ number_format($totals['current'], 2) }}</td>
                        <td class="px-6 py-4 text-right text-sm text-yellow-600">${{ number_format($totals['30_days'], 2) }}</td>
                        <td class="px-6 py-4 text-right text-sm text-orange-600">${{ number_format($totals['60_days'], 2) }}</td>
                        <td class="px-6 py-4 text-right text-sm text-red-600">${{ number_format($totals['90_plus_days'], 2) }}</td>
                        <td class="px-6 py-4 text-right text-sm text-gray-900">${{ number_format($totals['total'], 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>

<!-- Print Styles -->
<style>
    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>
@endsection

