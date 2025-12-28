@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Payroll Details</h1>
        <a href="{{ route('admin.finance.payroll.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Back</a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6">
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div><span class="text-gray-500">Employee:</span> <strong>{{ $payroll->user->name }}</strong></div>
            <div><span class="text-gray-500">Period:</span> <strong>{{ $payroll->pay_period }}</strong></div>
            <div><span class="text-gray-500">Pay Date:</span> <strong>{{ $payroll->pay_date->format('d M Y') }}</strong></div>
            <div><span class="text-gray-500">Status:</span> {!! $payroll->status_badge !!}</div>
        </div>
        <div class="border-t pt-4">
            <div class="grid grid-cols-3 gap-4">
                <div class="text-center p-4 bg-gray-50 rounded"><div class="text-sm text-gray-500">Gross Salary</div><div class="text-xl font-bold">${{ number_format($payroll->gross_salary, 2) }}</div></div>
                <div class="text-center p-4 bg-red-50 rounded"><div class="text-sm text-gray-500">Deductions</div><div class="text-xl font-bold text-red-600">-${{ number_format($payroll->total_deductions, 2) }}</div></div>
                <div class="text-center p-4 bg-green-50 rounded"><div class="text-sm text-gray-500">Net Salary</div><div class="text-xl font-bold text-green-600">${{ number_format($payroll->net_salary, 2) }}</div></div>
            </div>
        </div>
        @if($payroll->isPending())
        <div class="mt-6 flex space-x-2">
            <form action="{{ route('admin.finance.payroll.approve', $payroll->id) }}" method="POST">@csrf<button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Approve</button></form>
        </div>
        @endif
        @if($payroll->isApproved())
        <div class="mt-6">
            <form action="{{ route('admin.finance.payroll.mark-paid', $payroll->id) }}" method="POST">@csrf<button class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">Mark as Paid</button></form>
        </div>
        @endif
    </div>
</div>
@endsection
