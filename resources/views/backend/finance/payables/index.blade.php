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
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Payables</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($totalPayables, 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice-dollar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Overdue Invoices</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $overdueInvoices }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Recent Invoices</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $recentInvoices->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Recent Payments</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $recentPayments->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-check-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt mr-2"></i>Quick Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3 mb-md-0">
                            <a href="{{ route('finance.payables.invoices') }}" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-file-invoice mr-2"></i>View All Invoices
                            </a>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <a href="{{ route('finance.payables.aging') }}" class="btn btn-outline-warning btn-block">
                                <i class="fas fa-chart-bar mr-2"></i>Aging Report
                            </a>
                        </div>
                        <div class="col-md-3 mb-3 mb-md-0">
                            <a href="{{ route('finance.payables.invoices.create') }}" class="btn btn-outline-success btn-block">
                                <i class="fas fa-plus mr-2"></i>New Invoice
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('finance.reports.trial-balance') }}" class="btn btn-outline-info btn-block">
                                <i class="fas fa-balance-scale mr-2"></i>Trial Balance
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Invoices -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-invoice mr-2"></i>Recent Invoices
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Invoice #</th>
                                    <th>Supplier</th>
                                    <th class="text-right">Amount</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentInvoices as $invoice)
                                    <tr>
                                        <td>
                                            <a href="{{ route('finance.payables.invoices.show', $invoice->id) }}" class="text-primary">
                                                {{ $invoice->invoice_number }}
                                            </a>
                                        </td>
                                        <td><small>{{ $invoice->supplier->name ?? 'N/A' }}</small></td>
                                        <td class="text-right font-weight-bold">${{ number_format($invoice->amount, 2) }}</td>
                                        <td>
                                            @if($invoice->status == 'unpaid')
                                                <span class="badge badge-danger badge-sm">Unpaid</span>
                                            @elseif($invoice->status == 'partial')
                                                <span class="badge badge-warning badge-sm">Partial</span>
                                            @else
                                                <span class="badge badge-success badge-sm">Paid</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">
                                            <small>No recent invoices</small>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-money-check-alt mr-2"></i>Recent Payments
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Payment #</th>
                                    <th>Supplier</th>
                                    <th>Date</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPayments as $payment)
                                    <tr>
                                        <td>
                                            <small class="text-primary font-weight-bold">{{ $payment->payment_number }}</small>
                                        </td>
                                        <td><small>{{ $payment->invoice->supplier->name ?? 'N/A' }}</small></td>
                                        <td><small class="text-muted">{{ $payment->payment_date->format('M d') }}</small></td>
                                        <td class="text-right font-weight-bold text-success">${{ number_format($payment->amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-3">
                                            <small>No recent payments</small>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
