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

    <!-- Student Fees Summary -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Outstanding Student Fees (Active Students)</h3>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            <div class="text-center p-2 bg-orange-50 rounded">
                <p class="text-xs text-gray-500">Balance B/F</p>
                <p class="text-lg font-bold text-orange-600">${{ number_format($totalBalanceBf ?? 0, 2) }}</p>
            </div>
            <div class="text-center p-2 bg-blue-50 rounded">
                <p class="text-xs text-gray-500">Current Term</p>
                <p class="text-lg font-bold text-blue-600">${{ number_format($totalCurrentTermFees ?? 0, 2) }}</p>
            </div>
            <div class="text-center p-2 bg-gray-50 rounded">
                <p class="text-xs text-gray-500">Total Fees</p>
                <p class="text-lg font-bold text-gray-800">${{ number_format($totalStudentFees ?? 0, 2) }}</p>
            </div>
            <div class="text-center p-2 bg-green-50 rounded">
                <p class="text-xs text-gray-500">Paid</p>
                <p class="text-lg font-bold text-green-600">${{ number_format($totalStudentPayments ?? 0, 2) }}</p>
            </div>
            <div class="text-center p-2 bg-red-50 rounded">
                <p class="text-xs text-gray-500">Outstanding</p>
                <p class="text-lg font-bold text-red-600">${{ number_format($totalOutstandingFees ?? 0, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Graduated/Transferred Students Summary -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">
            Graduated/Transferred Students 
            <span class="text-xs font-normal text-gray-500">({{ $graduatedStudentsCount ?? 0 }} students)</span>
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
            <div class="text-center p-2 bg-purple-50 rounded">
                <p class="text-xs text-gray-500">Total Students</p>
                <p class="text-lg font-bold text-purple-600">{{ $graduatedStudentsCount ?? 0 }}</p>
            </div>
            <div class="text-center p-2 bg-gray-50 rounded">
                <p class="text-xs text-gray-500">Total Fees</p>
                <p class="text-lg font-bold text-gray-800">${{ number_format($graduatedTotalFees ?? 0, 2) }}</p>
            </div>
            <div class="text-center p-2 bg-green-50 rounded">
                <p class="text-xs text-gray-500">Total Paid</p>
                <p class="text-lg font-bold text-green-600">${{ number_format($graduatedTotalPayments ?? 0, 2) }}</p>
            </div>
            <div class="text-center p-2 {{ ($graduatedOutstanding ?? 0) > 0 ? 'bg-red-50' : 'bg-blue-50' }} rounded">
                <p class="text-xs text-gray-500">Balance</p>
                <p class="text-lg font-bold {{ ($graduatedOutstanding ?? 0) > 0 ? 'text-red-600' : 'text-blue-600' }}">
                    @if(($graduatedOutstanding ?? 0) > 0)
                        ${{ number_format($graduatedOutstanding, 2) }} (Debit)
                    @elseif(($graduatedOutstanding ?? 0) < 0)
                        ${{ number_format(abs($graduatedOutstanding), 2) }} (Credit)
                    @else
                        $0.00
                    @endif
                </p>
            </div>
            <div class="text-center p-2 bg-yellow-50 rounded">
                <p class="text-xs text-gray-500">With Debt / Credit</p>
                <p class="text-sm font-bold text-yellow-700">
                    <span class="text-red-600">{{ $graduatedStudentsWithDebt ?? 0 }}</span> / 
                    <span class="text-blue-600">{{ $graduatedStudentsWithCredit ?? 0 }}</span>
                </p>
            </div>
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
