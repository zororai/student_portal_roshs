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
                <h1 class="text-2xl font-bold text-gray-800">{{ $asset->name }}</h1>
                <p class="text-gray-600">{{ $asset->asset_code }}</p>
            </div>
        </div>
        @if(!$asset->isDisposed())
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('finance.assets.edit', $asset) }}" class="bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            <a href="{{ route('finance.assets.assign.form', $asset) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Assign
            </a>
            <a href="{{ route('finance.assets.maintenance.create', $asset) }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Maintenance
            </a>
            <a href="{{ route('finance.assets.dispose.form', $asset) }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Dispose
            </a>
        </div>
        @endif
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Asset Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Asset Details</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-sm text-gray-500">Asset Code</span>
                        <p class="font-medium">{{ $asset->asset_code }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Serial Number</span>
                        <p class="font-medium">{{ $asset->serial_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Category</span>
                        <p class="font-medium">{{ $asset->category->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Location</span>
                        <p class="font-medium">{{ $asset->location->full_name ?? 'Unassigned' }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Condition</span>
                        <p><span class="px-2 py-1 text-xs rounded-full {{ $asset->condition_badge }}">{{ ucfirst($asset->condition) }}</span></p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Status</span>
                        <p><span class="px-2 py-1 text-xs rounded-full {{ $asset->status_badge }}">{{ ucwords(str_replace('_', ' ', $asset->status)) }}</span></p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Assigned To</span>
                        <p class="font-medium">{{ $asset->assigned_to_name }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Age</span>
                        <p class="font-medium">{{ $asset->age_in_years }} years</p>
                    </div>
                </div>
                @if($asset->notes)
                <div class="mt-4 pt-4 border-t">
                    <span class="text-sm text-gray-500">Notes</span>
                    <p class="text-gray-700">{{ $asset->notes }}</p>
                </div>
                @endif
            </div>

            <!-- Financial Information -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Financial Information</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <span class="text-sm text-gray-500">Purchase Date</span>
                        <p class="font-medium">{{ $asset->purchase_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Purchase Cost</span>
                        <p class="font-medium text-gray-800">${{ number_format($asset->purchase_cost, 2) }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Current Value</span>
                        <p class="font-medium text-green-600">${{ number_format($asset->current_value, 2) }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-gray-500">Residual Value</span>
                        <p class="font-medium">${{ number_format($asset->residual_value, 2) }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-sm text-gray-500">Total Depreciation</span>
                    <p class="font-medium text-red-600">${{ number_format($asset->purchase_cost - $asset->current_value, 2) }}</p>
                </div>
            </div>

            <!-- Depreciation Schedule -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Depreciation Schedule</h2>
                @if(count($depreciationSchedule) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Year</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Opening</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Depreciation</th>
                                <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Closing</th>
                                <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($depreciationSchedule as $schedule)
                            <tr class="{{ $schedule['actual'] ? 'bg-blue-50' : '' }}">
                                <td class="px-4 py-2 font-medium">{{ $schedule['year'] }}</td>
                                <td class="px-4 py-2 text-right">${{ number_format($schedule['opening_value'], 2) }}</td>
                                <td class="px-4 py-2 text-right text-red-600">${{ number_format($schedule['depreciation'], 2) }}</td>
                                <td class="px-4 py-2 text-right">${{ number_format($schedule['closing_value'], 2) }}</td>
                                <td class="px-4 py-2 text-center">
                                    @if($schedule['posted'])
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Posted</span>
                                    @elseif($schedule['actual'])
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                    @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">Projected</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-gray-500 text-center py-4">No depreciation schedule available</p>
                @endif
            </div>

            <!-- Maintenance History -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Maintenance History</h2>
                @if($asset->maintenances->count() > 0)
                <div class="space-y-4">
                    @foreach($asset->maintenances as $maintenance)
                    <div class="border-l-4 {{ $maintenance->status == 'completed' ? 'border-green-500' : ($maintenance->status == 'in_progress' ? 'border-yellow-500' : 'border-gray-300') }} pl-4 py-2">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="px-2 py-1 text-xs rounded-full {{ $maintenance->type_badge }}">{{ ucfirst($maintenance->maintenance_type) }}</span>
                                <p class="font-medium mt-1">{{ $maintenance->description }}</p>
                                <p class="text-sm text-gray-500">Reported: {{ $maintenance->reported_date->format('M d, Y') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="px-2 py-1 text-xs rounded-full {{ $maintenance->status_badge }}">{{ ucfirst($maintenance->status) }}</span>
                                @if($maintenance->cost > 0)
                                <p class="text-sm font-medium mt-1">${{ number_format($maintenance->cost, 2) }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 text-center py-4">No maintenance records</p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Assignment History -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Assignment History</h2>
                @if($asset->assignmentHistories->count() > 0)
                <div class="space-y-4">
                    @foreach($asset->assignmentHistories as $history)
                    <div class="border-l-2 border-blue-300 pl-4 py-2">
                        <p class="text-sm font-medium">{{ $history->action_description }}</p>
                        <p class="text-xs text-gray-500">{{ $history->assigned_at->format('M d, Y H:i') }}</p>
                        <p class="text-xs text-gray-500">By: {{ $history->assigner->name ?? 'System' }}</p>
                        @if($history->notes)
                        <p class="text-xs text-gray-600 mt-1">{{ $history->notes }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 text-center py-4">No assignment history</p>
                @endif
            </div>

            <!-- Quick Info -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Quick Info</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Remaining Life</span>
                        <span class="font-medium">{{ $asset->remaining_useful_life }} years</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Depreciation Method</span>
                        <span class="font-medium">{{ ucwords(str_replace('_', ' ', $asset->category->depreciation_method ?? 'N/A')) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Annual Depreciation</span>
                        <span class="font-medium">${{ number_format($asset->calculateAnnualDepreciation(), 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Created By</span>
                        <span class="font-medium">{{ $asset->creator->name ?? 'System' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Created</span>
                        <span class="font-medium">{{ $asset->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            @if($asset->isDisposed())
            <div class="bg-red-50 rounded-lg border border-red-200 p-6">
                <h2 class="text-lg font-semibold text-red-800 mb-4">Disposal Information</h2>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm text-red-600">Disposed On</span>
                        <p class="font-medium">{{ $asset->disposed_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-red-600">Reason</span>
                        <p class="font-medium">{{ $asset->disposal_reason }}</p>
                    </div>
                    <div>
                        <span class="text-sm text-red-600">Disposal Value</span>
                        <p class="font-medium">${{ number_format($asset->disposal_value, 2) }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
