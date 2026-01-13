@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Scheme Evaluation Report</h1>
            <p class="mt-2 text-sm text-gray-600">{{ $reportData['scheme']->title }}</p>
        </div>
        <div class="flex space-x-3">
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Report
            </button>
            <a href="{{ route('teacher.schemes.show', $reportData['scheme']->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg">
                Back to Scheme
            </a>
        </div>
    </div>

    <!-- Report Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8 print:shadow-none print:border-0">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div>
                <p class="text-sm text-gray-500">Subject</p>
                <p class="font-semibold text-gray-900">{{ $reportData['scheme']->subject->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Class</p>
                <p class="font-semibold text-gray-900">{{ $reportData['scheme']->class->class_name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Term</p>
                <p class="font-semibold text-gray-900">{{ $reportData['scheme']->term }} - {{ $reportData['scheme']->academic_year }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Teacher</p>
                <p class="font-semibold text-gray-900">{{ $reportData['scheme']->teacher->user->name ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-8">
        <div class="bg-blue-50 rounded-xl p-4 text-center">
            <p class="text-3xl font-bold text-blue-600">{{ $reportData['summary']['total_topics'] }}</p>
            <p class="text-sm text-blue-800">Total Topics</p>
        </div>
        <div class="bg-green-50 rounded-xl p-4 text-center">
            <p class="text-3xl font-bold text-green-600">{{ $reportData['summary']['completed_topics'] }}</p>
            <p class="text-sm text-green-800">Completed</p>
        </div>
        <div class="bg-red-50 rounded-xl p-4 text-center">
            <p class="text-3xl font-bold text-red-600">{{ $reportData['summary']['weak_topics'] }}</p>
            <p class="text-sm text-red-800">Weak Topics</p>
        </div>
        <div class="bg-orange-50 rounded-xl p-4 text-center">
            <p class="text-3xl font-bold text-orange-600">{{ $reportData['summary']['needs_remedial'] }}</p>
            <p class="text-sm text-orange-800">Needs Remedial</p>
        </div>
        <div class="bg-purple-50 rounded-xl p-4 text-center">
            <p class="text-3xl font-bold text-purple-600">{{ $reportData['summary']['progress_percentage'] }}%</p>
            <p class="text-sm text-purple-800">Progress</p>
        </div>
        <div class="bg-indigo-50 rounded-xl p-4 text-center">
            <p class="text-3xl font-bold {{ ($reportData['summary']['performance_gap'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                {{ $reportData['summary']['performance_gap'] !== null ? (($reportData['summary']['performance_gap'] >= 0 ? '+' : '') . $reportData['summary']['performance_gap'] . '%') : '--' }}
            </p>
            <p class="text-sm text-indigo-800">Performance Gap</p>
        </div>
    </div>

    <!-- Topic Heatmap -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8 print:shadow-none">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Topic Performance Heatmap</h2>
        <div class="grid grid-cols-3 md:grid-cols-6 gap-2">
            @foreach($reportData['heatmap'] as $topic)
                <div class="p-2 rounded text-center {{ $topic['mastery_level'] == 'mastered' ? 'bg-green-100' : ($topic['mastery_level'] == 'partial' ? 'bg-yellow-100' : ($topic['mastery_level'] == 'weak' ? 'bg-red-100' : 'bg-gray-100')) }}">
                    <p class="text-xs text-gray-600 truncate" title="{{ $topic['topic_name'] }}">{{ Str::limit($topic['topic_name'], 15) }}</p>
                    <p class="text-lg font-bold {{ $topic['mastery_level'] == 'mastered' ? 'text-green-700' : ($topic['mastery_level'] == 'partial' ? 'text-yellow-700' : ($topic['mastery_level'] == 'weak' ? 'text-red-700' : 'text-gray-400')) }}">
                        {{ $topic['average_score'] !== null ? number_format($topic['average_score'], 0) . '%' : '--' }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Detailed Topic Analysis -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden print:shadow-none">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Detailed Topic Analysis</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Topic</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Periods (P/A)</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Expected</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Actual</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Gap</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Mastery</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600">Remedials</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($reportData['topics'] as $topic)
                        <tr class="{{ $topic['remedial_required'] ? 'bg-red-50' : '' }}">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $topic['name'] }}</td>
                            <td class="px-4 py-3 text-center">{{ $topic['planned_periods'] }}/{{ $topic['actual_periods'] }}</td>
                            <td class="px-4 py-3 text-center">{{ $topic['expected_performance'] ? $topic['expected_performance'] . '%' : '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="{{ $topic['actual_performance'] !== null ? ($topic['actual_performance'] >= 75 ? 'text-green-600' : ($topic['actual_performance'] >= 50 ? 'text-yellow-600' : 'text-red-600')) : 'text-gray-400' }} font-medium">
                                    {{ $topic['actual_performance'] !== null ? number_format($topic['actual_performance'], 1) . '%' : '--' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($topic['performance_gap'] !== null)
                                    <span class="{{ $topic['performance_gap'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $topic['performance_gap'] >= 0 ? '+' : '' }}{{ $topic['performance_gap'] }}%
                                    </span>
                                @else
                                    <span class="text-gray-400">--</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $topic['mastery_level'] == 'mastered' ? 'bg-green-100 text-green-800' : ($topic['mastery_level'] == 'partial' ? 'bg-yellow-100 text-yellow-800' : ($topic['mastery_level'] == 'weak' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                    {{ ucfirst($topic['mastery_level']) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $topic['status'] == 'completed' ? 'bg-green-100 text-green-800' : ($topic['status'] == 'in_progress' ? 'bg-blue-100 text-blue-800' : ($topic['status'] == 'needs_remedial' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                    {{ ucfirst(str_replace('_', ' ', $topic['status'])) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">{{ $topic['remedials_count'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Report Footer -->
    <div class="mt-8 text-center text-sm text-gray-500 print:mt-4">
        <p>Report generated on {{ now()->format('F d, Y \a\t H:i') }}</p>
        <p>{{ config('app.name') }} - Data-Driven Schemes of Work System</p>
    </div>
</div>

<style>
@media print {
    .print\:shadow-none { box-shadow: none !important; }
    .print\:border-0 { border: none !important; }
    .print\:mt-4 { margin-top: 1rem !important; }
}
</style>
@endsection
