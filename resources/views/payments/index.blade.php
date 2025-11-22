@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Payment History</h2>

    <table class="table">
        <thead>
            <tr>
                <th>Student</th>
                <th>Fee Category</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
                <tr>
                    <td>{{ $payment->student->name }}</td>
                    <td>{{ $payment->feeCategory->name }}</td>
                    <td>${{ $payment->amount }}</td>
                    <td>{{ ucfirst($payment->status) }}</td>
                    <td>
                        <a href="{{ route('payments.receipt.download', $payment->id) }}" class="btn btn-info">Download Receipt</a>
                    </td>
                    <td>
                        <a href="{{ route('payments.receipt', $payment->id) }}" class="btn btn-info">Receipt</a>
                        
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection