@extends('layouts.app')

@section('content')
<!-- Page Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Invoice {{ $invoice->invoice_number }}</h1>
        <p class="text-gray-500 text-sm">Supplier: {{ $invoice->supplier->name ?? 'N/A' }}</p>
    </div>
    <div class="flex gap-2">
        @if($invoice->status != 'paid')
            <a href="{{ route('finance.payables.payments.create', $invoice->id) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-md transition duration-200">
                Record Payment
            </a>
        @endif
        <a href="{{ route('finance.payables.invoices') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-md transition duration-200">
            ← Back to List
        </a>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Invoice Details -->
    <div class="lg:col-span-2">
        <div class="bg-white shadow-md rounded-xl p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Invoice Information</h3>
            
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Invoice Number</p>
                    <p class="font-semibold text-gray-900">{{ $invoice->invoice_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Status</p>
                    <div>
                        @if($invoice->status == 'unpaid')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Unpaid</span>
                        @elseif($invoice->status == 'partial')
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Partial</span>
                        @else
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Paid</span>
                        @endif
                    </div>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Invoice Date</p>
                    <p class="font-semibold text-gray-900">{{ $invoice->invoice_date->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Due Date</p>
                    <p class="font-semibold {{ $invoice->isOverdue() ? 'text-red-600' : 'text-gray-900' }}">
                        {{ $invoice->due_date->format('M d, Y') }}
                        @if($invoice->isOverdue())
                            <span class="text-xs ml-2 bg-red-100 text-red-800 px-2 py-0.5 rounded-full">{{ $invoice->getDaysOverdue() }} days overdue</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Supplier</p>
                    <p class="font-semibold text-gray-900">{{ $invoice->supplier->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Expense Account</p>
                    <p class="font-semibold text-gray-900">
                        {{ $invoice->expenseAccount->account_code ?? 'N/A' }} - {{ $invoice->expenseAccount->account_name ?? 'N/A' }}
                    </p>
                </div>
            </div>

            @if($invoice->description)
                <div class="mt-6 pt-6 border-t">
                    <p class="text-sm text-gray-500 mb-2">Description</p>
                    <p class="text-gray-700">{{ $invoice->description }}</p>
                </div>
            @endif
        </div>

        <!-- Payment History -->
        @if($invoice->payments && $invoice->payments->count() > 0)
            <div class="bg-white shadow-md rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Payment History</h3>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Payment #</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Method</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($invoice->payments as $payment)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-medium text-indigo-600">{{ $payment->payment_number }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $payment->payment_date->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700 capitalize">{{ $payment->payment_method }}</td>
                                    <td class="px-4 py-3 text-sm text-right font-semibold text-green-600">${{ number_format($payment->amount, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <!-- Summary Sidebar -->
    <div class="lg:col-span-1">
        <div class="bg-white shadow-md rounded-xl p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Summary</h3>
            
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Invoice Amount</span>
                    <span class="font-bold text-lg text-gray-900">${{ number_format($invoice->amount, 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">Amount Paid</span>
                    <span class="font-semibold text-green-600">${{ number_format($invoice->paid_amount, 2) }}</span>
                </div>
                <div class="pt-4 border-t">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-900 font-semibold">Balance Due</span>
                        <span class="font-bold text-xl text-red-600">${{ number_format($invoice->getOutstandingAmount(), 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Ledger Entries -->
        @if($invoice->ledgerEntries && $invoice->ledgerEntries->count() > 0)
            <div class="bg-white shadow-md rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Ledger Entries</h3>
                <div class="space-y-3">
                    @foreach($invoice->ledgerEntries as $entry)
                        <div class="text-sm">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $entry->account->account_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $entry->account->account_code }}</p>
                                </div>
                                <div class="text-right">
                                    @if($entry->entry_type == 'debit')
                                        <p class="font-semibold text-gray-900">${{ number_format($entry->amount, 2) }}</p>
                                        <p class="text-xs text-gray-500">Debit</p>
                                    @else
                                        <p class="font-semibold text-gray-900">${{ number_format($entry->amount, 2) }}</p>
                                        <p class="text-xs text-gray-500">Credit</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
