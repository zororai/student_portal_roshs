@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('finance.assets.reports') }}" class="text-gray-600 hover:text-gray-800 mr-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Disposed Assets Report</h1>
            <p class="text-gray-600">Assets that have been disposed</p>
        </div>
    </div>

    <!-- Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="text-gray-500 text-sm">Total Disposed</div>
            <div class="text-2xl font-bold text-gray-800">{{ $summary['total_count'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="text-gray-500 text-sm">Original Purchase Cost</div>
            <div class="text-2xl font-bold text-blue-600">${{ number_format($summary['total_purchase_cost'], 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="text-gray-500 text-sm">Total Disposal Value</div>
            <div class="text-2xl font-bold text-green-600">${{ number_format($summary['total_disposal_value'], 2) }}</div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asset</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Disposed Date</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reason</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Purchase Cost</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Disposal Value</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($disposedAssets as $asset)
                <tr>
                    <td class="px-4 py-2">
                        <div class="font-medium">{{ $asset->name }}</div>
                        <div class="text-sm text-gray-500">{{ $asset->asset_code }}</div>
                    </td>
                    <td class="px-4 py-2">{{ $asset->category->name ?? 'N/A' }}</td>
                    <td class="px-4 py-2">{{ $asset->disposed_at ? $asset->disposed_at->format('Y-m-d') : 'N/A' }}</td>
                    <td class="px-4 py-2">{{ $asset->disposal_reason ?? 'N/A' }}</td>
                    <td class="px-4 py-2 text-right">${{ number_format($asset->purchase_cost, 2) }}</td>
                    <td class="px-4 py-2 text-right">${{ number_format($asset->disposal_value ?? 0, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">No disposed assets found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
