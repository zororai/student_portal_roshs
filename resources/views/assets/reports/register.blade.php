@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div class="flex items-center">
            <a href="{{ route('finance.assets.reports') }}" class="text-gray-600 hover:text-gray-800 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Asset Register</h1>
                <p class="text-gray-600">Complete list of all school assets</p>
            </div>
        </div>
        <button onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6 print:hidden">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="under_maintenance" {{ request('status') == 'under_maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                    <option value="disposed" {{ request('status') == 'disposed' ? 'selected' : '' }}>Disposed</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Filter</button>
                <a href="{{ route('finance.assets.reports.register') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purchase Date</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Purchase Cost</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Current Value</th>
                        <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @php $totalPurchase = 0; $totalCurrent = 0; @endphp
                    @foreach($assets as $asset)
                    @php $totalPurchase += $asset->purchase_cost; $totalCurrent += $asset->current_value; @endphp
                    <tr>
                        <td class="px-4 py-2 font-mono text-sm">{{ $asset->asset_code }}</td>
                        <td class="px-4 py-2">{{ $asset->name }}</td>
                        <td class="px-4 py-2">{{ $asset->category->name ?? 'N/A' }}</td>
                        <td class="px-4 py-2">{{ $asset->location->name ?? 'N/A' }}</td>
                        <td class="px-4 py-2">{{ $asset->purchase_date->format('Y-m-d') }}</td>
                        <td class="px-4 py-2 text-right">${{ number_format($asset->purchase_cost, 2) }}</td>
                        <td class="px-4 py-2 text-right">${{ number_format($asset->current_value, 2) }}</td>
                        <td class="px-4 py-2 text-center">
                            <span class="px-2 py-1 text-xs rounded-full {{ $asset->status_badge }}">{{ ucfirst($asset->status) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-100">
                    <tr>
                        <td colspan="5" class="px-4 py-2 font-semibold text-right">Total ({{ $assets->count() }} assets)</td>
                        <td class="px-4 py-2 text-right font-semibold">${{ number_format($totalPurchase, 2) }}</td>
                        <td class="px-4 py-2 text-right font-semibold">${{ number_format($totalCurrent, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
