@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Subject</h1>
                    <p class="mt-1 text-sm text-gray-500">Update subject information and teacher assignment</p>
                </div>
                <a href="{{ route('admin.subjects.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 hover:shadow-md transition-all duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Subjects
                </a>
            </div>

            <form action="{{ route('admin.subjects.update', $subject->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Subject Info Card -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-500 to-indigo-600">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            Subject Details
                        </h3>
                    </div>
                    <div class="px-6 py-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Subject Name <span class="text-red-500">*</span></label>
                                <input name="name" type="text" value="{{ old('name', $subject->name) }}" required
                                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('name') border-red-500 @enderror"
                                       placeholder="Enter subject name">
                                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Subject Code <span class="text-red-500">*</span></label>
                                <input name="subject_code" type="text" value="{{ old('subject_code', $subject->subject_code) }}" required
                                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('subject_code') border-red-500 @enderror"
                                       placeholder="Enter subject code">
                                @error('subject_code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Teacher Assignment Card -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Teacher Assignment
                        </h3>
                    </div>
                    <div class="px-6 py-6 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Assign Teacher</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <select name="teacher_id" class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors appearance-none bg-white @error('teacher_id') border-red-500 @enderror">
                                    <option value="">-- Select Teacher --</option>
                                    @foreach ($teachers as $teacher)
                                        @if($teacher->user)
                                            <option value="{{ $teacher->id }}" {{ ($teacher->id === $subject->teacher_id) ? 'selected' : '' }}>
                                                {{ $teacher->user->name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                            @error('teacher_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <!-- Lesson Periods Card -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
                    <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-amber-500 to-orange-500">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Lesson Periods Per Week
                        </h3>
                    </div>
                    <div class="px-6 py-6">
                        <p class="text-sm text-gray-600 mb-4">Configure how many lessons of each type this subject has per week</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Single Lessons</label>
                                <input name="single_lessons_per_week" type="number" min="0" max="20" 
                                       value="{{ old('single_lessons_per_week', $subject->single_lessons_per_week ?? 0) }}" 
                                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors duration-200 text-center"
                                       placeholder="0">
                                <p class="mt-1 text-xs text-gray-500 text-center">1 period each</p>
                                @error('single_lessons_per_week')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Double Lessons</label>
                                <input name="double_lessons_per_week" type="number" min="0" max="10" 
                                       value="{{ old('double_lessons_per_week', $subject->double_lessons_per_week ?? 0) }}" 
                                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors duration-200 text-center"
                                       placeholder="0">
                                <p class="mt-1 text-xs text-gray-500 text-center">2 periods each</p>
                                @error('double_lessons_per_week')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Triple Lessons</label>
                                <input name="triple_lessons_per_week" type="number" min="0" max="5" 
                                       value="{{ old('triple_lessons_per_week', $subject->triple_lessons_per_week ?? 0) }}" 
                                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors duration-200 text-center"
                                       placeholder="0">
                                <p class="mt-1 text-xs text-gray-500 text-center">3 periods each</p>
                                @error('triple_lessons_per_week')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Quad Lessons</label>
                                <input name="quad_lessons_per_week" type="number" min="0" max="5" 
                                       value="{{ old('quad_lessons_per_week', $subject->quad_lessons_per_week ?? 0) }}" 
                                       class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors duration-200 text-center"
                                       placeholder="0">
                                <p class="mt-1 text-xs text-gray-500 text-center">4 periods each</p>
                                @error('quad_lessons_per_week')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-600">
                                <strong>Total periods/week:</strong> 
                                <span id="totalPeriods" class="text-lg font-bold text-amber-600">{{ $subject->periods_per_week ?? 0 }}</span>
                            </p>
                        </div>
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const single = document.querySelector('input[name="single_lessons_per_week"]');
                        const double = document.querySelector('input[name="double_lessons_per_week"]');
                        const triple = document.querySelector('input[name="triple_lessons_per_week"]');
                        const quad = document.querySelector('input[name="quad_lessons_per_week"]');
                        const totalDisplay = document.getElementById('totalPeriods');
                        
                        function calculateTotal() {
                            const total = (parseInt(single.value) || 0) * 1 
                                        + (parseInt(double.value) || 0) * 2 
                                        + (parseInt(triple.value) || 0) * 3 
                                        + (parseInt(quad.value) || 0) * 4;
                            totalDisplay.textContent = total;
                        }
                        
                        [single, double, triple, quad].forEach(input => {
                            input.addEventListener('input', calculateTotal);
                        });
                    });
                </script>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('admin.subjects.index') }}" class="px-6 py-3 rounded-lg border border-gray-300 text-gray-700 font-medium hover:bg-gray-50 transition-colors duration-200">
                        Cancel
                    </a>
                    <button type="submit" class="px-8 py-3 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold shadow-lg hover:from-blue-600 hover:to-indigo-700 hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Subject
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection