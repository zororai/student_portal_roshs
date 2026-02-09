@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Asset Management</h1>
            <p class="text-gray-600">Track and manage school assets</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('finance.assets.categories') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                </svg>
                Categories
            </a>
            <a href="{{ route('finance.assets.locations') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Locations
            </a>
            <a href="{{ route('finance.assets.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Asset
            </a>
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

    <!-- Quick Links -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <a href="{{ route('finance.assets.maintenance') }}" class="bg-white rounded-lg shadow-sm border p-4 hover:shadow-md transition">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg mr-3">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Maintenance</div>
                    <div class="font-semibold text-gray-800">Records</div>
                </div>
            </div>
        </a>
        <a href="{{ route('finance.assets.depreciation') }}" class="bg-white rounded-lg shadow-sm border p-4 hover:shadow-md transition">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg mr-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                    </svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Depreciation</div>
                    <div class="font-semibold text-gray-800">Schedule</div>
                </div>
            </div>
        </a>
        <a href="{{ route('finance.assets.reports') }}" class="bg-white rounded-lg shadow-sm border p-4 hover:shadow-md transition">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg mr-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Asset</div>
                    <div class="font-semibold text-gray-800">Reports</div>
                </div>
            </div>
        </a>
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg mr-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500">Total Assets</div>
                    <div class="font-semibold text-gray-800">{{ $assets->total() }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search code, name, serial..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <select name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="location_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Locations</option>
                    @foreach($locations as $loc)
                    <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="under_maintenance" {{ request('status') == 'under_maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                    <option value="disposed" {{ request('status') == 'disposed' ? 'selected' : '' }}>Disposed</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Filter</button>
                <a href="{{ route('finance.assets.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300">Reset</a>
            </div>
        </form>
    </div>

    <!-- Assets Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asset</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Value</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Condition</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($assets as $asset)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div>
                                <div class="font-medium text-gray-800">{{ $asset->name }}</div>
                                <div class="text-sm text-gray-500">{{ $asset->asset_code }}</div>
                                @if($asset->serial_number)
                                <div class="text-xs text-gray-400">S/N: {{ $asset->serial_number }}</div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-700">{{ $asset->category->name ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-700">{{ $asset->location->name ?? 'Unassigned' }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="font-semibold text-gray-800">${{ number_format($asset->current_value, 2) }}</div>
                            <div class="text-xs text-gray-500">Cost: ${{ number_format($asset->purchase_cost, 2) }}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 text-xs rounded-full {{ $asset->condition_badge }}">
                                {{ ucfirst($asset->condition) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 text-xs rounded-full {{ $asset->status_badge }}">
                                {{ ucwords(str_replace('_', ' ', $asset->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('finance.assets.show', $asset) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                @if(!$asset->isDisposed())
                                <a href="{{ route('finance.assets.edit', $asset) }}" class="text-yellow-600 hover:text-yellow-800" title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <p>No assets found</p>
                            <a href="{{ route('finance.assets.create') }}" class="text-blue-600 hover:underline mt-2 inline-block">Add your first asset</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $assets->withQueryString()->links() }}
    </div>
</div>
@endsection
