@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center mb-6">
        <a href="{{ route('finance.assets.locations') }}" class="text-gray-600 hover:text-gray-800 mr-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Location</h1>
            <p class="text-gray-600">{{ $location->name }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border p-6 max-w-2xl">
        <form action="{{ route('finance.assets.locations.update', $location) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Location Name *</label>
                    <input type="text" name="name" value="{{ old('name', $location->name) }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Building</label>
                    <input type="text" name="building" value="{{ old('building', $location->building) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Floor</label>
                    <input type="text" name="floor" value="{{ old('floor', $location->floor) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('description', $location->description) }}</textarea>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $location->is_active) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                    <label class="ml-2 text-sm text-gray-700">Active</label>
                </div>
            </div>

            <div class="mt-6 flex justify-end gap-4">
                <a href="{{ route('finance.assets.locations') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Update Location</button>
            </div>
        </form>
    </div>
</div>
@endsection
