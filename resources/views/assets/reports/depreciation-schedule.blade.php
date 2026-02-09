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
            <h1 class="text-2xl font-bold text-gray-800">Depreciation Schedule</h1>
            <p class="text-gray-600">Projected depreciation for all assets</p>
        </div>
    </div>

    @foreach($assets as $asset)
    @if(count($asset->depreciation_schedule) > 0)
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-4">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h3 class="font-semibold text-gray-800">{{ $asset->name }}</h3>
                <p class="text-sm text-gray-500">{{ $asset->asset_code }} | {{ $asset->category->name ?? 'N/A' }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Purchase Cost</p>
                <p class="font-semibold">${{ number_format($asset->purchase_cost, 2) }}</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500">Year</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Opening</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Depreciation</th>
                        <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">Closing</th>
                        <th class="px-3 py-2 text-center text-xs font-medium text-gray-500">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($asset->depreciation_schedule as $schedule)
                    <tr class="{{ $schedule['actual'] ? 'bg-blue-50' : '' }}">
                        <td class="px-3 py-2">{{ $schedule['year'] }}</td>
                        <td class="px-3 py-2 text-right">${{ number_format($schedule['opening_value'], 2) }}</td>
                        <td class="px-3 py-2 text-right text-red-600">${{ number_format($schedule['depreciation'], 2) }}</td>
                        <td class="px-3 py-2 text-right">${{ number_format($schedule['closing_value'], 2) }}</td>
                        <td class="px-3 py-2 text-center">
                            @if($schedule['posted'])
                            <span class="text-xs text-green-600">Posted</span>
                            @elseif($schedule['actual'])
                            <span class="text-xs text-yellow-600">Calculated</span>
                            @else
                            <span class="text-xs text-gray-400">Projected</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    @endforeach
</div>
@endsection
