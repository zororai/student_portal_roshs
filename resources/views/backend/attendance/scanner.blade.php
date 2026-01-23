@extends('layouts.app')
@section('title', 'Teacher Attendance Scanner')

@section('content')
<div class="min-h-screen bg-gray-100">
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Teacher Attendance Scanner</h1>
                <p class="text-gray-600 mt-1">Scan teacher QR codes for automatic check-in/check-out</p>
            </div>
            <button onclick="clearAllQrCodes()" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Clear All QR Codes
            </button>
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
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-semibold text-white flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                </svg>
                                QR Code Scanner
                            </h2>
                            <button onclick="toggleFullscreen()" class="p-2 bg-white/20 rounded-lg hover:bg-white/30 transition-colors" title="Fullscreen Kiosk Mode">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                                </svg>
                            </button>
                        </div>
                        <p class="text-blue-100 text-sm mt-1">Ready for 2D barcode scanner input</p>
                    </div>
                    
                    <div class="p-6">
                        <!-- Manual Input for Testing -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Manual QR Code Input</label>
                            <div class="flex space-x-2">
                                <input type="text" id="manualInput" placeholder="Paste QR code token here" 
                                       class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                       onkeydown="if(event.key === 'Enter') { manualScan(); event.stopPropagation(); }">
                                <button onclick="manualScan()" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                    Scan
                                </button>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Or use USB barcode scanner (auto-detects input)</p>
                        </div>

                        <!-- Scanner Status -->
                        <div id="scannerStatus" class="mb-6 text-center">
                            <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center" id="scannerIcon">
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
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">
                            Today's Attendance - {{ $selectedDate->format('l, F j, Y') }}
                        </h2>
                        <a href="{{ route('attendance.logbook.export', ['date' => $selectedDate->format('Y-m-d')]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export Excel
                        </a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teacher</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Session</th>
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
                                        @if($attendance->teacher->session === 'morning')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                                </svg>
                                                Morning
                                            </span>
                                        @elseif($attendance->teacher->session === 'afternoon')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                                </svg>
                                                Afternoon
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                Both
                                            </span>
                                        @endif
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
                                <div class="ml-3">
                                    <span class="text-sm font-medium text-gray-900">{{ $teacher->user->name }}</span>
                                    <div class="mt-1">
                                        @if($teacher->session === 'morning')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                                </svg>
                                                Morning
                                            </span>
                                        @elseif($teacher->session === 'afternoon')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                                </svg>
                                                Afternoon
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                Both
                                            </span>
                                        @endif
                                    </div>
                                </div>
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

