@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Payroll Management</h1>
        <div class="flex space-x-2">
            <a href="{{ route('admin.finance.payroll.salaries') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                <i class="fas fa-users-cog mr-2"></i>Salary Config
            </a>
            <a href="{{ route('admin.finance.payroll.generate') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                <i class="fas fa-plus mr-2"></i>Generate Payroll
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="text-yellow-600 text-sm font-medium">Pending</div>
            <div class="text-2xl font-bold text-yellow-800">${{ number_format($stats['total_pending'], 2) }}</div>
            <div class="text-yellow-600 text-xs">{{ $stats['pending_count'] }} records</div>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="text-blue-600 text-sm font-medium">Approved</div>
            <div class="text-2xl font-bold text-blue-800">${{ number_format($stats['total_approved'], 2) }}</div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="text-green-600 text-sm font-medium">Paid</div>
            <div class="text-2xl font-bold text-green-800">${{ number_format($stats['total_paid'], 2) }}</div>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
            <div class="text-purple-600 text-sm font-medium">Total Payroll</div>
            <div class="text-2xl font-bold text-purple-800">${{ number_format($stats['total_pending'] + $stats['total_approved'] + $stats['total_paid'], 2) }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pay Period</label>
                <select name="pay_period" class="border rounded-lg px-3 py-2">
                    <option value="">All Periods</option>
                    @foreach($payPeriods as $period)
                        <option value="{{ $period }}" {{ request('pay_period') == $period ? 'selected' : '' }}>{{ $period }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="border rounded-lg px-3 py-2">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Filter</button>
            </div>
        </form>
    </div>

    <!-- Payroll Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Period</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Gross</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deductions</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Net</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($payrolls as $payroll)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-medium text-gray-900">{{ $payroll->user->name }}</div>
                        <div class="text-sm text-gray-500">{{ $payroll->user->email }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $payroll->pay_period }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ number_format($payroll->gross_salary, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600">-${{ number_format($payroll->total_deductions, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${{ number_format($payroll->net_salary, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{!! $payroll->status_badge !!}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.finance.payroll.show', $payroll->id) }}" class="text-blue-600 hover:text-blue-800">View</a>
                            @if($payroll->isPending())
                                <form action="{{ route('admin.finance.payroll.approve', $payroll->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:text-green-800">Approve</button>
                                </form>
                            @endif
                            @if($payroll->isApproved())
                                <form action="{{ route('admin.finance.payroll.mark-paid', $payroll->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-purple-600 hover:text-purple-800">Mark Paid</button>
                                </form>
                            @endif
                            @if($payroll->isPaid())
                                <a href="{{ route('admin.finance.payroll.payslip', $payroll->id) }}" class="text-gray-600 hover:text-gray-800">Payslip</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">No payroll records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $payrolls->links() }}
    </div>
</div>
@endsection
