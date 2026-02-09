@extends('layouts.app')

@section('content')
<!-- Page Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Accounts Payable</h1>
        <p class="text-gray-500 text-sm">Manage supplier invoices and payments</p>
    </div>
    <a href="{{ route('finance.payables.invoices.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow-md transition duration-200">
        + New Invoice
    </a>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white shadow-md rounded-xl p-5 hover:shadow-lg transition duration-200">
        <p class="text-sm text-gray-500">Total Payables</p>
        <h2 class="text-2xl font-bold text-red-600">${{ number_format($totalPayables, 2) }}</h2>
    </div>

    <div class="bg-white shadow-md rounded-xl p-5 hover:shadow-lg transition duration-200">
        <p class="text-sm text-gray-500">Overdue Invoices</p>
        <h2 class="text-2xl font-bold text-orange-600">{{ $overdueInvoices }}</h2>
    </div>

    <div class="bg-white shadow-md rounded-xl p-5 hover:shadow-lg transition duration-200">
        <p class="text-sm text-gray-500">Recent Invoices</p>
        <h2 class="text-2xl font-bold text-blue-600">{{ $recentInvoices->count() }}</h2>
    </div>

    <div class="bg-white shadow-md rounded-xl p-5 hover:shadow-lg transition duration-200">
        <p class="text-sm text-gray-500">Recent Payments</p>
        <h2 class="text-2xl font-bold text-green-600">{{ $recentPayments->count() }}</h2>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white shadow-md rounded-xl p-6 mb-8">
    <h3 class="font-semibold text-gray-800 mb-4">Quick Actions</h3>
    <div class="flex flex-wrap gap-3">
        <a href="{{ route('finance.payables.invoices') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow-md transition duration-200">
            View All Invoices
        </a>
        <a href="{{ route('finance.payables.aging') }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg shadow-md transition duration-200">
            Aging Report
        </a>
        <a href="{{ route('finance.payables.invoices.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-md transition duration-200">
            New Invoice
        </a>
        <a href="{{ route('finance.reports.trial-balance') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition duration-200">
            Trial Balance
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Recent Invoices -->
    <div class="bg-white shadow-md rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800">Recent Invoices</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentInvoices as $invoice)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <a href="{{ route('finance.payables.invoices.show', $invoice->id) }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                                    {{ $invoice->invoice_number }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $invoice->supplier->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900">${{ number_format($invoice->amount, 2) }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($invoice->status == 'unpaid')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Unpaid</span>
                                @elseif($invoice->status == 'partial')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Partial</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Paid</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-400">
                                <p class="text-sm">No recent invoices</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Payments -->
    <div class="bg-white shadow-md rounded-xl overflow-hidden">
        <div class="px-6 py-4 border-b">
            <h3 class="font-semibold text-gray-800">Recent Payments</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment #</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recentPayments as $payment)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-indigo-600">{{ $payment->payment_number }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $payment->invoice->supplier->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $payment->payment_date->format('M d') }}</td>
                            <td class="px-4 py-3 text-right text-sm font-semibold text-green-600">${{ number_format($payment->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-400">
                                <p class="text-sm">No recent payments</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
