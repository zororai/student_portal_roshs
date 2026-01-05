@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Attendance Register</h1>
                <p class="mt-2 text-sm text-gray-600">{{ $class->class_name }} - {{ $students->count() }} students</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('teacher.class-students') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    View Students
                </a>
            </div>
        </div>
    </div>

    <!-- Date Selector -->
    <div class="mb-6 bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <form method="GET" action="{{ route('teacher.class-attendance') }}" class="flex items-center space-x-4">
            <label class="text-sm font-medium text-gray-700">Select Date:</label>
            <input type="date" name="date" value="{{ $date }}" 
                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                onchange="this.form.submit()">
            <span class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}</span>
        </form>
    </div>

    <!-- Attendance Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <form method="POST" action="{{ route('teacher.class-attendance.store') }}">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roll Number</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($students as $index => $student)
                        @php
                            $currentStatus = $attendances->get($student->id)->status ?? null;
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full object-cover" 
                                            src="{{ $student->user->profile_picture ? asset('images/profile/'.$student->user->profile_picture) : asset('images/profile/avatar.png') }}" 
                                            alt="{{ $student->user->name }}">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $student->user->name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->roll_number ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-center space-x-2">
                                    <label class="attendance-option cursor-pointer">
                                        <input type="radio" name="attendance[{{ $student->id }}]" value="present" class="hidden" {{ $currentStatus == 'present' ? 'checked' : '' }}>
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg border-2 transition-all
                                            {{ $currentStatus == 'present' ? 'border-green-500 bg-green-100 text-green-700' : 'border-gray-300 hover:border-green-400' }}">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            Present
                                        </span>
                                    </label>
                                    <label class="attendance-option cursor-pointer">
                                        <input type="radio" name="attendance[{{ $student->id }}]" value="absent" class="hidden" {{ $currentStatus == 'absent' ? 'checked' : '' }}>
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg border-2 transition-all
                                            {{ $currentStatus == 'absent' ? 'border-red-500 bg-red-100 text-red-700' : 'border-gray-300 hover:border-red-400' }}">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                            </svg>
                                            Absent
                                        </span>
                                    </label>
                                    <label class="attendance-option cursor-pointer">
                                        <input type="radio" name="attendance[{{ $student->id }}]" value="late" class="hidden" {{ $currentStatus == 'late' ? 'checked' : '' }}>
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg border-2 transition-all
                                            {{ $currentStatus == 'late' ? 'border-yellow-500 bg-yellow-100 text-yellow-700' : 'border-gray-300 hover:border-yellow-400' }}">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                            </svg>
                                            Late
                                        </span>
                                    </label>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No students</h3>
                                <p class="mt-1 text-sm text-gray-500">No students are enrolled in this class yet.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($students->count() > 0)
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                <div class="text-sm text-gray-500">
                    <span class="font-medium">Quick Actions:</span>
                    <button type="button" onclick="markAll('present')" class="ml-2 text-green-600 hover:text-green-800">Mark All Present</button>
                    <span class="mx-1">|</span>
                    <button type="button" onclick="markAll('absent')" class="text-red-600 hover:text-red-800">Mark All Absent</button>
                </div>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Attendance
                </button>
            </div>
            @endif
        </form>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
        // Attendance radio button styling
        $('.attendance-option input[type="radio"]').on('change', function() {
            const name = $(this).attr('name');
            $(`input[name="${name}"]`).each(function() {
                const $span = $(this).siblings('span');
                $span.removeClass('border-green-500 bg-green-100 text-green-700 border-red-500 bg-red-100 text-red-700 border-yellow-500 bg-yellow-100 text-yellow-700');
                $span.addClass('border-gray-300');
            });
            
            const $span = $(this).siblings('span');
            const value = $(this).val();
            
            if (value === 'present') {
                $span.removeClass('border-gray-300').addClass('border-green-500 bg-green-100 text-green-700');
            } else if (value === 'absent') {
                $span.removeClass('border-gray-300').addClass('border-red-500 bg-red-100 text-red-700');
            } else if (value === 'late') {
                $span.removeClass('border-gray-300').addClass('border-yellow-500 bg-yellow-100 text-yellow-700');
            }
        });
    });

    function markAll(status) {
        $(`input[type="radio"][value="${status}"]`).prop('checked', true).trigger('change');
    }
</script>
@endpush
@endsection
