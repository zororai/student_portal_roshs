<div class="w-full block mt-8">
    <div class="flex flex-wrap sm:flex-no-wrap justify-between">
        <div class="w-full bg-gray-200 text-center border border-gray-300 px-8 py-6 rounded">
            <h3 class="text-gray-700 uppercase font-bold">
            <svg class="fill-current float-left" style="width:39px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path d="M40 48C26.7 48 16 58.7 16 72v48c0 13.3 10.7 24 24 24H88c13.3 0 24-10.7 24-24V72c0-13.3-10.7-24-24-24H40zM192 64c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zm0 160c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zm0 160c-17.7 0-32 14.3-32 32s14.3 32 32 32H480c17.7 0 32-14.3 32-32s-14.3-32-32-32H192zM16 232v48c0 13.3 10.7 24 24 24H88c13.3 0 24-10.7 24-24V232c0-13.3-10.7-24-24-24H40c-13.3 0-24 10.7-24 24zM40 368c-13.3 0-24 10.7-24 24v48c0 13.3 10.7 24 24 24H88c13.3 0 24-10.7 24-24V392c0-13.3-10.7-24-24-24H40z"/></svg>
                <span class="text-4xl">{{ sprintf("%02d", $teacher->classes_count) }}</span>
                <span class="leading-tight">Classes</span>
            </h3>
        </div>
        <!-- Log on to codeastro.com for more projects -->
        <div class="w-full bg-gray-200 text-center border border-gray-300 px-8 py-6 mx-0 sm:mx-6 my-4 sm:my-0 rounded">
            <h3 class="text-gray-700 uppercase font-bold">
            <svg class="fill-current float-left" style="width:39px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path d="M96 0C43 0 0 43 0 96V416c0 53 43 96 96 96H384h32c17.7 0 32-14.3 32-32s-14.3-32-32-32V384c17.7 0 32-14.3 32-32V32c0-17.7-14.3-32-32-32H384 96zm0 384H352v64H96c-17.7 0-32-14.3-32-32s14.3-32 32-32zm32-240c0-8.8 7.2-16 16-16H336c8.8 0 16 7.2 16 16s-7.2 16-16 16H144c-8.8 0-16-7.2-16-16zm16 48H336c8.8 0 16 7.2 16 16s-7.2 16-16 16H144c-8.8 0-16-7.2-16-16s7.2-16 16-16z"/></svg>
                <span class="text-4xl">{{ sprintf("%02d", $teacher->subjects_count) }}</span>
                <span class="leading-tight">Subjects</span>
            </h3>
        </div>
        <div class="w-full bg-gray-200 text-center border border-gray-300 px-8 py-6 rounded">
            <h3 class="text-gray-700 uppercase font-bold">
            <svg class="fill-current float-left" style="width:39px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
            <path d="M319.4 320.6L224 416l-95.4-95.4C57.1 323.7 0 382.2 0 454.4v9.6c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-9.6c0-72.2-57.1-130.7-128.6-133.8zM13.6 79.8l6.4 1.5v58.4c-7 4.2-12 11.5-12 20.3 0 8.4 4.6 15.4 11.1 19.7L3.5 242c-1.7 6.9 2.1 14 7.6 14h41.8c5.5 0 9.3-7.1 7.6-14l-15.6-62.3C51.4 175.4 56 168.4 56 160c0-8.8-5-16.1-12-20.3V87.1l66 15.9c-8.6 17.2-14 36.4-14 57 0 70.7 57.3 128 128 128s128-57.3 128-128c0-20.6-5.3-39.8-14-57l96.3-23.2c18.2-4.4 18.2-27.1 0-31.5l-190.4-46c-13-3.1-26.7-3.1-39.7 0L13.6 48.2c-18.1 4.4-18.1 27.2 0 31.6z"/></svg>
                <span class="text-4xl">{{ ($teacher->students[0]->students_count) ?? 0 }}</span>
                <span class="leading-tight">Students</span>
            </h3>
        </div>
    </div>
</div>
<div class="w-full block mt-8">
    <div class="flex flex-wrap sm:flex-no-wrap justify-between">
        <div class="w-full sm:w-1/2 mr-2 mb-6">
            <h3 class="text-gray-700 uppercase font-bold mb-2">Class List</h3>
            <div class="flex flex-wrap items-center">
                @foreach ($teacher->classes as $class)
                    <div class="w-full sm:w-1/2 text-center border border-gray-400 rounded">
                        <div class="text-gray-800 uppercase font-semibold px-4 py-4 mb-2">{{ $class->class_name }}</div>
                        <a href="{{ route('teacher.attendance.create',$class->id) }}" class="bg-green-600 inline-block mb-4 text-xs text-white uppercase font-semibold px-4 py-2 border border-gray-200 rounded">Manage Attendence</a>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="w-full sm:w-1/2 ml-2 mb-6">
            <h3 class="text-gray-700 uppercase font-bold mb-2">Subject List</h3>
            <div class="flex items-center bg-gray-600 rounded-tl rounded-tr">
                <div class="w-1/3 text-left text-white py-2 px-4 font-semibold">Code</div>
                <div class="w-1/3 text-left text-white py-2 px-4 font-semibold">Subject</div>
                <div class="w-1/3 text-right text-white py-2 px-4 font-semibold">Teacher</div>
            </div>
       @foreach ($teacher->subjects as $subject)
       <div class="flex items-center justify-between border border-gray-200 cursor-pointer" onclick="window.location='{{ route('subject.Reading', $subject->id) }}'">
        <div class="w-1/3 text-left text-gray-600 py-2 px-4 font-medium">{{ $subject->name }}</div>
        <div class="w-1/3 text-right text-gray-600 py-2 px-4 font-medium">{{ $subject->teacher->user->name }}</div>
    </div>
