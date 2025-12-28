@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Add Employee Salary Configuration</h1>
        <a href="{{ route('admin.finance.payroll.salaries') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6">
        <form action="{{ route('admin.finance.payroll.store-salary') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Employee *</label>
                    <select name="user_id" class="w-full border rounded-lg px-3 py-2 @error('user_id') border-red-500 @enderror" required>
                        <option value="">Select Employee</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Basic Salary *</label>
                    <input type="number" step="0.01" name="basic_salary" value="{{ old('basic_salary') }}" class="w-full border rounded-lg px-3 py-2 @error('basic_salary') border-red-500 @enderror" required>
                    @error('basic_salary')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method *</label>
                    <select name="payment_method" class="w-full border rounded-lg px-3 py-2" required>
                        <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="mobile_money" {{ old('payment_method') == 'mobile_money' ? 'selected' : '' }}>Mobile Money</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Allowances</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Housing Allowance</label>
                    <input type="number" step="0.01" name="housing_allowance" value="{{ old('housing_allowance', 0) }}" class="w-full border rounded-lg px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Transport Allowance</label>
                    <input type="number" step="0.01" name="transport_allowance" value="{{ old('transport_allowance', 0) }}" class="w-full border rounded-lg px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Medical Allowance</label>
                    <input type="number" step="0.01" name="medical_allowance" value="{{ old('medical_allowance', 0) }}" class="w-full border rounded-lg px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Other Allowances</label>
                    <input type="number" step="0.01" name="other_allowances" value="{{ old('other_allowances', 0) }}" class="w-full border rounded-lg px-3 py-2">
                </div>

                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Deductions</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tax Deduction</label>
                    <input type="number" step="0.01" name="tax_deduction" value="{{ old('tax_deduction', 0) }}" class="w-full border rounded-lg px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pension Deduction</label>
                    <input type="number" step="0.01" name="pension_deduction" value="{{ old('pension_deduction', 0) }}" class="w-full border rounded-lg px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Other Deductions</label>
                    <input type="number" step="0.01" name="other_deductions" value="{{ old('other_deductions', 0) }}" class="w-full border rounded-lg px-3 py-2">
                </div>

                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Bank Details</h3>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name') }}" class="w-full border rounded-lg px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bank Account Number</label>
                    <input type="text" name="bank_account" value="{{ old('bank_account') }}" class="w-full border rounded-lg px-3 py-2">
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Save Salary Configuration
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
