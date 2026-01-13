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
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $scheme->title }}</h1>
                <p class="mt-2 text-sm text-gray-600">
                    <span class="font-medium">{{ $scheme->teacher->user->name ?? 'Unknown Teacher' }}</span> • 
                    {{ $scheme->subject->name ?? 'N/A' }} • 
                    {{ $scheme->class->class_name ?? 'N/A' }} • 
                    {{ $scheme->term }} {{ $scheme->academic_year }}
                </p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                @if($scheme->status == 'active') bg-green-100 text-green-800
                @elseif($scheme->status == 'draft') bg-yellow-100 text-yellow-800
                @elseif($scheme->status == 'completed') bg-blue-100 text-blue-800
                @else bg-gray-100 text-gray-800
                @endif">
                {{ ucfirst($scheme->status) }}
            </span>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500 uppercase">Progress</p>
            <p class="text-2xl font-bold text-blue-600">{{ $scheme->progress_percentage }}%</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500 uppercase">Topics</p>
            <p class="text-2xl font-bold text-gray-900">{{ $scheme->schemeTopics->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500 uppercase">Completed</p>
            <p class="text-2xl font-bold text-green-600">{{ $scheme->completed_topics_count }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500 uppercase">Assessments</p>
            <p class="text-2xl font-bold text-purple-600">{{ $assessments->count() }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
            <p class="text-xs font-medium text-gray-500 uppercase">Avg Performance</p>
            <p class="text-2xl font-bold {{ ($scheme->actual_performance ?? 0) >= 50 ? 'text-green-600' : 'text-red-600' }}">
                {{ $scheme->actual_performance ? number_format($scheme->actual_performance, 1) . '%' : '--' }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Topics List -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Scheme Topics</h2>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($scheme->schemeTopics as $topic)
                        <div class="p-4 {{ $topic->status == 'needs_remedial' ? 'bg-red-50' : '' }}">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-medium text-gray-900">{{ $topic->syllabusTopic->name ?? 'Unknown Topic' }}</h4>
                                        @if($topic->week_number)
                                            <span class="text-xs text-gray-500">Week {{ $topic->week_number }}</span>
                                        @endif
                                    </div>
                                    <div class="mt-2 flex items-center gap-4 text-sm text-gray-600">
                                        <span>Planned: {{ $topic->planned_periods }} periods</span>
                                        <span>Actual: {{ $topic->actual_periods ?? 0 }} periods</span>
                                        @if($topic->expected_performance)
                                            <span>Expected: {{ $topic->expected_performance }}%</span>
                                        @endif
                                        @if($topic->actual_performance)
                                            <span class="{{ $topic->actual_performance >= 50 ? 'text-green-600' : 'text-red-600' }} font-medium">
                                                Actual: {{ number_format($topic->actual_performance, 1) }}%
                                            </span>
                                        @endif
                                    </div>
                                    @if($topic->remarks)
                                        <p class="mt-2 text-sm text-gray-500 italic">{{ $topic->remarks }}</p>
                                    @endif
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium 
                                    @if($topic->status == 'completed') bg-green-100 text-green-800
                                    @elseif($topic->status == 'in_progress') bg-blue-100 text-blue-800
                                    @elseif($topic->status == 'needs_remedial') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst(str_replace('_', ' ', $topic->status)) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500">
                            No topics in this scheme
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Linked Assessments -->
        <div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Linked Assessments</h2>
                </div>
                <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
                    @forelse($assessments as $assessment)
                        <div class="p-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900 text-sm">{{ $assessment->topic }}</h4>
                                    <p class="text-xs text-gray-500">{{ $assessment->assessment_type }} • {{ $assessment->date->format('M d, Y') }}</p>
                                    @if($assessment->syllabusTopic)
                                        <p class="text-xs text-purple-600 mt-1">{{ $assessment->syllabusTopic->name }}</p>
                                    @endif
                                </div>
                                <span class="text-xs text-gray-500">{{ $assessment->marks->count() }} marks</span>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-gray-500 text-sm">
                            No assessments linked to this scheme's topics
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Teacher Info -->
            <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">Teacher Information</h3>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                        <span class="text-blue-600 font-bold text-lg">{{ substr($scheme->teacher->user->name ?? 'U', 0, 1) }}</span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">{{ $scheme->teacher->user->name ?? 'Unknown' }}</p>
                        <p class="text-sm text-gray-500">{{ $scheme->teacher->user->email ?? '' }}</p>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="{{ route('admin.schemes.teacher', $scheme->teacher_id) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View all schemes by this teacher →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
