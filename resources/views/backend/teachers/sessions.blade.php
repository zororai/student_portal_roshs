@extends('layouts.app')

@section('title', 'Teacher Sessions')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-br from-purple-500 to-indigo-600 p-3 rounded-2xl shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Teacher Sessions</h1>
                        <p class="text-gray-500 mt-1">Manage work schedules for all teachers</p>
                    </div>
                </div>
                <a href="{{ route('teacher.index') }}" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition-all duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Teachers
                </a>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-2xl p-4 flex items-center animate-fade-in">
            <div class="flex-shrink-0 bg-green-100 rounded-full p-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <p class="ml-3 text-green-800 font-medium">{{ session('success') }}</p>
        </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            @php
                $morningCount = $teachers->where('session', 'morning')->count();
                $afternoonCount = $teachers->where('session', 'afternoon')->count();
                $bothCount = $teachers->filter(function($t) { return $t->session === 'both' || !$t->session; })->count();
            @endphp
            <div style="background: linear-gradient(to bottom right, #fbbf24, #f97316);" class="rounded-2xl p-6 text-white shadow-lg transform hover:scale-105 transition-transform duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p style="color: #fef3c7;" class="text-sm font-medium">Morning Session</p>
                        <p class="text-4xl font-bold mt-1">{{ $morningCount }}</p>
                    </div>
                    <div style="background-color: rgba(255, 255, 255, 0.2);" class="rounded-xl p-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div style="background: linear-gradient(to bottom right, #818cf8, #a78bfa);" class="rounded-2xl p-6 text-white shadow-lg transform hover:scale-105 transition-transform duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p style="color: #e0e7ff;" class="text-sm font-medium">Afternoon Session</p>
                        <p class="text-4xl font-bold mt-1">{{ $afternoonCount }}</p>
                    </div>
                    <div style="background-color: rgba(255, 255, 255, 0.2);" class="rounded-xl p-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </div>
                </div>
            </div>
            <div style="background: linear-gradient(to bottom right, #34d399, #14b8a6);" class="rounded-2xl p-6 text-white shadow-lg transform hover:scale-105 transition-transform duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p style="color: #d1fae5;" class="text-sm font-medium">Both Sessions</p>
                        <p class="text-4xl font-bold mt-1">{{ $bothCount }}</p>
                    </div>
                    <div style="background-color: rgba(255, 255, 255, 0.2);" class="rounded-xl p-3">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
            <form action="{{ route('teacher.update-sessions') }}" method="POST">
                @csrf
                <input type="hidden" name="redirect" value="teacher.sessions">

                <!-- Quick Actions Bar -->
                <div style="background: linear-gradient(to right, #f9fafb, #f3f4f6);" class="px-6 py-4 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span class="font-semibold text-gray-700">Quick Actions:</span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" onclick="setAllSessions('morning')" style="background: linear-gradient(to right, #fbbf24, #f97316);" class="inline-flex items-center px-4 py-2 text-white text-sm font-medium rounded-xl transition-all duration-200 shadow-md hover:shadow-lg">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                All Morning
                            </button>
                            <button type="button" onclick="setAllSessions('afternoon')" style="background: linear-gradient(to right, #818cf8, #a78bfa);" class="inline-flex items-center px-4 py-2 text-white text-sm font-medium rounded-xl transition-all duration-200 shadow-md hover:shadow-lg">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                </svg>
                                All Afternoon
                            </button>
                            <button type="button" onclick="setAllSessions('both')" style="background: linear-gradient(to right, #34d399, #14b8a6);" class="inline-flex items-center px-4 py-2 text-white text-sm font-medium rounded-xl transition-all duration-200 shadow-md hover:shadow-lg">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                All Both
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Teachers Grid -->
                <div class="p-6">
                    @if($teachers->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($teachers as $teacher)
                        <div class="group bg-white border border-gray-200 rounded-2xl p-5 hover:shadow-lg hover:border-purple-300 transition-all duration-300">
                            <div class="flex flex-col items-center mb-4">
                                <div class="relative mb-3">
                                    <img src="{{ asset('images/profile/' . ($teacher->user->profile_picture ?? 'avatar.png')) }}" 
                                         alt="{{ $teacher->user->name }}" 
                                         class="rounded-full object-cover ring-4 ring-gray-100 group-hover:ring-purple-200 transition-all duration-300"
                                         style="width: 100px; height: 100px;">
                                    <div class="absolute bottom-0 right-0 w-6 h-6 rounded-full border-3 border-white
                                        @if($teacher->session === 'morning') bg-amber-400
                                        @elseif($teacher->session === 'afternoon') bg-indigo-400
                                        @else bg-emerald-400 @endif">
                                    </div>
                                </div>
                                <div class="text-center">
                                    <h3 class="text-base font-semibold text-gray-900">{{ $teacher->user->name }}</h3>
                                    <p class="text-sm text-gray-500">
                                        @if($teacher->session === 'morning')
                                            Currently: Morning
                                        @elseif($teacher->session === 'afternoon')
                                            Currently: Afternoon
                                        @else
                                            Currently: Both Sessions
                                        @endif
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Session Selection -->
                            <div class="flex rounded-xl bg-gray-100 p-1 session-group" data-teacher-id="{{ $teacher->id }}">
                                <input type="hidden" name="sessions[{{ $teacher->id }}]" id="session-input-{{ $teacher->id }}" value="{{ $teacher->session ?? 'both' }}">
                                <button type="button" onclick="selectSession({{ $teacher->id }}, 'morning', this)" 
                                        class="session-btn flex-1 py-2 px-3 text-center rounded-lg text-sm font-medium transition-all duration-200 cursor-pointer"
                                        data-session="morning"
                                        style="{{ $teacher->session === 'morning' ? 'background: linear-gradient(to right, #fbbf24, #f97316); color: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);' : 'color: #4b5563;' }}">
                                    <svg class="w-4 h-4 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    Morning
                                </button>
                                <button type="button" onclick="selectSession({{ $teacher->id }}, 'afternoon', this)" 
                                        class="session-btn flex-1 py-2 px-3 text-center rounded-lg text-sm font-medium transition-all duration-200 cursor-pointer"
                                        data-session="afternoon"
                                        style="{{ $teacher->session === 'afternoon' ? 'background: linear-gradient(to right, #818cf8, #a78bfa); color: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);' : 'color: #4b5563;' }}">
                                    <svg class="w-4 h-4 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                    </svg>
                                    Afternoon
                                </button>
                                <button type="button" onclick="selectSession({{ $teacher->id }}, 'both', this)" 
                                        class="session-btn flex-1 py-2 px-3 text-center rounded-lg text-sm font-medium transition-all duration-200 cursor-pointer"
                                        data-session="both"
                                        style="{{ ($teacher->session === 'both' || !$teacher->session) ? 'background: linear-gradient(to right, #34d399, #14b8a6); color: white; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);' : 'color: #4b5563;' }}">
                                    <svg class="w-4 h-4 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Both
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-16">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No Teachers Found</h3>
                        <p class="text-gray-500 mb-6">Add teachers to start managing their sessions.</p>
                        <a href="{{ route('teacher.create') }}" class="inline-flex items-center px-6 py-3 bg-purple-600 text-white font-medium rounded-xl hover:bg-purple-700 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Add Teacher
                        </a>
                    </div>
                    @endif
                </div>

                <!-- Save Button -->
                @if($teachers->count() > 0)
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-500">
                            <span class="font-medium text-gray-700">{{ $teachers->count() }}</span> teachers total
                        </p>
                        <button type="submit" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-semibold rounded-xl hover:from-purple-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Save All Changes
                        </button>
                    </div>
                </div>
                @endif
            </form>
        </div>
    </div>
</div>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fade-in 0.3s ease-out;
    }
</style>

<script>
function selectSession(teacherId, session, clickedBtn) {
    // Update hidden input value
    document.getElementById('session-input-' + teacherId).value = session;
    
    // Get the parent container
    var container = clickedBtn.parentElement;
    var buttons = container.querySelectorAll('.session-btn');
    
    // Reset all buttons in this group
    buttons.forEach(function(btn) {
        btn.style.background = '';
        btn.style.color = '#4b5563';
        btn.style.boxShadow = '';
    });
    
    // Style the clicked button based on session type
    if (session === 'morning') {
        clickedBtn.style.background = 'linear-gradient(to right, #fbbf24, #f97316)';
    } else if (session === 'afternoon') {
        clickedBtn.style.background = 'linear-gradient(to right, #818cf8, #a78bfa)';
    } else {
        clickedBtn.style.background = 'linear-gradient(to right, #34d399, #14b8a6)';
    }
    clickedBtn.style.color = 'white';
    clickedBtn.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1)';
}

function setAllSessions(session) {
    document.querySelectorAll('.session-group').forEach(function(group) {
        var teacherId = group.dataset.teacherId;
        var btn = group.querySelector('[data-session="' + session + '"]');
        if (btn) {
            selectSession(teacherId, session, btn);
        }
    });
}
</script>
@endsection
