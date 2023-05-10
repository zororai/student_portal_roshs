<?php

namespace App\Http\Controllers;

use App\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    // Display the banner management view
    public function index()
    {
        $banner = Banner::first(); // Fetch the first banner (if exists)
        return view('banner.banner', compact('banner'));
    }

    // Store or update the banner images
    public function store(Request $request)
    {
        $request->validate([
            'image_path_1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_path_2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_path_3' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            // Check if there is an existing banner record
            $banner = Banner::first();

            // Store new images if uploaded
            if ($request->hasFile('image_path_1')) {
                $path1 = $request->file('image_path_1')->store('uploads', 'public');
                if ($banner) {
                    $banner->image_path_1 = $path1;
                }
            }

            if ($request->hasFile('image_path_2')) {
                $path2 = $request->file('image_path_2')->store('uploads', 'public');
                if ($banner) {
                    $banner->image_path_2 = $path2;
                }
            }

            if ($request->hasFile('image_path_3')) {
                $path3 = $request->file('image_path_3')->store('uploads', 'public');
                if ($banner) {
                    $banner->image_path_3 = $path3;
                }
            }

            if ($banner) {
                // Update existing record
                $banner->save();
            } else {
                // Create new record if none exists
                Banner::create([
                    'image_path_1' => $path1 ?? null,
                    'image_path_2' => $path2 ?? null,
                    'image_path_3' => $path3 ?? null,
                ]);
            }

            return redirect()->route('banner.index')->with('success', 'Banner updated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Image upload failed.']);
        }
    }
}
