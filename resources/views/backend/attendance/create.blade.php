@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-teal-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                Take Attendance
            </h1>
            <p class="text-gray-500 mt-1 ml-13">{{ $class->class_name }} - {{ date('l, F j, Y') }}</p>
        </div>
        <div class="mt-4 md:mt-0">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-xl font-semibold hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Home
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if($errors->any() || session('status'))
        <div class="mb-6">
            @error('attendences')
                <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-r-lg mb-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-red-700 font-medium">{{ $message }}</p>
                    </div>
                </div>
            @enderror
            @if(session('status'))
                <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-r-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-amber-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-amber-700 font-medium">{{ session('status') }}</p>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Attendance Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Card Header -->
        <div class="bg-gradient-to-r from-green-50 to-teal-50 px-6 py-4 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Student List</h2>
                    <p class="text-sm text-gray-600 mt-1">Mark attendance for {{ $class->students->count() }} students</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500 uppercase font-semibold">Date</p>
                    <p class="text-sm font-bold text-gray-800">{{ date('Y-m-d') }}</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('teacher.attendance.store') }}" method="POST" id="attendanceForm">
            @csrf
            <input type="hidden" name="class_id" value="{{ $class->id }}">
            <input type="hidden" name="teacher_id" value="{{ $class->teacher_id }}">

            <!-- Quick Actions -->
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                <div class="flex items-center gap-4">
                    <span class="text-sm font-medium text-gray-700">Quick Actions:</span>
                    <button type="button" onclick="markAll('present')" class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-100 text-green-700 rounded-lg text-sm font-medium hover:bg-green-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Mark All Present
                    </button>
                    <button type="button" onclick="markAll('absent')" class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-100 text-red-700 rounded-lg text-sm font-medium hover:bg-red-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Mark All Absent
                    </button>
                    <button type="button" onclick="clearAll()" class="inline-flex items-center gap-2 px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Clear All
                    </button>
                </div>
            </div>

            <!-- Student List -->
            <div class="divide-y divide-gray-100">
                @foreach ($class->students as $index => $student)
                    <div class="px-6 py-4 hover:bg-gray-50 transition-colors" x-data="{ isAbsent: false }">
                        <div class="flex items-center justify-between">
                            <!-- Student Info -->
                            <div class="flex items-center gap-4 flex-1">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-bold text-indigo-600">{{ $student->roll_number }}</span>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800">{{ $student->user->name }}</p>
                                    <p class="text-xs text-gray-500">Roll #{{ $student->roll_number }}</p>
                                </div>
                            </div>

                            <!-- Attendance Options -->
                            <div class="flex items-center gap-4">
                                <label class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border-2 border-gray-200 cursor-pointer hover:border-green-400 hover:bg-green-50 transition-all has-[:checked]:border-green-500 has-[:checked]:bg-green-50">
                                    <input type="radio" name="attendences[{{ $student->id }}]" value="present" class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500" required @click="isAbsent = false">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700">Present</span>
                                </label>

                                <label class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border-2 border-gray-200 cursor-pointer hover:border-red-400 hover:bg-red-50 transition-all has-[:checked]:border-red-500 has-[:checked]:bg-red-50">
                                    <input type="radio" name="attendences[{{ $student->id }}]" value="absent" class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500" @click="isAbsent = true">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700">Absent</span>
                                </label>
                            </div>
                        </div>

                        <!-- Absent Reason Section -->
                        <div x-show="isAbsent" x-collapse class="mt-4 ml-14 p-4 bg-red-50 rounded-xl border border-red-100">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Absence</label>
                                    <select name="absent_reason_type[{{ $student->id }}]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm">
                                        <option value="">Select a reason...</option>
                                        <option value="Sick">Sick / Medical</option>
                                        <option value="Family Emergency">Family Emergency</option>
                                        <option value="Transport Issues">Transport Issues</option>
                                        <option value="Personal Reasons">Personal Reasons</option>
                                        <option value="School Event">School Event</option>
                                        <option value="Suspension">Suspension</option>
                                        <option value="Unknown">Unknown</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Additional Details</label>
                                    <input type="text" name="absent_reason_details[{{ $student->id }}]" placeholder="Enter additional details..." class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Submit Button -->
            <div class="px-6 py-6 bg-gray-50 border-t border-gray-100">
                <button type="submit" class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-xl font-semibold hover:from-green-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all shadow-lg hover:shadow-xl">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Submit Attendance
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function markAll(status) {
    const radios = document.querySelectorAll(`input[type="radio"][value="${status}"]`);
    radios.forEach(radio => {
        radio.checked = true;
    });
}

function clearAll() {
    const radios = document.querySelectorAll('input[type="radio"]');
    radios.forEach(radio => {
        radio.checked = false;
    });
}
</script>
@endpush
@endsection