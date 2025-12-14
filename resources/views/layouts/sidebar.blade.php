<div x-data="{ collapsed: false }" :class="collapsed ? 'w-20' : 'w-64'" class="sidebar block bg-white h-screen shadow-lg fixed top-0 left-0 bottom-0 z-40 overflow-y-auto border-r border-gray-200 transition-all duration-300">
    <!-- Header Section -->
    <div class="p-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div class="flex items-center flex-1" :class="collapsed ? 'justify-center' : 'space-x-3'">
                <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-pink-500 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                    </svg>
                </div>
                <div x-show="!collapsed">
                    <h2 class="text-sm font-semibold text-gray-900">ROSHS Portal</h2>
                    <p class="text-xs text-gray-500">Foundation</p>
                </div>
            </div>
            <button @click="collapsed = !collapsed" class="p-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex-shrink-0 ml-2" :title="collapsed ? 'Expand sidebar' : 'Collapse sidebar'">
                <svg class="w-5 h-5 transition-transform" :class="{'rotate-180': collapsed}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Navigation Section -->
    <div class="p-4 space-y-1">
        <!-- Toggle Button -->
        <button @click="collapsed = !collapsed" class="w-full flex items-center px-3 py-2 mb-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors" :class="collapsed ? 'justify-center' : 'justify-center'" :title="collapsed ? 'Expand sidebar' : 'Collapse sidebar'">
            <svg class="w-5 h-5 transition-transform flex-shrink-0" :class="{'rotate-180': collapsed}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
            <span class="ml-2 whitespace-nowrap" x-show="!collapsed">Toggle Menu</span>
        </button>
        
        <!-- Notifications -->
        <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'" :title="collapsed ? 'Notifications' : ''">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Notifications</span>
            </div>
        </a>

        <!-- Home/Dashboard -->
        <a href="{{ route('home') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'" :title="collapsed ? 'Home' : ''">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Home</span>
            </div>
        </a>

        @role('Parent')
        <!-- Parent Section -->
        <a href="{{ route('parentviewresults.studentresults') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Results</span>
            </div>
        </a>
        <a href="{{ route('viewresults.studentresults') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Student Grosery</span>
            </div>
        </a>
        @endrole


        @role('Teacher')
        <!-- Teacher Section -->
        <a href="{{ route('teacher.studentrecord') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Student Record</span>
            </div>
        </a>

        <a href="{{ route('subject.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Subject List</span>
            </div>
        </a>

        <a href="{{ route('teacher.assessment') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Student Assessment</span>
            </div>
        </a>

        <a href="{{ route('results.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Add Results</span>
            </div>
        </a>

        <a href="{{ route('results.record') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Results Records</span>
            </div>
        </a>

        <a href="{{ route('teacher.disciplinary.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Disciplinary Records</span>
            </div>
        </a>
        @endrole

        @role('Student')
        <!-- Student Section -->
        <a href="{{ route('viewresults.studentresults') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Results</span>
            </div>
        </a>

        <a href="{{ route('attendancy.studentattendance') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Attendance Report</span>
            </div>
        </a>
        
        <a href="{{ route('viewsubject.studentresults') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Reading Materials</span>
            </div>
        </a>
        @endrole

        @role('Admin')
        <!-- Admin Section -->
        
        <!-- Board Section with Submenu -->
        <div x-data="{ open: false }" class="mt-1" x-show="!collapsed">
            <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                    </svg>
                    <span class="font-medium">Board</span>
                </div>
                <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                <a href="{{ route('teacher.index') }}" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">Teachers</a>
                <a href="{{ route('student.index') }}" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">Students</a>
                <a href="{{ route('admin.subjects.index') }}" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">Subjects</a>
                <a href="{{ route('classes.index') }}" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">Classes</a>
                <a href="{{ route('parents.index') }}" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">Parents</a>
            </div>
        </div>
        
        <!-- Student Section with Submenu -->
        <div x-data="{ open: false }" class="mt-1" x-show="!collapsed">
            <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                    </svg>
                    <span class="font-medium">Student</span>
                </div>
                <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                <a href="{{ route('student.index') }}" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">Student Record</a>
                <a href="{{ route('admin.disciplinary.index') }}" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">Disciplinary Records</a>
                <a href="{{ route('manageresults.index') }}" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">Results Management</a>
                <a href="{{ route('attendance.index') }}" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">Attendance Register</a>
            </div>
        </div>
        
        <a href="{{ route('Webcam.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Webcam</span>
            </div>
        </a>
        
        <a href="{{ route('attendance.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'" style="display: none;">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Attendance</span>
            </div>
        </a>

        <div x-data="{ open: false }" class="mt-1" x-show="!collapsed">
            <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                    </svg>
                    <span class="font-medium">Website</span>
                </div>
                <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                <a href="{{ route('banner.index') }}" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">Banner</a>
                <a href="{{ route('newsletters.index') }}" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">Newsletter</a>
                <a href="" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">Events</a>
            </div>
        </div>

        <!-- Settings Section with Submenu -->
        <div x-data="{ open: false }" class="mt-1" x-show="!collapsed">
            <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="font-medium">Settings</span>
                </div>
                <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                <a href="{{ route('roles-permissions') }}" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">Roles & Permissions</a>
                <a href="{{ route('results_status.index') }}" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">Manage Term</a>
            </div>
        </div>

        <!-- Finance & Accounting Section -->
        <div x-data="{ open: false }" class="mt-1" x-show="!collapsed">
            <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 text-sm text-gray-700 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-medium">Finance & Accounting</span>
                </div>
                <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                <a href="{{ route('finance.student-payments') }}" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">Student Payments</a>
                <a href="{{ route('finance.parents-arrears') }}" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">Parents with Arrears</a>
                <a href="{{ route('finance.school-income') }}" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">School Income</a>
                <a href="{{ route('finance.school-expenses') }}" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">School Expenses</a>
                <a href="{{ route('finance.products') }}" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">Products Sold</a>
                <a href="{{ route('finance.statements') }}" class="block px-3 py-2 text-sm text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">Financial Statements</a>
            </div>
        </div>
        @endrole
    </div>

    <!-- User Profile Section at Bottom -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 bg-white">
        <div class="flex items-center" :class="collapsed ? 'justify-center' : 'space-x-3'">
            <div class="relative">
                <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name ?? 'User' }}&background=f97316&color=fff" alt="User Avatar" class="w-10 h-10 rounded-full">
                <span class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
            </div>
            <div class="flex-1 min-w-0" x-show="!collapsed">
                <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name ?? 'User' }}</p>
                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email ?? 'user@example.com' }}</p>
            </div>
            <button class="text-gray-400 hover:text-gray-600" x-show="!collapsed">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                </svg>
            </button>
        </div>
    </div>
</div>
