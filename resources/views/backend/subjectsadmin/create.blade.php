@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Add New Subject</h1>
                <p class="mt-2 text-sm text-gray-600">Create a new academic subject</p>
            </div>
            <a href="{{ route('admin.subjects.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to List
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ route('admin.subjects.store') }}" method="POST">
            @csrf
            
            <!-- Subject Details Section -->
            <div class="px-8 py-6 border-b border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </span>
                    Subject Details
                </h3>
            </div>
            
            <div class="px-8 py-6 space-y-6">
                <!-- Subject Name & Code Row -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subject Name <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <input type="text" id="subject_name" name="name" value="{{ old('name') }}" 
                                class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors @error('name') border-red-500 @enderror"
                                placeholder="e.g. Mathematics">
                        </div>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subject Code <span class="text-red-500">*</span></label>
                        <div class="w-full px-4 py-3 bg-gradient-to-r from-green-50 to-blue-50 border-2 border-green-400 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                                    </svg>
                                    <span class="text-sm font-medium">Auto-generated:</span>
                                </div>
                                <span class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-green-600 to-blue-600">{{ $nextSubjectCode }}</span>
                            </div>
                        </div>
                        <input type="hidden" name="subject_code" value="{{ $nextSubjectCode }}">
                        @error('subject_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">This code will be assigned to the new subject</p>
                    </div>
                </div>
            </div>

            <!-- Timetable Settings Section -->
            <div class="px-8 py-6 border-t border-b border-gray-100 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <span class="w-8 h-8 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                    Timetable Settings
                </h3>
                <p class="mt-1 text-sm text-gray-500 ml-11">Configure lesson frequency per week for timetable generation</p>
            </div>
            
            <div class="px-8 py-6 space-y-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <!-- Single Lessons -->
                    <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                        <label class="block text-sm font-medium text-blue-800 mb-2">Single Lessons</label>
                        <p class="text-xs text-blue-600 mb-2">1 period each</p>
                        <input type="number" name="single_lessons_per_week" value="{{ old('single_lessons_per_week', 0) }}" min="0" max="20"
                            class="block w-full px-3 py-2 border border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-center text-lg font-semibold @error('single_lessons_per_week') border-red-500 @enderror">
                        @error('single_lessons_per_week')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Double Lessons -->
                    <div class="bg-green-50 rounded-xl p-4 border border-green-100">
                        <label class="block text-sm font-medium text-green-800 mb-2">Double Lessons</label>
                        <p class="text-xs text-green-600 mb-2">2 periods each</p>
                        <input type="number" name="double_lessons_per_week" value="{{ old('double_lessons_per_week', 0) }}" min="0" max="10"
                            class="block w-full px-3 py-2 border border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-lg font-semibold @error('double_lessons_per_week') border-red-500 @enderror">
                        @error('double_lessons_per_week')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Triple Lessons -->
                    <div class="bg-amber-50 rounded-xl p-4 border border-amber-100">
                        <label class="block text-sm font-medium text-amber-800 mb-2">Triple Lessons</label>
                        <p class="text-xs text-amber-600 mb-2">3 periods each</p>
                        <input type="number" name="triple_lessons_per_week" value="{{ old('triple_lessons_per_week', 0) }}" min="0" max="5"
                            class="block w-full px-3 py-2 border border-amber-200 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 text-center text-lg font-semibold @error('triple_lessons_per_week') border-red-500 @enderror">
                        @error('triple_lessons_per_week')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Quad Lessons -->
                    <div class="bg-red-50 rounded-xl p-4 border border-red-100">
                        <label class="block text-sm font-medium text-red-800 mb-2">Quad Lessons</label>
                        <p class="text-xs text-red-600 mb-2">4 periods each</p>
                        <input type="number" name="quad_lessons_per_week" value="{{ old('quad_lessons_per_week', 0) }}" min="0" max="5"
                            class="block w-full px-3 py-2 border border-red-200 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 text-center text-lg font-semibold @error('quad_lessons_per_week') border-red-500 @enderror">
                        @error('quad_lessons_per_week')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <!-- Total Calculation Display -->
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-4 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium opacity-90">Total Periods Per Week</p>
                            <p class="text-xs opacity-75">(Singles × 1) + (Doubles × 2) + (Triples × 3) + (Quads × 4)</p>
                        </div>
                        <div class="text-right">
                            <span id="totalPeriods" class="text-4xl font-bold">0</span>
                            <p class="text-xs opacity-75">periods</p>
                        </div>
                    </div>
                </div>
                <p class="text-xs text-gray-500 text-center">Total periods per week will be calculated automatically</p>
            </div>

            <!-- Submit Section -->
            <div class="px-8 py-6 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-4">
                <a href="{{ route('admin.subjects.index') }}" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Create Subject
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const singleInput = document.querySelector('input[name="single_lessons_per_week"]');
        const doubleInput = document.querySelector('input[name="double_lessons_per_week"]');
        const tripleInput = document.querySelector('input[name="triple_lessons_per_week"]');
        const quadInput = document.querySelector('input[name="quad_lessons_per_week"]');
        const totalDisplay = document.getElementById('totalPeriods');

        function calculateTotal() {
            const singles = parseInt(singleInput.value) || 0;
            const doubles = parseInt(doubleInput.value) || 0;
            const triples = parseInt(tripleInput.value) || 0;
            const quads = parseInt(quadInput.value) || 0;

            const total = (singles * 1) + (doubles * 2) + (triples * 3) + (quads * 4);
            totalDisplay.textContent = total;
        }

        // Add event listeners to all inputs
        [singleInput, doubleInput, tripleInput, quadInput].forEach(input => {
            input.addEventListener('input', calculateTotal);
            input.addEventListener('change', calculateTotal);
        });

        // Calculate initial total
        calculateTotal();
    });
</script>
@endsection