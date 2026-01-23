@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center mb-6">
                <a href="{{ route('fee-level-groups.index') }}" class="mr-3 text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h2 class="text-2xl font-bold text-gray-800">Edit Fee Level Group</h2>
            </div>

            @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('fee-level-groups.update', $group->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Group Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $group->name) }}" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                        placeholder="e.g., Junior, Senior, Primary">
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-rose-500 focus:border-rose-500"
                        placeholder="Optional description...">{{ old('description', $group->description) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="min_class_numeric" class="block text-sm font-medium text-gray-700 mb-2">Min Class Level *</label>
                        <select name="min_class_numeric" id="min_class_numeric" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                            <option value="">Select minimum</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->class_numeric }}" {{ old('min_class_numeric', $group->min_class_numeric) == $class->class_numeric ? 'selected' : '' }}>
                                {{ $class->class_name }} (Level {{ $class->class_numeric }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="max_class_numeric" class="block text-sm font-medium text-gray-700 mb-2">Max Class Level *</label>
                        <select name="max_class_numeric" id="max_class_numeric" required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                            <option value="">Select maximum</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->class_numeric }}" {{ old('max_class_numeric', $group->max_class_numeric) == $class->class_numeric ? 'selected' : '' }}>
                                {{ $class->class_name }} (Level {{ $class->class_numeric }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="display_order" class="block text-sm font-medium text-gray-700 mb-2">Display Order</label>
                        <input type="number" name="display_order" id="display_order" value="{{ old('display_order', $group->display_order) }}" min="0"
                            class="w-32 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-rose-500 focus:border-rose-500">
                    </div>
                    <div class="flex items-end">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $group->is_active) ? 'checked' : '' }}
                                class="w-5 h-5 text-rose-600 border-gray-300 rounded focus:ring-rose-500">
                            <span class="ml-2 text-sm font-medium text-gray-700">Active</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('fee-level-groups.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-rose-500 text-white rounded-lg hover:bg-rose-600">
                        Update Level Group
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
