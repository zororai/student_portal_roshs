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
                <h1 class="text-2xl font-bold text-gray-800">Asset Locations</h1>
                <p class="text-gray-600">Manage physical asset locations</p>
            </div>
        </div>
        <a href="{{ route('finance.assets.locations.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add Location
        </a>
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Building</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Floor</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Assets</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($locations as $location)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-800">{{ $location->name }}</div>
                        @if($location->description)
                        <div class="text-sm text-gray-500">{{ Str::limit($location->description, 50) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">{{ $location->building ?? 'N/A' }}</td>
                    <td class="px-6 py-4">{{ $location->floor ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">{{ $location->assets_count }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($location->is_active)
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                        @else
                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Inactive</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('finance.assets.locations.edit', $location) }}" class="text-yellow-600 hover:text-yellow-800">
                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <p>No locations found</p>
                        <a href="{{ route('finance.assets.locations.create') }}" class="text-blue-600 hover:underline mt-2 inline-block">Add your first location</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
