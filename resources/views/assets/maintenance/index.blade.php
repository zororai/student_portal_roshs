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
            <h1 class="text-2xl font-bold text-gray-800">Maintenance Records</h1>
            <p class="text-gray-600">Track asset repairs, services, and inspections</p>
        </div>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div>
                <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                    <option value="repair" {{ request('type') == 'repair' ? 'selected' : '' }}>Repair</option>
                    <option value="service" {{ request('type') == 'service' ? 'selected' : '' }}>Service</option>
                    <option value="inspection" {{ request('type') == 'inspection' ? 'selected' : '' }}>Inspection</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Filter</button>
                <a href="{{ route('finance.assets.maintenance') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asset</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reported</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Cost</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($maintenances as $maintenance)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <a href="{{ route('finance.assets.show', $maintenance->asset) }}" class="text-blue-600 hover:underline">
                            {{ $maintenance->asset->name }}
                        </a>
                        <div class="text-sm text-gray-500">{{ $maintenance->asset->asset_code }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full {{ $maintenance->type_badge }}">
                            {{ ucfirst($maintenance->maintenance_type) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">{{ Str::limit($maintenance->description, 50) }}</td>
                    <td class="px-6 py-4">{{ $maintenance->reported_date->format('M d, Y') }}</td>
                    <td class="px-6 py-4 text-right">${{ number_format($maintenance->cost, 2) }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2 py-1 text-xs rounded-full {{ $maintenance->status_badge }}">
                            {{ ucfirst(str_replace('_', ' ', $maintenance->status)) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($maintenance->status != 'completed')
                        <form action="{{ route('finance.assets.maintenance.complete', $maintenance) }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="completed_date" value="{{ date('Y-m-d') }}">
                            <button type="submit" class="text-green-600 hover:text-green-800" title="Mark Complete">
                                <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        <p>No maintenance records found</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $maintenances->withQueryString()->links() }}
    </div>
</div>
@endsection
