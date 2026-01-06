@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Class</h1>
                    <p class="mt-1 text-sm text-gray-500">Update class information and teacher assignment</p>
                </div>
                <a href="{{ route('classes.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 hover:shadow-md transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Classes
                </a>
            </div>

            <form action="{{ route('classes.update', $class->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Class Info Card -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-violet-500 to-purple-600">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Class Details
                        </h3>
                    </div>
                    <div class="px-6 py-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Class Name</label>
                                <div class="relative">
                                    <select name="class_name" id="class-name-select" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors duration-200 appearance-none bg-white">
                                        <option value="">-- Select Class Name --</option>
                                        @foreach ($classFormats as $format)
                                            <option value="{{ $format->display_name }}" data-numeric="{{ $format->numeric_value }}" {{ $class->class_name == $format->display_name ? 'selected' : '' }}>{{ $format->display_name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                                @error('class_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Class Numeric</label>
                                <input name="class_numeric" type="number" value="{{ $class->class_numeric }}" 
                                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors duration-200"
                                       placeholder="Auto-filled from class name">
                                @error('class_numeric')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Teacher Assignment Card -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Class Teacher
                        </h3>
                    </div>
                    <div class="px-6 py-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Assign Teacher</label>
                        <div class="relative">
                            <select name="teacher_id" class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition-colors duration-200 appearance-none bg-white">
                                <option value="">-- Select Teacher --</option>
                                @foreach ($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ ($teacher->id === $class->teacher_id) ? 'selected' : '' }}>
                                        {{ $teacher->user->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                        @error('teacher_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('classes.index') }}" class="px-6 py-3 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors duration-200">
                        Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 rounded-lg bg-gradient-to-r from-violet-500 to-purple-600 text-gray-50 font-semibold shadow-lg hover:from-violet-600 hover:to-purple-700 hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Class
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('class-name-select').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var numericValue = selectedOption.getAttribute('data-numeric');
            if (numericValue) {
                document.querySelector('input[name="class_numeric"]').value = numericValue;
            }
        });
    </script>
@endsection