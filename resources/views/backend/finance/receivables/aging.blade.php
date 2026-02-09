@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1 text-gray-800">
                        <i class="fas fa-chart-bar text-primary mr-2"></i>Accounts Receivable Aging Report
                    </h1>
                    <p class="text-muted mb-0">Track outstanding balances by aging period</p>
                </div>
                <div>
                    <button onclick="window.print()" class="btn btn-secondary shadow-sm mr-2">
                        <i class="fas fa-print mr-2"></i>Print
                    </button>
                    <a href="{{ route('finance.receivables.index') }}" class="btn btn-outline-primary shadow-sm">
                        <i class="fas fa-arrow-left mr-2"></i>Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Current (0-30)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($totals['current'], 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">31-60 Days</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($totals['30_days'], 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-half fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">61-90 Days</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($totals['60_days'], 2) }}
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
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">90+ Days</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                ${{ number_format($totals['90_plus_days'], 2) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hourglass-end fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Aging Report Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table mr-2"></i>Aging Details - As of {{ $asOfDate }}
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th><i class="fas fa-user mr-1"></i>Student</th>
                            <th class="text-right"><i class="fas fa-clock mr-1"></i>Current</th>
                            <th class="text-right"><i class="fas fa-hourglass-half mr-1"></i>31-60 Days</th>
                            <th class="text-right"><i class="fas fa-exclamation-triangle mr-1"></i>61-90 Days</th>
                            <th class="text-right"><i class="fas fa-hourglass-end mr-1"></i>90+ Days</th>
                            <th class="text-right"><i class="fas fa-dollar-sign mr-1"></i>Total</th>
                            <th class="text-center"><i class="fas fa-cog mr-1"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agingData as $data)
                            <tr>
                                <td>
                                    <span class="font-weight-bold text-gray-800">{{ $data['student']->name }}</span>
                                    <br>
                                    <small class="text-muted">{{ $data['student']->student_id }}</small>
                                </td>
                                <td class="text-right">
                                    @if($data['current'] > 0)
                                        <span class="text-info font-weight-bold">${{ number_format($data['current'], 2) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($data['30_days'] > 0)
                                        <span class="text-warning font-weight-bold">${{ number_format($data['30_days'], 2) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($data['60_days'] > 0)
                                        <span class="text-danger font-weight-bold">${{ number_format($data['60_days'], 2) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($data['90_plus_days'] > 0)
                                        <span class="text-danger font-weight-bold">${{ number_format($data['90_plus_days'], 2) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <span class="font-weight-bold text-primary">${{ number_format($data['total'], 2) }}</span>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('finance.receivables.statement', $data['student']->id) }}" 
                                       class="btn btn-sm btn-info shadow-sm" 
                                       title="View Statement"
                                       data-toggle="tooltip">
                                        <i class="fas fa-file-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="text-gray-500">
                                        <i class="fas fa-check-circle fa-3x mb-3 text-success"></i>
                                        <p class="mb-0">No outstanding receivables!</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(count($agingData) > 0)
                        <tfoot class="font-weight-bold bg-light">
                            <tr>
                                <td>TOTAL</td>
                                <td class="text-right text-info">${{ number_format($totals['current'], 2) }}</td>
                                <td class="text-right text-warning">${{ number_format($totals['30_days'], 2) }}</td>
                                <td class="text-right text-danger">${{ number_format($totals['60_days'], 2) }}</td>
                                <td class="text-right text-danger">${{ number_format($totals['90_plus_days'], 2) }}</td>
                                <td class="text-right text-primary">${{ number_format($totals['total'], 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
        background: linear-gradient(135deg, #e6f7ff 0%, #ffffff 100%);
    }
    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
        background: linear-gradient(135deg, #fff5e6 0%, #ffffff 100%);
    }
    .border-left-danger {
        border-left: 0.25rem solid #e74a3b !important;
        background: linear-gradient(135deg, #ffe6e6 0%, #ffffff 100%);
    }
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
    .btn {
        border-radius: 0.5rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        border: none;
    }
    .btn-sm {
        padding: 0.4rem 0.8rem;
        font-size: 0.75rem;
        border-radius: 0.4rem;
    }
    .btn-info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    .text-xs {
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 1px;
    }
    .text-gray-300 {
        opacity: 0.3;
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
    @media print {
        .btn, .card-header {
            display: none !important;
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
