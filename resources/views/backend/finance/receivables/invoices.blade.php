@extends('layouts.app')

@section('content')
<!-- Page Header -->
<div class="flex justify-between items-center mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Student Invoices</h1>
        <p class="text-gray-500 text-sm">View and manage all student invoices</p>
    </div>
    <a href="{{ route('finance.receivables.invoices.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow-md transition duration-200">
        + Create Invoice
    </a>
</div>

<!-- Filters Card -->
<div class="bg-white shadow-md rounded-xl p-6 mb-6">
    <h3 class="font-semibold text-gray-800 mb-4">Filter Invoices</h3>
    <form method="GET" action="{{ route('finance.receivables.invoices') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4">
        <div class="md:col-span-3">
            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
            <select name="status" id="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
            </select>
        </div>
        <div class="md:col-span-6">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input type="text" name="search" id="search" value="{{ request('search') }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"
                   placeholder="Search by invoice # or student name...">
        </div>
        <div class="md:col-span-3 flex items-end">
            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg shadow-md transition duration-200">
                Search
            </button>
        </div>
    </form>
</div>

<!-- Invoices Table -->
<div class="bg-white shadow-md rounded-xl overflow-hidden">
    <div class="px-6 py-4 border-b">
        <h3 class="font-semibold text-gray-800">All Invoices</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Term/Year</th>
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
                @forelse($invoices as $invoice)
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
                            {{ $invoice->term }} {{ $invoice->year }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $invoice->invoice_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm {{ $invoice->isOverdue() ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                {{ $invoice->due_date->format('M d, Y') }}
                                @if($invoice->isOverdue())
                                    <br><span class="text-xs bg-red-100 text-red-800 px-2 py-0.5 rounded-full">{{ $invoice->getDaysOverdue() }} days overdue</span>
                                @endif
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
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('finance.receivables.invoices.show', $invoice->id) }}" 
                                   class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition duration-150">
                                    View
                                </a>
                                <a href="{{ route('finance.receivables.statement', $invoice->student_id) }}" 
                                   class="inline-flex items-center px-3 py-1 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition duration-150">
                                    Statement
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-12 h-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-lg font-medium mb-2">No invoices found</p>
                                <a href="{{ route('finance.receivables.invoices.create') }}" class="mt-3 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition duration-200">
                                    Create Invoice
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($invoices->hasPages())
        <div class="bg-white px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing <span class="font-medium">{{ $invoices->firstItem() }}</span> to <span class="font-medium">{{ $invoices->lastItem() }}</span> of <span class="font-medium">{{ $invoices->total() }}</span> invoices
                </div>
                <div>
                    {{ $invoices->links() }}
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