@endforeach

<!-- Upload Form -->

        </div>
    </div>
<!-- Subjects Taught Section with Assessment Stats -->
<div class="w-full block mt-8" x-data="{ currentPage: 0, itemsPerPage: 2 }">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-xl font-bold text-gray-800">Subjects Taught</h3>
        @if(isset($subjectAssessmentData) && count($subjectAssessmentData) > 2)
        <div class="flex items-center space-x-2">
            <button @click="currentPage = Math.max(0, currentPage - 1)" 
                    :disabled="currentPage === 0"
                    :class="currentPage === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                    class="p-2 rounded-lg border border-gray-300 bg-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </button>
            <span class="text-sm text-gray-600">
                Page <span x-text="currentPage + 1"></span> of <span x-text="Math.ceil({{ count($subjectAssessmentData) }} / itemsPerPage)"></span>
            </span>
            <button @click="currentPage = Math.min(Math.ceil({{ count($subjectAssessmentData) }} / itemsPerPage) - 1, currentPage + 1)" 
                    :disabled="currentPage >= Math.ceil({{ count($subjectAssessmentData) }} / itemsPerPage) - 1"
                    :class="currentPage >= Math.ceil({{ count($subjectAssessmentData) }} / itemsPerPage) - 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
                    class="p-2 rounded-lg border border-gray-300 bg-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
        @endif
    </div>
    
    @if(isset($subjectAssessmentData) && count($subjectAssessmentData) > 0)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach($subjectAssessmentData as $index => $subjectData)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6"
             x-show="Math.floor({{ $index }} / itemsPerPage) === currentPage"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100">
            <h4 class="text-lg font-semibold text-blue-600 mb-4">{{ $subjectData['subject'] }} - {{ $subjectData['class'] }}</h4>
            
            <div class="grid grid-cols-2 gap-4">
                <!-- Report on Work Given -->
                <div>
                    <h5 class="text-sm font-semibold text-gray-700 mb-3">Report on Work Given</h5>
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left py-1 text-gray-600">Assessment Type</th>
                                <th class="text-center py-1 text-gray-600">Given</th>
                                <th class="text-center py-1 text-gray-600">Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subjectData['stats'] as $stat)
                            <tr class="border-b border-gray-100">
                                <td class="py-1 text-blue-600">{{ $stat['type'] }}</td>
                                <td class="text-center py-1">{{ $stat['given'] }}</td>
                                <td class="text-center py-1">{{ $stat['performance'] }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Student Performance Chart -->
                <div>
                    <h5 class="text-sm font-semibold text-gray-700 mb-3">Student Performance</h5>
                    <canvas id="subjectChart{{ $index }}" height="200"></canvas>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-gray-100 rounded-lg p-6 text-center text-gray-500">
        No assessment data available yet.
    </div>
    @endif
</div>

<!-- Overall Assessment Performance Chart -->
<div class="w-full block mt-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Overall Assessment Performance</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <canvas id="teacherAssessmentChart" height="300"></canvas>
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
                        @if(isset($teacherAssessmentStats))
                        @foreach($teacherAssessmentStats as $stat)
                        <tr class="border-b border-gray-100 hover:bg-gray-50">
                            <td class="py-2 px-3 text-gray-700">{{ $stat['type'] }}</td>
                            <td class="text-center py-2 px-3">{{ $stat['given'] }}</td>
                            <td class="text-center py-2 px-3">
                                <span class="px-2 py-1 rounded text-xs font-medium {{ $stat['performance'] >= 50 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $stat['performance'] }}%
                                </span>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</div> <!-- ./END TEACHER -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Overall Assessment Chart
    @if(isset($teacherAssessmentStats))
    const teacherCtx = document.getElementById('teacherAssessmentChart');
    if (teacherCtx) {
        new Chart(teacherCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(collect($teacherAssessmentStats)->pluck('type')) !!},
                datasets: [{
                    label: 'Performance %',
                    data: {!! json_encode(collect($teacherAssessmentStats)->pluck('performance')) !!},
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
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: { display: true, text: 'Performance (%)' }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
    @endif
    
    // Subject-specific charts
    @if(isset($subjectAssessmentData))
    @foreach($subjectAssessmentData as $index => $subjectData)
    const ctx{{ $index }} = document.getElementById('subjectChart{{ $index }}');
    if (ctx{{ $index }}) {
        new Chart(ctx{{ $index }}, {
            type: 'line',
            data: {
                labels: {!! json_encode(collect($subjectData['stats'])->pluck('type')) !!},
                datasets: [{
                    label: 'Performance',
                    data: {!! json_encode(collect($subjectData['stats'])->map(function($s) { return floatval(str_replace(['%', '--'], ['', '0'], $s['performance'])); })) !!},
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true, max: 100 }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }
    @endforeach
    @endif
});
</script>