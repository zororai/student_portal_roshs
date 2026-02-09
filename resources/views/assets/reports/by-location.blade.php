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
            <h1 class="text-2xl font-bold text-gray-800">Assets by Location</h1>
            <p class="text-gray-600">Assets grouped by physical location</p>
        </div>
    </div>

    @foreach($locations as $location)
    <div class="bg-white rounded-lg shadow-sm border mb-4">
        <div class="p-4 border-b bg-gray-50">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="font-semibold text-gray-800">{{ $location->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $location->building }} {{ $location->floor ? '- ' . $location->floor : '' }}</p>
                </div>
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                    {{ $location->assets->count() }} assets
                </span>
            </div>
        </div>
        @if($location->assets->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-4 py-2 text-center text-xs font-medium text-gray-500 uppercase">Condition</th>
                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Value</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($location->assets as $asset)
                    <tr>
                        <td class="px-4 py-2 font-mono text-sm">{{ $asset->asset_code }}</td>
                        <td class="px-4 py-2">{{ $asset->name }}</td>
                        <td class="px-4 py-2">{{ $asset->category->name ?? 'N/A' }}</td>
                        <td class="px-4 py-2 text-center">
                            <span class="px-2 py-1 text-xs rounded-full {{ $asset->condition_badge }}">{{ ucfirst($asset->condition) }}</span>
                        </td>
                        <td class="px-4 py-2 text-right">${{ number_format($asset->current_value, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="4" class="px-4 py-2 text-right font-medium">Total Value:</td>
                        <td class="px-4 py-2 text-right font-semibold">${{ number_format($location->assets->sum('current_value'), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="p-4 text-center text-gray-500">No assets at this location</div>
        @endif
    </div>
    @endforeach
</div>
@endsection
