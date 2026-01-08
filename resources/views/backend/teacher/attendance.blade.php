@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://cdn.jsdelivr.net/npm/@aspect-analytics/browser-fingerprint@0.1.7/dist/fingerprint.min.js"></script>
<style>
    #attendanceMap { height: 300px; width: 100%; border-radius: 0.75rem; }
    @keyframes pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.2); opacity: 0.8; }
        100% { transform: scale(1); opacity: 1; }
    }
    .user-marker div { animation: pulse 2s infinite; }
</style>

@php
    $deviceRegistrationStatus = $teacher->device_registration_status ?? 'not_required';
@endphp

@if($deviceRegistrationStatus === 'pending')
<!-- Device Registration Modal -->
<div id="deviceRegistrationModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">
        <div class="bg-blue-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white">Register Your Device</h2>
        </div>
        <div class="p-6">
            <div class="flex items-center justify-center mb-6">
                <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-gray-600 text-center mb-6">
                This device will be registered for your attendance. You will only be able to mark attendance from this device.
            </p>
            <div id="deviceInfo" class="bg-gray-50 rounded-lg p-4 mb-6">
                <p class="text-sm text-gray-500">Detecting device information...</p>
            </div>
            <div id="registrationError" class="hidden bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4"></div>
            <button id="registerDeviceBtn" onclick="registerDevice()" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                Register This Device
            </button>
        </div>
    </div>
