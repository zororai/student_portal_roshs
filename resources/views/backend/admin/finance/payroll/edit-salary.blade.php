@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Salary - {{ $salary->user->name }}</h1>
        <a href="{{ route('admin.finance.payroll.salaries') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Back</a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6">
        <form action="{{ route('admin.finance.payroll.update-salary', $salary->id) }}" method="POST">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Basic Salary *</label>
                    <input type="number" step="0.01" name="basic_salary" value="{{ old('basic_salary', $salary->basic_salary) }}" class="w-full border rounded-lg px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method *</label>
                    <select name="payment_method" class="w-full border rounded-lg px-3 py-2" required>
                        <option value="bank_transfer" {{ $salary->payment_method == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="cash" {{ $salary->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="mobile_money" {{ $salary->payment_method == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                    </select>
                </div>
                <div class="md:col-span-2"><h3 class="text-lg font-semibold border-b pb-2">Allowances</h3></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Housing</label><input type="number" step="0.01" name="housing_allowance" value="{{ old('housing_allowance', $salary->housing_allowance) }}" class="w-full border rounded-lg px-3 py-2"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Transport</label><input type="number" step="0.01" name="transport_allowance" value="{{ old('transport_allowance', $salary->transport_allowance) }}" class="w-full border rounded-lg px-3 py-2"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Medical</label><input type="number" step="0.01" name="medical_allowance" value="{{ old('medical_allowance', $salary->medical_allowance) }}" class="w-full border rounded-lg px-3 py-2"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Other</label><input type="number" step="0.01" name="other_allowances" value="{{ old('other_allowances', $salary->other_allowances) }}" class="w-full border rounded-lg px-3 py-2"></div>
                <div class="md:col-span-2"><h3 class="text-lg font-semibold border-b pb-2">Deductions</h3></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Tax</label><input type="number" step="0.01" name="tax_deduction" value="{{ old('tax_deduction', $salary->tax_deduction) }}" class="w-full border rounded-lg px-3 py-2"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Pension</label><input type="number" step="0.01" name="pension_deduction" value="{{ old('pension_deduction', $salary->pension_deduction) }}" class="w-full border rounded-lg px-3 py-2"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Other</label><input type="number" step="0.01" name="other_deductions" value="{{ old('other_deductions', $salary->other_deductions) }}" class="w-full border rounded-lg px-3 py-2"></div>
                <div class="md:col-span-2"><h3 class="text-lg font-semibold border-b pb-2">Bank Details</h3></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label><input type="text" name="bank_name" value="{{ old('bank_name', $salary->bank_name) }}" class="w-full border rounded-lg px-3 py-2"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Account Number</label><input type="text" name="bank_account" value="{{ old('bank_account', $salary->bank_account) }}" class="w-full border rounded-lg px-3 py-2"></div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
