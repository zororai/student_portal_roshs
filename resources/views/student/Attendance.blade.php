@extends('layouts.app')

@section('content')
@php
    $totalDays = $student->attendances->count();
    $presentDays = $student->attendances->where('attendence_status', 1)->count();
    $absentDays = $totalDays - $presentDays;
    $attendanceRate = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 0;
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Attendance Record</h1>
        <p class="mt-2 text-sm text-gray-600">Track your attendance history and performance</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Days -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Days</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalDays }}</p>
                </div>
            </div>
        </div>

        <!-- Present Days -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Present</p>
                    <p class="text-2xl font-bold text-green-600">{{ $presentDays }}</p>
                </div>
            </div>
        </div>

        <!-- Absent Days -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Absent</p>
                    <p class="text-2xl font-bold text-red-600">{{ $absentDays }}</p>
                </div>
            </div>
        </div>

        <!-- Attendance Rate -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 {{ $attendanceRate >= 75 ? 'bg-emerald-100' : ($attendanceRate >= 50 ? 'bg-yellow-100' : 'bg-red-100') }} rounded-lg">
                    <svg class="w-6 h-6 {{ $attendanceRate >= 75 ? 'text-emerald-600' : ($attendanceRate >= 50 ? 'text-yellow-600' : 'text-red-600') }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Attendance Rate</p>
                    <p class="text-2xl font-bold {{ $attendanceRate >= 75 ? 'text-emerald-600' : ($attendanceRate >= 50 ? 'text-yellow-600' : 'text-red-600') }}">{{ $attendanceRate }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Progress Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">Overall Attendance</span>
            <span class="text-sm font-bold {{ $attendanceRate >= 75 ? 'text-emerald-600' : ($attendanceRate >= 50 ? 'text-yellow-600' : 'text-red-600') }}">{{ $attendanceRate }}%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-3">
            <div class="h-3 rounded-full {{ $attendanceRate >= 75 ? 'bg-emerald-500' : ($attendanceRate >= 50 ? 'bg-yellow-500' : 'bg-red-500') }}" style="width: {{ $attendanceRate }}%"></div>
        </div>
        <p class="mt-2 text-xs text-gray-500">
            @if($attendanceRate >= 75)
                Excellent attendance! Keep it up.
            @elseif($attendanceRate >= 50)
                Good attendance, but there's room for improvement.
            @else
                Your attendance needs improvement. Please try to attend more classes.
            @endif
        </p>
    </div>

    <!-- Attendance Records Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-500 to-blue-600 px-6 py-4">
            <h3 class="text-lg font-semibold text-white">Attendance History</h3>
            <p class="text-indigo-100 text-sm">Detailed record of your attendance</p>
        </div>

        @if($student->attendances && $student->attendances->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Class</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Teacher</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($student->attendances->sortByDesc('attendence_date') as $attendance)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($attendance->attendence_date)->format('M d, Y') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $attendance->class->class_name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $attendance->teacher->user->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($attendance->attendence_status)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Present
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    Absent
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-gray-500 text-lg font-medium">No attendance records yet</p>
            <p class="text-gray-400 text-sm mt-1">Your attendance records will appear here once available</p>
        </div>
        @endif
    </div>
</div>
@endsection