</div>
@endif

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">My Attendance</h1>
        <p class="mt-2 text-sm text-gray-600">Mark your attendance for today</p>
    </div>

    <!-- Today's Status Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="p-4 bg-gradient-to-r from-blue-500 to-blue-600">
            <h3 class="text-lg font-semibold text-white">Today - {{ now()->format('l, F d, Y') }}</h3>
        </div>
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center">
                    @if($teacher->user->profile_picture)
                        <img class="h-16 w-16 rounded-full object-cover" src="{{ asset('storage/' . $teacher->user->profile_picture) }}" alt="">
                    @else
                        <div class="h-16 w-16 rounded-full bg-blue-500 flex items-center justify-center">
                            <span class="text-white font-bold text-2xl">{{ substr($teacher->user->name, 0, 1) }}</span>
                        </div>
                    @endif
                    <div class="ml-4">
                        <h4 class="text-xl font-semibold text-gray-900">{{ $teacher->user->name }}</h4>
                        <p class="text-gray-500">{{ $teacher->user->email }}</p>
                    </div>
                </div>
                <div id="currentStatus">
                    @if($todayLog && $todayLog->status === 'absent')
                        <span class="px-4 py-2 bg-red-100 text-red-800 rounded-full font-semibold">Marked Absent</span>
                    @elseif($todayLog && $todayLog->time_in && $todayLog->time_out)
                        <span class="px-4 py-2 bg-gray-100 text-gray-800 rounded-full font-semibold">Completed</span>
                    @elseif($todayLog && $todayLog->time_in)
                        <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full font-semibold flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                            Checked In
                        </span>
                    @else
                        <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded-full font-semibold">Not Checked In</span>
                    @endif
                </div>
            </div>

            <!-- Time Display -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <p class="text-sm text-gray-500 mb-1">Check In Time</p>
                    <p id="timeInDisplay" class="text-2xl font-bold {{ $todayLog && $todayLog->time_in ? 'text-green-600' : 'text-gray-300' }}">
                        {{ $todayLog && $todayLog->time_in ? \Carbon\Carbon::parse($todayLog->time_in)->format('H:i') : '--:--' }}
                    </p>
                </div>
                <div class="bg-gray-50 rounded-lg p-4 text-center">
                    <p class="text-sm text-gray-500 mb-1">Check Out Time</p>
                    <p id="timeOutDisplay" class="text-2xl font-bold {{ $todayLog && $todayLog->time_out ? 'text-blue-600' : 'text-gray-300' }}">
                        {{ $todayLog && $todayLog->time_out ? \Carbon\Carbon::parse($todayLog->time_out)->format('H:i') : '--:--' }}
                    </p>
                </div>
            </div>

            <!-- Location Status -->
            <div id="locationStatus" class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        <span class="text-blue-700">Detecting your location...</span>
                    </div>
                    <button type="button" onclick="retryLocation()" class="text-sm px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Retry</button>
                </div>
            </div>

            <!-- Map -->
            <div id="attendanceMap" class="mb-6"></div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-4 justify-center">
                @if(!$todayLog || (!$todayLog->time_in && $todayLog->status !== 'absent'))
                    <button type="button" id="checkInBtn" onclick="markAttendance('check_in')" 
                            class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                        </svg>
                        Check In
                    </button>
                    <button type="button" id="absentBtn" onclick="markAttendance('absent')" 
                            class="px-8 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Mark Absent
                    </button>
                @elseif($todayLog && $todayLog->time_in && !$todayLog->time_out && $todayLog->status !== 'absent')
                    <button type="button" id="checkOutBtn" onclick="markAttendance('check_out')" 
                            class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Check Out
                    </button>
                @else
                    <div class="text-center text-gray-500">
                        <svg class="w-12 h-12 mx-auto text-green-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>Attendance completed for today</p>
                    </div>
                @endif
            </div>

            <!-- Message Area -->
            <div id="messageArea" class="mt-4 hidden">
                <div class="p-4 rounded-lg"></div>
            </div>
        </div>
    </div>

    <!-- Monthly Statistics -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="p-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">This Month's Summary - {{ now()->format('F Y') }}</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-3 gap-4 text-center">
                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-3xl font-bold text-blue-600">{{ $totalDays }}</p>
                    <p class="text-sm text-gray-500">Total Days</p>
                </div>
                <div class="bg-green-50 rounded-lg p-4">
                    <p class="text-3xl font-bold text-green-600">{{ $presentDays }}</p>
                    <p class="text-sm text-gray-500">Present</p>
                </div>
                <div class="bg-red-50 rounded-lg p-4">
                    <p class="text-3xl font-bold text-red-600">{{ $absentDays }}</p>
                    <p class="text-sm text-gray-500">Absent</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Attendance History -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Recent Attendance History</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time Out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($monthLogs as $log)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $log->log_date->format('D, M d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                    {{ $log->status === 'present' ? 'bg-green-100 text-green-800' : 
                                       ($log->status === 'absent' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $log->time_in ? \Carbon\Carbon::parse($log->time_in)->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $log->time_out ? \Carbon\Carbon::parse($log->time_out)->format('H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($log->time_in && $log->time_out)
                                    @php
                                        $mins = \Carbon\Carbon::parse($log->time_in)->diffInMinutes(\Carbon\Carbon::parse($log->time_out));
                                    @endphp
                                    {{ floor($mins / 60) }}h {{ $mins % 60 }}m
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">No attendance records this month.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var map = null;
    var userMarker = null;
    var boundaryLayer = null;
    var currentLat = null;
    var currentLng = null;
    var schoolBoundary = @json($schoolBoundary);

    var locationFailed = false;
    var mapInitialized = false;

    // Use DOMContentLoaded for more reliable initialization
    document.addEventListener('DOMContentLoaded', function() {
        initMap();
        // Small delay to ensure map is fully rendered
        setTimeout(function() {
            getLocation();
        }, 500);
    });

    function retryLocation() {
        var statusEl = document.getElementById('locationStatus');
        statusEl.innerHTML = '<div class="flex items-center justify-between"><div class="flex items-center"><svg class="w-5 h-5 text-blue-500 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg><span class="text-blue-700">Retrying location detection...</span></div><button type="button" onclick="retryLocation()" class="text-sm px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Retry</button></div>';
        statusEl.className = 'bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6';
        locationFailed = false;
        getLocation();
    }

    var locationSkipped = false;

    function skipLocation() {
        var statusEl = document.getElementById('locationStatus');
        statusEl.innerHTML = '<div class="flex items-center text-yellow-700"><svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>Location skipped - attendance will be recorded without location verification</div>';
        statusEl.className = 'bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6';
        currentLat = 0;
        currentLng = 0;
        locationSkipped = true;
        locationFailed = false;
    }

    function initMap() {
        map = L.map('attendanceMap').setView([-17.8292, 31.0522], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap'
        }).addTo(map);

        // Fix map display issues by invalidating size after render
        setTimeout(function() {
            map.invalidateSize();
        }, 100);

        // Draw school boundary if exists
        if (schoolBoundary) {
            drawBoundary(schoolBoundary);
        }
        
        mapInitialized = true;
    }

    function drawBoundary(geo) {
        if (!geo) return;
        
        var coords = geo.coordinates;
        var layer;

        if (geo.shape_type === 'circle' && geo.center_lat && geo.center_lng && geo.radius) {
            layer = L.circle([geo.center_lat, geo.center_lng], {
                radius: geo.radius,
                color: '#3b82f6',
                fillColor: '#3b82f6',
                fillOpacity: 0.2
            });
        } else if (geo.shape_type === 'rectangle' && coords && coords.length === 4) {
            var bounds = [[coords[0].lat, coords[0].lng], [coords[2].lat, coords[2].lng]];
            layer = L.rectangle(bounds, {
                color: '#3b82f6',
                fillColor: '#3b82f6',
                fillOpacity: 0.2
            });
        } else if (coords && coords.length > 0) {
            var latLngs = coords.map(function(c) { return [c.lat, c.lng]; });
            layer = L.polygon(latLngs, {
                color: '#3b82f6',
                fillColor: '#3b82f6',
                fillOpacity: 0.2
            });
        }

        if (layer) {
            boundaryLayer = layer;
            layer.addTo(map);
            layer.bindPopup('<strong>School Boundary</strong><br>You must be inside this area to check in.');
        }
    }

    function getLocation() {
        var statusEl = document.getElementById('locationStatus');
        
        if (!navigator.geolocation) {
            statusEl.innerHTML = '<div class="flex items-center text-red-700"><svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>Geolocation not supported</div>';
            statusEl.className = 'bg-red-50 border border-red-200 rounded-lg p-4 mb-6';
            return;
        }

        navigator.geolocation.getCurrentPosition(
            function(position) {
                currentLat = position.coords.latitude;
                currentLng = position.coords.longitude;

                // Update map
                map.setView([currentLat, currentLng], 17);

                // Add/update user marker with a more visible design
                if (userMarker) {
                    userMarker.setLatLng([currentLat, currentLng]);
                } else {
                    userMarker = L.marker([currentLat, currentLng], {
                        icon: L.divIcon({
                            className: 'user-marker',
                            html: '<div style="background: #ef4444; width: 20px; height: 20px; border-radius: 50%; border: 4px solid white; box-shadow: 0 3px 8px rgba(0,0,0,0.4); animation: pulse 2s infinite;"></div>',
                            iconSize: [20, 20],
                            iconAnchor: [10, 10]
                        })
                    }).addTo(map);
                    userMarker.bindPopup('<strong>You are here</strong>').openPopup();
                }
                
                // Also invalidate map size after adding marker
                map.invalidateSize();

                // Check if within boundary
                var withinBoundary = checkWithinBoundary(currentLat, currentLng);
                
                if (withinBoundary) {
                    statusEl.innerHTML = '<div class="flex items-center text-green-700"><svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>You are within the school boundary</div>';
                    statusEl.className = 'bg-green-50 border border-green-200 rounded-lg p-4 mb-6';
                } else if (schoolBoundary) {
                    statusEl.innerHTML = '<div class="flex items-center text-yellow-700"><svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>You are outside the school boundary. Move closer to check in.</div>';
                    statusEl.className = 'bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6';
                } else {
                    statusEl.innerHTML = '<div class="flex items-center text-blue-700"><svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>Location detected. No school boundary set.</div>';
                    statusEl.className = 'bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6';
                }
            },
            function(error) {
                locationFailed = true;
                var message = 'Location error: ';
                var helpText = '';
                switch(error.code) {
                    case error.PERMISSION_DENIED: 
                        message += 'Permission denied.'; 
                        helpText = '<div class="mt-2 text-xs text-red-600"><strong>Note:</strong> Location access requires HTTPS. If using HTTP, click "Skip" to proceed without location verification.</div>';
                        break;
                    case error.POSITION_UNAVAILABLE: message += 'Position unavailable. Try moving to a different location.'; break;
                    case error.TIMEOUT: message += 'Request timed out. Please retry.'; break;
                }
                statusEl.innerHTML = '<div class="flex items-center justify-between"><div class="flex-1"><div class="flex items-center text-red-700"><svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>' + message + '</div>' + helpText + '</div><div class="flex gap-2"><button type="button" onclick="retryLocation()" class="text-sm px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Retry</button><button type="button" onclick="skipLocation()" class="text-sm px-3 py-1 bg-yellow-600 text-white rounded hover:bg-yellow-700">Skip Location</button></div></div>';
                statusEl.className = 'bg-red-50 border border-red-200 rounded-lg p-4 mb-6';
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    }

    function checkWithinBoundary(lat, lng) {
        if (!schoolBoundary) return true; // Allow if no boundary set
        
        // Simple point-in-polygon check for client-side (server does actual validation)
        if (schoolBoundary.shape_type === 'circle') {
            var centerLat = parseFloat(schoolBoundary.center_lat);
            var centerLng = parseFloat(schoolBoundary.center_lng);
            var radius = parseFloat(schoolBoundary.radius);
            var distance = getDistance(lat, lng, centerLat, centerLng);
            return distance <= radius;
        }
        return true; // Default to true, server will validate
    }

    function getDistance(lat1, lng1, lat2, lng2) {
        var R = 6371e3; // Earth radius in meters
        var φ1 = lat1 * Math.PI / 180;
        var φ2 = lat2 * Math.PI / 180;
        var Δφ = (lat2 - lat1) * Math.PI / 180;
        var Δλ = (lng2 - lng1) * Math.PI / 180;
        var a = Math.sin(Δφ/2) * Math.sin(Δφ/2) + Math.cos(φ1) * Math.cos(φ2) * Math.sin(Δλ/2) * Math.sin(Δλ/2);
        var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }

    function markAttendance(action) {
        if ((currentLat === null || currentLng === null) && !locationFailed) {
            showMessage('Please wait for your location to be detected, or click "Skip" to proceed without location.', 'error');
            return;
        }
        
        // Use 0,0 if location was skipped
        var lat = currentLat !== null ? currentLat : 0;
        var lng = currentLng !== null ? currentLng : 0;

        var btn = document.getElementById(action === 'check_in' ? 'checkInBtn' : (action === 'check_out' ? 'checkOutBtn' : 'absentBtn'));
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>Processing...';
        }

        fetch('{{ route("teacher.attendance.mark") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                latitude: lat,
                longitude: lng,
                action: action,
                location_skipped: locationSkipped
            })
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                showMessage(data.message, 'success');
                setTimeout(function() { location.reload(); }, 1500);
            } else {
                showMessage(data.message, 'error');
                if (btn) {
                    btn.disabled = false;
                    if (action === 'check_in') btn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>Check In';
                    else if (action === 'check_out') btn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>Check Out';
                    else btn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>Mark Absent';
                }
            }
        })
        .catch(function(error) {
            showMessage('An error occurred. Please try again.', 'error');
            console.error(error);
        });
    }

    function showMessage(message, type) {
        var area = document.getElementById('messageArea');
        var inner = area.querySelector('div');
        area.classList.remove('hidden');
        inner.className = 'p-4 rounded-lg ' + (type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800');
        inner.textContent = message;
    }

    // Device Fingerprinting
    var deviceFingerprint = null;
    var deviceInfo = {};

    function generateFingerprint() {
        // Check if we already have a stored fingerprint in localStorage
        var storedFingerprint = localStorage.getItem('device_fingerprint');
        if (storedFingerprint) {
            return storedFingerprint;
        }
        
        // Create a simple but effective fingerprint based on browser properties
        var canvas = document.createElement('canvas');
        var ctx = canvas.getContext('2d');
        ctx.textBaseline = 'top';
        ctx.font = '14px Arial';
        ctx.fillText('Device Fingerprint', 2, 2);
        var canvasData = canvas.toDataURL();
        
        var fingerprint = [
            navigator.userAgent,
            navigator.language,
            screen.width + 'x' + screen.height,
            screen.colorDepth,
            new Date().getTimezoneOffset(),
            navigator.hardwareConcurrency || 'unknown',
            navigator.platform,
            canvasData.substring(0, 100)
        ].join('|');
        
        // Create a hash of the fingerprint
        var hash = 0;
        for (var i = 0; i < fingerprint.length; i++) {
            var char = fingerprint.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash;
        }
        
        // Generate consistent fingerprint without timestamp
        var newFingerprint = 'FP' + Math.abs(hash).toString(36);
        
        // Store in localStorage for consistency
        localStorage.setItem('device_fingerprint', newFingerprint);
        
        return newFingerprint;
    }

    function getDeviceInfo() {
        var ua = navigator.userAgent;
        var browser = 'Unknown';
        var device = 'Unknown Device';
        
        // Detect browser
        if (ua.indexOf('Chrome') > -1) browser = 'Chrome';
        else if (ua.indexOf('Firefox') > -1) browser = 'Firefox';
        else if (ua.indexOf('Safari') > -1) browser = 'Safari';
        else if (ua.indexOf('Edge') > -1) browser = 'Edge';
        else if (ua.indexOf('Opera') > -1) browser = 'Opera';
        
        // Detect device
        if (/Android/i.test(ua)) device = 'Android Phone';
        else if (/iPhone/i.test(ua)) device = 'iPhone';
        else if (/iPad/i.test(ua)) device = 'iPad';
        else if (/Windows/i.test(ua)) device = 'Windows PC';
        else if (/Mac/i.test(ua)) device = 'Mac';
        else if (/Linux/i.test(ua)) device = 'Linux PC';
        
        return {
            browser: browser,
            device: device,
            platform: navigator.platform,
            language: navigator.language
        };
    }

    // Initialize fingerprint on page load
    document.addEventListener('DOMContentLoaded', function() {
        deviceFingerprint = generateFingerprint();
        deviceInfo = getDeviceInfo();
        
        // Store fingerprint in cookie for middleware validation
        document.cookie = 'device_fingerprint=' + deviceFingerprint + '; path=/; max-age=31536000';
        
        // Update device info display if registration modal exists
        var deviceInfoEl = document.getElementById('deviceInfo');
        if (deviceInfoEl) {
            deviceInfoEl.innerHTML = '<div class="space-y-2">' +
                '<p class="text-sm"><strong>Device:</strong> ' + deviceInfo.device + '</p>' +
                '<p class="text-sm"><strong>Browser:</strong> ' + deviceInfo.browser + '</p>' +
                '<p class="text-sm"><strong>Platform:</strong> ' + deviceInfo.platform + '</p>' +
                '</div>';
        }
    });

    function registerDevice() {
        var btn = document.getElementById('registerDeviceBtn');
        var errorEl = document.getElementById('registrationError');
        
        btn.disabled = true;
        btn.innerHTML = '<svg class="animate-spin w-5 h-5 inline mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>Registering...';
        errorEl.classList.add('hidden');
        
        fetch('{{ route("teacher.device.register") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Device-Fingerprint': deviceFingerprint
            },
            body: JSON.stringify({
                device_id: deviceFingerprint,
                device_name: deviceInfo.device,
                browser: deviceInfo.browser
            })
        })
        .then(function(response) { return response.json(); })
        .then(function(data) {
            if (data.success) {
                document.getElementById('deviceRegistrationModal').innerHTML = 
                    '<div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">' +
                    '<div class="bg-green-600 px-6 py-4"><h2 class="text-xl font-bold text-white">Device Registered</h2></div>' +
                    '<div class="p-6 text-center">' +
                    '<div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">' +
                    '<svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>' +
                    '</div>' +
                    '<p class="text-gray-600 mb-6">Your device has been successfully registered!</p>' +
                    '<button onclick="location.reload()" class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg">Continue</button>' +
                    '</div></div>';
            } else {
                errorEl.textContent = data.error || 'Registration failed. Please try again.';
                errorEl.classList.remove('hidden');
                btn.disabled = false;
                btn.textContent = 'Register This Device';
            }
        })
        .catch(function(error) {
            errorEl.textContent = 'An error occurred. Please try again.';
            errorEl.classList.remove('hidden');
            btn.disabled = false;
            btn.textContent = 'Register This Device';
            console.error(error);
        });
    }
</script>
@endsection
