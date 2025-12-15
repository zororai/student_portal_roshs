<div class="max-w-7xl mx-auto">
    <!-- Welcome Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ $student->user->name }}!</h1>
        <p class="text-gray-600 mt-1">Here's an overview of your academic progress</p>
    </div>

    <!-- Profile Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        <div class="h-24 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-600"></div>
        <div class="relative px-6 pb-6">
            <div class="flex flex-col sm:flex-row sm:items-end">
                <div class="flex items-end -mt-12">
                    <img class="w-24 h-24 rounded-xl border-4 border-white shadow-lg object-cover bg-white" 
                         src="{{ asset('images/profile/' . $student->user->profile_picture) }}" 
                         alt="{{ $student->user->name }}">
                    <div class="ml-4 mb-1 hidden sm:block">
                        <h2 class="text-xl font-bold text-gray-900">{{ $student->user->name }}</h2>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $student->class->class_name ?? 'No Class' }}
                            </span>
                            <span class="text-sm text-gray-500">Roll #{{ $student->roll_number }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4 sm:hidden">
                <h2 class="text-xl font-bold text-gray-900">{{ $student->user->name }}</h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $student->class->class_name ?? 'No Class' }}
                    </span>
                    <span class="text-sm text-gray-500">Roll #{{ $student->roll_number }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Cards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Student Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-5 py-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-white">My Information</h3>
                </div>
            </div>
            <div class="p-5 space-y-3">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500 text-sm">Email</span>
                    <span class="text-gray-900 font-medium text-sm">{{ $student->user->email }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500 text-sm">Phone</span>
                    <span class="text-gray-900 font-medium text-sm">{{ $student->phone ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500 text-sm">Gender</span>
                    <span class="text-gray-900 font-medium text-sm">{{ $student->gender ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500 text-sm">Date of Birth</span>
                    <span class="text-gray-900 font-medium text-sm">{{ $student->dateofbirth ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-500 text-sm">Address</span>
                    <span class="text-gray-900 font-medium text-sm text-right max-w-xs truncate">{{ $student->current_address ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Parent/Guardian Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-5 py-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white">Parent/Guardian</h3>
            </div>
            <div class="p-5 space-y-3">
                @if($student->parent)
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500 text-sm">Name</span>
                    <span class="text-gray-900 font-medium text-sm">{{ $student->parent->user->name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500 text-sm">Email</span>
                    <span class="text-gray-900 font-medium text-sm">{{ $student->parent->user->email ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <span class="text-gray-500 text-sm">Phone</span>
                    <span class="text-gray-900 font-medium text-sm">{{ $student->parent->phone ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between py-2">
                    <span class="text-gray-500 text-sm">Address</span>
                    <span class="text-gray-900 font-medium text-sm text-right max-w-xs truncate">{{ $student->parent->current_address ?? 'N/A' }}</span>
                </div>
                @else
                <div class="text-center py-6 text-gray-500">
                    <p>No parent/guardian information available</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Subject Performance Section -->
    @if(isset($subjectStats) && count($subjectStats) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-5 py-4">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-white">Subject Performance</h3>
            </div>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($subjectStats as $stat)
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-semibold text-gray-800">{{ $stat['subject'] }}</span>
                        <span class="text-lg font-bold {{ $stat['performance'] >= 50 ? 'text-green-600' : 'text-red-600' }}">{{ $stat['performance'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mb-2">
                        <div class="h-2.5 rounded-full {{ $stat['performance'] >= 50 ? 'bg-green-500' : 'bg-red-500' }}" style="width: {{ min($stat['performance'], 100) }}%"></div>
                    </div>
                    <span class="text-xs text-gray-500">{{ $stat['assessments'] }} assessments completed</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h3 class="text-lg font-bold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <a href="{{ route('viewsubject.studentresults') }}" class="flex flex-col items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors group">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-2 group-hover:bg-blue-200 transition-colors">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700">My Subjects</span>
            </a>
            <a href="{{ route('viewresults.studentresults') }}" class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors group">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-2 group-hover:bg-green-200 transition-colors">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700">My Results</span>
            </a>
            <a href="{{ route('attendancy.studentattendance') }}" class="flex flex-col items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors group">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-2 group-hover:bg-purple-200 transition-colors">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700">Attendance</span>
            </a>
            <a href="{{ route('profile') }}" class="flex flex-col items-center p-4 bg-orange-50 rounded-lg hover:bg-orange-100 transition-colors group">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-2 group-hover:bg-orange-200 transition-colors">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700">My Profile</span>
            </a>
        </div>
    </div>

    <!-- My Assessment Performance -->
    @if(isset($studentAssessmentStats))
    <div class="w-full block mt-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">My Assessment Performance</h3>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Chart -->
                <div>
                    <canvas id="studentAssessmentChart" height="300"></canvas>
                </div>
                <!-- Table -->
                <div>
                    <h5 class="text-sm font-semibold text-gray-700 mb-3">Performance Summary</h5>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="text-left py-2 px-3">Assessment Type</th>
                                <th class="text-center py-2 px-3">Taken</th>
                                <th class="text-center py-2 px-3">Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($studentAssessmentStats as $stat)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-2 px-3 text-gray-700">{{ $stat['type'] }}</td>
                                <td class="text-center py-2 px-3">{{ $stat['given'] }}</td>
                                <td class="text-center py-2 px-3">
                                    @if($stat['given'] > 0)
                                    <span class="px-2 py-1 rounded text-xs font-medium {{ $stat['performance'] >= 50 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $stat['performance'] }}%
                                    </span>
                                    @else
                                    <span class="text-gray-400">--</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('studentAssessmentChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode(collect($studentAssessmentStats)->pluck('type')) !!},
                    datasets: [{
                        label: 'My Performance %',
                        data: {!! json_encode(collect($studentAssessmentStats)->pluck('performance')) !!},
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
                        legend: { display: false },
                        title: {
                            display: true,
                            text: 'My Performance by Assessment Type'
                        }
                    }
                });
            }
        });
        </script>
        @endif
    </div>