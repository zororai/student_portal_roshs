@extends('layouts.app')

@section('content')
<!-- Page Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Accounts Receivable</h1>
        <p class="text-gray-500 text-sm">Manage student invoices and track outstanding balances</p>
    </div>
    <a href="{{ route('finance.receivables.invoices.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow-md transition duration-200">
        + Create Invoice
    </a>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white shadow-md rounded-xl p-5 hover:shadow-lg transition duration-200">
        <p class="text-sm text-gray-500">Total Receivables</p>
        <h2 class="text-2xl font-bold text-indigo-600">${{ number_format($totalReceivables, 2) }}</h2>
    </div>

    <div class="bg-white shadow-md rounded-xl p-5 hover:shadow-lg transition duration-200">
        <p class="text-sm text-gray-500">Overdue Invoices</p>
        <h2 class="text-2xl font-bold text-red-600">{{ $overdueInvoices }}</h2>
    </div>

    <div class="bg-white shadow-md rounded-xl p-5 hover:shadow-lg transition duration-200">
        <p class="text-sm text-gray-500">Current (0-30 days)</p>
        <h2 class="text-2xl font-bold text-blue-600">${{ number_format($agingSummary['current'], 2) }}</h2>
    </div>

    <div class="bg-white shadow-md rounded-xl p-5 hover:shadow-lg transition duration-200">
        <p class="text-sm text-gray-500">90+ Days</p>
        <h2 class="text-2xl font-bold text-orange-600">${{ number_format($agingSummary['90_plus_days'], 2) }}</h2>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white shadow-md rounded-xl p-6 mb-8">
    <h3 class="font-semibold text-gray-800 mb-4">Quick Actions</h3>
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('finance.receivables.invoices') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow-md transition duration-200">
            View All Invoices
        </a>
        <a href="{{ route('finance.receivables.aging') }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg shadow-md transition duration-200">
            Aging Report
        </a>
        <a href="{{ route('finance.receivables.invoices.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-md transition duration-200">
            New Invoice
        </a>
        <a href="{{ route('finance.reports.trial-balance') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition duration-200">
            Trial Balance
        </a>
    </div>
</div>

<!-- Recent Invoices -->
<div class="bg-white shadow-md rounded-xl overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h3 class="font-semibold text-gray-800">Recent Invoices</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Paid</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($recentInvoices as $invoice)
                    <tr class="hover:bg-gray-50 transition duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <a href="{{ route('finance.receivables.invoices.show', $invoice->id) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                {{ $invoice->invoice_number }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-900">{{ $invoice->student->user->name ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $invoice->invoice_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm {{ $invoice->isOverdue() ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                {{ $invoice->due_date->format('M d, Y') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                            ${{ number_format($invoice->amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-green-600">
                            ${{ number_format($invoice->paid_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-red-600">
                            ${{ number_format($invoice->getOutstandingAmount(), 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($invoice->status == 'unpaid')
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Unpaid</span>
                            @elseif($invoice->status == 'partial')
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Partial</span>
                            @else
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Paid</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                            <a href="{{ route('finance.receivables.invoices.show', $invoice->id) }}" 
                               class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition duration-150">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-lg font-medium mb-2">No recent invoices found</p>
                                <a href="{{ route('finance.receivables.invoices.create') }}" class="mt-3 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200">
                                    Create Your First Invoice
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

