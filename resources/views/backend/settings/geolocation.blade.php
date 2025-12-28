@extends('layouts.app')

@section('content')
<!-- Leaflet CSS - Must be loaded before map renders -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<link rel="stylesheet" href="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.css"/>
<style>
    #map {
        z-index: 1;
        height: 500px;
        width: 100%;
    }
    
    /* Custom Leaflet Draw Toolbar Icons */
    .leaflet-draw-toolbar a {
        background-image: none !important;
        background-color: #fff;
        border: 1px solid #ccc;
        display: flex !important;
        align-items: center;
        justify-content: center;
    }
    
    .leaflet-draw-toolbar a:hover {
        background-color: #f4f4f4;
    }
    
    /* Polygon icon */
    .leaflet-draw-draw-polygon::before {
        content: '⬠';
        font-size: 18px;
        color: #3b82f6;
    }
    
    /* Rectangle icon */
    .leaflet-draw-draw-rectangle::before {
        content: '▭';
        font-size: 20px;
        color: #10b981;
    }
    
    /* Circle icon */
    .leaflet-draw-draw-circle::before {
        content: '◯';
        font-size: 18px;
        color: #8b5cf6;
    }
    
    /* Edit icon */
    .leaflet-draw-edit-edit::before {
        content: '✏';
        font-size: 16px;
        color: #f59e0b;
    }
    
    /* Delete/trash icon */
    .leaflet-draw-edit-remove::before {
        content: '🗑';
        font-size: 14px;
        color: #ef4444;
    }
    
    /* Active/selected state */
    .leaflet-draw-toolbar a.leaflet-draw-toolbar-button-enabled {
        background-color: #e0f2fe !important;
    }
