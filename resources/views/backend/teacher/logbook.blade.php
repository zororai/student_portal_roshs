@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">My Attendance Log</h1>
            <p class="text-gray-600">View your attendance history</p>
        </div>
    </div>

    <!-- Today's Status -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Today's Status</h3>
        <div class="flex items-center gap-6">
            @if($todayLog)
                <div class="flex items-center gap-2">
                    <span class="text-gray-600">Time In:</span>
                    <span class="font-medium text-green-600">{{ $todayLog->time_in ? \Carbon\Carbon::parse($todayLog->time_in)->format('H:i') : '-' }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-gray-600">Time Out:</span>
                    <span class="font-medium {{ $todayLog->time_out ? 'text-red-600' : 'text-gray-400' }}">{{ $todayLog->time_out ? \Carbon\Carbon::parse($todayLog->time_out)->format('H:i') : 'Not yet' }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-gray-600">Status:</span>
                    <span class="px-2 py-1 text-xs rounded-full {{ $todayLog->status == 'present' ? 'bg-green-100 text-green-800' : ($todayLog->status == 'late' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">{{ ucfirst($todayLog->status) }}</span>
                </div>
            @else
                <span class="text-gray-500">No attendance recorded today</span>
            @endif
        </div>
    </div>

    <!-- Monthly Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="text-2xl font-bold text-blue-600">{{ $totalDays }}</div>
            <div class="text-sm text-gray-600">Total Days Logged</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="text-2xl font-bold text-green-600">{{ $presentDays }}</div>
            <div class="text-sm text-gray-600">Present</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="text-2xl font-bold text-yellow-600">{{ $lateDays }}</div>
            <div class="text-sm text-gray-600">Late</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="text-2xl font-bold text-red-600">{{ $absentDays }}</div>
            <div class="text-sm text-gray-600">Absent</div>
        </div>
    </div>

    <!-- Month Filter -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <form method="GET" class="flex items-center gap-4">
            <label class="text-sm font-medium text-gray-700">Select Month:</label>
            <input type="month" name="month" value="{{ $month }}" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Filter</button>
        </form>
    </div>

    <!-- Log History -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Attendance History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Day</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time Out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-800">{{ \Carbon\Carbon::parse($log->log_date)->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ \Carbon\Carbon::parse($log->log_date)->format('l') }}</td>
                        <td class="px-6 py-4 text-sm text-green-600 font-medium">{{ $log->time_in ? \Carbon\Carbon::parse($log->time_in)->format('H:i') : '-' }}</td>
                        <td class="px-6 py-4 text-sm text-red-600 font-medium">{{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('H:i') : '-' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $log->status == 'present' ? 'bg-green-100 text-green-800' : ($log->status == 'late' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">No attendance records for this month.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
