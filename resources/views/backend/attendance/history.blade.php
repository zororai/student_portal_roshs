@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Teacher Attendance History</h1>
                <p class="text-gray-600 mt-1">View and manage teacher attendance records</p>
            </div>
            <button onclick="document.getElementById('addModal').classList.remove('hidden')" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add Record
            </button>
        </div>

        <!-- Standard Work Hours Info -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl shadow-sm p-4 mb-6 text-white">
            @if($sessionMode === 'dual')
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center space-x-8">
                    <!-- Morning Session -->
                    <div class="bg-amber-500/20 rounded-lg p-3">
                        <p class="text-amber-200 text-xs uppercase tracking-wide mb-1">Morning Session</p>
                        <div class="flex items-center space-x-3">
                            <span class="text-xl font-bold">{{ \Carbon\Carbon::parse($standardCheckIn)->format('H:i') }}</span>
                            <span class="text-blue-300">→</span>
                            <span class="text-xl font-bold">{{ \Carbon\Carbon::parse($standardCheckOut)->format('H:i') }}</span>
                        </div>
                    </div>
                    <!-- Afternoon Session -->
                    <div class="bg-indigo-500/20 rounded-lg p-3">
                        <p class="text-indigo-200 text-xs uppercase tracking-wide mb-1">Afternoon Session</p>
                        <div class="flex items-center space-x-3">
                            <span class="text-xl font-bold">{{ \Carbon\Carbon::parse($afternoonCheckIn)->format('H:i') }}</span>
                            <span class="text-blue-300">→</span>
                            <span class="text-xl font-bold">{{ \Carbon\Carbon::parse($afternoonCheckOut)->format('H:i') }}</span>
                        </div>
                    </div>
                </div>
                <div class="text-sm text-blue-100">
                    <p class="flex items-center"><span class="w-2 h-2 bg-amber-400 rounded-full mr-2"></span>Morning: {{ \Carbon\Carbon::parse($standardCheckIn)->format('H:i') }} - {{ \Carbon\Carbon::parse($standardCheckOut)->format('H:i') }}</p>
                    <p class="flex items-center"><span class="w-2 h-2 bg-indigo-400 rounded-full mr-2"></span>Afternoon: {{ \Carbon\Carbon::parse($afternoonCheckIn)->format('H:i') }} - {{ \Carbon\Carbon::parse($afternoonCheckOut)->format('H:i') }}</p>
                </div>
            </div>
            @else
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center space-x-6">
                    <div>
                        <p class="text-blue-100 text-xs uppercase tracking-wide">Standard Check-In</p>
                        <p class="text-2xl font-bold">{{ \Carbon\Carbon::parse($standardCheckIn)->format('H:i') }}</p>
                    </div>
                    <div class="text-blue-300">→</div>
                    <div>
                        <p class="text-blue-100 text-xs uppercase tracking-wide">Standard Check-Out</p>
                        <p class="text-2xl font-bold">{{ \Carbon\Carbon::parse($standardCheckOut)->format('H:i') }}</p>
                    </div>
                    <div class="border-l border-blue-400 pl-6">
                        <p class="text-blue-100 text-xs uppercase tracking-wide">Work Day</p>
                        <p class="text-2xl font-bold">9 Hours</p>
                    </div>
                </div>
                <div class="text-sm text-blue-100">
                    <p>Late = Check-in after {{ \Carbon\Carbon::parse($standardCheckIn)->format('H:i') }}</p>
                    <p>Overtime = Check-out after {{ \Carbon\Carbon::parse($standardCheckOut)->format('H:i') }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <p class="text-xs text-gray-500 uppercase">Total Records</p>
                <p class="text-2xl font-bold text-gray-900">{{ $totalRecords }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <p class="text-xs text-gray-500 uppercase">On Time</p>
                <p class="text-2xl font-bold text-green-600">{{ $onTimeCount }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <p class="text-xs text-gray-500 uppercase">Late Arrivals</p>
                <p class="text-2xl font-bold text-yellow-600">{{ $lateCount }}</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <p class="text-xs text-gray-500 uppercase">Total Worked</p>
                <p class="text-2xl font-bold text-blue-600">{{ floor($totalWorkedMinutes / 60) }}h {{ $totalWorkedMinutes % 60 }}m</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <p class="text-xs text-gray-500 uppercase">Total Late</p>
                <p class="text-2xl font-bold text-red-600">{{ floor($totalLateMinutes / 60) }}h {{ $totalLateMinutes % 60 }}m</p>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                <p class="text-xs text-gray-500 uppercase">Total Overtime</p>
                <p class="text-2xl font-bold text-purple-600">{{ floor($totalOvertimeMinutes / 60) }}h {{ $totalOvertimeMinutes % 60 }}m</p>
            </div>
        </div>

        <!-- Teacher Summaries -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900">Teacher Time Summaries</h3>
                <span class="text-sm text-gray-500">Weekly | Monthly | Termly</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teacher</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="3">This Week</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="3">This Month</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase" colspan="3">This Term</th>
                        </tr>
                        <tr class="bg-gray-50">
                            <th></th>
                            <th class="px-2 py-1 text-xs text-gray-400">Hours</th>
                            <th class="px-2 py-1 text-xs text-gray-400">Late</th>
                            <th class="px-2 py-1 text-xs text-gray-400">OT</th>
                            <th class="px-2 py-1 text-xs text-gray-400">Hours</th>
                            <th class="px-2 py-1 text-xs text-gray-400">Late</th>
                            <th class="px-2 py-1 text-xs text-gray-400">OT</th>
                            <th class="px-2 py-1 text-xs text-gray-400">Hours</th>
                            <th class="px-2 py-1 text-xs text-gray-400">Late</th>
                            <th class="px-2 py-1 text-xs text-gray-400">OT</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($teacherSummaries as $summary)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-xs">
                                        {{ substr($summary['teacher']->user->name, 0, 1) }}
                                    </div>
                                    <span class="ml-2 text-sm font-medium">{{ $summary['teacher']->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-2 py-2 text-center text-sm">
                                <span class="font-medium">{{ floor($summary['weekly']['total_minutes'] / 60) }}h</span>
                                <span class="text-gray-400 text-xs">({{ $summary['weekly']['days'] }}d)</span>
                            </td>
                            <td class="px-2 py-2 text-center text-sm text-red-600">
                                @if($summary['weekly']['late_minutes'] > 0)
                                    {{ floor($summary['weekly']['late_minutes'] / 60) }}h {{ $summary['weekly']['late_minutes'] % 60 }}m
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-2 py-2 text-center text-sm text-purple-600">
                                @if($summary['weekly']['overtime_minutes'] > 0)
                                    {{ floor($summary['weekly']['overtime_minutes'] / 60) }}h {{ $summary['weekly']['overtime_minutes'] % 60 }}m
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-2 py-2 text-center text-sm border-l">
                                <span class="font-medium">{{ floor($summary['monthly']['total_minutes'] / 60) }}h</span>
                                <span class="text-gray-400 text-xs">({{ $summary['monthly']['days'] }}d)</span>
                            </td>
                            <td class="px-2 py-2 text-center text-sm text-red-600">
                                @if($summary['monthly']['late_minutes'] > 0)
                                    {{ floor($summary['monthly']['late_minutes'] / 60) }}h {{ $summary['monthly']['late_minutes'] % 60 }}m
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-2 py-2 text-center text-sm text-purple-600">
                                @if($summary['monthly']['overtime_minutes'] > 0)
                                    {{ floor($summary['monthly']['overtime_minutes'] / 60) }}h {{ $summary['monthly']['overtime_minutes'] % 60 }}m
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-2 py-2 text-center text-sm border-l">
                                <span class="font-medium">{{ floor($summary['termly']['total_minutes'] / 60) }}h</span>
                                <span class="text-gray-400 text-xs">({{ $summary['termly']['days'] }}d)</span>
                            </td>
                            <td class="px-2 py-2 text-center text-sm text-red-600">
                                @if($summary['termly']['late_minutes'] > 0)
                                    {{ floor($summary['termly']['late_minutes'] / 60) }}h {{ $summary['termly']['late_minutes'] % 60 }}m
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-2 py-2 text-center text-sm text-purple-600">
                                @if($summary['termly']['overtime_minutes'] > 0)
                                    {{ floor($summary['termly']['overtime_minutes'] / 60) }}h {{ $summary['termly']['overtime_minutes'] % 60 }}m
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
            <form method="GET" action="{{ route('attendance.history') }}" class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                    <select name="month" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <select name="year" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @for($y = now()->year; $y >= now()->year - 2; $y--)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teacher</label>
                    <select name="teacher_id" class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Teachers</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ $teacherId == $teacher->id ? 'selected' : '' }}>
                                {{ $teacher->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Filter
                </button>
                <a href="{{ route('attendance.history') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    Reset
                </a>
            </form>
        </div>

        <!-- Attendance Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Attendance Records</h3>
            </div>

            @if($attendances->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            @if($sessionMode === 'dual')
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Session</th>
                            @endif
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Worked</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Late</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overtime</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($attendances as $attendance)
                        <tr class="hover:bg-gray-50 {{ $attendance->is_late ? 'bg-yellow-50' : '' }}">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                        <span class="text-blue-600 font-medium text-sm">{{ substr($attendance->teacher->user->name, 0, 1) }}</span>
                                    </div>
                                    <div class="ml-2">
                                        <p class="text-sm font-medium text-gray-900">{{ $attendance->teacher->user->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($attendance->date)->format('D, M d') }}
                            </td>
                            @if($sessionMode === 'dual')
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($attendance->session_type === 'morning')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                        Morning
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                        </svg>
                                        Afternoon
                                    </span>
                                @endif
                            </td>
                            @endif
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($attendance->check_in_time)
                                    <span class="text-sm font-medium {{ $attendance->is_late ? 'text-red-600' : 'text-green-600' }}">
                                        {{ \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') }}
                                    </span>
                                    @if($attendance->is_late)
                                        <span class="ml-1 text-xs text-red-500">LATE</span>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-400">--:--</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($attendance->check_out_time)
                                    <span class="text-sm font-medium {{ $attendance->has_overtime ? 'text-purple-600' : 'text-blue-600' }}">
                                        {{ \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') }}
                                    </span>
                                    @if($attendance->has_overtime)
                                        <span class="ml-1 text-xs text-purple-500">OT</span>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-400">--:--</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                @if($attendance->worked_minutes > 0)
                                    <span class="font-medium text-gray-700">{{ floor($attendance->worked_minutes / 60) }}h {{ $attendance->worked_minutes % 60 }}m</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                @if($attendance->late_minutes > 0)
                                    <span class="text-red-600 font-medium">{{ floor($attendance->late_minutes / 60) }}h {{ $attendance->late_minutes % 60 }}m</span>
                                @else
                                    <span class="text-green-600">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                @if($attendance->overtime_minutes > 0)
                                    <span class="text-purple-600 font-medium">{{ floor($attendance->overtime_minutes / 60) }}h {{ $attendance->overtime_minutes % 60 }}m</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @if($attendance->check_out_time)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">Done</span>
                                @elseif($attendance->check_in_time)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">In</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                <div class="flex items-center space-x-1">
                                    <button onclick="openEditModal({{ $attendance->id }}, '{{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '' }}', '{{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') : '' }}')" 
                                            class="p-1 text-blue-600 hover:bg-blue-100 rounded" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                    <form action="{{ route('attendance.delete', $attendance->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete this record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-red-600 hover:bg-red-100 rounded" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No attendance records</h3>
                <p class="mt-1 text-sm text-gray-500">No records found for the selected period.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Add Attendance Modal -->
<div id="addModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Add Attendance Record</h3>
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form action="{{ route('attendance.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Teacher</label>
                    <select name="teacher_id" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Select Teacher</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Check In Time</label>
                    <input type="time" name="check_in_time" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Check Out Time</label>
                    <input type="time" name="check_out_time" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Add Record
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Attendance Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Edit Attendance Record</h3>
            <button onclick="document.getElementById('editModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Check In Time</label>
                    <input type="time" name="check_in_time" id="editCheckIn" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Check Out Time</label>
                    <input type="time" name="check_out_time" id="editCheckOut" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Cancel
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Update
                </button>
            </div>
        </form>
    </div>
</div>

@if(session('success'))
<script>alert('{{ session('success') }}')</script>
@endif

@if(session('error'))
<script>alert('{{ session('error') }}')</script>
@endif

@push('scripts')
<script>
    function openEditModal(id, checkIn, checkOut) {
        document.getElementById('editForm').action = '/attendance/' + id + '/update';
        document.getElementById('editCheckIn').value = checkIn;
        document.getElementById('editCheckOut').value = checkOut;
        document.getElementById('editModal').classList.remove('hidden');
    }
</script>
@endpush
@endsection
