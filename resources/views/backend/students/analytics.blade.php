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
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <form method="GET" action="{{ route('admin.student-analytics.index') }}" id="analyticsFilterForm">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Class</label>
                    <select name="class_id" id="classFilter" class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ $selectedClassId == $class->id ? 'selected' : '' }}>{{ $class->class_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Student</label>
                    <select name="student_id" id="studentFilter" class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Student</option>
                        @foreach($students as $student)
                            <option value="{{ $student->id }}" {{ $selectedStudentId == $student->id ? 'selected' : '' }}>
                                {{ $student->user ? $student->user->name : 'N/A' }} ({{ $student->roll_number }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                    <select name="year" id="yearFilter" class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @php
                            $currentYearVal = date('Y');
                            for($y = $currentYearVal; $y >= $currentYearVal - 5; $y--) {
                                $selected = $selectedYear == $y ? 'selected' : '';
                                echo "<option value=\"$y\" $selected>$y</option>";
                            }
                        @endphp
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Term</label>
                    <select name="term" id="termFilter" class="block w-full px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="first" {{ $selectedTerm == 'first' ? 'selected' : '' }}>Term 1</option>
                        <option value="second" {{ $selectedTerm == 'second' ? 'selected' : '' }}>Term 2</option>
                        <option value="third" {{ $selectedTerm == 'third' ? 'selected' : '' }}>Term 3</option>
                    </select>
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    View Analytics
                </button>
            </div>
        </form>
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
    @elseif($selectedClassId && !$selectedStudentId)
    <!-- Prompt to select student -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
        <svg class="w-16 h-16 text-yellow-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <h3 class="text-lg font-semibold text-yellow-800 mb-2">Select a Student</h3>
        <p class="text-yellow-600">Please select a student from the dropdown above to view their analytics.</p>
    </div>
    @else
    <!-- Prompt to select class -->
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-8 text-center">
        <svg class="w-16 h-16 text-blue-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        <h3 class="text-lg font-semibold text-blue-800 mb-2">Get Started</h3>
        <p class="text-blue-600">Select a class to begin viewing student analytics and compare assessment performance to term results.</p>
    </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dynamic student loading when class changes
    const classFilter = document.getElementById('classFilter');
    const studentFilter = document.getElementById('studentFilter');
    
    classFilter.addEventListener('change', function() {
        const classId = this.value;
        studentFilter.innerHTML = '<option value="">Select Student</option>';
        
        if (classId) {
            fetch(`/api/student-analytics/students?class_id=${classId}`)
                .then(response => response.json())
                .then(data => {
                    data.students.forEach(student => {
                        const option = document.createElement('option');
                        option.value = student.id;
                        option.textContent = `${student.name} (${student.roll_number})`;
                        studentFilter.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching students:', error));
        }
    });

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