</style>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">School Geolocation Settings</h1>
                <p class="mt-2 text-sm text-gray-600">Map and define the school boundary using shapes on the map</p>
            </div>
        </div>
    </div>

    <!-- Location Capture -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="p-4 bg-blue-600">
            <h3 class="text-lg font-semibold text-white">Step 1: Locate Your School</h3>
            <p class="text-blue-100 text-sm">Search for your school address or enter coordinates manually</p>
        </div>
        <div class="p-4">
            <!-- Search by Address -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Search by Address</label>
                <div class="flex gap-2">
                    <input type="text" id="addressSearch" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="Enter school address (e.g., 123 Main St, Harare, Zimbabwe)">
                    <button type="button" onclick="searchAddress()" style="background-color: #2563eb; color: white; padding: 8px 16px; border-radius: 8px; font-weight: 600; border: none; cursor: pointer;">
                        Search
                    </button>
                </div>
                <div id="searchResults" class="mt-2 text-sm"></div>
            </div>

            <!-- Manual Coordinate Entry -->
            <div class="border-t pt-4 mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Or Enter Coordinates Manually</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Latitude</label>
                        <input type="number" step="any" id="manualLat" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="-17.8292">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">Longitude</label>
                        <input type="number" step="any" id="manualLng" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="31.0522">
                    </div>
                    <div class="flex items-end">
                        <button type="button" onclick="goToManualCoords()" style="background-color: #10b981; color: white; padding: 8px 16px; border-radius: 8px; font-weight: 600; border: none; cursor: pointer; width: 100%;">
                            Go to Location
                        </button>
                    </div>
                </div>
            </div>

            <!-- Current Location Display -->
            <div class="border-t pt-4 mt-4">
                <div class="flex flex-wrap gap-4 items-center mb-3">
                    <button type="button" id="getLocationBtn" onclick="getCurrentLocation()" style="background-color: #6366f1; color: white; padding: 10px 20px; border-radius: 8px; font-weight: 600; border: none; cursor: pointer; display: inline-flex; align-items: center;">
                        <svg style="width: 18px; height: 18px; margin-right: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Use My GPS Location
                    </button>
                    <div id="locationStatus" class="text-sm text-gray-600">
                        <span style="color: #6b7280;">Requires HTTPS or localhost</span>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Latitude</label>
                        <input type="text" id="currentLat" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" placeholder="Not set">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Longitude</label>
                        <input type="text" id="currentLng" readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-100" placeholder="Not set">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Container -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="p-4 bg-gradient-to-r from-blue-500 to-blue-600">
            <h3 class="text-lg font-semibold text-white">Step 2: Draw School Boundary</h3>
            <p class="text-blue-100 text-sm">Use the drawing tools on the right side of the map to mark the school area</p>
        </div>
        
        <!-- Map -->
        <div id="map"></div>
        
        <!-- Map Controls Info -->
        <div class="p-4 bg-gray-50 border-t border-gray-200">
            <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-500 rounded mr-2 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"/>
                        </svg>
                    </div>
                    <span><strong>Polygon</strong> - Draw custom shape by clicking points</span>
                </div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-green-500 rounded mr-2 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <rect x="4" y="4" width="16" height="16" rx="1" stroke-width="2"/>
                        </svg>
                    </div>
                    <span><strong>Rectangle</strong> - Click and drag to draw</span>
                </div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-purple-500 rounded mr-2 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="8" stroke-width="2"/>
                        </svg>
                    </div>
                    <span><strong>Circle</strong> - Click center, drag for radius</span>
                </div>
            </div>
            <p class="mt-3 text-xs text-gray-500">Tip: Look for the drawing toolbar on the <strong>top-right</strong> of the map. Click a shape icon, then draw on the map.</p>
        </div>
    </div>

    <!-- Save Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <div class="p-4 bg-gradient-to-r from-emerald-500 to-emerald-600">
            <h3 class="text-lg font-semibold text-white">Save Boundary</h3>
            <p class="text-emerald-100 text-sm">Enter details and save the drawn boundary</p>
        </div>
        <div class="p-6">
            <form id="geolocationForm">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Boundary Name</label>
                        <input type="text" id="name" name="name" value="{{ $geolocation->name ?? 'School Boundary' }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="shape_type" class="block text-sm font-medium text-gray-700 mb-2">Shape Type</label>
                        <input type="text" id="shape_type" name="shape_type" readonly
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100" 
                               value="{{ $geolocation->shape_type ?? 'Not drawn yet' }}">
                    </div>
                </div>
                
                <div class="mt-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description (Optional)</label>
                    <textarea id="description" name="description" rows="2" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ $geolocation->description ?? '' }}</textarea>
                </div>

                <!-- Hidden fields for coordinates -->
                <input type="hidden" id="coordinates" name="coordinates" value="{{ $geolocation ? json_encode($geolocation->coordinates) : '' }}">
                <input type="hidden" id="center_lat" name="center_lat" value="{{ $geolocation->center_lat ?? '' }}">
                <input type="hidden" id="center_lng" name="center_lng" value="{{ $geolocation->center_lng ?? '' }}">
                <input type="hidden" id="radius" name="radius" value="{{ $geolocation->radius ?? '' }}">
                <input type="hidden" id="geolocation_id" value="{{ $geolocation->id ?? '' }}">

                <div class="mt-6 flex items-center justify-between">
                    <div id="coordinatesPreview" class="text-sm text-gray-500">
                        @if($geolocation)
                            <span class="text-green-600 font-medium">✓ Boundary loaded from database</span>
                        @else
                            <span class="text-amber-600">Draw a shape on the map to define the boundary</span>
                        @endif
                    </div>
                    <div class="flex space-x-3">
                        <button type="button" id="clearBtn" onclick="clearForm()" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
                            Clear Drawing
                        </button>
                        <button type="submit" id="saveBtn" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            Save Boundary
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Saved Boundaries -->
    @if($allGeolocations->count() > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 bg-gradient-to-r from-gray-700 to-gray-800">
            <h3 class="text-lg font-semibold text-white">Saved Boundaries</h3>
            <p class="text-gray-300 text-sm">Previously saved school boundaries</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shape</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($allGeolocations as $geo)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $geo->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $geo->shape_type === 'polygon' ? 'bg-blue-100 text-blue-800' : ($geo->shape_type === 'circle' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800') }}">
                                {{ ucfirst($geo->shape_type) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($geo->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $geo->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                @if(!$geo->is_active)
                                <button onclick="setActive({{ $geo->id }})" class="text-blue-600 hover:text-blue-900" title="Set Active">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                                @endif
                                <button onclick="loadBoundary({{ json_encode($geo) }})" class="text-emerald-600 hover:text-emerald-900" title="Load on Map">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                    </svg>
                                </button>
                                <button onclick="deleteBoundary({{ $geo->id }})" class="text-red-600 hover:text-red-900" title="Delete">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
<!-- Leaflet JS - Load inline to ensure proper initialization -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-draw@1.0.4/dist/leaflet.draw.js"></script>

<script>
    var map = null;
    var drawnItems = null;
    var drawControl = null;
    var currentLayer = null;
    var locationMarker = null;

    // Wait for everything to load
    window.onload = function() {
        console.log('Window loaded, initializing map...');
        
        try {
            // Default to Zimbabwe coordinates
            var defaultLat = -17.8292;
            var defaultLng = 31.0522;
            var defaultZoom = 15;

            // Create map
            map = L.map('map').setView([defaultLat, defaultLng], defaultZoom);
            console.log('Map created');

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

        // Initialize feature group for drawn items
        drawnItems = new L.FeatureGroup();
        map.addLayer(drawnItems);

        // Initialize draw control with all tools
        drawControl = new L.Control.Draw({
            position: 'topright',
            draw: {
                polygon: {
                    allowIntersection: false,
                    showArea: true,
                    drawError: {
                        color: '#e74c3c',
                        message: '<strong>Error:</strong> Shape edges cannot cross!'
                    },
                    shapeOptions: {
                        color: '#3b82f6',
                        fillColor: '#3b82f6',
                        fillOpacity: 0.3,
                        weight: 3
                    }
                },
                rectangle: {
                    showArea: true,
                    shapeOptions: {
                        color: '#10b981',
                        fillColor: '#10b981',
                        fillOpacity: 0.3,
                        weight: 3
                    }
                },
                circle: {
                    showRadius: true,
                    metric: true,
                    shapeOptions: {
                        color: '#8b5cf6',
                        fillColor: '#8b5cf6',
                        fillOpacity: 0.3,
                        weight: 3
                    }
                },
                polyline: false,
                marker: false,
                circlemarker: false
            },
            edit: {
                featureGroup: drawnItems,
                remove: true
            }
        });
        map.addControl(drawControl);

        // Handle draw created event
        map.on('draw:created', function(event) {
            const layer = event.layer;
            const type = event.layerType;

            // Clear existing drawings
            drawnItems.clearLayers();
            currentLayer = layer;

            // Add new layer
            drawnItems.addLayer(layer);

            // Extract coordinates based on shape type
            extractCoordinates(layer, type);

            document.getElementById('saveBtn').disabled = false;
            console.log('Shape drawn:', type);
        });

        // Handle draw edited event
        map.on('draw:edited', function(event) {
            const layers = event.layers;
            layers.eachLayer(function(layer) {
                let type = 'polygon';
                if (layer instanceof L.Circle) type = 'circle';
                else if (layer instanceof L.Rectangle) type = 'rectangle';
                
                extractCoordinates(layer, type);
            });
        });

        // Handle draw deleted event
        map.on('draw:deleted', function(event) {
            clearForm();
        });

        // Load existing boundary if available
        @if($geolocation)
            setTimeout(function() {
                loadExistingBoundary({!! json_encode($geolocation) !!});
            }, 500);
        @endif

        console.log('Map initialized successfully');
        
        } catch(e) {
            console.error('Error initializing map:', e);
        }
    };

    // Get current location button handler
    function getCurrentLocation() {
        const statusEl = document.getElementById('locationStatus');
        const latEl = document.getElementById('currentLat');
        const lngEl = document.getElementById('currentLng');
        
        statusEl.innerHTML = '<span class="text-blue-600">Detecting location...</span>';
        
        if (!navigator.geolocation) {
            statusEl.innerHTML = '<span class="text-red-600">Geolocation is not supported by your browser</span>';
            return;
        }
        
        navigator.geolocation.getCurrentPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                latEl.value = lat.toFixed(8);
                lngEl.value = lng.toFixed(8);
                statusEl.innerHTML = '<span class="text-green-600">✓ Location captured successfully!</span>';
                
                // Move map to location
                map.setView([lat, lng], 17);
                
                // Add/update marker
                if (locationMarker) {
                    locationMarker.setLatLng([lat, lng]);
                } else {
                    locationMarker = L.marker([lat, lng], {
                        icon: L.divIcon({
                            className: 'custom-marker',
                            html: '<div style="background: #ef4444; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>',
                            iconSize: [20, 20],
                            iconAnchor: [10, 10]
                        })
                    }).addTo(map);
                    locationMarker.bindPopup('<strong>Your Location</strong><br>Draw a shape around this area').openPopup();
                }
            },
            function(error) {
                let message = 'Unable to get location: ';
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        message += 'Permission denied. Please allow location access.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        message += 'Position unavailable.';
                        break;
                    case error.TIMEOUT:
                        message += 'Request timed out.';
                        break;
                }
                statusEl.innerHTML = '<span class="text-red-600">' + message + '</span>';
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
        );
    }

    // Search address using OpenStreetMap Nominatim API
    function searchAddress() {
        const address = document.getElementById('addressSearch').value.trim();
        const resultsEl = document.getElementById('searchResults');
        
        if (!address) {
            resultsEl.innerHTML = '<span style="color: #dc2626;">Please enter an address to search</span>';
            return;
        }
        
        resultsEl.innerHTML = '<span style="color: #2563eb;">Searching...</span>';
        
        fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(address))
            .then(response => response.json())
            .then(data => {
                if (data.length === 0) {
                    resultsEl.innerHTML = '<span style="color: #dc2626;">No results found. Try a different address.</span>';
                    return;
                }
                
                const result = data[0];
                const lat = parseFloat(result.lat);
                const lng = parseFloat(result.lon);
                
                resultsEl.innerHTML = '<span style="color: #16a34a;">✓ Found: ' + result.display_name.substring(0, 80) + '...</span>';
                
                // Update coordinate fields
                document.getElementById('currentLat').value = lat.toFixed(8);
                document.getElementById('currentLng').value = lng.toFixed(8);
                document.getElementById('manualLat').value = lat.toFixed(6);
                document.getElementById('manualLng').value = lng.toFixed(6);
                
                // Move map to location
                map.setView([lat, lng], 17);
                
                // Add marker
                if (locationMarker) {
                    locationMarker.setLatLng([lat, lng]);
                } else {
                    locationMarker = L.marker([lat, lng], {
                        icon: L.divIcon({
                            className: 'custom-marker',
                            html: '<div style="background: #ef4444; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>',
                            iconSize: [20, 20],
                            iconAnchor: [10, 10]
                        })
                    }).addTo(map);
                }
                locationMarker.bindPopup('<strong>' + result.display_name.substring(0, 50) + '</strong><br>Draw a shape around this area').openPopup();
            })
            .catch(error => {
                console.error('Search error:', error);
                resultsEl.innerHTML = '<span style="color: #dc2626;">Search failed. Please try again.</span>';
            });
    }

    // Go to manually entered coordinates
    function goToManualCoords() {
        const lat = parseFloat(document.getElementById('manualLat').value);
        const lng = parseFloat(document.getElementById('manualLng').value);
        
        if (isNaN(lat) || isNaN(lng)) {
            alert('Please enter valid latitude and longitude values');
            return;
        }
        
        if (lat < -90 || lat > 90) {
            alert('Latitude must be between -90 and 90');
            return;
        }
        
        if (lng < -180 || lng > 180) {
            alert('Longitude must be between -180 and 180');
            return;
        }
        
        // Update display fields
        document.getElementById('currentLat').value = lat.toFixed(8);
        document.getElementById('currentLng').value = lng.toFixed(8);
        
        // Move map to location
        map.setView([lat, lng], 17);
        
        // Add marker
        if (locationMarker) {
            locationMarker.setLatLng([lat, lng]);
        } else {
            locationMarker = L.marker([lat, lng], {
                icon: L.divIcon({
                    className: 'custom-marker',
                    html: '<div style="background: #ef4444; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>',
                    iconSize: [20, 20],
                    iconAnchor: [10, 10]
                })
            }).addTo(map);
        }
        locationMarker.bindPopup('<strong>Selected Location</strong><br>Lat: ' + lat.toFixed(6) + '<br>Lng: ' + lng.toFixed(6) + '<br>Draw a shape around this area').openPopup();
    }

    function extractCoordinates(layer, type) {
        let coordinates = [];
        let shapeType = type;
        let centerLat = null;
        let centerLng = null;
        let radius = null;

        if (type === 'circle') {
            const center = layer.getLatLng();
            centerLat = center.lat;
            centerLng = center.lng;
            radius = layer.getRadius();
            coordinates = [{ lat: centerLat, lng: centerLng }];
            shapeType = 'circle';
        } else if (type === 'rectangle') {
            const bounds = layer.getBounds();
            coordinates = [
                { lat: bounds.getNorthWest().lat, lng: bounds.getNorthWest().lng },
                { lat: bounds.getNorthEast().lat, lng: bounds.getNorthEast().lng },
                { lat: bounds.getSouthEast().lat, lng: bounds.getSouthEast().lng },
                { lat: bounds.getSouthWest().lat, lng: bounds.getSouthWest().lng }
            ];
            shapeType = 'rectangle';
        } else {
            const latLngs = layer.getLatLngs()[0];
            coordinates = latLngs.map(function(latLng) {
                return { lat: latLng.lat, lng: latLng.lng };
            });
            shapeType = 'polygon';
        }

        // Update form fields
        document.getElementById('shape_type').value = shapeType;
        document.getElementById('coordinates').value = JSON.stringify(coordinates);
        document.getElementById('center_lat').value = centerLat || '';
        document.getElementById('center_lng').value = centerLng || '';
        document.getElementById('radius').value = radius || '';

        // Update preview
        document.getElementById('coordinatesPreview').innerHTML = 
            '<span class="text-green-600 font-medium">✓ ' + shapeType.charAt(0).toUpperCase() + shapeType.slice(1) + 
            ' drawn with ' + coordinates.length + ' point(s)</span>';
    }

    function loadExistingBoundary(geo) {
        drawnItems.clearLayers();

        const coords = geo.coordinates;
        let layer;

        if (geo.shape_type === 'circle' && geo.center_lat && geo.center_lng && geo.radius) {
            layer = L.circle([geo.center_lat, geo.center_lng], {
                radius: geo.radius,
                color: '#8b5cf6',
                fillColor: '#8b5cf6',
                fillOpacity: 0.3
            });
        } else if (geo.shape_type === 'rectangle' && coords.length === 4) {
            const bounds = [
                [coords[0].lat, coords[0].lng],
                [coords[2].lat, coords[2].lng]
            ];
            layer = L.rectangle(bounds, {
                color: '#10b981',
                fillColor: '#10b981',
                fillOpacity: 0.3
            });
        } else if (coords && coords.length > 0) {
            const latLngs = coords.map(c => [c.lat, c.lng]);
            layer = L.polygon(latLngs, {
                color: '#3b82f6',
                fillColor: '#3b82f6',
                fillOpacity: 0.3
            });
        }

        if (layer) {
            drawnItems.addLayer(layer);
            currentLayer = layer;
            map.fitBounds(layer.getBounds(), { padding: [50, 50] });
            document.getElementById('saveBtn').disabled = false;
        }
    }

    function loadBoundary(geo) {
        document.getElementById('name').value = geo.name;
        document.getElementById('description').value = geo.description || '';
        document.getElementById('geolocation_id').value = geo.id;
        loadExistingBoundary(geo);
        
        // Scroll to map
        document.getElementById('map').scrollIntoView({ behavior: 'smooth' });
    }

    function clearForm() {
        drawnItems.clearLayers();
        currentLayer = null;
        document.getElementById('shape_type').value = 'Not drawn yet';
        document.getElementById('coordinates').value = '';
        document.getElementById('center_lat').value = '';
        document.getElementById('center_lng').value = '';
        document.getElementById('radius').value = '';
        document.getElementById('geolocation_id').value = '';
        document.getElementById('saveBtn').disabled = true;
        document.getElementById('coordinatesPreview').innerHTML = 
            '<span class="text-amber-600">Draw a shape on the map to define the boundary</span>';
    }

    // Clear button handler
    document.getElementById('clearBtn').addEventListener('click', clearForm);

    // Form submission
    document.getElementById('geolocationForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const id = document.getElementById('geolocation_id').value;
        const url = id ? '/admin/settings/geolocation/' + id : '/admin/settings/geolocation';
        const method = id ? 'PUT' : 'POST';

        const formData = {
            name: document.getElementById('name').value,
            shape_type: document.getElementById('shape_type').value,
            coordinates: document.getElementById('coordinates').value,
            center_lat: document.getElementById('center_lat').value || null,
            center_lng: document.getElementById('center_lng').value || null,
            radius: document.getElementById('radius').value || null,
            description: document.getElementById('description').value,
            _token: '{{ csrf_token() }}'
        };

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to save boundary'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while saving the boundary.');
        });
    });

    function setActive(id) {
        if (!confirm('Set this boundary as active?')) return;

        fetch('/admin/settings/geolocation/' + id + '/set-active', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }

    function deleteBoundary(id) {
        if (!confirm('Are you sure you want to delete this boundary?')) return;

        fetch('/admin/settings/geolocation/' + id, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        });
    }
</script>
@endsection
