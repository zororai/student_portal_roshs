@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6 print:hidden">
        <h1 class="text-2xl font-bold text-gray-800">Payslip</h1>
        <div class="flex space-x-2">
            <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Print</button>
            <a href="{{ route('admin.finance.payroll.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Back</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-8 max-w-2xl mx-auto">
        <div class="text-center mb-6 border-b pb-4">
            <h2 class="text-xl font-bold">PAYSLIP</h2>
            <p class="text-gray-600">{{ $payroll->pay_period }}</p>
        </div>
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div><span class="text-gray-500">Employee:</span> {{ $payroll->user->name }}</div>
            <div><span class="text-gray-500">Pay Date:</span> {{ $payroll->pay_date->format('d M Y') }}</div>
        </div>
        <table class="w-full mb-6">
            <tr class="border-b"><td class="py-2">Basic Salary</td><td class="text-right">${{ number_format($payroll->basic_salary, 2) }}</td></tr>
            <tr class="border-b"><td class="py-2">Allowances</td><td class="text-right text-green-600">+${{ number_format($payroll->total_allowances, 2) }}</td></tr>
            <tr class="border-b"><td class="py-2 font-medium">Gross Salary</td><td class="text-right font-medium">${{ number_format($payroll->gross_salary, 2) }}</td></tr>
            <tr class="border-b"><td class="py-2">Deductions</td><td class="text-right text-red-600">-${{ number_format($payroll->total_deductions, 2) }}</td></tr>
            <tr class="bg-gray-100"><td class="py-3 font-bold">Net Salary</td><td class="text-right font-bold text-lg">${{ number_format($payroll->net_salary, 2) }}</td></tr>
        </table>
        <div class="text-center text-sm text-gray-500 mt-8">Generated on {{ now()->format('d M Y H:i') }}</div>
    </div>
</div>
@endsection
