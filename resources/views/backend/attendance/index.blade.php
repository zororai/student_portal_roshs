@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Attendance Report</h1>
                <p class="mt-2 text-sm text-gray-600">View and analyze student attendance records</p>
            </div>
            <a href="{{ route('home') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                </span>
                Filter Options
            </h3>
        </div>
        <form action="{{ route('attendance.index') }}" method="GET" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                <!-- Report Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">Report Type</label>
                    <div class="flex items-center space-x-4">
                        <label class="relative flex items-center p-3 rounded-lg border border-blue-500 bg-blue-50 cursor-pointer">
                            <input name="type" type="radio" value="class" checked class="sr-only">
                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <span class="text-sm font-medium text-blue-700">By Class</span>
                        </label>
                    </div>
                </div>

                <!-- Month Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Month</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <select name="month" class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors appearance-none bg-white">
                            <option value="">-- Select Month --</option>
                            @foreach ($months as $month => $values)
                                <option value="{{ $month }}">{{ $month }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Generate Button -->
                <div>
                    <button type="submit" class="w-full px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Generate Report
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Attendance Results -->
    @if(count($attendances) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            @foreach ($attendances as $classid => $classAttendances)
                @php
                    $className = $classAttendances->first()->class->class_name ?? 'Class ' . $classid;
                    $totalRecords = $classAttendances->count();
                    $presentCount = $classAttendances->where('attendence_status', 1)->count();
                    $absentCount = $totalRecords - $presentCount;
                    $attendanceRate = $totalRecords > 0 ? round(($presentCount / $totalRecords) * 100, 1) : 0;
                    
                    // Group by student
                    $studentAttendances = $classAttendances->groupBy('student_id');
                @endphp
                
                <a href="{{ route('attendance.class-detail', ['class_id' => $classid, 'month' => request('month')]) }}" class="block bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
                    <!-- Class Header -->
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-600">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-bold text-white flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                {{ $className }}
                            </h3>
                            <div class="flex items-center gap-3">
                                <span class="text-white text-sm font-medium">{{ $studentAttendances->count() }} Students</span>
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Class Stats -->
                    <div class="px-6 py-4 bg-gray-50">
                        <div class="grid grid-cols-3 gap-4">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-gray-900">{{ $totalRecords }}</p>
                                <p class="text-xs text-gray-500 mt-1">Total Records</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-green-600">{{ $presentCount }}</p>
                                <p class="text-xs text-gray-500 mt-1">Present</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-red-600">{{ $absentCount }}</p>
                                <p class="text-xs text-gray-500 mt-1">Absent</p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="text-gray-600">Attendance Rate</span>
                                <span class="font-semibold text-gray-900">{{ $attendanceRate }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-2 rounded-full transition-all" style="width: {{ $attendanceRate }}%"></div>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
            <p class="text-gray-500 text-lg font-medium">No attendance records found</p>
            <p class="text-gray-400 text-sm mt-1">Select a month to view attendance reports</p>
        </div>
    @endif
</div>
@endsection