@extends('backend.layouts.app')

@section('title', 'Grocery Stock Management')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Grocery Stock Management</h1>
                <p class="mt-1 text-gray-500">Track grocery stock, usage, and balances</p>
            </div>
            <div class="mt-4 md:mt-0 flex flex-wrap gap-2">
                <a href="{{ route('admin.grocery-stock.items') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    Manage Items
                </a>
                <a href="{{ route('admin.grocery-stock.transactions') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    All Transactions
                </a>
                <a href="{{ route('admin.grocery-stock.print', ['term' => $term, 'year' => $year]) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Print Report
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <form method="GET" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Term</label>
                    <select name="term" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        @foreach($terms as $t)
                        <option value="{{ $t }}" {{ $term == $t ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $t)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <select name="year" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        @foreach($years as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
            </form>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <a href="{{ route('admin.grocery-stock.record-usage') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Record Usage</h3>
                        <p class="text-sm text-gray-500">Deduct stock for daily usage</p>
                    </div>
                </div>
            </a>
            <a href="{{ route('admin.grocery-stock.record-bad-stock') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Record Bad Stock</h3>
                        <p class="text-sm text-gray-500">Write off spoiled/damaged items</p>
                    </div>
                </div>
            </a>
            <button onclick="openCarryForwardModal()" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow text-left w-full">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-lg font-semibold text-gray-800">Carry Forward</h3>
                        <p class="text-sm text-gray-500">Bring balances to new term</p>
                    </div>
                </div>
            </button>
        </div>

        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
        @endif

        <!-- Stock Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800">Stock Summary - {{ ucfirst(str_replace('_', ' ', $term)) }} {{ $year }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Item</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Unit</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Balance B/F</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Received</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Usage</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Bad Stock</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Closing Balance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($stockItems as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $item->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $item->unit }}</td>
                            <td class="px-6 py-4 text-sm text-right text-purple-600">{{ number_format($item->balance_bf, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-right text-green-600">{{ number_format($item->received, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-right text-blue-600">{{ number_format($item->usage, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-right text-red-600">{{ number_format($item->bad_stock, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-right font-bold {{ $item->closing_balance < 0 ? 'text-red-600' : 'text-gray-800' }}">
                                {{ number_format($item->closing_balance, 2) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                No stock items found. <a href="{{ route('admin.grocery-stock.items') }}" class="text-blue-600 hover:underline">Add stock items</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Carry Forward Modal -->
<div id="carryForwardModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Carry Forward Balances</h3>
        </div>
        <form action="{{ route('admin.grocery-stock.carry-forward') }}" method="POST" class="p-6">
            @csrf
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">From Term</label>
                    <select name="from_term" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        @foreach($terms as $t)
                        <option value="{{ $t }}" {{ $term == $t ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $t)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">From Year</label>
                    <select name="from_year" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        @foreach($years as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">To Term</label>
                    <select name="to_term" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        @foreach($terms as $t)
                        <option value="{{ $t }}">{{ ucfirst(str_replace('_', ' ', $t)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">To Year</label>
                    <select name="to_year" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        @foreach($years as $y)
                        <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeCarryForwardModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">Carry Forward</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCarryForwardModal() {
    document.getElementById('carryForwardModal').classList.remove('hidden');
}
function closeCarryForwardModal() {
    document.getElementById('carryForwardModal').classList.add('hidden');
}
</script>
@endsection
