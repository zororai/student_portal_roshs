@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Financial Statements</h2>
        <div class="flex space-x-2">
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print
            </button>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form action="{{ route('finance.statements') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                <select name="year" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Years</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Term</label>
                <select name="term" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Terms</option>
                    @foreach($terms as $key => $label)
                        <option value="{{ $key }}" {{ $selectedTerm == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
                <a href="{{ route('finance.statements') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Reset</a>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Income</p>
                    <h3 class="text-2xl font-bold text-green-600">${{ number_format($totalIncome, 2) }}</h3>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total Expenses</p>
                    <h3 class="text-2xl font-bold text-red-600">${{ number_format($totalExpenses, 2) }}</h3>
                </div>
                <div class="bg-red-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Net Profit/Loss</p>
                    <h3 class="text-2xl font-bold {{ $netProfit >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        ${{ number_format($netProfit, 2) }}
                    </h3>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Fees Summary -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Outstanding Student Fees Summary</h3>
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
            <div class="text-center p-3 bg-orange-50 rounded-lg">
                <p class="text-xs text-gray-500 mb-1">Balance B/F</p>
                <p class="text-lg font-bold text-orange-600">${{ number_format($totalBalanceBf ?? 0, 2) }}</p>
            </div>
            <div class="text-center p-3 bg-blue-50 rounded-lg">
                <p class="text-xs text-gray-500 mb-1">Current Term</p>
                <p class="text-lg font-bold text-blue-600">${{ number_format($totalCurrentTermFees ?? 0, 2) }}</p>
            </div>
            <div class="text-center p-3 bg-gray-50 rounded-lg">
                <p class="text-xs text-gray-500 mb-1">Total Fees</p>
                <p class="text-lg font-bold text-gray-800">${{ number_format($totalStudentFees ?? 0, 2) }}</p>
            </div>
            <div class="text-center p-3 bg-green-50 rounded-lg">
                <p class="text-xs text-gray-500 mb-1">Total Paid</p>
                <p class="text-lg font-bold text-green-600">${{ number_format($totalStudentPayments ?? 0, 2) }}</p>
            </div>
            <div class="text-center p-3 bg-red-50 rounded-lg">
                <p class="text-xs text-gray-500 mb-1">Outstanding</p>
                <p class="text-lg font-bold text-red-600">${{ number_format($totalOutstandingFees ?? 0, 2) }}</p>
            </div>
        </div>

        <!-- Fee Breakdown by Category (Day/Boarding, ZIMSEC/Cambridge) -->
        @if(isset($feesByCategory))
        <div class="border-t pt-4">
            <h4 class="text-md font-semibold text-gray-700 mb-3">
                Current Term Fee Breakdown by Category
                @if(isset($currentTerm))
                    <span class="text-sm font-normal text-gray-500">
                        ({{ ucfirst($currentTerm->result_period) }} Term {{ $currentTerm->year }})
                    </span>
                @endif
            </h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($feesByCategory as $categoryKey => $category)
                @if($category['count'] > 0)
                <div class="border rounded-lg p-4 {{ $categoryKey == 'zimsec_day' ? 'bg-blue-50 border-blue-200' : ($categoryKey == 'zimsec_boarding' ? 'bg-green-50 border-green-200' : ($categoryKey == 'cambridge_day' ? 'bg-purple-50 border-purple-200' : 'bg-orange-50 border-orange-200')) }}">
                    <div class="flex justify-between items-center mb-3">
                        <h5 class="font-semibold text-gray-800">{{ $category['label'] }}</h5>
                        <span class="text-xs bg-gray-200 px-2 py-1 rounded-full">{{ $category['count'] }} students</span>
                    </div>
                    
                    @if(count($category['fees']) > 0)
                    <div class="space-y-2">
                        @foreach($category['fees'] as $feeTypeName => $amount)
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-700">{{ $feeTypeName }}</span>
                            <span class="font-medium text-gray-900">${{ number_format($amount, 2) }}</span>
                        </div>
                        @endforeach
                        <div class="border-t pt-2 mt-2 flex justify-between items-center">
                            <span class="font-semibold text-gray-800">Total</span>
                            <span class="font-bold text-gray-900">${{ number_format($category['total'], 2) }}</span>
                        </div>
                        <div class="text-xs text-gray-500 text-right">
                            Avg per student: ${{ number_format($category['total'] / $category['count'], 2) }}
                        </div>
                    </div>
                    @else
                    <p class="text-sm text-gray-500">No fees configured</p>
                    @endif
                </div>
                @endif
                @endforeach
            </div>
            
            <!-- Summary comparison -->
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Students</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total Fees</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Avg/Student</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php $grandTotal = 0; $totalStudents = 0; @endphp
                        @foreach($feesByCategory as $categoryKey => $category)
                        @if($category['count'] > 0)
                        @php $grandTotal += $category['total']; $totalStudents += $category['count']; @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 text-sm font-medium text-gray-900">
                                <span class="inline-block w-3 h-3 rounded-full mr-2 {{ $categoryKey == 'zimsec_day' ? 'bg-blue-500' : ($categoryKey == 'zimsec_boarding' ? 'bg-green-500' : ($categoryKey == 'cambridge_day' ? 'bg-purple-500' : 'bg-orange-500')) }}"></span>
                                {{ $category['label'] }}
                            </td>
                            <td class="px-4 py-2 text-sm text-right text-gray-700">{{ $category['count'] }}</td>
                            <td class="px-4 py-2 text-sm text-right font-medium text-gray-900">${{ number_format($category['total'], 2) }}</td>
                            <td class="px-4 py-2 text-sm text-right text-gray-700">${{ number_format($category['total'] / $category['count'], 2) }}</td>
                        </tr>
                        @endif
                        @endforeach
                        <tr class="bg-gray-100 font-semibold">
                            <td class="px-4 py-2 text-sm text-gray-900">Grand Total</td>
                            <td class="px-4 py-2 text-sm text-right text-gray-900">{{ $totalStudents }}</td>
                            <td class="px-4 py-2 text-sm text-right text-gray-900">${{ number_format($grandTotal, 2) }}</td>
                            <td class="px-4 py-2 text-sm text-right text-gray-700">${{ $totalStudents > 0 ? number_format($grandTotal / $totalStudents, 2) : '0.00' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="border-t pt-4">
            <p class="text-sm text-gray-500 text-center py-4">No fee structure breakdown available for the current term.</p>
        </div>
        @endif
    </div>

    <!-- Income by Category -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Income by Category</h3>
            <div class="space-y-3">
                @forelse($incomeByCategory as $income)
                <div class="flex items-center justify-between">
                    <span class="text-gray-700">{{ $income->category }}</span>
                    <span class="font-semibold text-green-600">${{ number_format($income->total, 2) }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $totalIncome > 0 ? ($income->total / $totalIncome) * 100 : 0 }}%"></div>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">No income data available</p>
                @endforelse
            </div>
        </div>

        <!-- Expenses by Category -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Expenses by Category</h3>
            <div class="space-y-3">
                @forelse($expensesByCategory as $expense)
                <div class="flex items-center justify-between">
                    <span class="text-gray-700">{{ $expense->category }}</span>
                    <span class="font-semibold text-red-600">${{ number_format($expense->total, 2) }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-red-600 h-2 rounded-full" style="width: {{ $totalExpenses > 0 ? ($expense->total / $totalExpenses) * 100 : 0 }}%"></div>
                </div>
                @empty
                <p class="text-gray-500 text-center py-4">No expense data available</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- School Fees Income Breakdown -->
    @if(isset($schoolFeesByType) && count($schoolFeesByType) > 0)
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            School Fees Income by Fee Structure
            <span class="text-sm font-normal text-gray-500">(Total: ${{ number_format($totalSchoolFeesIncome ?? 0, 2) }})</span>
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- By Fee Type -->
            <div>
                <h4 class="text-md font-semibold text-gray-700 mb-3">By Fee Type</h4>
                <div class="space-y-2">
                    @foreach($schoolFeesByType as $feeTypeName => $amount)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-700">{{ $feeTypeName }}</span>
                        <span class="font-medium text-green-600">${{ number_format($amount, 2) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ $totalSchoolFeesIncome > 0 ? ($amount / $totalSchoolFeesIncome) * 100 : 0 }}%"></div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- By Student Category -->
            <div>
                <h4 class="text-md font-semibold text-gray-700 mb-3">By Student Category</h4>
                <div class="space-y-2">
                    @foreach($schoolFeesByCategory as $categoryKey => $category)
                    @if($category['total'] > 0)
                    <div class="flex items-center justify-between text-sm">
                        <span class="flex items-center text-gray-700">
                            <span class="inline-block w-3 h-3 rounded-full mr-2 {{ $categoryKey == 'zimsec_day' ? 'bg-blue-500' : ($categoryKey == 'zimsec_boarding' ? 'bg-green-500' : ($categoryKey == 'cambridge_day' ? 'bg-purple-500' : 'bg-orange-500')) }}"></span>
                            {{ $category['label'] }}
                        </span>
                        <span class="font-medium text-green-600">${{ number_format($category['total'], 2) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="{{ $categoryKey == 'zimsec_day' ? 'bg-blue-500' : ($categoryKey == 'zimsec_boarding' ? 'bg-green-500' : ($categoryKey == 'cambridge_day' ? 'bg-purple-500' : 'bg-orange-500')) }} h-2 rounded-full" style="width: {{ $totalSchoolFeesIncome > 0 ? ($category['total'] / $totalSchoolFeesIncome) * 100 : 0 }}%"></div>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Summary Table -->
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Fee Type</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount Collected</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">% of Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($schoolFeesByType as $feeTypeName => $amount)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $feeTypeName }}</td>
                        <td class="px-4 py-2 text-sm text-right font-medium text-green-600">${{ number_format($amount, 2) }}</td>
                        <td class="px-4 py-2 text-sm text-right text-gray-500">{{ $totalSchoolFeesIncome > 0 ? number_format(($amount / $totalSchoolFeesIncome) * 100, 1) : 0 }}%</td>
                    </tr>
                    @endforeach
                    <tr class="bg-gray-100 font-semibold">
                        <td class="px-4 py-2 text-sm text-gray-900">Total</td>
                        <td class="px-4 py-2 text-sm text-right text-green-600">${{ number_format($totalSchoolFeesIncome, 2) }}</td>
                        <td class="px-4 py-2 text-sm text-right text-gray-500">100%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Monthly Trends -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Monthly Trends (Last 12 Months)</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Income</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expenses</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    @endphp
                    @forelse($monthlyIncome as $index => $income)
                    @php
                        $expense = $monthlyExpenses->where('month', $income->month)->where('year', $income->year)->first();
                        $expenseTotal = $expense ? $expense->total : 0;
                        $net = $income->total - $expenseTotal;
                    @endphp
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $months[$income->month - 1] }} {{ $income->year }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-semibold">
                            ${{ number_format($income->total, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-semibold">
                            ${{ number_format($expenseTotal, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold {{ $net >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            ${{ number_format($net, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No monthly data available</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
