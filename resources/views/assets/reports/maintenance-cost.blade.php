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
            <h1 class="text-2xl font-bold text-gray-800">Maintenance Cost Report</h1>
            <p class="text-gray-600">{{ $startDate }} to {{ $endDate }}</p>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm text-gray-600 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label class="block text-sm text-gray-600 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 border border-gray-300 rounded-lg">
            </div>
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Filter</button>
        </form>
    </div>

    <!-- Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="text-gray-500 text-sm">Total Maintenance Cost</div>
            <div class="text-2xl font-bold text-red-600">${{ number_format($summary['total_cost'], 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="text-gray-500 text-sm">By Type</div>
            <div class="space-y-1 mt-2">
                @foreach($summary['by_type'] as $type => $cost)
                <div class="flex justify-between text-sm">
                    <span>{{ ucfirst($type) }}</span>
                    <span class="font-medium">${{ number_format($cost, 2) }}</span>
                </div>
                @endforeach
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="text-gray-500 text-sm">Completed Records</div>
            <div class="text-2xl font-bold text-gray-800">{{ $maintenances->count() }}</div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asset</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Completed</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cost</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($maintenances as $m)
                <tr>
                    <td class="px-4 py-2">{{ $m->asset->name ?? 'N/A' }}</td>
                    <td class="px-4 py-2">{{ ucfirst($m->maintenance_type) }}</td>
                    <td class="px-4 py-2">{{ Str::limit($m->description, 40) }}</td>
                    <td class="px-4 py-2">{{ $m->completed_date->format('Y-m-d') }}</td>
                    <td class="px-4 py-2 text-right">${{ number_format($m->cost, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">No maintenance records found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
