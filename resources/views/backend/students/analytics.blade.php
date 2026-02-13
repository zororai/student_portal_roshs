@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Student Analytics</h1>
                <p class="mt-2 text-sm text-gray-600">Compare student assessment performance vs term results for each subject</p>
            </div>
            @if($selectedClassId)
            <a href="{{ route('admin.student-analytics.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Classes
            </a>
            @endif
        </div>
    </div>

    <!-- Year/Term Filter (always visible) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <form method="GET" action="{{ route('admin.student-analytics.index') }}" id="termFilterForm" class="flex items-center space-x-4">
            @if($selectedClassId)
            <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
            @endif
            @if($selectedStudentId)
            <input type="hidden" name="student_id" value="{{ $selectedStudentId }}">
            @endif
            <div class="flex items-center space-x-2">
                <label class="text-sm font-medium text-gray-700">Year:</label>
                <select name="year" id="yearFilter" onchange="this.form.submit()" class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @php
                        $currentYearVal = date('Y');
                        for($y = $currentYearVal; $y >= $currentYearVal - 5; $y--) {
                            $selected = $selectedYear == $y ? 'selected' : '';
                            echo "<option value=\"$y\" $selected>$y</option>";
                        }
                    @endphp
                </select>
            </div>
            <div class="flex items-center space-x-2">
                <label class="text-sm font-medium text-gray-700">Term:</label>
                <select name="term" id="termFilter" onchange="this.form.submit()" class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="first" {{ $selectedTerm == 'first' ? 'selected' : '' }}>Term 1</option>
                    <option value="second" {{ $selectedTerm == 'second' ? 'selected' : '' }}>Term 2</option>
                    <option value="third" {{ $selectedTerm == 'third' ? 'selected' : '' }}>Term 3</option>
                </select>
            </div>
            <button type="submit" class="inline-flex items-center px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filter
            </button>
        </form>
    </div>

    @if(!$selectedClassId)
    <!-- CLASS CARDS VIEW -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($classes as $class)
        <a href="{{ route('admin.student-analytics.index', ['class_id' => $class->id, 'year' => $selectedYear, 'term' => $selectedTerm]) }}" 
           class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-blue-300 transition-all transform hover:-translate-y-1 p-6">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                    {{ $class->students_count ?? 0 }} students
                </span>
            </div>
            <h3 class="text-lg font-bold text-gray-900">{{ $class->class_name }}</h3>
            <p class="text-sm text-gray-500 mt-1">Click to view students</p>
        </a>
        @endforeach
    </div>

    @elseif($selectedClassId && !$selectedStudentId)
    <!-- STUDENT CARDS VIEW -->
    <div class="mb-6">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $selectedClass->class_name ?? 'Class' }}</h2>
                <p class="text-sm text-gray-500">{{ $students->count() }} students - Select a student to view analytics</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse($students as $student)
        <a href="{{ route('admin.student-analytics.index', ['class_id' => $selectedClassId, 'student_id' => $student->id, 'year' => $selectedYear, 'term' => $selectedTerm]) }}" 
           class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-green-300 transition-all transform hover:-translate-y-1 p-4">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-emerald-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-lg font-bold text-white">{{ substr($student->user->name ?? 'S', 0, 1) }}</span>
                </div>
                <div class="min-w-0">
                    <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $student->user->name ?? 'N/A' }}</h3>
                    <p class="text-xs text-gray-500">{{ $student->roll_number }}</p>
                </div>
            </div>
            <div class="mt-3 flex items-center justify-between">
                <span class="text-xs text-gray-400">Click for analytics</span>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </div>
        </a>
        @empty
        <div class="col-span-full bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
            <svg class="w-12 h-12 text-yellow-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <p class="text-yellow-700 font-medium">No students found in this class</p>
        </div>
        @endforelse
    </div>

    @else
    <!-- STUDENT ANALYTICS VIEW -->
    <div class="mb-6">
        <a href="{{ route('admin.student-analytics.index', ['class_id' => $selectedClassId, 'year' => $selectedYear, 'term' => $selectedTerm]) }}" 
           class="inline-flex items-center text-sm text-blue-600 hover:text-blue-800 mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to {{ $selectedClass->class_name ?? 'Class' }} Students
        </a>
    </div>

    @if($analyticsData)
    <!-- Student Info Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center">
            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mr-4">
                <span class="text-2xl font-bold text-white">{{ substr($analyticsData['student']->user->name ?? 'S', 0, 1) }}</span>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">{{ $analyticsData['student']->user->name ?? 'N/A' }}</h2>
                <p class="text-sm text-gray-600">
                    {{ $analyticsData['student']->class->class_name ?? 'N/A' }} | 
                    Roll Number: {{ $analyticsData['student']->roll_number }} | 
                    @php
                        $termLabels = ['first' => 'Term 1', 'second' => 'Term 2', 'third' => 'Term 3'];
                    @endphp
                    {{ $termLabels[$analyticsData['term']] ?? ucfirst($analyticsData['term']) }} {{ $analyticsData['year'] }}
                </p>
            </div>
        </div>
    </div>

    <!-- Overall Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-blue-50 rounded-xl p-6 border border-blue-200">
            <p class="text-sm font-medium text-blue-600">Subjects</p>
            <p class="text-3xl font-bold text-blue-900">{{ $analyticsData['overall']['subjects_count'] }}</p>
            <p class="text-xs text-blue-500">total subjects</p>
        </div>
        <div class="bg-green-50 rounded-xl p-6 border border-green-200">
            <p class="text-sm font-medium text-green-600">Assessment Average</p>
            <p class="text-3xl font-bold text-green-900">{{ $analyticsData['overall']['assessment_avg'] ?? '--' }}%</p>
            <p class="text-xs text-green-500">across {{ $analyticsData['overall']['subjects_with_data'] }} subjects</p>
        </div>
        <div class="bg-purple-50 rounded-xl p-6 border border-purple-200">
            <p class="text-sm font-medium text-purple-600">Term Result Average</p>
            <p class="text-3xl font-bold text-purple-900">{{ $analyticsData['overall']['term_avg'] ?? '--' }}%</p>
            <p class="text-xs text-purple-500">final results</p>
        </div>
        <div class="{{ $analyticsData['overall']['difference'] !== null && $analyticsData['overall']['difference'] >= 0 ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200' }} rounded-xl p-6 border">
            <p class="text-sm font-medium {{ $analyticsData['overall']['difference'] !== null && $analyticsData['overall']['difference'] >= 0 ? 'text-emerald-600' : 'text-red-600' }}">Difference</p>
            <p class="text-3xl font-bold {{ $analyticsData['overall']['difference'] !== null && $analyticsData['overall']['difference'] >= 0 ? 'text-emerald-900' : 'text-red-900' }}">
                @if($analyticsData['overall']['difference'] !== null)
                    {{ $analyticsData['overall']['difference'] >= 0 ? '+' : '' }}{{ $analyticsData['overall']['difference'] }}%
                @else
                    --
                @endif
            </p>
            <p class="text-xs {{ $analyticsData['overall']['difference'] !== null && $analyticsData['overall']['difference'] >= 0 ? 'text-emerald-500' : 'text-red-500' }}">term vs assessment</p>
        </div>
    </div>

    <!-- Performance Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Assessment vs Term Results Comparison</h3>
        <div style="height: 400px;">
            <canvas id="comparisonChart"></canvas>
        </div>
    </div>

    <!-- Subject Details Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Subject-by-Subject Comparison</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="text-left py-3 px-4 rounded-l-lg font-semibold text-gray-700">Subject</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">Assessments</th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">
                            <span class="inline-flex items-center">
                                <span class="w-3 h-3 bg-blue-500 rounded-full mr-2"></span>Assessment Avg
                            </span>
                        </th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">
                            <span class="inline-flex items-center">
                                <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>Term Result
                            </span>
                        </th>
                        <th class="text-center py-3 px-4 font-semibold text-gray-700">Grade</th>
                        <th class="text-center py-3 px-4 rounded-r-lg font-semibold text-gray-700">Difference</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($analyticsData['subjects'] as $subject)
                    <tr class="border-b border-gray-100 hover:bg-gray-50" x-data="{ showDetails: false }">
                        <td class="py-3 px-4 text-gray-900 font-medium">
                            <button @click="showDetails = !showDetails" class="flex items-center hover:text-blue-600">
                                <svg class="w-4 h-4 mr-2 transition-transform" :class="{ 'rotate-90': showDetails }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                                {{ $subject['subject_name'] }}
                            </button>
                        </td>
                        <td class="text-center py-3 px-4">
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-medium">
                                {{ $subject['assessment_count'] }}
                            </span>
                        </td>
                        <td class="text-center py-3 px-4">
                            @if($subject['assessment_performance'] !== null)
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $subject['assessment_performance'] >= 50 ? 'bg-blue-100 text-blue-700' : 'bg-blue-50 text-blue-600' }}">
                                {{ $subject['assessment_performance'] }}%
                            </span>
                            @else
                            <span class="text-gray-400">--</span>
                            @endif
                        </td>
                        <td class="text-center py-3 px-4">
                            @if($subject['term_performance'] !== null)
                            <span class="px-2 py-1 rounded text-xs font-medium {{ $subject['term_performance'] >= 50 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $subject['term_performance'] }}%
                            </span>
                            @else
                            <span class="text-gray-400">--</span>
                            @endif
                        </td>
                        <td class="text-center py-3 px-4">
                            @if($subject['term_grade'])
                            <span class="px-2 py-1 bg-purple-100 text-purple-700 rounded text-xs font-bold">
                                {{ $subject['term_grade'] }}
                            </span>
                            @else
                            <span class="text-gray-400">--</span>
                            @endif
                        </td>
                        <td class="text-center py-3 px-4">
                            @if($subject['difference'] !== null)
                                @if($subject['difference'] >= 0)
                                <span class="text-emerald-600 font-semibold text-xs">↑ +{{ $subject['difference'] }}%</span>
                                @else
                                <span class="text-red-600 font-semibold text-xs">↓ {{ $subject['difference'] }}%</span>
                                @endif
                            @else
                            <span class="text-gray-400">--</span>
                            @endif
                        </td>
                    </tr>
                    <!-- Assessment Details Row (Expandable) -->
                    <tr x-show="showDetails" x-collapse class="bg-gray-50">
                        <td colspan="6" class="py-4 px-8">
                            @if(count($subject['assessment_details']) > 0)
                            <div class="text-xs">
                                <p class="font-semibold text-gray-700 mb-2">Assessment Breakdown:</p>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    @foreach($subject['assessment_details'] as $detail)
                                    <div class="bg-white p-3 rounded-lg border border-gray-200">
                                        <p class="font-medium text-gray-800">{{ $detail['type'] }}</p>
                                        <p class="text-gray-500 truncate" title="{{ $detail['topic'] }}">{{ $detail['topic'] }}</p>
                                        <div class="mt-2 flex justify-between items-center">
                                            <span class="text-gray-600">{{ $detail['mark'] }}/{{ $detail['total'] }}</span>
                                            <span class="px-2 py-0.5 rounded text-xs font-medium {{ $detail['percentage'] >= 50 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $detail['percentage'] }}%
                                            </span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @else
                            <p class="text-gray-500 text-sm">No assessment data available for this subject.</p>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <!-- No data message -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
        <svg class="w-16 h-16 text-yellow-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <h3 class="text-lg font-semibold text-yellow-800 mb-2">No Data Available</h3>
        <p class="text-yellow-600">No assessment or term result data found for this student in the selected term.</p>
    </div>
    @endif
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($analyticsData)
    // Comparison Chart
    const ctx = document.getElementById('comparisonChart');
    if (ctx) {
        const subjectData = @json($analyticsData['subjects']);
        const labels = subjectData.map(s => s.subject_name.length > 15 ? s.subject_name.substring(0, 15) + '...' : s.subject_name);
        const assessmentData = subjectData.map(s => s.assessment_performance);
        const termData = subjectData.map(s => s.term_performance);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Assessment Average',
                        data: assessmentData,
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Term Result',
                        data: termData,
                        backgroundColor: 'rgba(34, 197, 94, 0.7)',
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: { display: true, text: 'Performance (%)' }
                    }
                },
                plugins: {
                    legend: { position: 'top' },
                    title: {
                        display: true,
                        text: 'Assessment Performance vs Term Results by Subject'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const value = context.raw;
                                return value !== null ? `${context.dataset.label}: ${value}%` : `${context.dataset.label}: No data`;
                            }
                        }
                    }
                }
            }
        });
    }
    @endif
});
</script>
@endsection
