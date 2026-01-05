@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                Generate Timetable
            </h1>
            <p class="text-gray-500 mt-1 ml-13">Configure school hours and generate class timetable</p>
        </div>
        <a href="{{ route('admin.timetable.index') }}" class="mt-4 md:mt-0 inline-flex items-center gap-2 text-gray-600 hover:text-gray-800 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Timetables
        </a>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.timetable.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Class Selection -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Select Classes
                    </h2>
                    
                    <div class="mb-3">
                        <label class="flex items-center p-3 bg-emerald-50 border-2 border-emerald-200 rounded-xl cursor-pointer hover:bg-emerald-100 transition-all">
                            <input type="checkbox" id="select-all-classes" 
                                   class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 rounded">
                            <span class="ml-3 font-semibold text-emerald-700">Select All Classes</span>
                        </label>
                    </div>
                    
                    <div class="space-y-3 max-h-96 overflow-y-auto">
                        @foreach($classes as $class)
                            <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all hover:border-emerald-300 border-gray-200 class-checkbox-label">
                                <input type="checkbox" name="class_ids[]" value="{{ $class->id }}" 
                                       class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 rounded class-checkbox"
                                       {{ is_array(old('class_ids')) && in_array($class->id, old('class_ids')) ? 'checked' : '' }}>
                                <div class="ml-3">
                                    <p class="font-semibold text-gray-800">{{ $class->name }}</p>
                                    <p class="text-sm text-gray-500">Form {{ $class->class_numeric }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right Column - Time Settings -->
            <div class="lg:col-span-2 space-y-6">
                <!-- School Hours -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        School Hours
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                            <input type="time" name="start_time" value="{{ old('start_time', '07:30') }}" 
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                            <input type="time" name="end_time" value="{{ old('end_time', '15:30') }}" 
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Break Time -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Break Time
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Break Start</label>
                            <input type="time" name="break_start" value="{{ old('break_start', '10:00') }}" 
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Break End</label>
                            <input type="time" name="break_end" value="{{ old('break_end', '10:30') }}" 
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Lunch Time -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Lunch Time
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Lunch Start</label>
                            <input type="time" name="lunch_start" value="{{ old('lunch_start', '12:30') }}" 
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Lunch End</label>
                            <input type="time" name="lunch_end" value="{{ old('lunch_end', '13:30') }}" 
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <!-- Subject Duration -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Subject Duration
                    </h2>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Duration per Subject (minutes)</label>
                        <input type="number" name="subject_duration" value="{{ old('subject_duration', '40') }}" 
                               min="20" max="120" step="5"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        <p class="text-sm text-gray-500 mt-2">Recommended: 35-45 minutes per subject</p>
                    </div>
                </div>

                <!-- Academic Period -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Academic Period
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Academic Year</label>
                            <input type="text" name="academic_year" 
                                   value="{{ old('academic_year', $currentTerm ? $currentTerm->year : date('Y')) }}" 
                                   placeholder="e.g., 2024"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Term</label>
                            @php
                                $defaultTerm = 1;
                                if ($currentTerm && $currentTerm->result_period) {
                                    // Extract term number from result_period (e.g., "Term 1" -> 1)
                                    preg_match('/\d+/', $currentTerm->result_period, $matches);
                                    $defaultTerm = $matches[0] ?? 1;
                                }
                            @endphp
                            <select name="term" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                <option value="1" {{ old('term', $defaultTerm) == '1' ? 'selected' : '' }}>Term 1</option>
                                <option value="2" {{ old('term', $defaultTerm) == '2' ? 'selected' : '' }}>Term 2</option>
                                <option value="3" {{ old('term', $defaultTerm) == '3' ? 'selected' : '' }}>Term 3</option>
                            </select>
                        </div>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">
                        @if($currentTerm)
                            <span class="text-emerald-600 font-medium">Current Term: {{ $currentTerm->result_period }} {{ $currentTerm->year }}</span> - 
                        @endif
                        This allows you to create separate timetables for different terms
                    </p>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end gap-4 mt-6">
                    <a href="{{ route('admin.timetable.index') }}" 
                       class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl font-semibold hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all"
                            style="color: black">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Generate Timetable
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // Select All functionality
    const selectAllCheckbox = document.getElementById('select-all-classes');
    const classCheckboxes = document.querySelectorAll('.class-checkbox');
    const classLabels = document.querySelectorAll('.class-checkbox-label');
    
    selectAllCheckbox.addEventListener('change', function() {
        classCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateLabelStyles();
    });
    
    // Update Select All state when individual checkboxes change
    classCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            selectAllCheckbox.checked = [...classCheckboxes].every(cb => cb.checked);
            updateLabelStyles();
        });
    });
    
    // Update label styles based on checkbox state
    function updateLabelStyles() {
        classLabels.forEach(label => {
            const checkbox = label.querySelector('.class-checkbox');
            if (checkbox.checked) {
                label.classList.remove('border-gray-200');
                label.classList.add('border-emerald-500', 'bg-emerald-50');
            } else {
                label.classList.remove('border-emerald-500', 'bg-emerald-50');
                label.classList.add('border-gray-200');
            }
        });
    }
    
    // Initialize styles on page load
    updateLabelStyles();
</script>
@endsection
