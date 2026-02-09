@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Journal Entry Details</h2>
                <div>
                    @if($batch->status == 'draft')
                        <a href="{{ route('finance.journals.edit', $batch->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('finance.journals.approve', $batch->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary" onclick="return confirm('Approve this journal batch?')">
                                <i class="fas fa-check"></i> Approve
                            </button>
                        </form>
                    @elseif($batch->status == 'approved')
                        <form action="{{ route('finance.journals.post', $batch->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success" onclick="return confirm('Post this journal to the ledger? This action cannot be undone.')">
                                <i class="fas fa-paper-plane"></i> Post to Ledger
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('finance.journals.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
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

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Batch Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Reference:</th>
                            <td>{{ $batch->reference }}</td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td>{{ $batch->description }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($batch->status == 'draft')
                                    <span class="badge badge-secondary">Draft</span>
                                @elseif($batch->status == 'approved')
                                    <span class="badge badge-warning">Approved</span>
                                @else
                                    <span class="badge badge-success">Posted</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created By:</th>
                            <td>{{ $batch->creator->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td>{{ $batch->created_at->format('Y-m-d H:i:s') }}</td>
                        </tr>
                        @if($batch->approved_by)
                        <tr>
                            <th>Approved By:</th>
                            <td>{{ $batch->approver->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Approved At:</th>
                            <td>{{ $batch->approved_at ? $batch->approved_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                        </tr>
                        @endif
                        @if($batch->posted_by)
                        <tr>
                            <th>Posted By:</th>
                            <td>{{ $batch->poster->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Posted At:</th>
                            <td>{{ $batch->posted_at ? $batch->posted_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Summary</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th>Total Debit:</th>
                            <td class="text-right">${{ number_format($batch->total_debit, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Total Credit:</th>
                            <td class="text-right">${{ number_format($batch->total_credit, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Difference:</th>
                            <td class="text-right">
                                @php
                                    $diff = $batch->total_debit - $batch->total_credit;
                                @endphp
                                <span class="{{ abs($diff) < 0.01 ? 'text-success' : 'text-danger' }}">
                                    ${{ number_format(abs($diff), 2) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Balanced:</th>
                            <td class="text-right">
                                @if($batch->isBalanced())
                                    <span class="badge badge-success">Yes ✓</span>
                                @else
                                    <span class="badge badge-danger">No</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-header">
            <h5 class="mb-0">Journal Entries ({{ $batch->entries->count() }})</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Account Code</th>
                            <th>Account Name</th>
                            <th>Debit</th>
                            <th>Credit</th>
                            <th>Narration</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($batch->entries as $entry)
                            <tr>
                                <td>{{ $entry->ledgerAccount->account_code }}</td>
                                <td>{{ $entry->ledgerAccount->account_name }}</td>
                                <td class="text-right">
                                    @if($entry->debit_amount > 0)
                                        ${{ number_format($entry->debit_amount, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-right">
                                    @if($entry->credit_amount > 0)
                                        ${{ number_format($entry->credit_amount, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $entry->narration }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="font-weight-bold">
                        <tr>
                            <td colspan="2" class="text-right">Totals:</td>
                            <td class="text-right">${{ number_format($batch->total_debit, 2) }}</td>
                            <td class="text-right">${{ number_format($batch->total_credit, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
