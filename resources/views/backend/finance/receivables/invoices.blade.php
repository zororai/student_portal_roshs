@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">
                        <i class="fas fa-file-invoice text-primary mr-2"></i>Student Invoices
                    </h1>
                    <p class="text-muted mb-0">View and manage all student invoices</p>
                </div>
                <a href="{{ route('finance.receivables.invoices.create') }}" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus-circle mr-2"></i>Create Invoice
                </a>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter mr-2"></i>Filter Invoices
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('finance.receivables.invoices') }}" class="row align-items-end">
                <div class="col-md-3 mb-3 mb-md-0">
                    <label for="status" class="small font-weight-bold text-gray-700">Status</label>
                    <select name="status" id="status" class="form-control" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                        <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3 mb-md-0">
                    <label for="search" class="small font-weight-bold text-gray-700">Search</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Search by invoice # or student name..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search mr-2"></i>Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table mr-2"></i>All Invoices
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th><i class="fas fa-hashtag mr-1"></i>Invoice #</th>
                            <th><i class="fas fa-user mr-1"></i>Student</th>
                            <th><i class="fas fa-book mr-1"></i>Term/Year</th>
                            <th><i class="fas fa-calendar mr-1"></i>Date</th>
                            <th><i class="fas fa-calendar-check mr-1"></i>Due Date</th>
                            <th class="text-right"><i class="fas fa-dollar-sign mr-1"></i>Amount</th>
                            <th class="text-right"><i class="fas fa-money-check mr-1"></i>Paid</th>
                            <th class="text-right"><i class="fas fa-balance-scale mr-1"></i>Balance</th>
                            <th><i class="fas fa-info-circle mr-1"></i>Status</th>
                            <th class="text-center"><i class="fas fa-cog mr-1"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td>
                                    <a href="{{ route('finance.receivables.invoices.show', $invoice->id) }}" class="font-weight-bold text-primary">
                                        {{ $invoice->invoice_number }}
                                    </a>
                                </td>
                                <td>
                                    <span class="text-gray-800">{{ $invoice->student->name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $invoice->term }} {{ $invoice->year }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="far fa-clock mr-1"></i>{{ $invoice->invoice_date->format('M d, Y') }}
                                    </small>
                                </td>
                                <td>
                                    <small class="{{ $invoice->isOverdue() ? 'text-danger font-weight-bold' : 'text-muted' }}">
                                        <i class="far fa-calendar mr-1"></i>{{ $invoice->due_date->format('M d, Y') }}
                                        @if($invoice->isOverdue())
                                            <br><span class="badge badge-danger badge-sm">{{ $invoice->getDaysOverdue() }} days overdue</span>
                                        @endif
                                    </small>
                                </td>
                                <td class="text-right font-weight-bold">
                                    ${{ number_format($invoice->amount, 2) }}
                                </td>
                                <td class="text-right text-success">
                                    ${{ number_format($invoice->paid_amount, 2) }}
                                </td>
                                <td class="text-right font-weight-bold text-danger">
                                    ${{ number_format($invoice->getOutstandingAmount(), 2) }}
                                </td>
                                <td>
                                    @if($invoice->status == 'unpaid')
                                        <span class="badge badge-danger badge-pill">
                                            <i class="fas fa-times mr-1"></i>Unpaid
                                        </span>
                                    @elseif($invoice->status == 'partial')
                                        <span class="badge badge-warning badge-pill">
                                            <i class="fas fa-adjust mr-1"></i>Partial
                                        </span>
                                    @else
                                        <span class="badge badge-success badge-pill">
                                            <i class="fas fa-check-double mr-1"></i>Paid
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('finance.receivables.invoices.show', $invoice->id) }}" 
                                       class="btn btn-sm btn-info shadow-sm" 
                                       title="View Details"
                                       data-toggle="tooltip">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('finance.receivables.statement', $invoice->student_id) }}" 
                                       class="btn btn-sm btn-primary shadow-sm" 
                                       title="Student Statement"
                                       data-toggle="tooltip">
                                        <i class="fas fa-file-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5">
                                    <div class="text-gray-500">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p class="mb-0">No invoices found.</p>
                                        <a href="{{ route('finance.receivables.invoices.create') }}" class="btn btn-primary btn-sm mt-3">
                                            <i class="fas fa-plus mr-2"></i>Create Invoice
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($invoices->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $invoices->firstItem() }} to {{ $invoices->lastItem() }} of {{ $invoices->total() }} invoices
                    </div>
                    <div>
                        {{ $invoices->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        animation: fadeIn 0.5s ease-out;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.25);
    }
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-bottom: none;
        border-radius: 1rem 1rem 0 0 !important;
        padding: 1.25rem 1.5rem;
    }
    .card-header h6 {
        color: white !important;
        margin: 0;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    .table thead th {
        background: linear-gradient(135deg, #f8f9fc 0%, #e9ecef 100%);
        border: none;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.7rem;
        letter-spacing: 1px;
        padding: 1rem;
        color: #5a5c69;
    }
    .table tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid #f1f3f5;
    }
    .table tbody tr:hover {
        background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
        transform: scale(1.01);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .table tbody td {
        padding: 1.25rem 1rem;
        vertical-align: middle;
        border: none;
    }
    .badge-pill {
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        border-radius: 50px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .badge-danger {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
    }
    .badge-warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    .badge-success {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }
    .btn {
        border-radius: 0.5rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        border: none;
    }
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
    }
    .btn-sm {
        padding: 0.4rem 0.8rem;
        font-size: 0.75rem;
        border-radius: 0.4rem;
    }
    .btn-info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    .form-control {
        border-radius: 0.5rem;
        border: 2px solid #e3e6f0;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush

@push('scripts')
<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    });
</script>
@endpush
