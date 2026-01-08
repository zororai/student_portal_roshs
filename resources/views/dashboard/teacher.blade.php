<div class="max-w-7xl mx-auto">
    <!-- Welcome Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ Auth::user()->name }}!</h1>
        <p class="mt-2 text-gray-600">Here's an overview of your teaching activities</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Classes Card -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">My Classes</p>
                    <p class="text-4xl font-bold mt-2">{{ sprintf("%02d", $teacher->classes_count) }}</p>
                </div>
                <div class="p-3 bg-white/20 rounded-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-blue-100 text-sm">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
                Active classes
            </div>
        </div>

        <!-- Subjects Card -->
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-lg p-6 text-gray-900">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-emerald-100 text-sm font-medium">My Subjects</p>
                    <p class="text-4xl font-bold mt-2">{{ sprintf("%02d", $teacher->subjects_count) }}</p>
                </div>
                <div class="p-3 bg-white/20 rounded-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-gray-900 text-sm">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                </svg>
                Teaching subjects
            </div>
        </div>

        <!-- Students Card -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Total Students</p>
                    <p class="text-4xl font-bold mt-2">{{ ($teacher->students[0]->students_count) ?? 0 }}</p>
                </div>
                <div class="p-3 bg-white/20 rounded-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-purple-100 text-sm">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                </svg>
                Across all classes
            </div>
        </div>

        <!-- Assessments Card -->
        <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl shadow-lg p-6 text-gray-900">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-amber-100 text-sm font-medium">Assessments</p>
                    <p class="text-4xl font-bold mt-2">{{ isset($teacherAssessmentStats) ? collect($teacherAssessmentStats)->sum('given') : 0 }}</p>
                </div>
                <div class="p-3 bg-white/20 rounded-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-gray-900 text-sm">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                </svg>
                Total given
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="#my-classes" class="flex flex-col items-center p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors group" onclick="document.getElementById('my-classes').scrollIntoView({behavior: 'smooth'})">
                <div class="p-3 bg-blue-500 rounded-lg mb-3 group-hover:bg-blue-600 transition-colors">
                    <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700">Take Attendance</span>
            </a>
            <a href="{{ route('teacher.assessment') }}" class="flex flex-col items-center p-4 bg-emerald-50 rounded-xl hover:bg-emerald-100 transition-colors group">
                <div class="p-3 bg-emerald-500 rounded-lg mb-3 group-hover:bg-emerald-600 transition-colors">
                    <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700">Assessments</span>
            </a>
            <a href="{{ route('teacher.studentrecord') }}" class="flex flex-col items-center p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition-colors group">
                <div class="p-3 bg-purple-500 rounded-lg mb-3 group-hover:bg-purple-600 transition-colors">
                    <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700">Student Records</span>
            </a>
            <a href="{{ route('teacher.timetable') }}" class="flex flex-col items-center p-4 bg-amber-50 rounded-xl hover:bg-amber-100 transition-colors group">
                <div class="p-3 bg-amber-500 rounded-lg mb-3 group-hover:bg-amber-600 transition-colors">
                    <svg class="w-6 h-6 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700">Timetable</span>
            </a>
        </div>
    </div>

    <!-- Classes and Subjects Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- My Classes -->
        <div id="my-classes" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                <h3 class="text-lg font-semibold text-white">My Classes</h3>
                <p class="text-blue-100 text-sm">Manage attendance for your classes</p>
            </div>
            <div class="p-6">
                @if($teacher->classes && count($teacher->classes) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach ($teacher->classes as $class)
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 hover:border-blue-200 hover:bg-blue-50 transition-all">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ $class->class_name }}</h4>
                                    <p class="text-xs text-gray-500">{{ $class->students_count ?? 0 }} students</p>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('teacher.attendance.create', $class->id) }}" class="block w-full text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                            Manage Attendance
                        </a>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <p>No classes assigned yet</p>
                </div>
                @endif
            </div>
        </div>

        <!-- My Subjects -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 px-6 py-4">
                <h3 class="text-lg font-semibold text-white">My Subjects</h3>
                <p class="text-emerald-100 text-sm">View and manage reading materials</p>
            </div>
            <div class="p-6">
                @if($teacher->subjects && count($teacher->subjects) > 0)
                <div class="space-y-3">
                    @foreach ($teacher->subjects as $subject)
                    <a href="{{ route('subject.Reading', $subject->id) }}" class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100 hover:border-emerald-200 hover:bg-emerald-50 transition-all group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center mr-3 group-hover:bg-emerald-200 transition-colors">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $subject->name }}</h4>
                                <p class="text-xs text-gray-500">{{ $subject->subject_code ?? 'No code' }}</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 group-hover:text-emerald-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <p>No subjects assigned yet</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Subjects Taught Section with Assessment Stats -->
    <div class="mb-8" x-data="{ currentPage: 0, itemsPerPage: 2 }">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800">Subjects Taught - Assessment Stats</h3>
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
                    Page <span x-text="currentPage + 1"></span> of <span x-text="Math.ceil({{ count($subjectAssessmentData ?? []) }} / itemsPerPage)"></span>
                </span>
                <button @click="currentPage = Math.min(Math.ceil({{ count($subjectAssessmentData ?? []) }} / itemsPerPage) - 1, currentPage + 1)" 
                        :disabled="currentPage >= Math.ceil({{ count($subjectAssessmentData ?? []) }} / itemsPerPage) - 1"
                        :class="currentPage >= Math.ceil({{ count($subjectAssessmentData ?? []) }} / itemsPerPage) - 1 ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-200'"
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
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