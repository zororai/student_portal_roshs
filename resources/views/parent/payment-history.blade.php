@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 -m-6 p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">Payment History</h1>
                    <p class="text-gray-500 mt-1">View all fee payments for your children</p>
                </div>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all duration-200 font-medium">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </div>

        @if(count($paymentData) == 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="mt-4 text-lg font-semibold text-gray-700">No Children Found</h3>
            <p class="text-gray-500 mt-2">No students are linked to your account.</p>
        </div>
        @endif

        @foreach($paymentData as $data)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <!-- Student Header -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 text-white px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold">{{ $data['student']->user->name ?? 'Unknown Student' }}</h2>
                            <p class="text-indigo-100">{{ $data['student']->class->class_name ?? 'N/A' }} • {{ ucfirst($data['student']->student_type ?? 'Day') }} Scholar</p>
                        </div>
                    </div>
                    <div class="text-right hidden sm:block">
                        <p class="text-sm text-indigo-100">Total Balance</p>
                        <p class="text-2xl font-bold {{ $data['totalBalance'] > 0 ? 'text-red-200' : 'text-green-200' }}">
                            ${{ number_format($data['totalBalance'], 2) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-6 bg-gray-50 border-b">
                <div class="bg-white rounded-xl p-4 border border-gray-200">
                    <p class="text-sm text-gray-500 uppercase tracking-wide">Total Fees</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">${{ number_format($data['totalFees'], 2) }}</p>
                </div>
                <div class="bg-white rounded-xl p-4 border border-green-200">
                    <p class="text-sm text-green-600 uppercase tracking-wide">Total Paid</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">${{ number_format($data['totalPaid'], 2) }}</p>
                </div>
                <div class="bg-white rounded-xl p-4 border {{ $data['totalBalance'] > 0 ? 'border-red-200' : 'border-green-200' }}">
                    <p class="text-sm {{ $data['totalBalance'] > 0 ? 'text-red-600' : 'text-green-600' }} uppercase tracking-wide">Balance</p>
                    <p class="text-2xl font-bold {{ $data['totalBalance'] > 0 ? 'text-red-600' : 'text-green-600' }} mt-1">${{ number_format($data['totalBalance'], 2) }}</p>
                </div>
            </div>

            <!-- Term Summary -->
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Term by Term Summary
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Term</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Fees</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Paid</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($data['termSummary'] as $term)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $term['term'] }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600 text-right">${{ number_format($term['fees'], 2) }}</td>
                                <td class="px-4 py-3 text-sm text-green-600 text-right">${{ number_format($term['paid'], 2) }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-right {{ $term['balance'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    ${{ number_format($term['balance'], 2) }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($term['balance'] <= 0)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Paid</span>
                                    @elseif($term['paid'] > 0)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Partial</span>
                                    @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Unpaid</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payment Transactions -->
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Payment Transactions
                </h3>

                @if($data['payments']->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Term</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fee Type</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reference</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($data['payments'] as $payment)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $payment->payment_date ? $payment->payment_date->format('d M Y') : 'N/A' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    @if($payment->resultsStatus)
                                        {{ ucfirst($payment->resultsStatus->result_period) }} {{ $payment->resultsStatus->year }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    @if($payment->termFee && $payment->termFee->feeType)
                                        <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">{{ $payment->termFee->feeType->name }}</span>
                                    @else
                                        <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded">General</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm font-semibold text-green-600 text-right">
                                    ${{ number_format($payment->amount_paid, 2) }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    <span class="px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded">{{ ucfirst($payment->payment_method ?? 'Cash') }}</span>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-500 font-mono">
                                    {{ $payment->reference_number ?? '-' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="3" class="px-4 py-3 text-sm font-semibold text-gray-700 text-right">Total Payments:</td>
                                <td class="px-4 py-3 text-sm font-bold text-green-600 text-right">${{ number_format($data['payments']->sum('amount_paid'), 2) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="text-center py-8 bg-gray-50 rounded-xl">
                    <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <p class="mt-2 text-gray-500">No payment transactions found for this student.</p>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
