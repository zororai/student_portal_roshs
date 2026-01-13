<div class="max-w-7xl mx-auto">
    <!-- Welcome Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ $student->user->name }}!</h1>
        <p class="mt-2 text-gray-600">Here's your academic overview</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Class Card -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white text-sm font-semibold">My Class</p>
                    <p class="text-2xl font-bold mt-2">{{ $student->class->class_name ?? 'N/A' }}</p>
                </div>
                <div class="p-3 bg-white/20 rounded-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-white text-base font-bold">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                </svg>
                Roll #{{ $student->roll_number }}
            </div>
        </div>

        <!-- Subjects Card -->
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-900 text-sm font-semibold">My Subjects</p>
                    <p class="text-gray-900 text-4xl font-bold mt-2">{{ $student->class ? $student->class->subjects->count() : 0 }}</p>
                </div>
                <div class="p-3 bg-white/20 rounded-lg">
                    <svg class="w-8 h-8 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-gray-900 text-base font-bold">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                </svg>
                Currently enrolled
            </div>
        </div>

        <!-- Attendance Card -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white text-sm font-semibold">Attendance</p>
                    @php
                        $totalAttendance = $student->attendances ? $student->attendances->count() : 0;
                        $presentCount = $student->attendances ? $student->attendances->where('attendence', 1)->count() : 0;
                        $attendancePercent = $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100) : 0;
                    @endphp
                    <p class="text-4xl font-bold mt-2">{{ $attendancePercent }}%</p>
                </div>
                <div class="p-3 bg-white/20 rounded-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-white text-base font-bold">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                </svg>
                {{ $presentCount }}/{{ $totalAttendance }} days present
            </div>
        </div>

        <!-- Performance Card -->
        <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-900 text-sm font-semibold">Avg Performance</p>
                    @php
                        $avgPerformance = isset($subjectStats) && count($subjectStats) > 0 
                            ? round(collect($subjectStats)->avg('performance')) 
                            : 0;
                    @endphp
                    <p class="text-gray-900 text-4xl font-bold mt-2">{{ $avgPerformance }}%</p>
                </div>
                <div class="p-3 bg-white/20 rounded-lg">
                    <svg class="w-8 h-8 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-gray-900 text-base font-bold">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M12 7a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0V8.414l-4.293 4.293a1 1 0 01-1.414 0L8 10.414l-4.293 4.293a1 1 0 01-1.414-1.414l5-5a1 1 0 011.414 0L11 10.586 14.586 7H12z" clip-rule="evenodd"/>
                </svg>
                Across all subjects
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('viewsubject.studentresults') }}" class="flex flex-col items-center p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors group">
                <div class="p-3 bg-blue-500 rounded-lg mb-3 group-hover:bg-blue-600 transition-colors">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700">My Subjects</span>
            </a>
            <a href="{{ route('viewresults.studentresults') }}" class="flex flex-col items-center p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition-colors group">
                <div class="p-3 bg-blue-500 rounded-lg mb-3 group-hover:bg-blue-600 transition-colors">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700">My Results</span>
            </a>
            <a href="{{ route('attendancy.studentattendance') }}" class="flex flex-col items-center p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition-colors group">
                <div class="p-3 bg-purple-500 rounded-lg mb-3 group-hover:bg-purple-600 transition-colors">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700">Attendance</span>
            </a>
            <a href="{{ route('student.timetable') }}" class="flex flex-col items-center p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition-colors group">
                <div class="p-3 bg-purple-500 rounded-lg mb-3 group-hover:bg-purple-600 transition-colors">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700">Timetable</span>
            </a>
        </div>
    </div>

    <!-- Chair & Desk Assignment Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">My Seat Assignment</h3>
                <p class="text-gray-500 text-sm">Your assigned chair and desk in class</p>
            </div>
            <button onclick="openChairDeskModal()" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                Update
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="flex items-center p-4 bg-gradient-to-r from-indigo-50 to-indigo-100 rounded-xl border border-indigo-200">
                <div class="p-3 bg-indigo-500 rounded-lg mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-indigo-600 font-medium uppercase tracking-wide">Chair Number</p>
                    <p id="currentChair" class="text-2xl font-bold text-gray-900">{{ $student->chair ?? 'Not assigned' }}</p>
                </div>
            </div>
            <div class="flex items-center p-4 bg-gradient-to-r from-amber-50 to-amber-100 rounded-xl border border-amber-200">
                <div class="p-3 bg-amber-500 rounded-lg mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-amber-600 font-medium uppercase tracking-wide">Desk/Table Number</p>
                    <p id="currentDesk" class="text-2xl font-bold text-gray-900">{{ $student->desk ?? 'Not assigned' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Chair/Desk Edit Modal -->
    <div id="chairDeskModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeChairDeskModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white">Update Seat Assignment</h3>
                    <p class="text-blue-100 text-sm">Enter your chair and desk numbers</p>
                </div>
                <div class="bg-white px-6 py-4">
                    <div class="space-y-4">
                        <div>
                            <label for="chairInput" class="block text-sm font-medium text-gray-700 mb-1">Chair Number</label>
                            <input type="text" id="chairInput" value="{{ $student->chair ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., A1, B5, 12">
                        </div>
                        <div>
                            <label for="deskInput" class="block text-sm font-medium text-gray-700 mb-1">Desk/Table Number</label>
                            <input type="text" id="deskInput" value="{{ $student->desk ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g., T1, D3, 7">
                        </div>
                    </div>
                    <div id="chairDeskMessage" class="mt-4 hidden"></div>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end space-x-3">
                    <button type="button" onclick="closeChairDeskModal()" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-300 transition-colors">
                        Cancel
                    </button>
                    <button type="button" onclick="saveChairDesk()" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile and Parent Info Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- My Profile Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                <h3 class="text-lg font-semibold text-white">My Profile</h3>
                <p class="text-blue-100 text-sm">Personal information</p>
            </div>
            <div class="p-6">
                <div class="flex items-center mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white text-2xl font-bold mr-4">
                        {{ strtoupper(substr($student->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-gray-900">{{ $student->user->name }}</h4>
                        <p class="text-gray-500">{{ $student->user->email }}</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500">Phone</p>
                            <p class="font-medium text-gray-900">{{ $student->phone ?? 'Not provided' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500">Gender</p>
                            <p class="font-medium text-gray-900">{{ $student->gender ?? 'Not specified' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500">Date of Birth</p>
                            <p class="font-medium text-gray-900">{{ $student->dateofbirth ?? 'Not provided' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500">Address</p>
                            <p class="font-medium text-gray-900">{{ $student->current_address ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Parent Info Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 px-6 py-4">
                <h3 class="text-lg font-semibold text-white">Parent/Guardian</h3>
                <p class="text-emerald-100 text-sm">Contact information</p>
            </div>
            <div class="p-6">
                @if($student->parent)
                <div class="flex items-center mb-6">
                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-400 to-emerald-600 rounded-full flex items-center justify-center text-white text-2xl font-bold mr-4">
                        {{ strtoupper(substr($student->parent->user->name ?? 'P', 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="text-xl font-bold text-gray-900">{{ $student->parent->user->name ?? 'N/A' }}</h4>
                        <p class="text-gray-500">{{ $student->parent->user->email ?? 'N/A' }}</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500">Phone</p>
                            <p class="font-medium text-gray-900">{{ $student->parent->phone ?? 'Not provided' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <div>
                            <p class="text-xs text-gray-500">Address</p>
                            <p class="font-medium text-gray-900">{{ $student->parent->current_address ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>
                @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p>No parent information available</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Subject Performance Section -->
    @if(isset($subjectStats) && count($subjectStats) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h3 class="text-xl font-bold text-gray-800 mb-6">Subject Performance</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($subjectStats as $stat)
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 hover:border-blue-200 hover:shadow-md transition-all">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-semibold text-gray-900">{{ $stat['subject'] }}</h4>
                    <span class="text-2xl font-bold {{ $stat['performance'] >= 50 ? 'text-green-600' : 'text-red-600' }}">{{ $stat['performance'] }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                    <div class="h-3 rounded-full transition-all {{ $stat['performance'] >= 75 ? 'bg-green-500' : ($stat['performance'] >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $stat['performance'] }}%"></div>
                </div>
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <span>{{ $stat['assessments'] }} assessments</span>
                    <span class="px-2 py-0.5 rounded text-xs font-medium {{ $stat['performance'] >= 75 ? 'bg-green-100 text-green-700' : ($stat['performance'] >= 50 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                        {{ $stat['performance'] >= 75 ? 'Excellent' : ($stat['performance'] >= 50 ? 'Good' : 'Needs Work') }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- My Assessment Performance -->
    @if(isset($studentAssessmentStats))
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Performance by Assessment Type</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Chart -->
            <div>
                <canvas id="studentAssessmentChart" height="300"></canvas>
            </div>
            <!-- Table -->
            <div>
                <h5 class="text-sm font-semibold text-gray-700 mb-3">Performance Summary</h5>
                <div class="overflow-hidden rounded-lg border border-gray-200">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="text-left py-3 px-4 font-semibold text-gray-700">Assessment Type</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-700">Taken</th>
                                <th class="text-center py-3 px-4 font-semibold text-gray-700">Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($studentAssessmentStats as $stat)
                            <tr class="border-t border-gray-100 hover:bg-gray-50 transition-colors">
                                <td class="py-3 px-4 text-gray-700 font-medium">{{ $stat['type'] }}</td>
                                <td class="text-center py-3 px-4">
                                    <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-700 rounded-full font-semibold">{{ $stat['given'] }}</span>
                                </td>
                                <td class="text-center py-3 px-4">
                                    @if($stat['given'] > 0)
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $stat['performance'] >= 75 ? 'bg-green-100 text-green-700' : ($stat['performance'] >= 50 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
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
    @endif
</div>

@if(isset($studentAssessmentStats))
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
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(20, 184, 166, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                        'rgba(99, 102, 241, 0.8)',
                        'rgba(34, 197, 94, 0.8)'
                    ],
                    borderRadius: 8,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: { display: true, text: 'Performance (%)' },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: 'My Performance by Assessment Type',
                        font: { size: 14, weight: 'bold' }
                    }
                }
            }
        });
    }
});
</script>
@endif

<script>
function openChairDeskModal() {
    document.getElementById('chairDeskModal').classList.remove('hidden');
}

function closeChairDeskModal() {
    document.getElementById('chairDeskModal').classList.add('hidden');
    document.getElementById('chairDeskMessage').classList.add('hidden');
}

function saveChairDesk() {
    const chair = document.getElementById('chairInput').value;
    const desk = document.getElementById('deskInput').value;
    const messageDiv = document.getElementById('chairDeskMessage');

    fetch('{{ route("student.update-chair-desk") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ chair: chair, desk: desk })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('currentChair').textContent = data.chair || 'Not assigned';
            document.getElementById('currentDesk').textContent = data.desk || 'Not assigned';
            
            messageDiv.innerHTML = '<div class="p-3 bg-green-100 text-green-700 rounded-lg">' + data.message + '</div>';
            messageDiv.classList.remove('hidden');
            
            setTimeout(() => {
                closeChairDeskModal();
            }, 1500);
        } else {
            messageDiv.innerHTML = '<div class="p-3 bg-red-100 text-red-700 rounded-lg">' + data.message + '</div>';
            messageDiv.classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        messageDiv.innerHTML = '<div class="p-3 bg-red-100 text-red-700 rounded-lg">An error occurred. Please try again.</div>';
        messageDiv.classList.remove('hidden');
    });
}
</script>