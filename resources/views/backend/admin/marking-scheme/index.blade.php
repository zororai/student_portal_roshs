@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Marking Scheme</h1>
                    <p class="mt-2 text-sm text-gray-600">View assessment marks for all classes</p>
                </div>
            </div>
        </div>

        <!-- Classes Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($classes as $class)
                <a href="{{ route('admin.marking-scheme.assessments', $class->id) }}" class="block bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-blue-300 transition-all duration-200">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div class="flex flex-col items-end space-y-1">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">
                                    {{ $class->assessments_count }} Assessments
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                    {{ $class->students_count }} Students
                                </span>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $class->class_name }}</h3>
                        <p class="text-sm text-gray-600 mb-3">{{ $class->class_description ?? 'No description' }}</p>
                        @if($class->teacher)
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Teacher: {{ $class->teacher->user->name ?? 'Not Assigned' }}
                            </div>
                        @endif
                    </div>
                    <div class="px-6 py-3 bg-gray-50 rounded-b-xl border-t border-gray-100">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-blue-600 font-medium">View Assessments</span>
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                        </svg>
                        <p class="text-gray-500 text-lg font-medium">No assessments found</p>
                        <p class="text-gray-400 text-sm mt-1">No classes have assessments yet. Teachers need to create assessments first.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
