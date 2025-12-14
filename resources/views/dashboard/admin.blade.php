<div class="w-full block mt-8">
    <div class="flex flex-wrap sm:flex-no-wrap justify-between">
        <div class="w-full bg-gray-200 text-center border border-gray-300 px-8 py-6 rounded">
            <h3 class="text-gray-700 uppercase font-bold">
            <svg class="fill-current float-left" style="width:39px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
            <path d="M319.4 320.6L224 416l-95.4-95.4C57.1 323.7 0 382.2 0 454.4v9.6c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-9.6c0-72.2-57.1-130.7-128.6-133.8zM13.6 79.8l6.4 1.5v58.4c-7 4.2-12 11.5-12 20.3 0 8.4 4.6 15.4 11.1 19.7L3.5 242c-1.7 6.9 2.1 14 7.6 14h41.8c5.5 0 9.3-7.1 7.6-14l-15.6-62.3C51.4 175.4 56 168.4 56 160c0-8.8-5-16.1-12-20.3V87.1l66 15.9c-8.6 17.2-14 36.4-14 57 0 70.7 57.3 128 128 128s128-57.3 128-128c0-20.6-5.3-39.8-14-57l96.3-23.2c18.2-4.4 18.2-27.1 0-31.5l-190.4-46c-13-3.1-26.7-3.1-39.7 0L13.6 48.2c-18.1 4.4-18.1 27.2 0 31.6z"/></svg>
                <span class="text-4xl">{{ sprintf("%02d", count($students)) }}</span>
                <span class="leading-tight">Students</span>
            </h3>
        </div>
        <!-- Log on to codeastro.com for more projects -->
        <div class="w-full bg-gray-200 text-center border border-gray-300 px-8 py-6 mx-0 sm:mx-6 my-4 sm:my-0 rounded">
            <h3 class="text-gray-700 uppercase font-bold">
            <svg class="fill-current float-left" style="width:39px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M224 256c70.7 0 128-57.3 128-128S294.7 0 224 0S96 57.3 96 128s57.3 128 128 128zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z"/></svg>
                <span class="text-4xl">{{ sprintf("%02d", count($teachers)) }}</span>
                <span class="leading-tight">Teachers</span>
            </h3>
        </div>
        <div class="w-full bg-gray-200 text-center border border-gray-300 px-8 py-6 rounded">
            <h3 class="text-gray-700 uppercase font-bold">
            <svg class="fill-current float-left" style="width:39px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M224 0c70.7 0 128 57.3 128 128s-57.3 128-128 128s-128-57.3-128-128S153.3 0 224 0zM209.1 359.2l-18.6-31c-6.4-10.7 1.3-24.2 13.7-24.2H224h19.7c12.4 0 20.1 13.6 13.7 24.2l-18.6 31 33.4 123.9 39.5-161.2c77.2 12 136.3 78.8 136.3 159.4c0 17-13.8 30.7-30.7 30.7H265.1 182.9 30.7C13.8 512 0 498.2 0 481.3c0-80.6 59.1-147.4 136.3-159.4l39.5 161.2 33.4-123.9z"/></svg>
                <span class="text-4xl">{{ sprintf("%02d", count($parents)) }}</span>
                <span class="leading-tight">Parents</span>
            </h3>
        </div>
        
    </div>
</div>

<div class="w-full block mt-8">
    <div class="flex flex-wrap sm:flex-no-wrap justify-between">
        <div class="w-full bg-gray-200 text-center border border-gray-300 px-8 py-6 rounded">
            <h3 class="text-gray-700 uppercase font-bold">
            <svg class="fill-current float-left" style="width:39px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M96 0C43 0 0 43 0 96V416c0 53 43 96 96 96H384h32c17.7 0 32-14.3 32-32s-14.3-32-32-32V384c17.7 0 32-14.3 32-32V32c0-17.7-14.3-32-32-32H384 96zm0 384H352v64H96c-17.7 0-32-14.3-32-32s14.3-32 32-32zm32-240c0-8.8 7.2-16 16-16H336c8.8 0 16 7.2 16 16s-7.2 16-16 16H144c-8.8 0-16-7.2-16-16zm16 48H336c8.8 0 16 7.2 16 16s-7.2 16-16 16H144c-8.8 0-16-7.2-16-16s7.2-16 16-16z"/></svg>
                <span class="text-4xl">{{ sprintf("%02d", count($subjects)) }}</span>
                <span class="leading-tight">Subjects</span>
            </h3>
        </div>
        <div class="w-full bg-gray-200 text-center border border-gray-300 px-8 py-6 mx-0 sm:mx-6 my-4 sm:my-0 rounded">
            <h3 class="text-gray-700 uppercase font-bold">
            <svg class="fill-current float-left" style="width:39px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M40 48C26.7 48 16 58.7 16 72v48c0 13.3 10.7 24 24 24H88c13.3 0 24-10.7 24-24V72c0-13.3-10.7-24-24-24H40zM192 64c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zm0 160c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zm0 160c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zM16 232v48c0 13.3 10.7 24 24 24H88c13.3 0 24-10.7 24-24V232c0-13.3-10.7-24-24-24H40c-13.3 0-24 10.7-24 24zM40 368c-13.3 0-24 10.7-24 24v48c0 13.3 10.7 24 24 24H88c13.3 0 24-10.7 24-24V392c0-13.3-10.7-24-24-24H40z"/></svg>
                <span class="text-4xl">{{ sprintf("%02d", count($classes)) }}</span>
                <span class="leading-tight">Classes</span>
            </h3>
        </div>

        
        
    </div>
</div>

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

<!-- Assessment Performance Chart -->
@if(isset($assessmentStats))
<div class="w-full block mt-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-xl font-bold text-gray-900 mb-4">Assessment Performance by Type</h3>
        <p class="text-sm text-gray-600 mb-6">School-wide average performance across all assessment types</p>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-gray-50 rounded-lg p-4" style="height: 350px;">
                <canvas id="assessmentPerformanceChart"></canvas>
            </div>
            <div>
                <h5 class="text-sm font-semibold text-gray-700 mb-3">Assessment Summary</h5>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-gray-50">
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gender Results Chart
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
        const assessmentCtx = document.getElementById('assessmentPerformanceChart');
        if (assessmentCtx) {
            new Chart(assessmentCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode(collect($assessmentStats)->pluck('type')) !!},
                    datasets: [{
                        label: 'Performance %',
                        data: {!! json_encode(collect($assessmentStats)->pluck('performance')) !!},
                        backgroundColor: [
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
                        ],
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
        @endif
    });
</script>
@endpush