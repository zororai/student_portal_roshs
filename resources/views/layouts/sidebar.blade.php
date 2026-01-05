<aside x-data="{ collapsed: false }" 
     :class="[collapsed ? 'w-20' : 'w-64', sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0']" 
     class="sidebar bg-blue-600 h-screen shadow-sm fixed top-0 left-0 bottom-0 z-40 border-r border-blue-700 transition-all duration-300 transform flex flex-col">
    <!-- Header Section -->
    <div class="p-4 border-b border-blue-700">
        <div class="flex items-center justify-between">
            <div class="flex items-center flex-1" :class="collapsed ? 'justify-center' : 'space-x-3'">
                <div class="w-10 h-10 bg-gradient-to-br from-orange-400 to-pink-500 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                    </svg>
                </div>
                <div x-show="!collapsed">
                    <h2 class="text-sm font-semibold text-white">ROSHS Portal</h2>
                    <p class="text-xs text-blue-200">Foundation</p>
                </div>
            </div>
            <button @click="collapsed = !collapsed" class="p-2 bg-blue-700 hover:bg-blue-800 text-white rounded-lg transition-colors flex-shrink-0 ml-2" :title="collapsed ? 'Expand sidebar' : 'Collapse sidebar'">
                <svg class="w-5 h-5 transition-transform" :class="{'rotate-180': collapsed}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Navigation Section -->
    <div class="flex-1 overflow-y-auto p-4 space-y-1">
        <!-- Toggle Button -->
        <button @click="collapsed = !collapsed" class="w-full flex items-center px-3 py-2 mb-3 bg-blue-700 hover:bg-blue-800 text-white rounded-lg transition-colors" :class="collapsed ? 'justify-center' : 'justify-center'" :title="collapsed ? 'Expand sidebar' : 'Collapse sidebar'">
            <svg class="w-5 h-5 transition-transform flex-shrink-0" :class="{'rotate-180': collapsed}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
            <span class="ml-2 whitespace-nowrap" x-show="!collapsed">Toggle Menu</span>
        </button>
        
        <!-- Notifications -->
        <a href="{{ route('notifications.inbox') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'" :title="collapsed ? 'Notifications' : ''">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Notifications</span>
            </div>
        </a>

        <!-- Home/Dashboard -->
        @can('sidebar-home')
        <a href="{{ route('home') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'" :title="collapsed ? 'Home' : ''">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Home</span>
            </div>
        </a>
        @endcan

        @role('Parent')
        <!-- Parent Section -->
        <a href="{{ route('home') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Dashboard</span>
            </div>
        </a>

        <a href="{{ route('parentviewresults.studentresults') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Results</span>
            </div>
        </a>
        <a href="{{ route('parent.groceries.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Groceries List</span>
            </div>
        </a>

        <a href="{{ route('parent.timetable') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Class Timetable</span>
            </div>
        </a>

        <a href="{{ route('parent.assessments') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Student Assessments</span>
            </div>
        </a>

        <a href="{{ route('parent.medical-reports.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Medical Reports</span>
            </div>
        </a>

        <a href="{{ route('parent.disciplinary.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Disciplinary Records</span>
            </div>
        </a>

        <a href="{{ route('parent.payment-verification.create') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Payment Verification</span>
            </div>
        </a>
        @endrole


        @role('Teacher')
        <!-- Teacher Section -->
        <a href="http://student_portal_roshs.test/home" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Dashboard</span>
            </div>
        </a>

        @if(auth()->user()->teacher && auth()->user()->teacher->is_class_teacher)
        <!-- Class Teacher Section -->
        <div class="pt-2 pb-1" x-show="!collapsed">
            <span class="px-3 text-xs font-semibold text-blue-200 uppercase tracking-wider">Class Teacher</span>
        </div>
        
        <a href="{{ route('teacher.class-students') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'" :title="collapsed ? 'My Class Students' : ''">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">My Class Students</span>
            </div>
        </a>

        <a href="{{ route('teacher.class-attendance') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'" :title="collapsed ? 'Attendance Register' : ''">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Attendance Register</span>
            </div>
        </a>
        @endif

        <a href="{{ route('teacher.attendance.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">My Attendance</span>
            </div>
        </a>

        <a href="{{ route('teacher.leave.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Leave Applications</span>
            </div>
        </a>

        <a href="{{ route('teacher.studentrecord') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Student Record</span>
            </div>
        </a>

        <a href="{{ route('subject.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Subject List</span>
            </div>
        </a>

        <a href="{{ route('teacher.assessment') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Student Assessment</span>
            </div>
        </a>

        <a href="{{ route('results.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Add Results</span>
            </div>
        </a>

        <a href="{{ route('results.record') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Results Records</span>
            </div>
        </a>

        <a href="{{ route('teacher.disciplinary.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Disciplinary Records</span>
            </div>
        </a>

        <a href="{{ route('teacher.timetable') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Class Timetable</span>
            </div>
        </a>

        <a href="{{ route('teacher.logbook.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">My Logbook</span>
            </div>
        </a>
        @endrole

        @role('Student')
        <!-- Student Section -->
        <a href="{{ route('home') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Dashboard</span>
            </div>
        </a>

        <a href="{{ route('viewresults.studentresults') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Results</span>
            </div>
        </a>

        <a href="{{ route('attendancy.studentattendance') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Attendance Report</span>
            </div>
        </a>
        
        <a href="{{ route('viewsubject.studentresults') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Reading Materials</span>
            </div>
        </a>

        <a href="{{ route('student.timetable') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Class Timetable</span>
            </div>
        </a>
        @endrole

        @role('Admin')
        <!-- Admin Section -->
        
        <!-- Send Notifications (Admin Only) -->
        <a href="{{ route('admin.notifications.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'" :title="collapsed ? 'Send Notifications' : ''">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Send Notifications</span>
            </div>
        </a>
        @endrole
        
        <!-- Permission-based sections - accessible by any role with the permission -->
        <!-- Onboarding Process Section with Submenu -->
        @can('sidebar-onboard')
        <div x-data="{ open: false }" class="mt-1" x-show="!collapsed">
            <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 text-sm rounded-lg transition-colors" :class="open ? 'bg-blue-600 text-white' : 'text-white hover:bg-blue-700'">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                    </svg>
                    <span class="font-medium">Onboarding Process</span>
                </div>
                <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                @can('sidebar-user-management')
                <a href="{{ route('admin.users.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    User Management
                </a>
                @endcan
                @can('sidebar-teachers')
                <a href="{{ route('teacher.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Teachers
                </a>
                @endcan
                @can('sidebar-students')
                <a href="{{ route('student.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 14l9-5-9-5-9 5 9 5z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/>
                    </svg>
                    Students
                </a>
                @endcan
                @can('sidebar-subjects')
                <a href="{{ route('admin.subjects.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Subjects
                </a>
                @endcan
                @can('sidebar-classes')
                <a href="{{ route('classes.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Classes
                </a>
                @endcan
                @can('sidebar-parents')
                <a href="{{ route('parents.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Parents
                </a>
                @endcan
            </div>
        </div>
        @endcan
        
        <!-- Student Section with Submenu -->
        @can('sidebar-student-section')
        <div x-data="{ open: false }" class="mt-1" x-show="!collapsed">
            <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 text-sm rounded-lg transition-colors" :class="open ? 'bg-blue-600 text-white' : 'text-white hover:bg-blue-700'">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                @can('sidebar-student-record')
                <a href="{{ route('student.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Student Record
                </a>
                @endcan
                @can('sidebar-applicants')
                <a href="{{ route('admin.applicants.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    Applicants
                </a>
                @endcan
                @can('sidebar-disciplinary')
                <a href="{{ route('admin.disciplinary.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Disciplinary Records
                </a>
                @endcan
                @can('sidebar-medical-reports')
                <a href="{{ route('admin.medical-reports.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Student Medical Records
                </a>
                @endcan
                @can('sidebar-results-management')
                <a href="{{ route('manageresults.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Results Management
                </a>
                @endcan
                @can('sidebar-marking-scheme')
                <a href="{{ route('admin.marking-scheme.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    Marking Scheme
                </a>
                @endcan
                @can('sidebar-attendance')
                <a href="{{ route('attendance.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    Attendance Register
                </a>
                @endcan
            </div>
        </div>
        @endcan
        
        <!-- School Staff Section with Submenu -->
        @can('sidebar-school-staff')
        <div x-data="{ open: false }" class="mt-1" x-show="!collapsed">
            <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 text-sm rounded-lg transition-colors" :class="open ? 'bg-blue-600 text-white' : 'text-white hover:bg-blue-700'">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="font-medium">School Staff</span>
                </div>
                <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                @can('sidebar-staff-members')
                <a href="{{ route('admin.staff.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    Staff Members
                </a>
                @endcan
                @can('sidebar-logbook')
                <a href="{{ route('admin.logbook.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    Teacher Log Book
                </a>
                @endcan
                @can('sidebar-leave-management')
                <a href="{{ route('admin.leave.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Leave Management
                </a>
                @endcan
                @can('sidebar-timetable')
                <a href="{{ route('admin.timetable.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Timetable
                </a>
                @endcan
                @can('sidebar-webcam')
                <a href="{{ route('Webcam.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Webcam
                </a>
                @endcan
            </div>
        </div>
        @endcan
        
        <a href="{{ route('attendance.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors" :class="collapsed ? 'justify-center' : 'justify-between'" style="display: none;">
            <div class="flex items-center" :class="collapsed ? '' : 'space-x-3'">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span class="font-medium" x-show="!collapsed">Attendance</span>
            </div>
        </a>

        @can('sidebar-website')
        <div x-data="{ open: false }" class="mt-1" x-show="!collapsed">
            <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 text-sm rounded-lg transition-colors" :class="open ? 'bg-blue-600 text-white' : 'text-white hover:bg-blue-700'">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                    </svg>
                    <span class="font-medium">Website</span>
                </div>
                <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                <a href="{{ route('admin.website.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.website.general') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    General Settings
                </a>
                <a href="{{ route('admin.website.colors') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                    </svg>
                    Theme Colors
                </a>
                <a href="{{ route('admin.website.images') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Images & Logo
                </a>
                <a href="{{ route('admin.website.text') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Text Content
                </a>
                @can('sidebar-banner')
                <a href="{{ route('admin.website.banners') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    Homepage Banners
                </a>
                @endcan
                @can('sidebar-newsletter')
                <a href="{{ route('newsletters.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                    </svg>
                    Newsletter
                </a>
                @endcan
                @can('sidebar-events')
                <a href="" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Events
                </a>
                @endcan
            </div>
        </div>
        @endcan

        <!-- Finance & Accounting Section -->
        @can('sidebar-finance')
        <div x-data="{ open: false }" class="mt-1" x-show="!collapsed">
            <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 text-sm rounded-lg transition-colors" :class="open ? 'bg-blue-600 text-white' : 'text-white hover:bg-blue-700'">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-medium">Finance & Accounting</span>
                </div>
                <svg class="w-4 h-4 transition-transform" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div x-show="open" x-collapse class="ml-8 mt-1 space-y-1">
                @can('sidebar-student-payments')
                <a href="{{ route('finance.student-payments') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Student Payments
                </a>
                @endcan
                <a href="{{ route('admin.payment-verification.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Payment Verification
                </a>
                @can('sidebar-parents-arrears')
                <a href="{{ route('finance.parents-arrears') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Parents with Arrears
                </a>
                @endcan
                @can('sidebar-school-income')
                <a href="{{ route('finance.school-income') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                    School Income
                </a>
                @endcan
                @can('sidebar-school-expenses')
                <a href="{{ route('admin.finance.expenses.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                    </svg>
                    School Expenses
                </a>
                @endcan
                @can('sidebar-inventory')
                <a href="{{ route('finance.inventory.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Inventory
                </a>
                @endcan
                @can('sidebar-pos')
                <a href="{{ route('finance.products.pos') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    POS / Sell
                </a>
                @endcan
                @can('sidebar-student-groceries')
                <a href="{{ route('admin.groceries.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Student Groceries
                </a>
                @endcan
                <a href="{{ route('finance.statements') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Financial Statements
                </a>
                @can('sidebar-payroll')
                <a href="{{ route('admin.finance.payroll.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Payroll
                </a>
                @endcan
                @can('sidebar-cashbook')
                <a href="{{ route('admin.finance.cashbook.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Cash Book
                </a>
                @endcan
                @can('sidebar-purchase-orders')
                <a href="{{ route('admin.finance.purchase-orders.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Purchase Orders
                </a>
                @endcan
                @can('sidebar-reports')
                <a href="{{ route('admin.finance.dashboard') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Reports & Dashboard
                </a>
                @endcan
            </div>
        </div>
        @endcan

        <!-- Settings Section with Submenu -->
        @if(Auth::user()->hasRole('Admin') || Auth::user()->can('sidebar-settings'))
        <div x-data="{ open: false }" class="mt-1" x-show="!collapsed">
            <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 text-sm rounded-lg transition-colors" :class="open ? 'bg-blue-600 text-white' : 'text-white hover:bg-blue-700'">
                <div class="flex items-center space-x-3">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <a href="{{ route('roles-permissions') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Roles & Permissions
                </a>
                <a href="{{ route('sidebar.permissions') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                    Sidebar Permissions
                </a>
                <a href="{{ route('results_status.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Manage Term
                </a>
                <a href="{{ route('admin.settings.class-formats') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                    </svg>
                    Class Formats
                </a>
                <a href="{{ route('admin.settings.upgrade-direction') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                    Upgrade Direction
                </a>
                <a href="{{ route('admin.audit-trail.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Audit Trail
                </a>
                <a href="{{ route('admin.student-upgrade.index') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                    Student Class Upgrade
                </a>
                <a href="{{ route('admin.settings.geolocation') }}" class="flex items-center px-3 py-2 text-sm text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    School Geolocation
                </a>
            </div>
        </div>
        @endif
    </div>

</aside>
