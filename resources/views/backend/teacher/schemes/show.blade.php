@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <div class="flex items-center space-x-3">
                    <h1 class="text-3xl font-bold text-gray-900">{{ $scheme->title }}</h1>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                        @if($scheme->status == 'active') bg-green-100 text-green-800
                        @elseif($scheme->status == 'draft') bg-yellow-100 text-yellow-800
                        @elseif($scheme->status == 'completed') bg-blue-100 text-blue-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst($scheme->status) }}
                    </span>
                </div>
                <p class="mt-2 text-sm text-gray-600">
                    {{ $scheme->subject->name ?? 'N/A' }} | {{ $scheme->class->class_name ?? 'N/A' }} | {{ $scheme->term }} - {{ $scheme->academic_year }}
                </p>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('teacher.schemes.evaluation-report', $scheme->id) }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Report
                </a>
                <a href="{{ route('teacher.schemes.edit', $scheme->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg">
                    Edit
                </a>
                <a href="{{ route('teacher.schemes.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg">
                    Back
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-500">Progress</p>
            <p class="text-2xl font-bold text-gray-900">{{ $scheme->progress_percentage }}%</p>
            <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                <div class="h-2 rounded-full bg-blue-500" style="width: {{ min($scheme->progress_percentage, 100) }}%"></div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-500">Performance</p>
            <p class="text-2xl font-bold {{ ($scheme->actual_performance ?? 0) >= 75 ? 'text-green-600' : (($scheme->actual_performance ?? 0) >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                {{ $scheme->actual_performance ? number_format($scheme->actual_performance, 1) . '%' : '--' }}
            </p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-500">Topics</p>
            <p class="text-2xl font-bold text-gray-900">{{ $scheme->completed_topics_count }}/{{ $scheme->topics_count }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-500">Weak Topics</p>
            <p class="text-2xl font-bold text-red-600">{{ $scheme->weak_topics_count }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
            <p class="text-sm font-medium text-gray-500">Needs Remedial</p>
            <p class="text-2xl font-bold text-orange-600">{{ $scheme->needs_remedial_count }}</p>
        </div>
    </div>

    <!-- Topic Performance Heatmap -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Topic Performance Heatmap</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3">
            @foreach($heatmapData as $topic)
                <div class="p-3 rounded-lg border-2 
                    @if($topic['mastery_level'] == 'mastered') border-green-200 bg-green-50
                    @elseif($topic['mastery_level'] == 'partial') border-yellow-200 bg-yellow-50
                    @elseif($topic['mastery_level'] == 'weak') border-red-200 bg-red-50
                    @else border-gray-200 bg-gray-50 @endif">
                    <p class="text-xs font-medium text-gray-900 truncate" title="{{ $topic['topic_name'] }}">{{ $topic['topic_name'] }}</p>
                    <p class="text-lg font-bold mt-1
                        @if($topic['mastery_level'] == 'mastered') text-green-600
                        @elseif($topic['mastery_level'] == 'partial') text-yellow-600
                        @elseif($topic['mastery_level'] == 'weak') text-red-600
                        @else text-gray-400 @endif">
                        {{ $topic['average_score'] !== null ? number_format($topic['average_score'], 0) . '%' : '--' }}
                    </p>
                    <p class="text-xs text-gray-500">{{ $topic['students_assessed'] }} assessed</p>
                </div>
            @endforeach
        </div>
        <div class="mt-4 flex items-center space-x-6 text-xs">
            <div class="flex items-center"><span class="w-3 h-3 rounded bg-green-500 mr-2"></span> Mastered (75%+)</div>
            <div class="flex items-center"><span class="w-3 h-3 rounded bg-yellow-500 mr-2"></span> Partial (50-74%)</div>
            <div class="flex items-center"><span class="w-3 h-3 rounded bg-red-500 mr-2"></span> Weak (&lt;50%)</div>
            <div class="flex items-center"><span class="w-3 h-3 rounded bg-gray-300 mr-2"></span> Not Assessed</div>
        </div>
    </div>

    <!-- Scheme Topics Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Scheme Topics</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Topic</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Week</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Periods</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Expected</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actual</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Mastery</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($scheme->schemeTopics as $topic)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $topic->syllabusTopic->name ?? 'Unknown' }}</div>
                                @if($topic->remedial_required)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800 mt-1">
                                        Remedial Required
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-600">{{ $topic->week_number ?? '-' }}</td>
                            <td class="px-6 py-4 text-center text-sm">
                                <span class="text-gray-900">{{ $topic->actual_periods }}</span>
                                <span class="text-gray-400">/{{ $topic->planned_periods }}</span>
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-600">{{ $topic->expected_performance ? $topic->expected_performance . '%' : '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                @if($topic->actual_performance !== null)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $topic->actual_performance >= 75 ? 'bg-green-100 text-green-800' : ($topic->actual_performance >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ number_format($topic->actual_performance, 1) }}%
                                    </span>
                                @else
                                    <span class="text-gray-400">--</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($topic->mastery_level == 'mastered') bg-green-100 text-green-800
                                    @elseif($topic->mastery_level == 'partial') bg-yellow-100 text-yellow-800
                                    @elseif($topic->mastery_level == 'weak') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($topic->mastery_level) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($topic->status == 'completed') bg-green-100 text-green-800
                                    @elseif($topic->status == 'in_progress') bg-blue-100 text-blue-800
                                    @elseif($topic->status == 'needs_remedial') bg-red-100 text-red-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $topic->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($topic->remedial_required && $topic->remedialLessons->where('status', '!=', 'completed')->count() == 0)
                                    <button onclick="openRemedialModal({{ $topic->id }})" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                        + Remedial
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Remedial Lessons -->
    @if($remedialLessons->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Remedial Lessons</h2>
            </div>
            <div class="divide-y divide-gray-100">
                @foreach($remedialLessons as $remedial)
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $remedial->title }}</h3>
                                <p class="text-sm text-gray-500 mt-1">{{ $remedial->syllabusTopic->name ?? 'Unknown Topic' }}</p>
                                <div class="mt-2 flex items-center space-x-4 text-sm">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($remedial->status == 'completed') bg-green-100 text-green-800
                                        @elseif($remedial->status == 'scheduled') bg-yellow-100 text-yellow-800
                                        @elseif($remedial->status == 'in_progress') bg-blue-100 text-blue-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($remedial->status) }}
                                    </span>
                                    <span class="text-gray-500">{{ $remedial->students_count }} students</span>
                                    @if($remedial->scheduled_date)
                                        <span class="text-gray-500">{{ $remedial->scheduled_date->format('M d, Y') }}</span>
                                    @endif
                                </div>
                                @if($remedial->improvement !== null)
                                    <p class="mt-2 text-sm">
                                        <span class="text-gray-500">Improvement:</span>
                                        <span class="{{ $remedial->improvement >= 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                                            {{ $remedial->improvement >= 0 ? '+' : '' }}{{ $remedial->improvement }}%
                                        </span>
                                    </p>
                                @endif
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $remedial->trigger_type == 'auto' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $remedial->trigger_type == 'auto' ? 'Auto-Triggered' : 'Manual' }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
