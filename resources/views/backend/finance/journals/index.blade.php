@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">
                        <i class="fas fa-book text-primary mr-2"></i>General Journal
                    </h1>
                    <p class="text-muted mb-0">Manage journal entries and post to ledger</p>
                </div>
                <a href="{{ route('finance.journals.create') }}" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus-circle mr-2"></i>Create Journal Entry
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Draft</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $batches->where('status', 'draft')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-edit fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Approved</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $batches->where('status', 'approved')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Posted</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $batches->where('status', 'posted')->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-paper-plane fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Entries</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $batches->total() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Filters Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter mr-2"></i>Filter Journal Entries
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('finance.journals.index') }}" class="row align-items-end">
                <div class="col-md-3 mb-3 mb-md-0">
                    <label for="status" class="small font-weight-bold text-gray-700">Status</label>
                    <select name="status" id="status" class="form-control" onchange="this.form.submit()">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="posted" {{ request('status') == 'posted' ? 'selected' : '' }}>Posted</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3 mb-md-0">
                    <label for="search" class="small font-weight-bold text-gray-700">Search</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Search by reference or description..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search mr-2"></i>Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Journal Entries Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table mr-2"></i>Journal Entries
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th><i class="fas fa-hashtag mr-1"></i>Reference</th>
                            <th><i class="fas fa-align-left mr-1"></i>Description</th>
                            <th><i class="fas fa-calendar mr-1"></i>Date</th>
                            <th class="text-right"><i class="fas fa-arrow-up mr-1"></i>Debit</th>
                            <th class="text-right"><i class="fas fa-arrow-down mr-1"></i>Credit</th>
                            <th><i class="fas fa-info-circle mr-1"></i>Status</th>
                            <th><i class="fas fa-user mr-1"></i>Created By</th>
                            <th class="text-center"><i class="fas fa-cog mr-1"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($batches as $batch)
                            <tr>
                                <td>
                                    <a href="{{ route('finance.journals.show', $batch->id) }}" class="font-weight-bold text-primary">
                                        {{ $batch->reference }}
                                    </a>
                                </td>
                                <td>
                                    <span class="text-gray-800">{{ Str::limit($batch->description, 60) }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <i class="far fa-clock mr-1"></i>{{ $batch->created_at->format('M d, Y') }}
                                    </small>
                                </td>
                                <td class="text-right font-weight-bold text-success">
                                    ${{ number_format($batch->total_debit, 2) }}
                                </td>
                                <td class="text-right font-weight-bold text-danger">
                                    ${{ number_format($batch->total_credit, 2) }}
                                </td>
                                <td>
                                    @if($batch->status == 'draft')
                                        <span class="badge badge-warning badge-pill">
                                            <i class="fas fa-edit mr-1"></i>Draft
                                        </span>
                                    @elseif($batch->status == 'approved')
                                        <span class="badge badge-info badge-pill">
                                            <i class="fas fa-check mr-1"></i>Approved
                                        </span>
                                    @else
                                        <span class="badge badge-success badge-pill">
                                            <i class="fas fa-check-double mr-1"></i>Posted
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-gray-700">
                                        <i class="fas fa-user-circle mr-1"></i>{{ $batch->creator->name ?? 'N/A' }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('finance.journals.show', $batch->id) }}" 
                                           class="btn btn-sm btn-info shadow-sm" 
                                           title="View Details"
                                           data-toggle="tooltip">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($batch->status == 'draft')
                                            <a href="{{ route('finance.journals.edit', $batch->id) }}" 
                                               class="btn btn-sm btn-warning shadow-sm" 
                                               title="Edit"
                                               data-toggle="tooltip">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('finance.journals.approve', $batch->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-sm btn-primary shadow-sm" 
                                                        title="Approve"
                                                        data-toggle="tooltip"
                                                        onclick="return confirm('Approve this journal batch?')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('finance.journals.destroy', $batch->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-danger shadow-sm" 
                                                        title="Delete"
                                                        data-toggle="tooltip"
                                                        onclick="return confirm('Delete this journal batch?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @elseif($batch->status == 'approved')
                                            <form action="{{ route('finance.journals.post', $batch->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-sm btn-success shadow-sm" 
                                                        title="Post to Ledger"
                                                        data-toggle="tooltip"
                                                        onclick="return confirm('Post this journal to the ledger? This action cannot be undone.')">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-gray-500">
                                        <i class="fas fa-inbox fa-3x mb-3"></i>
                                        <p class="mb-0">No journal entries found.</p>
                                        <a href="{{ route('finance.journals.create') }}" class="btn btn-primary btn-sm mt-3">
                                            <i class="fas fa-plus mr-2"></i>Create Your First Entry
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($batches->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        Showing {{ $batches->firstItem() }} to {{ $batches->lastItem() }} of {{ $batches->total() }} entries
                    </div>
                    <div>
                        {{ $batches->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Modern Color Palette */
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --warning-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --success-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }

    /* Stats Cards with Gradient */
    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
        background: linear-gradient(135deg, #fff5e6 0%, #ffffff 100%);
    }
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
        background: linear-gradient(135deg, #e6f7ff 0%, #ffffff 100%);
    }
    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
        background: linear-gradient(135deg, #e6fff9 0%, #ffffff 100%);
    }
    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
        background: linear-gradient(135deg, #e8eeff 0%, #ffffff 100%);
    }

    /* Modern Card Styling */
    .card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
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

    /* Modern Table */
    .table {
        border-collapse: separate;
        border-spacing: 0;
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

    /* Modern Badges */
    .badge-pill {
        padding: 0.5rem 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        border-radius: 50px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }

    .badge-warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border: none;
    }

    .badge-info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        border: none;
    }

    .badge-success {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        border: none;
    }

    /* Modern Buttons */
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

    .btn-warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .btn-success {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }

    .btn-danger {
        background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
    }

    /* Stats Card Icons */
    .text-gray-300 {
        opacity: 0.3;
    }

    .text-xs {
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 1px;
    }

    /* Form Controls */
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

    /* Page Header */
    .h3 {
        font-weight: 700;
        color: #2c3e50;
    }

    .text-muted {
        color: #7f8c8d !important;
    }

    /* Empty State */
    .text-gray-500 {
        color: #95a5a6;
    }

    .fa-inbox {
        color: #bdc3c7;
    }

    /* Animations */
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

    .card {
        animation: fadeIn 0.5s ease-out;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .card {
            margin-bottom: 1rem;
        }
        
        .btn-group {
            display: flex;
            flex-direction: column;
        }
        
        .btn-group .btn {
            margin: 2px 0;
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
