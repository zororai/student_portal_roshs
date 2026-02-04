@extends('backend.layouts.app')

@section('title', 'Record Bad Stock')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Record Bad Stock</h1>
                <p class="mt-1 text-gray-500">Write off spoiled or damaged stock</p>
            </div>
            <a href="{{ route('admin.grocery-stock.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Stock
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <strong>Warning:</strong> Bad stock will be permanently deducted from inventory. Please verify quantities before submitting.
            </div>

            <form action="{{ route('admin.grocery-stock.store-bad-stock') }}" method="POST">
                @csrf
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Term</label>
                        <select name="term" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="term_1" {{ $term == 'term_1' ? 'selected' : '' }}>Term 1</option>
                            <option value="term_2" {{ $term == 'term_2' ? 'selected' : '' }}>Term 2</option>
                            <option value="term_3" {{ $term == 'term_3' ? 'selected' : '' }}>Term 3</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                        <select name="year" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Write-off</label>
                    <input type="text" name="description" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="e.g. Spoiled due to storage conditions" required>
                </div>

                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Stock Items</h3>
                    <div class="space-y-3">
                        @foreach($stockItems as $index => $item)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <span class="font-medium text-gray-800">{{ $item->name }}</span>
                                <span class="text-sm text-gray-500 ml-2">(Current: {{ number_format($item->current_balance, 2) }} {{ $item->unit }})</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="hidden" name="items[{{ $index }}][stock_item_id]" value="{{ $item->id }}">
                                <input type="number" name="items[{{ $index }}][quantity]" value="0" min="0" step="0.01" 
                                       class="w-24 px-3 py-2 border border-red-300 rounded-lg text-right focus:ring-red-500 focus:border-red-500">
                                <span class="text-sm text-gray-500 w-16">{{ $item->unit }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                        Record Bad Stock
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
