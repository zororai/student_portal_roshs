@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Attendance Settings</h1>
            <p class="text-gray-600 mt-1">Configure standard work hours for teacher attendance</p>
        </div>

        @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Settings Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-blue-700">
                <h3 class="text-lg font-semibold text-white">Work Hours Configuration</h3>
                <p class="text-blue-100 text-sm">Set the expected check-in and check-out times for teachers</p>
            </div>

            <form action="{{ route('admin.attendance.settings.update') }}" method="POST" class="p-6">
                @csrf

                <div class="space-y-6">
                    <!-- Session Mode Toggle -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">School Session Mode</label>
                        <div class="flex space-x-4">
                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all {{ $settings['attendance_session_mode'] === 'single' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}">
                                <input type="radio" name="session_mode" value="single" 
                                       {{ $settings['attendance_session_mode'] === 'single' ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500"
                                       onchange="toggleSessionMode()">
                                <div class="ml-3">
                                    <span class="block font-medium text-gray-900">Single Session</span>
                                    <span class="block text-sm text-gray-500">Full day (e.g., 7:30 AM - 4:30 PM)</span>
                                </div>
                            </label>
                            <label class="flex items-center p-4 border-2 rounded-lg cursor-pointer transition-all {{ $settings['attendance_session_mode'] === 'dual' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300' }}">
                                <input type="radio" name="session_mode" value="dual" 
                                       {{ $settings['attendance_session_mode'] === 'dual' ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500"
                                       onchange="toggleSessionMode()">
                                <div class="ml-3">
                                    <span class="block font-medium text-gray-900">Dual Session</span>
                                    <span class="block text-sm text-gray-500">Morning & Afternoon shifts</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Morning Session (always shown) -->
                    <div class="bg-amber-50 rounded-lg p-4 border border-amber-200">
                        <h4 class="font-medium text-amber-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span id="morning_label">{{ $settings['attendance_session_mode'] === 'dual' ? 'Morning Session' : 'Work Hours' }}</span>
                        </h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="check_in_time" class="block text-sm font-medium text-gray-700 mb-2">Check-In Time</label>
                                <input type="time" name="check_in_time" id="check_in_time" 
                                       value="{{ $settings['attendance_check_in_time'] }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg p-3"
                                       required>
                            </div>
                            <div>
                                <label for="check_out_time" class="block text-sm font-medium text-gray-700 mb-2">Check-Out Time</label>
                                <input type="time" name="check_out_time" id="check_out_time" 
                                       value="{{ $settings['attendance_check_out_time'] }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg p-3"
                                       required>
                            </div>
                        </div>
                    </div>

                    <!-- Afternoon Session (only shown in dual mode) -->
                    <div id="afternoon_section" class="bg-indigo-50 rounded-lg p-4 border border-indigo-200 {{ $settings['attendance_session_mode'] === 'dual' ? '' : 'hidden' }}">
                        <h4 class="font-medium text-indigo-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                            </svg>
                            Afternoon Session
                        </h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="afternoon_check_in_time" class="block text-sm font-medium text-gray-700 mb-2">Check-In Time</label>
                                <input type="time" name="afternoon_check_in_time" id="afternoon_check_in_time" 
                                       value="{{ $settings['attendance_afternoon_check_in_time'] }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg p-3">
                            </div>
                            <div>
                                <label for="afternoon_check_out_time" class="block text-sm font-medium text-gray-700 mb-2">Check-Out Time</label>
                                <input type="time" name="afternoon_check_out_time" id="afternoon_check_out_time" 
                                       value="{{ $settings['attendance_afternoon_check_out_time'] }}"
                                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg p-3">
                            </div>
                        </div>
                    </div>

                    <!-- Grace Period -->
                    <div>
                        <label for="late_grace_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                            Late Grace Period (minutes)
                        </label>
                        <div class="flex items-center">
                            <input type="number" name="late_grace_minutes" id="late_grace_minutes" 
                                   value="{{ $settings['attendance_late_grace_minutes'] }}"
                                   min="0" max="60"
                                   class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-lg p-3 w-24"
                                   required>
                            <span class="ml-4 text-gray-500">Minutes after check-in time before marking as late</span>
                        </div>
                    </div>

                    <!-- Preview -->
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <h4 class="font-medium text-gray-900 mb-3">Preview</h4>
                        <div id="single_preview" class="{{ $settings['attendance_session_mode'] === 'dual' ? 'hidden' : '' }}">
                            <div class="flex items-center space-x-8">
                                <div class="text-center">
                                    <p class="text-xs text-gray-500 uppercase">Check-In</p>
                                    <p class="text-2xl font-bold text-green-600" id="preview_check_in">{{ $settings['attendance_check_in_time'] }}</p>
                                </div>
                                <div class="text-gray-400">→</div>
                                <div class="text-center">
                                    <p class="text-xs text-gray-500 uppercase">Check-Out</p>
                                    <p class="text-2xl font-bold text-blue-600" id="preview_check_out">{{ $settings['attendance_check_out_time'] }}</p>
                                </div>
                                <div class="text-gray-400">|</div>
                                <div class="text-center">
                                    <p class="text-xs text-gray-500 uppercase">Work Day</p>
                                    <p class="text-2xl font-bold text-gray-700" id="preview_hours">{{ $settings['attendance_work_hours'] }} hrs</p>
                                </div>
                            </div>
                        </div>
                        <div id="dual_preview" class="{{ $settings['attendance_session_mode'] === 'dual' ? '' : 'hidden' }}">
                            <div class="grid grid-cols-2 gap-6">
                                <div class="bg-amber-50 rounded-lg p-3">
                                    <p class="text-xs font-medium text-amber-700 uppercase mb-2">Morning Session</p>
                                    <div class="flex items-center space-x-3">
                                        <span class="text-lg font-bold text-green-600" id="preview_morning_in">{{ $settings['attendance_check_in_time'] }}</span>
                                        <span class="text-gray-400">→</span>
                                        <span class="text-lg font-bold text-blue-600" id="preview_morning_out">{{ $settings['attendance_check_out_time'] }}</span>
                                        <span class="text-sm text-gray-500">(<span id="preview_morning_hrs">{{ $settings['attendance_work_hours'] }}</span> hrs)</span>
                                    </div>
                                </div>
                                <div class="bg-indigo-50 rounded-lg p-3">
                                    <p class="text-xs font-medium text-indigo-700 uppercase mb-2">Afternoon Session</p>
                                    <div class="flex items-center space-x-3">
                                        <span class="text-lg font-bold text-green-600" id="preview_afternoon_in">{{ $settings['attendance_afternoon_check_in_time'] }}</span>
                                        <span class="text-gray-400">→</span>
                                        <span class="text-lg font-bold text-blue-600" id="preview_afternoon_out">{{ $settings['attendance_afternoon_check_out_time'] }}</span>
                                        <span class="text-sm text-gray-500">(<span id="preview_afternoon_hrs">{{ $settings['attendance_afternoon_work_hours'] }}</span> hrs)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- Info Card -->
        <div class="mt-6 bg-blue-50 rounded-xl border border-blue-200 p-4">
            <div class="flex">
                <svg class="w-6 h-6 text-blue-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h4 class="font-medium text-blue-900">How it works</h4>
                    <ul class="mt-2 text-sm text-blue-800 space-y-1">
                        <li>• <strong>Single Session:</strong> One set of work hours for the entire day</li>
                        <li>• <strong>Dual Session:</strong> Separate morning and afternoon shifts - system auto-detects based on scan time</li>
                        <li>• <strong>Late:</strong> Teacher checks in after the session check-in time (+ grace period)</li>
                        <li>• <strong>Overtime:</strong> Teacher checks out after the session check-out time</li>
                        <li>• Changes apply immediately to all attendance calculations</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('check_in_time').addEventListener('change', updatePreview);
    document.getElementById('check_out_time').addEventListener('change', updatePreview);
    document.getElementById('afternoon_check_in_time').addEventListener('change', updatePreview);
    document.getElementById('afternoon_check_out_time').addEventListener('change', updatePreview);

    function toggleSessionMode() {
        const isDual = document.querySelector('input[name="session_mode"]:checked').value === 'dual';
        document.getElementById('afternoon_section').classList.toggle('hidden', !isDual);
        document.getElementById('single_preview').classList.toggle('hidden', isDual);
        document.getElementById('dual_preview').classList.toggle('hidden', !isDual);
        document.getElementById('morning_label').textContent = isDual ? 'Morning Session' : 'Work Hours';
        
        // Update radio button styles
        document.querySelectorAll('input[name="session_mode"]').forEach(radio => {
            const label = radio.closest('label');
            if (radio.checked) {
                label.classList.add('border-blue-500', 'bg-blue-50');
                label.classList.remove('border-gray-200');
            } else {
                label.classList.remove('border-blue-500', 'bg-blue-50');
                label.classList.add('border-gray-200');
            }
        });
    }

    function calculateHours(inTime, outTime) {
        if (!inTime || !outTime) return 0;
        const [inH, inM] = inTime.split(':').map(Number);
        const [outH, outM] = outTime.split(':').map(Number);
        return (outH - inH + (outM - inM) / 60).toFixed(1);
    }

    function updatePreview() {
        const checkIn = document.getElementById('check_in_time').value;
        const checkOut = document.getElementById('check_out_time').value;
        const afternoonIn = document.getElementById('afternoon_check_in_time').value;
        const afternoonOut = document.getElementById('afternoon_check_out_time').value;
        
        // Single session preview
        document.getElementById('preview_check_in').textContent = checkIn;
        document.getElementById('preview_check_out').textContent = checkOut;
        document.getElementById('preview_hours').textContent = calculateHours(checkIn, checkOut) + ' hrs';
        
        // Dual session preview
        document.getElementById('preview_morning_in').textContent = checkIn;
        document.getElementById('preview_morning_out').textContent = checkOut;
        document.getElementById('preview_morning_hrs').textContent = calculateHours(checkIn, checkOut);
        
        document.getElementById('preview_afternoon_in').textContent = afternoonIn;
        document.getElementById('preview_afternoon_out').textContent = afternoonOut;
        document.getElementById('preview_afternoon_hrs').textContent = calculateHours(afternoonIn, afternoonOut);
    }
</script>
@endpush
@endsection
