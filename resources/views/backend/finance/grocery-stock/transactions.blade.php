@extends('backend.layouts.app')

@section('title', 'Stock Transactions')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Stock Transactions</h1>
                <p class="mt-1 text-gray-500">View and add stock transactions</p>
            </div>
            <div class="mt-4 md:mt-0 flex gap-2">
                <a href="{{ route('admin.grocery-stock.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Stock
                </a>
                <button onclick="openAddModal()" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Transaction
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <form method="GET" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Term</label>
                    <select name="term" class="px-4 py-2 border border-gray-300 rounded-lg">
                        @foreach($terms as $t)
                        <option value="{{ $t }}" {{ $term == $t ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $t)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <select name="year" class="px-4 py-2 border border-gray-300 rounded-lg">
                        @foreach($years as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="type" class="px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">All Types</option>
                        <option value="received" {{ $type == 'received' ? 'selected' : '' }}>Received</option>
                        <option value="usage" {{ $type == 'usage' ? 'selected' : '' }}>Usage</option>
                        <option value="bad_stock" {{ $type == 'bad_stock' ? 'selected' : '' }}>Bad Stock</option>
                        <option value="balance_bf" {{ $type == 'balance_bf' ? 'selected' : '' }}>Balance B/F</option>
                        <option value="adjustment" {{ $type == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                    </select>
                </div>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
            </form>
        </div>

        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
            {{ session('success') }}
        </div>
        @endif

        <!-- Transactions Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Item</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Type</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Quantity</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Balance After</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Recorded By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($transactions as $txn)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $txn->transaction_date->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-800">{{ $txn->stockItem->name }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $txn->getTypeBadgeClass() }}">
                                    {{ $txn->getTypeLabel() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-right {{ in_array($txn->type, ['usage', 'bad_stock']) ? 'text-red-600' : 'text-green-600' }}">
                                {{ in_array($txn->type, ['usage', 'bad_stock']) ? '-' : '+' }}{{ number_format($txn->quantity, 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-right font-medium text-gray-800">{{ number_format($txn->balance_after, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $txn->description ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $txn->recordedBy->name ?? 'System' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">No transactions found for this period.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($transactions->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $transactions->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Transaction Modal -->
<div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800">Add Transaction</h3>
        </div>
        <form action="{{ route('admin.grocery-stock.store-transaction') }}" method="POST" class="p-6">
            @csrf
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Stock Item</label>
                    <select name="stock_item_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="">Select Item</option>
                        @foreach($stockItems as $item)
                        <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select name="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        <option value="received">Received from Students</option>
                        <option value="usage">Usage/Deduction</option>
                        <option value="bad_stock">Bad Stock</option>
                        <option value="balance_bf">Balance B/F</option>
                        <option value="adjustment">Adjustment</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                    <input type="number" name="quantity" step="0.01" min="0.01" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Term</label>
                    <select name="term" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        @foreach($terms as $t)
                        <option value="{{ $t }}" {{ $term == $t ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $t)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <select name="year" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        @foreach($years as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <input type="text" name="description" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Optional">
                </div>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeAddModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Add Transaction</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('addModal').classList.remove('hidden');
}
function closeAddModal() {
    document.getElementById('addModal').classList.add('hidden');
}
</script>
@endsection
