@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $class->class_name ?? 'Class' }} Attendance</h1>
                <p class="mt-2 text-sm text-gray-600">
                    @if($month)
                        Month {{ $month }} - {{ $studentAttendances->count() }} Students
                    @else
                        All Time - {{ $studentAttendances->count() }} Students
                    @endif
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="openDeleteModal()" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Clean Attendance
                </button>
                <a href="{{ route('attendance.index', ['type' => 'class', 'month' => $month]) }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Classes
                </a>
            </div>
        </div>
    </div>

    <!-- Class Statistics -->
    @php
        $totalRecords = $attendances->count();
        $presentCount = $attendances->where('attendence_status', 1)->count();
        $absentCount = $totalRecords - $presentCount;
        $attendanceRate = $totalRecords > 0 ? round(($presentCount / $totalRecords) * 100, 1) : 0;
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Records</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalRecords }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Present</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $presentCount }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Absent</p>
                    <p class="text-3xl font-bold text-red-600 mt-2">{{ $absentCount }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Attendance Rate</p>
                    <p class="text-3xl font-bold text-blue-600 mt-2">{{ $attendanceRate }}%</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Students List -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-semibold text-gray-900">Student Attendance Records</h2>
        </div>
        
        <div class="divide-y divide-gray-100">
            @foreach ($studentAttendances as $studentId => $studentRecords)
                @php
                    $studentName = $studentRecords->first()->student->user->name ?? 'Unknown Student';
                    $studentPresent = $studentRecords->where('attendence_status', 1)->count();
                    $studentAbsent = $studentRecords->count() - $studentPresent;
                    $studentRate = $studentRecords->count() > 0 ? round(($studentPresent / $studentRecords->count()) * 100) : 0;
                @endphp
                
                <div class="border-b border-gray-100" x-data="{ expanded: false }">
                    <div class="px-6 py-4 hover:bg-gray-50 transition-colors cursor-pointer" @click="expanded = !expanded">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center flex-1">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-base font-semibold text-gray-900">{{ $studentName }}</p>
                                    <div class="flex items-center gap-4 mt-1">
                                        <span class="text-sm text-gray-500">{{ $studentRecords->count() }} days recorded</span>
                                        <span class="text-sm text-green-600 font-medium">{{ $studentPresent }} present</span>
                                        <span class="text-sm text-red-600 font-medium">{{ $studentAbsent }} absent</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="text-right mr-2">
                                    <p class="text-lg font-bold {{ $studentRate >= 75 ? 'text-green-600' : ($studentRate >= 50 ? 'text-yellow-600' : 'text-red-600') }}">
                                        {{ $studentRate }}%
                                    </p>
                                    <p class="text-xs text-gray-500">Attendance</p>
                                </div>
                                <div class="w-14 h-14 rounded-xl {{ $studentRate >= 75 ? 'bg-green-100' : ($studentRate >= 50 ? 'bg-yellow-100' : 'bg-red-100') }} flex items-center justify-center">
                                    @if($studentRate >= 75)
                                        <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @elseif($studentRate >= 50)
                                        <svg class="w-7 h-7 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                    @else
                                        <svg class="w-7 h-7 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    @endif
                                </div>
                                <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': expanded }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Detailed Attendance Records -->
                    <div x-show="expanded" x-collapse class="px-6 pb-4 bg-gray-50">
                        <div class="mt-3">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Attendance Details</h4>
                            <div class="space-y-2">
                                @foreach($studentRecords->sortByDesc('attendence_date') as $record)
                                    <div class="flex items-center justify-between py-2 px-3 bg-white rounded-lg border border-gray-200">
                                        <div class="flex items-center gap-3">
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ \Carbon\Carbon::parse($record->attendence_date)->format('M d, Y') }}
                                            </span>
                                            @if($record->attendence_status == 1)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    Present
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                    Absent
                                                </span>
                                            @endif
                                        </div>
                                        @if($record->attendence_status == 0 && ($record->absent_reason_type || $record->absent_reason_details))
                                            <div class="flex items-start gap-2 max-w-md">
                                                @if($record->absent_reason_type)
                                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-orange-100 text-orange-800">
                                                        {{ ucfirst($record->absent_reason_type) }}
                                                    </span>
                                                @endif
                                                @if($record->absent_reason_details)
                                                    <span class="text-xs text-gray-600 italic">{{ $record->absent_reason_details }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-2xl bg-white">
            <div class="mt-3">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="mt-3 text-center">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Clean Attendance Records</h3>
                    <div class="mt-2 px-7 py-3">
                        <p class="text-sm text-gray-500">
                            Are you sure you want to delete all attendance records for <strong>{{ $class->class_name ?? 'this class' }}</strong>
                            @if($month)
                                for <strong>Month {{ $month }}</strong>
                            @endif
                            ?
                        </p>
                        <p class="text-sm text-red-600 font-semibold mt-2">
                            This will delete {{ $totalRecords }} record(s). This action cannot be undone!
                        </p>
                    </div>
                    <div class="items-center px-4 py-3">
                        <form action="{{ route('attendance.clean', $class->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <input type="hidden" name="month" value="{{ $month }}">
                            <div class="flex gap-3">
                                <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2 bg-gray-200 text-gray-800 text-base font-medium rounded-lg hover:bg-gray-300 transition-colors">
                                    Cancel
                                </button>
                                <button type="submit" class="flex-1 px-4 py-2 bg-red-600 text-white text-base font-medium rounded-lg hover:bg-red-700 transition-colors">
                                    Delete All
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openDeleteModal() {
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('deleteModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
</script>
@endsection
