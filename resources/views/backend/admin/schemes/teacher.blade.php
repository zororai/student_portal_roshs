@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <a href="{{ route('admin.schemes.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to All Schemes
        </a>
        <div class="flex items-center">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                <span class="text-blue-600 font-bold text-2xl">{{ substr($teacher->user->name ?? 'U', 0, 1) }}</span>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $teacher->user->name ?? 'Unknown Teacher' }}</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $teacher->user->email ?? '' }}</p>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-500">Total Schemes</p>
            <p class="text-2xl font-bold text-gray-900">{{ $schemes->total() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-500">Active Schemes</p>
            <p class="text-2xl font-bold text-green-600">{{ $schemes->where('status', 'active')->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-500">Syllabus Topics</p>
            <p class="text-2xl font-bold text-purple-600">{{ $syllabusTopics->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-500">Subjects Taught</p>
            <p class="text-2xl font-bold text-blue-600">{{ $teacher->subjects->count() }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Schemes List -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Schemes of Work</h2>
                </div>

                @if($schemes->count() > 0)
                    <div class="divide-y divide-gray-100">
                        @foreach($schemes as $scheme)
                            <div class="p-4 hover:bg-gray-50">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-medium text-gray-900">{{ $scheme->title }}</h4>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                                @if($scheme->status == 'active') bg-green-100 text-green-800
                                                @elseif($scheme->status == 'draft') bg-yellow-100 text-yellow-800
                                                @elseif($scheme->status == 'completed') bg-blue-100 text-blue-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($scheme->status) }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-500 mt-1">
                                            {{ $scheme->subject->name ?? 'N/A' }} • {{ $scheme->class->class_name ?? 'N/A' }} • {{ $scheme->term }}
                                        </p>
                                        <div class="mt-2 flex items-center gap-4 text-sm">
                                            <span class="text-gray-600">Topics: {{ $scheme->schemeTopics->count() }}</span>
                                            <span class="text-gray-600">Progress: {{ $scheme->progress_percentage }}%</span>
                                            @if($scheme->assessments_count > 0)
                                                <span class="text-purple-600">{{ $scheme->assessments_count }} assessments</span>
                                            @endif
                                        </div>
                                    </div>
                                    <a href="{{ route('admin.schemes.show', $scheme->id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        View →
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $schemes->links() }}
                    </div>
                @else
                    <div class="p-8 text-center text-gray-500">
                        This teacher hasn't created any schemes yet
                    </div>
                @endif
            </div>
        </div>

        <!-- Syllabus Topics -->
        <div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Available Syllabus Topics</h2>
                    <p class="text-xs text-gray-500 mt-1">Topics for subjects this teacher teaches</p>
                </div>
                <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
                    @forelse($syllabusTopics as $topic)
                        <div class="p-3">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900 text-sm">{{ $topic->name }}</h4>
                                    <p class="text-xs text-gray-500">{{ $topic->subject->subject_code ?? '' }} - {{ $topic->subject->name ?? 'N/A' }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-xs text-gray-400">{{ $topic->term }}</span>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium 
                                            @if($topic->difficulty_level == 'easy') bg-green-100 text-green-700
                                            @elseif($topic->difficulty_level == 'medium') bg-yellow-100 text-yellow-700
                                            @else bg-red-100 text-red-700
                                            @endif">
                                            {{ ucfirst($topic->difficulty_level) }}
                                        </span>
                                    </div>
                                </div>
                                @if($topic->is_active)
                                    <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                @else
                                    <span class="w-2 h-2 bg-gray-300 rounded-full"></span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500 text-sm">
                            No syllabus topics found for this teacher's subjects
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Subjects Taught -->
            <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Subjects Taught</h3>
                <div class="space-y-2">
                    @forelse($teacher->subjects as $subject)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0">
                            <span class="text-sm text-gray-900">{{ $subject->name }}</span>
                            <span class="text-xs text-gray-500">{{ $subject->subject_code ?? '' }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No subjects assigned</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
