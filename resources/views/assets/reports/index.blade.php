@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('finance.assets.index') }}" class="text-gray-600 hover:text-gray-800 mr-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Asset Reports</h1>
            <p class="text-gray-600">Asset analytics and reporting</p>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="text-gray-500 text-sm">Total Assets</div>
            <div class="text-2xl font-bold text-gray-800">{{ $summary['asset_count'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="text-gray-500 text-sm">Total Purchase Cost</div>
            <div class="text-2xl font-bold text-blue-600">${{ number_format($summary['total_purchase_cost'], 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="text-gray-500 text-sm">Current Value</div>
            <div class="text-2xl font-bold text-green-600">${{ number_format($summary['total_current_value'], 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="text-gray-500 text-sm">Total Depreciation</div>
            <div class="text-2xl font-bold text-red-600">${{ number_format($summary['total_depreciation'], 2) }}</div>
        </div>
    </div>

    <!-- Report Links -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <a href="{{ route('finance.assets.reports.register') }}" class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg mr-4">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Asset Register</h3>
                    <p class="text-sm text-gray-500">Complete list of all assets</p>
                </div>
            </div>
        </a>

        <a href="{{ route('finance.assets.reports.depreciation-schedule') }}" class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-lg mr-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Depreciation Schedule</h3>
                    <p class="text-sm text-gray-500">Projected depreciation by asset</p>
                </div>
            </div>
        </a>

        <a href="{{ route('finance.assets.reports.maintenance-cost') }}" class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-lg mr-4">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Maintenance Costs</h3>
                    <p class="text-sm text-gray-500">Maintenance expense analysis</p>
                </div>
            </div>
        </a>

        <a href="{{ route('finance.assets.reports.disposed') }}" class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition">
            <div class="flex items-center">
                <div class="p-3 bg-gray-100 rounded-lg mr-4">
                    <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Disposed Assets</h3>
                    <p class="text-sm text-gray-500">Assets that have been disposed</p>
                </div>
            </div>
        </a>

        <a href="{{ route('finance.assets.reports.by-location') }}" class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition">
            <div class="flex items-center">
                <div class="p-3 bg-indigo-100 rounded-lg mr-4">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Assets by Location</h3>
                    <p class="text-sm text-gray-500">Assets grouped by location</p>
                </div>
            </div>
        </a>
    </div>

    <!-- By Category -->
    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Assets by Category</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Count</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Purchase Cost</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Current Value</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($summary['by_category'] as $category => $data)
                    <tr>
                        <td class="px-4 py-2 font-medium">{{ $category }}</td>
                        <td class="px-4 py-2 text-center">{{ $data['count'] }}</td>
                        <td class="px-4 py-2 text-right">${{ number_format($data['purchase_cost'], 2) }}</td>
                        <td class="px-4 py-2 text-right text-green-600">${{ number_format($data['current_value'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
