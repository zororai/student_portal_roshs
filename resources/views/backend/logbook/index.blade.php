@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Teacher Log Book</h1>
                <p class="mt-2 text-sm text-gray-600">Monitor teacher attendance and availability</p>
            </div>
            <div class="flex items-center space-x-4">
                <form method="GET" action="{{ route('admin.logbook.index') }}" class="flex items-center space-x-2">
                    <input type="date" name="date" value="{{ $selectedDate->format('Y-m-d') }}" 
                           class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Filter
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Teachers -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Teachers</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalTeachers }}</p>
                </div>
            </div>
        </div>

        <!-- Present Today -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Present</p>
                    <p class="text-3xl font-bold text-green-600">{{ $presentTeachers }}</p>
                </div>
            </div>
        </div>

        <!-- Absent Today -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-full">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Absent</p>
                    <p class="text-3xl font-bold text-red-600">{{ $absentTeachers }}</p>
                </div>
            </div>
        </div>

        <!-- Currently On Site -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">On Site Now</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $clockedInNow }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance Progress -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Attendance Rate for {{ $selectedDate->format('F d, Y') }}</h3>
        <div class="relative pt-1">
            @php
                $attendanceRate = $totalTeachers > 0 ? round(($presentTeachers / $totalTeachers) * 100) : 0;
            @endphp
            <div class="flex mb-2 items-center justify-between">
                <div>
                    <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full {{ $attendanceRate >= 80 ? 'text-green-600 bg-green-200' : ($attendanceRate >= 50 ? 'text-yellow-600 bg-yellow-200' : 'text-red-600 bg-red-200') }}">
                        {{ $attendanceRate }}% Attendance
                    </span>
                </div>
                <div class="text-right">
                    <span class="text-xs font-semibold text-gray-600">{{ $presentTeachers }}/{{ $totalTeachers }} teachers</span>
                </div>
            </div>
            <div class="overflow-hidden h-4 mb-4 text-xs flex rounded-full bg-gray-200">
                <div style="width: {{ $attendanceRate }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center {{ $attendanceRate >= 80 ? 'bg-green-500' : ($attendanceRate >= 50 ? 'bg-yellow-500' : 'bg-red-500') }} transition-all duration-500"></div>
            </div>
        </div>
    </div>

    <!-- Teacher List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Teacher Attendance List</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time Out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($teachers as $teacher)
                        @php
                            $log = $teacher->logs->first();
                            $status = 'absent';
                            $statusColor = 'red';
                            if ($log) {
                                if ($log->time_in && !$log->time_out) {
                                    $status = 'present';
                                    $statusColor = 'green';
                                } elseif ($log->time_in && $log->time_out) {
                                    $status = 'left';
                                    $statusColor = 'gray';
                                }
                            }
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if($teacher->user->profile_picture)
                                            <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $teacher->user->profile_picture) }}" alt="">
                                        @else
                                            <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                                <span class="text-white font-medium text-sm">{{ substr($teacher->user->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $teacher->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $teacher->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                                    @if($status == 'present')
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                                    @endif
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $log && $log->time_in ? \Carbon\Carbon::parse($log->time_in)->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $log && $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($log && $log->within_boundary)
                                    <span class="text-green-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Within Boundary
                                    </span>
                                @elseif($log)
                                    <span class="text-yellow-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        Outside
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($log && $log->time_in && $log->time_out)
                                    @php
                                        $timeIn = \Carbon\Carbon::parse($log->time_in);
                                        $timeOut = \Carbon\Carbon::parse($log->time_out);
                                        $hours = $timeOut->diffInHours($timeIn);
                                        $minutes = $timeOut->diffInMinutes($timeIn) % 60;
                                    @endphp
                                    {{ $hours }}h {{ $minutes }}m
                                @elseif($log && $log->time_in)
                                    @php
                                        $timeIn = \Carbon\Carbon::parse($log->time_in);
                                        $hours = now()->diffInHours($timeIn);
                                        $minutes = now()->diffInMinutes($timeIn) % 60;
                                    @endphp
                                    <span class="text-green-600">{{ $hours }}h {{ $minutes }}m (ongoing)</span>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                No teachers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
