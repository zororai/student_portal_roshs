@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div class="flex items-center">
            <a href="{{ route('finance.assets.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Depreciation Management</h1>
                <p class="text-gray-600">Calculate and post asset depreciation</p>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        {{ session('error') }}
    </div>
    @endif

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="text-gray-500 text-sm">Total Depreciation ({{ $year }})</div>
            <div class="text-2xl font-bold text-red-600">${{ number_format($summary['total_depreciation'], 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="text-gray-500 text-sm">Posted to Ledger</div>
            <div class="text-2xl font-bold text-green-600">{{ $summary['posted_count'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="text-gray-500 text-sm">Pending Posting</div>
            <div class="text-2xl font-bold text-yellow-600">{{ $summary['pending_count'] }}</div>
        </div>
    </div>

    <!-- Actions -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <div class="flex flex-wrap gap-4 items-center justify-between">
            <form method="GET" class="flex gap-2">
                <select name="year" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @for($y = date('Y'); $y >= date('Y') - 10; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">View</button>
            </form>
            
            <div class="flex gap-2">
                <form action="{{ route('finance.assets.depreciation.run') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="year" value="{{ $year }}">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Calculate Depreciation
                    </button>
                </form>
                
                @if($summary['pending_count'] > 0)
                <form action="{{ route('finance.assets.depreciation.post-all') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="year" value="{{ $year }}">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 flex items-center" onclick="return confirm('Post all pending depreciation to ledger?')">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Post All to Ledger
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Depreciation Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asset</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Opening</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Depreciation</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Closing</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($depreciations as $depreciation)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <a href="{{ route('finance.assets.show', $depreciation->asset) }}" class="text-blue-600 hover:underline">
                            {{ $depreciation->asset->name }}
                        </a>
                        <div class="text-sm text-gray-500">{{ $depreciation->asset->asset_code }}</div>
                    </td>
                    <td class="px-6 py-4">{{ $depreciation->asset->category->name ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-right">${{ number_format($depreciation->opening_value, 2) }}</td>
                    <td class="px-6 py-4 text-right text-red-600">${{ number_format($depreciation->depreciation_amount, 2) }}</td>
                    <td class="px-6 py-4 text-right">${{ number_format($depreciation->closing_value, 2) }}</td>
                    <td class="px-6 py-4 text-center">
                        @if($depreciation->posted_to_ledger)
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Posted</span>
                        @else
                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if(!$depreciation->posted_to_ledger)
                        <form action="{{ route('finance.assets.depreciation.post', $depreciation) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-green-600 hover:text-green-800" title="Post to Ledger">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <p>No depreciation records for {{ $year }}</p>
                        <p class="text-sm mt-2">Click "Calculate Depreciation" to generate records</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $depreciations->withQueryString()->links() }}
    </div>
</div>
@endsection
