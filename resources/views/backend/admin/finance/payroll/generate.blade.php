@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Generate Payroll</h1>
        <a href="{{ route('admin.finance.payroll.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6">
        <form action="{{ route('admin.finance.payroll.process-generate') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pay Period *</label>
                    <input type="month" name="pay_period" value="{{ $currentPeriod }}" class="w-full border rounded-lg px-3 py-2" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pay Date *</label>
                    <input type="date" name="pay_date" value="{{ date('Y-m-d') }}" class="w-full border rounded-lg px-3 py-2" required>
                </div>
            </div>

            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Select Employees</h3>
                <div class="flex items-center mb-3">
                    <input type="checkbox" id="selectAll" class="mr-2">
                    <label for="selectAll" class="text-sm font-medium text-gray-700">Select All</label>
                </div>
                
                <div class="border rounded-lg overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase w-12"></th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Basic</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Allowances</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deductions</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Net</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($salaries as $salary)
                            <tr>
                                <td class="px-4 py-3">
                                    <input type="checkbox" name="employee_ids[]" value="{{ $salary->id }}" class="employee-checkbox">
                                </td>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $salary->user->name }}</div>
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">${{ number_format($salary->basic_salary, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-green-600">+${{ number_format($salary->total_allowances, 2) }}</td>
                                <td class="px-4 py-3 text-sm text-red-600">-${{ number_format($salary->total_deductions, 2) }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">${{ number_format($salary->net_salary, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-4 text-center text-gray-500">
                                    No salary configurations found. <a href="{{ route('admin.finance.payroll.create-salary') }}" class="text-blue-600">Add one</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($salaries->count() > 0)
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Generate Payroll
                </button>
            </div>
            @endif
        </form>
    </div>
</div>

<script>
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.employee-checkbox').forEach(cb => cb.checked = this.checked);
});
</script>
@endsection
