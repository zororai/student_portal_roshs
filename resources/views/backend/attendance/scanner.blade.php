@extends('layouts.app')
@section('title', 'Teacher Attendance Scanner')

@section('content')
<div class="min-h-screen bg-gray-100">
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Teacher Attendance Scanner</h1>
            <p class="text-gray-600 mt-1">Scan teacher QR codes for automatic check-in/check-out</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Total Teachers</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalTeachers }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Present Today</p>
                        <p class="text-2xl font-bold text-green-600">{{ $presentTeachers }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Currently In</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $currentlyIn }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Not Arrived</p>
                        <p class="text-2xl font-bold text-red-600">{{ $absentTeachers }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Scanner Section -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-600">
                        <h2 class="text-xl font-semibold text-white flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                            </svg>
                            QR Code Scanner
                        </h2>
                        <p class="text-blue-100 text-sm mt-1">Ready for 2D barcode scanner input</p>
                    </div>
                    
                    <div class="p-6">
                        <!-- Scanner Status -->
                        <div id="scannerStatus" class="mb-6 text-center">
                            <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center animate-pulse" id="scannerIcon">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                </svg>
                            </div>
                            <p class="text-lg font-medium text-gray-700" id="statusText">Waiting for scan...</p>
                            <p class="text-sm text-gray-500 mt-1">Scanner is active and listening</p>
                        </div>

                        <!-- Last Scan Result -->
                        <div id="lastScanResult" class="hidden">
                            <div class="border-t border-gray-200 pt-4">
                                <h3 class="text-sm font-medium text-gray-700 mb-3">Last Scan</h3>
                                <div id="scanResultCard" class="p-4 rounded-lg">
                                    <div class="flex items-center">
                                        <div id="resultAvatar" class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                                            <img id="resultImage" src="" alt="" class="w-full h-full object-cover hidden">
                                            <span id="resultInitial" class="text-xl font-bold text-gray-500"></span>
                                        </div>
                                        <div class="ml-4">
                                            <p id="resultName" class="font-medium text-gray-900"></p>
                                            <p id="resultMessage" class="text-sm"></p>
                                            <p id="resultTime" class="text-xs text-gray-500 mt-1"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Date Selector -->
                        <div class="border-t border-gray-200 pt-4 mt-4">
                            <form method="GET" action="{{ route('attendance.logbook') }}" class="flex items-center space-x-2">
                                <label class="text-sm text-gray-600">View date:</label>
                                <input type="date" name="date" value="{{ $selectedDate->format('Y-m-d') }}" 
                                       class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       onchange="this.form.submit()">
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance List -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">
                            Today's Attendance - {{ $selectedDate->format('l, F j, Y') }}
                        </h2>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="attendanceTableBody">
                                @forelse($attendances as $attendance)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full object-cover" 
                                                     src="{{ asset('images/profile/' . ($attendance->teacher->user->profile_picture ?? 'avatar.png')) }}" 
                                                     alt="">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $attendance->teacher->user->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900">
                                            {{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm text-gray-900">
                                            {{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') : '-' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($attendance->status === 'IN')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Checked In
                                            </span>
                                        @elseif($attendance->status === 'OUT')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                Checked Out
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                {{ $attendance->status }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($attendance->check_in_time && $attendance->check_out_time)
                                            {{ \Carbon\Carbon::parse($attendance->check_out_time)->diffForHumans(\Carbon\Carbon::parse($attendance->check_in_time), true) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        <p class="text-lg font-medium">No attendance records yet</p>
                                        <p class="text-sm">Scan a teacher's QR code to record attendance</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Teachers Without QR Codes -->
                @php
                    $teachersWithoutQr = $teachers->filter(function($t) { return !$t->qr_code_token; });
                @endphp
                @if($teachersWithoutQr->count() > 0)
                <div class="mt-6 bg-yellow-50 rounded-xl border border-yellow-200 p-6">
                    <h3 class="text-lg font-semibold text-yellow-800 mb-4">
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Teachers Without QR Codes ({{ $teachersWithoutQr->count() }})
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($teachersWithoutQr as $teacher)
                        <div class="flex items-center justify-between bg-white p-3 rounded-lg border border-yellow-200">
                            <div class="flex items-center">
                                <img class="h-8 w-8 rounded-full object-cover" 
                                     src="{{ asset('images/profile/' . ($teacher->user->profile_picture ?? 'avatar.png')) }}" 
                                     alt="">
                                <span class="ml-3 text-sm font-medium text-gray-900">{{ $teacher->user->name }}</span>
                            </div>
                            <button onclick="generateQrCode({{ $teacher->id }})" 
                                    class="px-3 py-1 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                Generate QR
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let buffer = '';
    let timer = null;
    const csrfToken = '{{ csrf_token() }}';

    // Focus on document to capture scanner input
    document.addEventListener('DOMContentLoaded', function() {
        document.body.focus();
    });

    // Capture barcode scanner input
    document.addEventListener('keydown', function(e) {
        // Clear previous timer
        if (timer) clearTimeout(timer);

        // If Enter key is pressed, submit the scan
        if (e.key === 'Enter') {
            if (buffer.trim().length > 0) {
                submitScan(buffer.trim());
            }
            buffer = '';
            return;
        }

        // Ignore modifier keys
        if (e.key.length > 1) return;

        // Add character to buffer
        buffer += e.key;

        // Clear buffer after 100ms of no input (scanner is fast, typing is slow)
        timer = setTimeout(() => {
            buffer = '';
        }, 100);
    });

    function submitScan(token) {
        // Update UI to show processing
        document.getElementById('statusText').innerText = 'Processing scan...';
        document.getElementById('scannerIcon').classList.add('animate-spin');

        fetch('{{ route("attendance.scan") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ qr_code_token: token })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('scannerIcon').classList.remove('animate-spin');
            showScanResult(data);
            
            // Refresh the page after 3 seconds to update the list
            if (data.success) {
                setTimeout(() => {
                    window.location.reload();
                }, 3000);
            }
        })
        .catch(error => {
            document.getElementById('scannerIcon').classList.remove('animate-spin');
            document.getElementById('statusText').innerText = 'Error processing scan';
            console.error('Scan error:', error);
        });
    }

    function showScanResult(data) {
        const resultSection = document.getElementById('lastScanResult');
        const resultCard = document.getElementById('scanResultCard');
        const resultName = document.getElementById('resultName');
        const resultMessage = document.getElementById('resultMessage');
        const resultTime = document.getElementById('resultTime');
        const statusText = document.getElementById('statusText');
        const scannerIcon = document.getElementById('scannerIcon');

        resultSection.classList.remove('hidden');

        if (data.success) {
            if (data.type === 'success') {
                resultCard.className = 'p-4 rounded-lg bg-green-50 border border-green-200';
                resultMessage.className = 'text-sm text-green-600';
                scannerIcon.innerHTML = '<svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
            } else {
                resultCard.className = 'p-4 rounded-lg bg-blue-50 border border-blue-200';
                resultMessage.className = 'text-sm text-blue-600';
                scannerIcon.innerHTML = '<svg class="w-12 h-12 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
            }
            statusText.innerText = data.message;
            statusText.className = 'text-lg font-medium text-green-600';
        } else {
            if (data.type === 'warning') {
                resultCard.className = 'p-4 rounded-lg bg-yellow-50 border border-yellow-200';
                resultMessage.className = 'text-sm text-yellow-600';
                scannerIcon.innerHTML = '<svg class="w-12 h-12 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>';
            } else {
                resultCard.className = 'p-4 rounded-lg bg-red-50 border border-red-200';
                resultMessage.className = 'text-sm text-red-600';
                scannerIcon.innerHTML = '<svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
            }
            statusText.innerText = data.message;
            statusText.className = 'text-lg font-medium text-red-600';
        }

        if (data.teacher) {
            resultName.innerText = data.teacher.name;
            document.getElementById('resultInitial').innerText = data.teacher.name.charAt(0);
        }
        resultMessage.innerText = data.message;
        resultTime.innerText = new Date().toLocaleTimeString();

        // Reset status after 5 seconds
        setTimeout(() => {
            statusText.innerText = 'Waiting for scan...';
            statusText.className = 'text-lg font-medium text-gray-700';
            scannerIcon.innerHTML = '<svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>';
        }, 5000);
    }

    function generateQrCode(teacherId) {
        fetch(`/attendance/teacher/${teacherId}/generate-qr`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('QR code generated successfully!');
                window.location.reload();
            } else {
                alert('Failed to generate QR code: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error generating QR code');
        });
    }
</script>
@endpush
@endsection
