@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">School Income</h1>
            <p class="text-gray-500 mt-1">Track all income sources</p>
        </div>
        <button onclick="document.getElementById('addIncomeModal').classList.remove('hidden')" class="mt-4 md:mt-0 inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Income
        </button>
    </div>

    <!-- Summary Card -->
    <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-6 mb-6 text-white">
        <p class="text-green-100 text-sm uppercase tracking-wide">Total Income</p>
        <p class="text-4xl font-bold mt-1">${{ number_format($totalIncome, 2) }}</p>
    </div>

    <!-- Income Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">#</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Category</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Amount</th>
                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($incomes as $index => $income)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $incomes->firstItem() + $index }}</td>
                    <td class="px-6 py-4 text-sm text-gray-800">{{ \Carbon\Carbon::parse($income->date)->format('M d, Y') }}</td>
                    <td class="px-6 py-4 text-sm text-gray-800">{{ $income->description }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">{{ $income->category ?? 'General' }}</span>
                    </td>
                    <td class="px-6 py-4 text-sm text-right font-semibold text-green-600">${{ number_format($income->amount, 2) }}</td>
                    <td class="px-6 py-4 text-center">
                        <form action="{{ route('finance.income.destroy', $income->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this income record?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">No income records found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $incomes->links() }}
        </div>
    </div>
</div>

<!-- Add Income Modal -->
<div id="addIncomeModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Add Income</h3>
            <button onclick="document.getElementById('addIncomeModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form action="{{ route('finance.income.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" name="date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" value="{{ date('Y-m-d') }}">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <input type="text" name="description" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="Income description">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="Tuition">Tuition</option>
                    <option value="Donations">Donations</option>
                    <option value="Events">Events</option>
                    <option value="Grants">Grants</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                <input type="number" name="amount" step="0.01" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="0.00">
            </div>
            <div class="flex justify-end space-x-3 pt-4">
                <button type="button" onclick="document.getElementById('addIncomeModal').classList.add('hidden')" class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Save Income</button>
            </div>
        </form>
    </div>
</div>
@endsection
