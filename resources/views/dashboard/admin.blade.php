@include('dashboard.partials.stats-cards')

<!-- Pass/Fail Results by Gender Chart -->
<div class="w-full block mt-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Student Results by Gender</h3>
        <p class="text-sm text-gray-600 mb-6">Pass/Fail distribution for male and female students (Pass = 50% and above)</p>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Bar Chart -->
            <div class="bg-gray-50 rounded-lg p-4">
                <canvas id="genderResultsChart" height="300"></canvas>
            </div>
            <!-- Statistics Summary -->
            <div class="space-y-4">
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-blue-600">Male Students</p>
                                <p class="text-2xl font-bold text-blue-900">{{ ($genderStats['malePass'] ?? 0) + ($genderStats['maleFail'] ?? 0) }} Results</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 flex space-x-4">
                        <div class="flex items-center">
                            <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                            <span class="text-sm text-gray-700">Pass: <strong>{{ $genderStats['malePass'] ?? 0 }}</strong></span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                            <span class="text-sm text-gray-700">Fail: <strong>{{ $genderStats['maleFail'] ?? 0 }}</strong></span>
                        </div>
                    </div>
                </div>
                <div class="bg-pink-50 rounded-lg p-4 border border-pink-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-pink-500 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-pink-600">Female Students</p>
                                <p class="text-2xl font-bold text-pink-900">{{ ($genderStats['femalePass'] ?? 0) + ($genderStats['femaleFail'] ?? 0) }} Results</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 flex space-x-4">
                        <div class="flex items-center">
                            <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                            <span class="text-sm text-gray-700">Pass: <strong>{{ $genderStats['femalePass'] ?? 0 }}</strong></span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                            <span class="text-sm text-gray-700">Fail: <strong>{{ $genderStats['femaleFail'] ?? 0 }}</strong></span>
                        </div>
                    </div>
                </div>
                <!-- Pass Rate Comparison -->
                <div class="bg-gray-100 rounded-lg p-4">
                    <p class="text-sm font-medium text-gray-700 mb-2">Pass Rate Comparison</p>
                    @php
                        $maleTotal = ($genderStats['malePass'] ?? 0) + ($genderStats['maleFail'] ?? 0);
                        $femaleTotal = ($genderStats['femalePass'] ?? 0) + ($genderStats['femaleFail'] ?? 0);
                        $malePassRate = $maleTotal > 0 ? round(($genderStats['malePass'] ?? 0) / $maleTotal * 100, 1) : 0;
                        $femalePassRate = $femaleTotal > 0 ? round(($genderStats['femalePass'] ?? 0) / $femaleTotal * 100, 1) : 0;
                    @endphp
                    <div class="space-y-2">
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-blue-600 font-medium">Male</span>
                                <span class="font-bold">{{ $malePassRate }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-blue-500 h-3 rounded-full" style="width: {{ $malePassRate }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-pink-600 font-medium">Female</span>
                                <span class="font-bold">{{ $femalePassRate }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-pink-500 h-3 rounded-full" style="width: {{ $femalePassRate }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Classroom Population Chart -->
<div class="w-full block mt-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Classroom Population</h3>
        <p class="text-sm text-gray-600 mb-6">Number of students enrolled in each class</p>
        <div class="bg-gray-50 rounded-lg p-4" style="height: 400px;">
            <canvas id="classroomPopulationChart"></canvas>
        </div>
        <!-- Summary Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
            <div class="bg-blue-50 rounded-lg p-4 text-center">
                <p class="text-sm font-medium text-blue-600">Total Classes</p>
                <p class="text-2xl font-bold text-blue-900">{{ $classroomPopulation->count() }}</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4 text-center">
                <p class="text-sm font-medium text-green-600">Total Students</p>
                <p class="text-2xl font-bold text-green-900">{{ $classroomPopulation->sum('count') }}</p>
            </div>
            <div class="bg-purple-50 rounded-lg p-4 text-center">
                <p class="text-sm font-medium text-purple-600">Largest Class</p>
                <p class="text-2xl font-bold text-purple-900">{{ $classroomPopulation->max('count') ?? 0 }}</p>
            </div>
            <div class="bg-orange-50 rounded-lg p-4 text-center">
                <p class="text-sm font-medium text-orange-600">Average Size</p>
                <p class="text-2xl font-bold text-orange-900">{{ $classroomPopulation->count() > 0 ? round($classroomPopulation->avg('count'), 1) : 0 }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Assessment Performance Chart with Filters -->
@if(isset($assessmentStats))
<div class="w-full block mt-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
            <div class="mb-4 lg:mb-0">
                <h3 class="text-xl font-bold text-gray-900 mb-1">Assessment Performance by Type</h3>
                <p class="text-sm text-gray-600">Filter by class and subject to view specific performance</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Class</label>
                    <select id="assessClassFilter" class="block w-full sm:w-40 px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="all">All Classes</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-500 mb-1">Subject</label>
                    <select id="assessSubjectFilter" class="block w-full sm:w-40 px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="all">All Subjects</option>
                        @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-gray-50 rounded-lg p-4" style="height: 350px;">
                <canvas id="assessmentPerformanceChart"></canvas>
            </div>
            <div>
                <h5 class="text-sm font-semibold text-gray-700 mb-3">Assessment Summary</h5>
                <table class="w-full text-sm">
                    <thead>
                            <th class="text-left py-2 px-3">Type</th>
                            <th class="text-center py-2 px-3">Given</th>
                            <th class="text-center py-2 px-3">Avg Performance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assessmentStats as $stat)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-2 px-3 text-gray-700">{{ $stat['type'] }}</td>
                            <td class="text-center py-2 px-3">{{ $stat['given'] }}</td>
                            <td class="text-center py-2 px-3">
                                <span class="px-2 py-1 rounded text-xs font-medium {{ $stat['performance'] >= 50 ? 'bg-green-100 text-green-700' : ($stat['performance'] > 0 ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-500') }}">
                                    {{ $stat['performance'] > 0 ? $stat['performance'] . '%' : '--' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Subject Performance Chart -->
@if(isset($subjectPerformanceData) && count($subjectPerformanceData) > 0)
<div class="w-full block mt-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Subject-Wise Performance</h3>
        <p class="text-sm text-gray-600 mb-6">Average performance for each subject across all assessments</p>
        <div class="bg-gray-50 rounded-lg p-4" style="height: 400px;">
            <canvas id="subjectPerformanceChart"></canvas>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-3 mt-6">
            @foreach($subjectPerformanceData as $index => $subject)
            <div class="bg-gray-50 rounded-lg p-3 text-center border {{ $subject['performance'] >= 50 ? 'border-green-200' : ($subject['performance'] > 0 ? 'border-red-200' : 'border-gray-200') }}">
                <p class="text-xs font-medium text-gray-600 truncate" title="{{ $subject['subject'] }}">{{ Str::limit($subject['subject'], 12) }}</p>
                <p class="text-lg font-bold {{ $subject['performance'] >= 50 ? 'text-green-600' : ($subject['performance'] > 0 ? 'text-red-600' : 'text-gray-400') }}">
                    {{ $subject['performance'] > 0 ? $subject['performance'] . '%' : '--' }}
                </p>
                <p class="text-xs text-gray-500">{{ $subject['assessments'] }} assessments</p>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Expandable Subject Cards with Class Filter -->
@if(isset($subjectAssessmentMatrix) && count($subjectAssessmentMatrix) > 0)
<div class="w-full block mt-8" x-data="{ selectedClass: 'all' }">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h3 class="text-xl font-bold text-gray-900 mb-1">Subject Performance Cards</h3>
                <p class="text-sm text-gray-600">Click on a subject to expand and view detailed assessment breakdown</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <label class="block text-xs font-medium text-gray-500 mb-1">Filter by Class</label>
                <select x-model="selectedClass" class="block w-full sm:w-48 px-3 py-2 text-sm border border-gray-300 rounded-lg bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">All Classes</option>
                    @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->class_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($subjectAssessmentMatrix as $index => $subjectData)
            <div x-data="{ expanded: false }" 
                 x-show="selectedClass === 'all' || {{ json_encode($subjectData['class_ids'] ?? []) }}.includes(parseInt(selectedClass))"
                 class="border rounded-xl overflow-hidden transition-all duration-300 {{ $subjectData['overall_performance'] >= 50 ? 'border-green-200' : ($subjectData['overall_performance'] > 0 ? 'border-red-200' : 'border-gray-200') }}">
                <div @click="expanded = !expanded" class="cursor-pointer p-4 {{ $subjectData['overall_performance'] >= 50 ? 'bg-green-50 hover:bg-green-100' : ($subjectData['overall_performance'] > 0 ? 'bg-red-50 hover:bg-red-100' : 'bg-gray-50 hover:bg-gray-100') }} transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center {{ $subjectData['overall_performance'] >= 50 ? 'bg-green-500' : ($subjectData['overall_performance'] > 0 ? 'bg-red-500' : 'bg-gray-400') }}">
                                <span class="text-white font-bold text-lg">{{ substr($subjectData['subject'], 0, 2) }}</span>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">{{ $subjectData['subject'] }}</h4>
                                <p class="text-xs text-gray-500">Click to expand</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold {{ $subjectData['overall_performance'] >= 50 ? 'text-green-600' : ($subjectData['overall_performance'] > 0 ? 'text-red-600' : 'text-gray-400') }}">
                                {{ $subjectData['overall_performance'] > 0 ? $subjectData['overall_performance'] . '%' : '--' }}
                            </p>
                            <p class="text-xs text-gray-500">Overall</p>
                        </div>
                    </div>
                    <div class="flex justify-center mt-2">
                        <svg class="w-5 h-5 text-gray-400 transition-transform duration-300" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </div>
                </div>
                <div x-show="expanded" x-collapse class="bg-white border-t p-4">
                    <h5 class="text-sm font-semibold text-gray-700 mb-3">Assessment Type Breakdown</h5>
                    <div class="space-y-2">
                        @foreach($assessmentTypes as $type)
                        @php $typeData = $subjectData['types'][$type] ?? ['given' => 0, 'performance' => 0]; @endphp
                        <div class="flex items-center justify-between py-1 border-b border-gray-100 last:border-0">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-600">{{ $type }}</span>
                                @if($typeData['given'] > 0)
                                <span class="text-xs bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded">{{ $typeData['given'] }}</span>
                                @endif
                            </div>
                            <div>
                                @if($typeData['given'] > 0)
                                <div class="flex items-center space-x-2">
                                    <div class="w-16 bg-gray-200 rounded-full h-2">
                                        <div class="h-2 rounded-full {{ $typeData['performance'] >= 50 ? 'bg-green-500' : 'bg-red-500' }}" style="width: {{ min($typeData['performance'], 100) }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium {{ $typeData['performance'] >= 50 ? 'text-green-600' : 'text-red-600' }}">{{ $typeData['performance'] }}%</span>
                                </div>
                                @else
                                <span class="text-sm text-gray-300">--</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ... rest of the script remains the same ...
        const ctx = document.getElementById('genderResultsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Male', 'Female'],
                datasets: [
                    {
                        label: 'Pass',
                        data: [{{ $genderStats['malePass'] ?? 0 }}, {{ $genderStats['femalePass'] ?? 0 }}],
                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Fail',
                        data: [{{ $genderStats['maleFail'] ?? 0 }}, {{ $genderStats['femaleFail'] ?? 0 }}],
                        backgroundColor: 'rgba(239, 68, 68, 0.8)',
                        borderColor: 'rgba(239, 68, 68, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Pass vs Fail by Gender'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        // Classroom Population Chart with Male/Female breakdown (Line Graph)
        const classCtx = document.getElementById('classroomPopulationChart').getContext('2d');
        new Chart(classCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($classroomPopulation->pluck('name')) !!},
                datasets: [
                    {
                        label: 'Male',
                        data: {!! json_encode($classroomPopulation->pluck('male')) !!},
                        backgroundColor: 'rgba(59, 130, 246, 0.2)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 6
                    },
                    {
                        label: 'Female',
                        data: {!! json_encode($classroomPopulation->pluck('female')) !!},
                        backgroundColor: 'rgba(236, 72, 153, 0.2)',
                        borderColor: 'rgba(236, 72, 153, 1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgba(236, 72, 153, 1)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    title: {
                        display: true,
                        text: 'Male vs Female Students per Class'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        
        // Assessment Performance Chart
        @if(isset($assessmentStats))
        let assessmentChart = null;
        const assessmentCtx = document.getElementById('assessmentPerformanceChart');
        const chartColors = [
            'rgba(59, 130, 246, 0.7)',
            'rgba(16, 185, 129, 0.7)',
            'rgba(245, 158, 11, 0.7)',
            'rgba(239, 68, 68, 0.7)',
            'rgba(139, 92, 246, 0.7)',
            'rgba(236, 72, 153, 0.7)',
            'rgba(20, 184, 166, 0.7)',
            'rgba(249, 115, 22, 0.7)',
            'rgba(99, 102, 241, 0.7)',
            'rgba(34, 197, 94, 0.7)'
        ];
        
        if (assessmentCtx) {
            assessmentChart = new Chart(assessmentCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode(collect($assessmentStats)->pluck('type')) !!},
                    datasets: [{
                        label: 'Performance %',
                        data: {!! json_encode(collect($assessmentStats)->pluck('performance')) !!},
                        backgroundColor: chartColors,
                        borderWidth: 1
                    }]
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
                        legend: { display: false },
                        title: {
                            display: true,
                            text: 'School-Wide Assessment Performance'
                        }
                    }
                }
            });
        }
        
        // Filter functionality for Assessment Performance
        const classFilter = document.getElementById('assessClassFilter');
        const subjectFilter = document.getElementById('assessSubjectFilter');
        const summaryTable = document.querySelector('#assessmentPerformanceChart').closest('.grid').querySelector('tbody');
        
        function updateAssessmentStats() {
            const classId = classFilter.value;
            const subjectId = subjectFilter.value;
            
            fetch(`/api/assessment-stats?class_id=${classId}&subject_id=${subjectId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update chart
                        const performances = data.stats.map(s => s.performance);
                        assessmentChart.data.datasets[0].data = performances;
                        assessmentChart.update();
                        
                        // Update table
                        summaryTable.innerHTML = '';
                        data.stats.forEach(stat => {
                            const perfClass = stat.performance >= 50 ? 'bg-green-100 text-green-700' : (stat.performance > 0 ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-500');
                            const perfText = stat.performance > 0 ? stat.performance + '%' : '--';
                            summaryTable.innerHTML += `
                                <tr class="border-b border-gray-100 hover:bg-gray-50">
                                    <td class="py-2 px-3 text-gray-700">${stat.type}</td>
                                    <td class="text-center py-2 px-3">${stat.given}</td>
                                    <td class="text-center py-2 px-3">
                                        <span class="px-2 py-1 rounded text-xs font-medium ${perfClass}">${perfText}</span>
                                    </td>
                                </tr>
                            `;
                        });
                    }
                })
                .catch(error => console.error('Error fetching assessment stats:', error));
        }
        
        if (classFilter && subjectFilter) {
            classFilter.addEventListener('change', updateAssessmentStats);
            subjectFilter.addEventListener('change', updateAssessmentStats);
        }
        @endif

        // Subject Performance Chart
        @if(isset($subjectPerformanceData) && count($subjectPerformanceData) > 0)
        const subjectCtx = document.getElementById('subjectPerformanceChart');
        if (subjectCtx) {
            new Chart(subjectCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode(collect($subjectPerformanceData)->pluck('subject')) !!},
                    datasets: [{
                        label: 'Performance %',
                        data: {!! json_encode(collect($subjectPerformanceData)->pluck('performance')) !!},
                        backgroundColor: {!! json_encode(collect($subjectPerformanceData)->map(function($s) { return $s['performance'] >= 50 ? 'rgba(34, 197, 94, 0.7)' : ($s['performance'] > 0 ? 'rgba(239, 68, 68, 0.7)' : 'rgba(156, 163, 175, 0.7)'); })) !!},
                        borderColor: {!! json_encode(collect($subjectPerformanceData)->map(function($s) { return $s['performance'] >= 50 ? 'rgba(34, 197, 94, 1)' : ($s['performance'] > 0 ? 'rgba(239, 68, 68, 1)' : 'rgba(156, 163, 175, 1)'); })) !!},
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 100,
                            title: { display: true, text: 'Performance (%)' }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        title: {
                            display: true,
                            text: 'Subject-Wise Average Performance'
                        }
                    }
                }
            });
        }
        @endif
    });
</script>