<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Banner;

class PageController extends Controller
{
    public function index()
    {
        $banners = Banner::all(); // Retrieve all banner images from the database
        return view('school', ['banners' => $banners]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'banner1' => 'image|nullable|max:2048',
            'banner2' => 'image|nullable|max:2048',
        ]);

        // Handle banner image uploads
        if ($request->hasFile('banner1')) {
            $banner1Path = $request->file('banner1')->store('banners', 'public');
            // Update or create the banner in the database
            Banner::updateOrCreate(['id' => 1], ['image_path' => $banner1Path]);
        }

        if ($request->hasFile('banner2')) {
            $banner2Path = $request->file('banner2')->store('banners', 'public');
            // Update or create the banner in the database
            Banner::updateOrCreate(['id' => 2], ['image_path' => $banner2Path]);
        }

        return response()->json(['message' => 'Banner images updated successfully']);
    }
}
