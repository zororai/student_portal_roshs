<?php

namespace App\Http\Controllers;

use App\SchoolGeolocation;
use Illuminate\Http\Request;

class SchoolGeolocationController extends Controller
{
    /**
     * Display the geolocation settings page with Leaflet map.
     */
    public function index()
    {
        $geolocation = SchoolGeolocation::getActive();
        $allGeolocations = SchoolGeolocation::orderBy('created_at', 'desc')->get();

        return view('backend.settings.geolocation', compact('geolocation', 'allGeolocations'));
    }

    /**
     * Store a new geolocation boundary.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'shape_type' => 'required|in:polygon,circle,rectangle',
            'coordinates' => 'required|json',
            'center_lat' => 'nullable|numeric',
            'center_lng' => 'nullable|numeric',
            'radius' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        // Deactivate all existing boundaries
        SchoolGeolocation::where('is_active', true)->update(['is_active' => false]);

        // Create new boundary
        $geolocation = SchoolGeolocation::create([
            'name' => $request->name,
            'shape_type' => $request->shape_type,
            'coordinates' => json_decode($request->coordinates, true),
            'center_lat' => $request->center_lat,
            'center_lng' => $request->center_lng,
            'radius' => $request->radius,
            'description' => $request->description,
            'is_active' => true,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'School boundary saved successfully!',
            'geolocation' => $geolocation,
        ]);
    }

    /**
     * Update an existing geolocation boundary.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'shape_type' => 'required|in:polygon,circle,rectangle',
            'coordinates' => 'required|json',
            'center_lat' => 'nullable|numeric',
            'center_lng' => 'nullable|numeric',
            'radius' => 'nullable|numeric|min:0',
            'description' => 'nullable|string|max:1000',
        ]);

        $geolocation = SchoolGeolocation::findOrFail($id);

        $geolocation->update([
            'name' => $request->name,
            'shape_type' => $request->shape_type,
            'coordinates' => json_decode($request->coordinates, true),
            'center_lat' => $request->center_lat,
            'center_lng' => $request->center_lng,
            'radius' => $request->radius,
            'description' => $request->description,
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'School boundary updated successfully!',
            'geolocation' => $geolocation,
        ]);
    }

    /**
     * Delete a geolocation boundary.
     */
    public function destroy($id)
    {
        $geolocation = SchoolGeolocation::findOrFail($id);
        $geolocation->delete();

        return response()->json([
            'success' => true,
            'message' => 'School boundary deleted successfully!',
        ]);
    }

    /**
     * Set a geolocation as the active boundary.
     */
    public function setActive($id)
    {
        // Deactivate all
        SchoolGeolocation::where('is_active', true)->update(['is_active' => false]);

        // Activate selected
        $geolocation = SchoolGeolocation::findOrFail($id);
        $geolocation->update(['is_active' => true]);

        return response()->json([
            'success' => true,
            'message' => 'School boundary set as active!',
            'geolocation' => $geolocation,
        ]);
    }

    /**
     * Get the active geolocation boundary (API).
     */
    public function getActive()
    {
        $geolocation = SchoolGeolocation::getActive();

        if (!$geolocation) {
            return response()->json([
                'success' => false,
                'message' => 'No active school boundary found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'geolocation' => $geolocation,
        ]);
    }

    /**
     * Check if a point is within the school boundary.
     */
    public function checkPoint(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $geolocation = SchoolGeolocation::getActive();

        if (!$geolocation) {
            return response()->json([
                'success' => false,
                'message' => 'No active school boundary configured.',
                'within_boundary' => null,
            ]);
        }

        $isWithin = $geolocation->containsPoint($request->lat, $request->lng);

        return response()->json([
            'success' => true,
            'within_boundary' => $isWithin,
            'message' => $isWithin ? 'Point is within school boundary.' : 'Point is outside school boundary.',
        ]);
    }
}
