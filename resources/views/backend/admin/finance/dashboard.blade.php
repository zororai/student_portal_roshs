@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Finance Dashboard</h1>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Monthly Income</div>
            <div class="text-2xl font-bold text-green-600">${{ number_format($monthlyIncome, 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Monthly Expenses</div>
            <div class="text-2xl font-bold text-red-600">${{ number_format($monthlyExpenses, 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Current Balance</div>
            <div class="text-2xl font-bold text-blue-600">${{ number_format($currentBalance, 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500">Pending Payroll ({{ $pendingPayrollCount }})</div>
            <div class="text-2xl font-bold text-yellow-600">${{ number_format($pendingPayroll, 2) }}</div>
        </div>
    </div>

    <!-- Yearly Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow p-6 text-white">
            <div class="text-sm opacity-80">Year-to-Date Income</div>
            <div class="text-3xl font-bold">${{ number_format($yearlyIncome, 2) }}</div>
        </div>
        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg shadow p-6 text-white">
            <div class="text-sm opacity-80">Year-to-Date Expenses</div>
            <div class="text-3xl font-bold">${{ number_format($yearlyExpenses, 2) }}</div>
        </div>
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow p-6 text-white">
            <div class="text-sm opacity-80">Net Income (YTD)</div>
            <div class="text-3xl font-bold">${{ number_format($yearlyIncome - $yearlyExpenses, 2) }}</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Income vs Expenses Chart -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Income vs Expenses (Last 6 Months)</h3>
            <canvas id="incomeExpenseChart" height="200"></canvas>
        </div>

        <!-- Expense by Category -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold mb-4">Expenses by Category (This Month)</h3>
            @if($expenseByCategory->count() > 0)
            <canvas id="expenseCategoryChart" height="200"></canvas>
            @else
            <p class="text-gray-500 text-center py-8">No expenses recorded this month.</p>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Transactions -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b"><h3 class="font-semibold">Recent Transactions</h3></div>
            <div class="divide-y">
                @forelse($recentTransactions as $tx)
                <div class="px-6 py-3 flex justify-between items-center">
                    <div>
                        <div class="text-sm font-medium">{{ $tx->description }}</div>
                        <div class="text-xs text-gray-500">{{ $tx->entry_date->format('d M Y') }}</div>
                    </div>
                    <div class="{{ $tx->transaction_type == 'receipt' ? 'text-green-600' : 'text-red-600' }} font-semibold">
                        {{ $tx->transaction_type == 'receipt' ? '+' : '-' }}${{ number_format($tx->amount, 2) }}
                    </div>
                </div>
                @empty
                <div class="px-6 py-4 text-gray-500 text-center">No transactions yet.</div>
                @endforelse
            </div>
        </div>

        <!-- Quick Links -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-semibold mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('admin.finance.cashbook.create') }}" class="bg-blue-50 hover:bg-blue-100 rounded-lg p-4 text-center">
                    <div class="text-blue-600 font-medium">New Transaction</div>
                </a>
                <a href="{{ route('admin.finance.expenses.create') }}" class="bg-red-50 hover:bg-red-100 rounded-lg p-4 text-center">
                    <div class="text-red-600 font-medium">Record Expense</div>
                </a>
                <a href="{{ route('admin.finance.payroll.generate') }}" class="bg-green-50 hover:bg-green-100 rounded-lg p-4 text-center">
                    <div class="text-green-600 font-medium">Generate Payroll</div>
                </a>
                <a href="{{ route('admin.finance.reports.income-statement') }}" class="bg-purple-50 hover:bg-purple-100 rounded-lg p-4 text-center">
                    <div class="text-purple-600 font-medium">View Reports</div>
                </a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('incomeExpenseChart'), {
    type: 'bar',
    data: {
        labels: {!! json_encode($chartData['labels']) !!},
        datasets: [{
            label: 'Income',
            data: {!! json_encode($chartData['income']) !!},
            backgroundColor: 'rgba(34, 197, 94, 0.8)',
        }, {
            label: 'Expenses',
            data: {!! json_encode($chartData['expenses']) !!},
            backgroundColor: 'rgba(239, 68, 68, 0.8)',
        }]
    },
    options: { responsive: true, scales: { y: { beginAtZero: true } } }
});

@if($expenseByCategory->count() > 0)
new Chart(document.getElementById('expenseCategoryChart'), {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($expenseByCategory->pluck('category.name')) !!},
        datasets: [{
            data: {!! json_encode($expenseByCategory->pluck('total')) !!},
            backgroundColor: ['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899'],
        }]
    },
    options: { responsive: true }
});
@endif
</script>
@endsection
