@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Student Records</h1>
            <p class="mt-2 text-sm text-gray-600">Select a class to view students</p>
        </div>

        <!-- Classes Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($classes as $class)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition-all duration-200 overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 bg-indigo-100 rounded-lg p-3">
                                    <svg class="w-6 h-6 text-indigo-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ $class->class_name }}</h3>
                                    <p class="text-xs text-gray-500">Class #{{ $class->class_numeric }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                </svg>
                                <span class="font-medium">{{ $class->students_count ?? 0 }}</span>
                                <span class="ml-1">{{ Str::plural('Student', $class->students_count ?? 0) }}</span>
                            </div>
                        </div>

                        <a href="{{ route('teacher.class.students', $class->id) }}" class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white text-center font-semibold py-3 px-4 rounded-lg transition-all duration-150 shadow-lg hover:shadow-xl">
                            <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                                </svg>
                                View Students
                            </span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        <p class="text-gray-500 text-lg font-medium">No classes assigned</p>
                        <p class="text-gray-400 text-sm mt-1">You haven't been assigned to any classes yet</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
