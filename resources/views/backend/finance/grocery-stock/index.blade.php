@extends('layouts.app')

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
        <div class="grid grid-cols-1 md:grid-cols-1 gap-4 mb-6">
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

        <!-- Collected Groceries from Students -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mt-6">
            <div class="px-6 py-4 border-b border-gray-100 bg-green-50 flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">
                        <svg class="w-5 h-5 inline-block mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Groceries Collected from Parents - {{ ucfirst(str_replace('_', ' ', $term)) }} {{ $year }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Track collected groceries, usage, spoiled items, and remaining balance</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="openRecordUsageModal()" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Record Usage
                    </button>
                    <button onclick="openRecordSpoiledModal()" class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Record Spoiled
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Item Name</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Students</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Collected</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Usage</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Spoiled</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Balance</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($collectedGroceries as $index => $grocery)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 text-sm font-medium text-gray-800">
                                {{ $grocery['name'] }}
                                <span class="text-xs text-gray-500 block">{{ $grocery['required_per_student'] }}/student</span>
                            </td>
                            <td class="px-4 py-4 text-sm text-right text-gray-600">{{ $grocery['students_submitted'] }}</td>
                            <td class="px-4 py-4 text-sm text-right text-green-600 font-medium">{{ number_format($grocery['total_collected'], 2) }}</td>
                            <td class="px-4 py-4 text-sm text-right text-blue-600">
                                @if($grocery['usage'] > 0)
                                -{{ number_format($grocery['usage'], 2) }}
                                @else
                                <span class="text-gray-400">0.00</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-sm text-right text-red-600">
                                @if($grocery['spoiled'] > 0)
                                -{{ number_format($grocery['spoiled'], 2) }}
                                @else
                                <span class="text-gray-400">0.00</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-sm text-right font-bold {{ $grocery['balance'] < 0 ? 'text-red-600' : 'text-green-600' }}">
                                {{ number_format($grocery['balance'], 2) }}
                            </td>
                            <td class="px-4 py-4 text-center">
                                @if($grocery['stock_item_id'])
                                <div class="flex justify-center gap-1">
                                    <button onclick="openSingleUsageModal('{{ $grocery['name'] }}', {{ $grocery['stock_item_id'] }}, {{ $grocery['balance'] }})" 
                                            class="p-1 text-blue-600 hover:bg-blue-50 rounded" title="Record Usage">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </button>
                                    <button onclick="openSingleSpoiledModal('{{ $grocery['name'] }}', {{ $grocery['stock_item_id'] }}, {{ $grocery['balance'] }})" 
                                            class="p-1 text-red-600 hover:bg-red-50 rounded" title="Record Spoiled">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                                @else
                                <span class="text-xs text-gray-400">No stock item</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                                No groceries collected for this term/year yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($collectedGroceries->count() > 0)
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td class="px-4 py-3 text-sm font-bold text-gray-800">TOTALS</td>
                            <td class="px-4 py-3 text-sm text-right font-bold text-gray-600">{{ $collectedGroceries->sum('students_submitted') }}</td>
                            <td class="px-4 py-3 text-sm text-right font-bold text-green-600">{{ number_format($collectedGroceries->sum('total_collected'), 2) }}</td>
                            <td class="px-4 py-3 text-sm text-right font-bold text-blue-600">-{{ number_format($collectedGroceries->sum('usage'), 2) }}</td>
                            <td class="px-4 py-3 text-sm text-right font-bold text-red-600">-{{ number_format($collectedGroceries->sum('spoiled'), 2) }}</td>
                            <td class="px-4 py-3 text-sm text-right font-bold {{ $collectedGroceries->sum('balance') < 0 ? 'text-red-600' : 'text-green-600' }}">
                                {{ number_format($collectedGroceries->sum('balance'), 2) }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                    @endif
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

// Single item usage modal
function openSingleUsageModal(itemName, stockItemId, balance) {
    document.getElementById('singleUsageItemName').textContent = itemName;
    document.getElementById('singleUsageBalance').textContent = balance.toFixed(2);
    document.getElementById('singleUsageStockItemId').value = stockItemId;
    document.getElementById('singleUsageQuantity').value = '';
    document.getElementById('singleUsageQuantity').max = balance;
    document.getElementById('singleUsageModal').classList.remove('hidden');
}
function closeSingleUsageModal() {
    document.getElementById('singleUsageModal').classList.add('hidden');
}

// Single item spoiled modal
function openSingleSpoiledModal(itemName, stockItemId, balance) {
    document.getElementById('singleSpoiledItemName').textContent = itemName;
    document.getElementById('singleSpoiledBalance').textContent = balance.toFixed(2);
    document.getElementById('singleSpoiledStockItemId').value = stockItemId;
    document.getElementById('singleSpoiledQuantity').value = '';
    document.getElementById('singleSpoiledQuantity').max = balance;
    document.getElementById('singleSpoiledModal').classList.remove('hidden');
}
function closeSingleSpoiledModal() {
    document.getElementById('singleSpoiledModal').classList.add('hidden');
}

// Bulk record usage modal
function openRecordUsageModal() {
    document.getElementById('usageItemSelect').value = '';
    document.getElementById('usageQuantityInput').value = '';
    document.getElementById('usageBalanceInfo').classList.add('hidden');
    document.getElementById('recordUsageModal').classList.remove('hidden');
}
function closeRecordUsageModal() {
    document.getElementById('recordUsageModal').classList.add('hidden');
}
function updateUsageBalance() {
    var select = document.getElementById('usageItemSelect');
    var selectedOption = select.options[select.selectedIndex];
    var balance = selectedOption.getAttribute('data-balance');
    var collected = selectedOption.getAttribute('data-collected');
    var name = selectedOption.getAttribute('data-name');
    
    if (balance && name) {
        document.getElementById('usageSelectedItemName').textContent = name;
        document.getElementById('usageTotalCollected').textContent = parseFloat(collected).toFixed(2);
        document.getElementById('usageCurrentBalance').textContent = parseFloat(balance).toFixed(2);
        document.getElementById('usageBalanceInfo').classList.remove('hidden');
        document.getElementById('usageQuantityInput').max = balance;
    } else {
        document.getElementById('usageBalanceInfo').classList.add('hidden');
    }
}

// Bulk record spoiled modal
function openRecordSpoiledModal() {
    document.getElementById('spoiledItemSelect').value = '';
    document.getElementById('spoiledQuantityInput').value = '';
    document.getElementById('spoiledBalanceInfo').classList.add('hidden');
    document.getElementById('recordSpoiledModal').classList.remove('hidden');
}
function closeRecordSpoiledModal() {
    document.getElementById('recordSpoiledModal').classList.add('hidden');
}
function updateSpoiledBalance() {
    var select = document.getElementById('spoiledItemSelect');
    var selectedOption = select.options[select.selectedIndex];
    var balance = selectedOption.getAttribute('data-balance');
    var collected = selectedOption.getAttribute('data-collected');
    var name = selectedOption.getAttribute('data-name');
    
    if (balance && name) {
        document.getElementById('spoiledSelectedItemName').textContent = name;
        document.getElementById('spoiledTotalCollected').textContent = parseFloat(collected).toFixed(2);
        document.getElementById('spoiledCurrentBalance').textContent = parseFloat(balance).toFixed(2);
        document.getElementById('spoiledBalanceInfo').classList.remove('hidden');
        document.getElementById('spoiledQuantityInput').max = balance;
    } else {
        document.getElementById('spoiledBalanceInfo').classList.add('hidden');
    }
}
</script>

<!-- Single Item Usage Modal -->
<div id="singleUsageModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-100 bg-blue-50">
            <h3 class="text-lg font-semibold text-gray-800">Record Usage</h3>
            <p class="text-sm text-gray-600">Item: <span id="singleUsageItemName" class="font-medium"></span></p>
        </div>
        <form action="{{ route('admin.grocery-stock.store-usage') }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="term" value="{{ $term }}">
            <input type="hidden" name="year" value="{{ $year }}">
            <input type="hidden" name="items[0][stock_item_id]" id="singleUsageStockItemId">
            
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-2">Available Balance: <span id="singleUsageBalance" class="font-bold text-green-600"></span></p>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity Used</label>
                <input type="number" name="items[0][quantity]" id="singleUsageQuantity" step="0.01" min="0.01" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description (optional)</label>
                <input type="text" name="description" placeholder="e.g. Weekly usage"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeSingleUsageModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Record Usage</button>
            </div>
        </form>
    </div>
</div>

<!-- Single Item Spoiled Modal -->
<div id="singleSpoiledModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-100 bg-red-50">
            <h3 class="text-lg font-semibold text-gray-800">Record Spoiled/Bad Stock</h3>
            <p class="text-sm text-gray-600">Item: <span id="singleSpoiledItemName" class="font-medium"></span></p>
        </div>
        <form action="{{ route('admin.grocery-stock.store-bad-stock') }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="term" value="{{ $term }}">
            <input type="hidden" name="year" value="{{ $year }}">
            <input type="hidden" name="items[0][stock_item_id]" id="singleSpoiledStockItemId">
            
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-2">Available Balance: <span id="singleSpoiledBalance" class="font-bold text-green-600"></span></p>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity Spoiled</label>
                <input type="number" name="items[0][quantity]" id="singleSpoiledQuantity" step="0.01" min="0.01" required
                       class="w-full px-4 py-2 border border-red-300 rounded-lg focus:ring-red-500 focus:border-red-500">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                <input type="text" name="description" placeholder="e.g. Expired, Damaged, Spoiled" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeSingleSpoiledModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Record Spoiled</button>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Record Usage Modal -->
<div id="recordUsageModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center overflow-y-auto">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4 my-8">
        <div class="px-6 py-4 border-b border-gray-100 bg-blue-50">
            <h3 class="text-lg font-semibold text-gray-800">Record Daily Usage</h3>
            <p class="text-sm text-gray-600">Deduct stock for items used from Groceries Collected</p>
        </div>
        <form action="{{ route('admin.grocery-stock.store-usage') }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="term" value="{{ $term }}">
            <input type="hidden" name="year" value="{{ $year }}">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Item from Collected Groceries</label>
                <select name="items[0][stock_item_id]" id="usageItemSelect" required onchange="updateUsageBalance()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">-- Select an item --</option>
                    @foreach($collectedGroceries as $grocery)
                    @if($grocery['stock_item_id'])
                    <option value="{{ $grocery['stock_item_id'] }}" 
                            data-balance="{{ $grocery['balance'] }}"
                            data-collected="{{ $grocery['total_collected'] }}"
                            data-name="{{ $grocery['name'] }}">
                        {{ $grocery['name'] }}
                    </option>
                    @endif
                    @endforeach
                </select>
            </div>
            
            <!-- Balance Info Box -->
            <div id="usageBalanceInfo" class="mb-4 p-4 bg-gray-50 rounded-lg hidden">
                <h4 class="text-sm font-semibold text-gray-700 mb-2">Item: <span id="usageSelectedItemName" class="text-blue-600"></span></h4>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div>
                        <span class="text-gray-500">Total Collected:</span>
                        <span id="usageTotalCollected" class="font-medium text-green-600 ml-1"></span>
                    </div>
                    <div>
                        <span class="text-gray-500">Current Balance:</span>
                        <span id="usageCurrentBalance" class="font-bold text-blue-600 ml-1"></span>
                    </div>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity to Deduct</label>
                <input type="number" name="items[0][quantity]" id="usageQuantityInput" step="0.01" min="0.01" required
                       class="w-full px-4 py-2 border border-blue-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description (optional)</label>
                <input type="text" name="description" placeholder="e.g. Weekly usage, Daily cooking"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeRecordUsageModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Record Usage</button>
            </div>
        </form>
    </div>
</div>

<!-- Bulk Record Spoiled Modal -->
<div id="recordSpoiledModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center overflow-y-auto">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4 my-8">
        <div class="px-6 py-4 border-b border-gray-100 bg-red-50">
            <h3 class="text-lg font-semibold text-gray-800">Record Spoiled/Bad Stock</h3>
            <p class="text-sm text-gray-600">Write off damaged or expired items from Groceries Collected</p>
        </div>
        <form action="{{ route('admin.grocery-stock.store-bad-stock') }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="term" value="{{ $term }}">
            <input type="hidden" name="year" value="{{ $year }}">
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Item from Collected Groceries</label>
                <select name="items[0][stock_item_id]" id="spoiledItemSelect" required onchange="updateSpoiledBalance()"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500">
                    <option value="">-- Select an item --</option>
                    @foreach($collectedGroceries as $grocery)
                    @if($grocery['stock_item_id'])
                    <option value="{{ $grocery['stock_item_id'] }}" 
                            data-balance="{{ $grocery['balance'] }}"
                            data-collected="{{ $grocery['total_collected'] }}"
                            data-name="{{ $grocery['name'] }}">
                        {{ $grocery['name'] }}
                    </option>
                    @endif
                    @endforeach
                </select>
            </div>
            
            <!-- Balance Info Box -->
            <div id="spoiledBalanceInfo" class="mb-4 p-4 bg-gray-50 rounded-lg hidden">
                <h4 class="text-sm font-semibold text-gray-700 mb-2">Item: <span id="spoiledSelectedItemName" class="text-red-600"></span></h4>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div>
                        <span class="text-gray-500">Total Collected:</span>
                        <span id="spoiledTotalCollected" class="font-medium text-green-600 ml-1"></span>
                    </div>
                    <div>
                        <span class="text-gray-500">Current Balance:</span>
                        <span id="spoiledCurrentBalance" class="font-bold text-red-600 ml-1"></span>
                    </div>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity Spoiled/Damaged</label>
                <input type="number" name="items[0][quantity]" id="spoiledQuantityInput" step="0.01" min="0.01" required
                       class="w-full px-4 py-2 border border-red-300 rounded-lg focus:ring-red-500 focus:border-red-500">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason</label>
                <input type="text" name="description" placeholder="e.g. Expired, Damaged, Spoiled due to storage" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg">
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeRecordSpoiledModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Record Spoiled</button>
            </div>
        </form>
    </div>
</div>
@endsection