<!-- QR Code Success Modal -->
<div id="qrSuccessModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeQrModal()"></div>
        <div class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-6">
            <div class="text-center">
                <div class="flex items-center justify-center w-16 h-16 mx-auto bg-green-100 rounded-full">
                    <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h3 class="mt-4 text-xl font-semibold text-gray-900">QR Code Generated!</h3>
                <p class="mt-2 text-sm text-gray-500">The QR code has been successfully generated for this teacher.</p>
                <div id="qrPreview" class="mt-4 p-4 bg-gray-50 rounded-lg">
                    <img id="qrImage" src="" alt="QR Code" class="mx-auto w-48 h-48">
                </div>
                <div class="mt-6 flex justify-center space-x-3">
                    <button onclick="closeQrModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Close
                    </button>
                    <button onclick="closeQrModal(); window.location.reload();" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors">
                        Refresh Page
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Error Modal -->
<div id="qrErrorModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" onclick="closeErrorModal()"></div>
        <div class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-xl sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-6">
            <div class="text-center">
                <div class="flex items-center justify-center w-16 h-16 mx-auto bg-red-100 rounded-full">
                    <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <h3 class="mt-4 text-xl font-semibold text-gray-900">Error</h3>
                <p id="errorMessage" class="mt-2 text-sm text-gray-500">Failed to generate QR code.</p>
                <div class="mt-6">
                    <button onclick="closeErrorModal()" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                        Close
                    </button>
                </div>
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

    // Capture barcode scanner input (only when not focused on input field)
    document.addEventListener('keydown', function(e) {
        // Skip if focused on manual input field
        if (document.activeElement.id === 'manualInput') return;

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

    // Manual scan function
    function manualScan() {
        const input = document.getElementById('manualInput');
        const token = input.value.trim();
        if (token.length > 0) {
            submitScan(token);
            input.value = '';
        }
    }

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
            
            // Play sound feedback
            playSound(data.success ? 'success' : 'error');
            
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
                showQrSuccessModal(data.qr_code_url);
            } else {
                showErrorModal('Failed to generate QR code: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorModal('Error generating QR code. Please try again.');
        });
    }

    function clearAllQrCodes() {
        if (!confirm('Are you sure you want to clear ALL QR codes for all teachers? They will need to be regenerated.')) {
            return;
        }

        fetch('/attendance/clear-all-qr', {
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
                alert(data.message);
                window.location.reload();
            } else {
                showErrorModal('Failed to clear QR codes: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showErrorModal('Error clearing QR codes. Please try again.');
        });
    }

    function showQrSuccessModal(qrCodeUrl) {
        document.getElementById('qrImage').src = qrCodeUrl;
        document.getElementById('qrSuccessModal').classList.remove('hidden');
    }

    function closeQrModal() {
        document.getElementById('qrSuccessModal').classList.add('hidden');
    }

    function showErrorModal(message) {
        document.getElementById('errorMessage').innerText = message;
        document.getElementById('qrErrorModal').classList.remove('hidden');
    }

    function closeErrorModal() {
        document.getElementById('qrErrorModal').classList.add('hidden');
    }

    // Fullscreen Kiosk Mode
    function toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(err => {
                console.log('Fullscreen error:', err);
            });
        } else {
            document.exitFullscreen();
        }
    }

    // Prevent screen sleep (Wake Lock API)
    let wakeLock = null;
    async function requestWakeLock() {
        try {
            if ('wakeLock' in navigator) {
                wakeLock = await navigator.wakeLock.request('screen');
                console.log('Wake Lock is active - screen will stay on');
            }
        } catch (err) {
            console.log('Wake Lock error:', err);
        }
    }

    // Re-acquire wake lock when page becomes visible again
    document.addEventListener('visibilitychange', async () => {
        if (wakeLock !== null && document.visibilityState === 'visible') {
            await requestWakeLock();
        }
    });

    // Sound feedback for scans
    function playSound(type) {
        const audio = new Audio();
        if (type === 'success') {
            audio.src = 'data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdH2JkpeLgHBibGx8iZSYl4x+bmBjbHuIk5eWjoB0ZWJqeIeRlZSQg3dsZWl2hI6TlJGEeXFoanuFj5KTkYV8dGtsfIaNkZGPhX16c299h42Qj42Gf3l1dn+GjI+OjIaAfHd4f4aLjoyKhoJ+eXp/hYqMi4mFgn98fICFiYqJh4SBf31+gYWIiYeGg4F/fn+ChYeHhoSCgIB/gIKEhoaFhIOBgH+AgYOFhYWEg4KAgICBgoSEhISCgoGAgYGCg4ODg4KCgYGBgYKCg4OCgoKBgYGBgoKCgoKCgoGBgYGCgoKCgoKCgYGBgYKCgoKCgoKBgYGBgoKCgoKCgQ==';
        } else {
            audio.src = 'data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAAB/goWIi42PkJGRkZGQj42LiIWCf3x5d3Z1dXV2d3l8f4KFiIuNj5CRkZGRkI+NioiFgn98eXd2dXV1dnd5e36Bg4aJi42PkJGRkZCPjoyKh4SCf3x5d3Z1dXV2d3l7foGDhoiLjY+QkZGRkI+OjIqHhIF/fHl3dnV1dXZ3eXt+gYOGiIuNj5CRkZGQj46MioeFgn98eXd2dXV1dnd5e36Bg4aIi42PkJGRkZCPjoyJh4SCf3x5d3Z1dXV2d3l7foGDhoiLjY+Q';
        }
        audio.volume = 0.5;
        audio.play().catch(e => console.log('Audio play error:', e));
    }

    // Request wake lock on page load
    requestWakeLock();

    // Keep page alive - prevent browser from throttling
    setInterval(() => {
        if (document.hidden) return;
        console.log('Scanner active:', new Date().toLocaleTimeString());
    }, 30000);
</script>
@endpush
@endsection
