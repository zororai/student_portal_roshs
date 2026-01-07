@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                My Subjects
            </h1>
            <p class="text-gray-500 mt-1 ml-13">Access your subjects and learning materials</p>
        </div>
    </div>

    <!-- Subjects Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse ($subjects as $subject)
            <a href="{{ route('subject.Reading', $subject->id) }}" class="group">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:scale-105 transition-all duration-300">
                    <!-- Subject Header with Gradient -->
                    @php
                        $gradientStyles = [
                            'background: linear-gradient(to bottom right, #3b82f6, #4f46e5);',
                            'background: linear-gradient(to bottom right, #a855f7, #ec4899);',
                            'background: linear-gradient(to bottom right, #10b981, #14b8a6);',
                            'background: linear-gradient(to bottom right, #f97316, #ef4444);',
                            'background: linear-gradient(to bottom right, #06b6d4, #3b82f6);',
                            'background: linear-gradient(to bottom right, #f59e0b, #f97316);'
                        ];
                        $gradientStyle = $gradientStyles[$loop->index % 6];
                    @endphp
                    <div class="h-32 relative" style="{{ $gradientStyle }}">
                        <div class="absolute inset-0 bg-black bg-opacity-10"></div>
                        <div class="relative h-full flex items-center justify-center">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto mb-2">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                <p class="text-white text-xs font-semibold uppercase tracking-wider">{{ $subject->subject_code }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Subject Details -->
                    <div class="p-5">
                        <h3 class="font-bold text-gray-800 text-lg mb-3 group-hover:text-blue-600 transition-colors truncate">
                            {{ $subject->name }}
                        </h3>
                        
                        <!-- Teacher Info -->
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-8 h-8 bg-gradient-to-br from-gray-200 to-gray-300 rounded-full flex items-center justify-center">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-gray-500">Teacher</p>
                                <p class="text-sm font-medium text-gray-700 truncate">{{ $subject->teacher->user->name ?? 'Not Assigned' }}</p>
                            </div>
                        </div>
                        
                        <!-- Materials Count -->
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                            <span class="text-xs text-gray-500 font-medium">Learning Materials</span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                {{ $subject->readings_count ?? 0 }}
                            </span>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="col-span-full">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 py-16">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        <p class="text-gray-500 text-lg font-medium">No subjects found</p>
                        <p class="text-gray-400 text-sm mt-2">You don't have any subjects assigned yet</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($subjects->hasPages())
        <div class="mt-8">
            {{ $subjects->links() }}
        </div>
    @endif
</div>
@endsection
