<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class WebcamController extends Controller
{
    public function index()
    {
        return view('Webcam.index');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $image = $request->file('image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('images'), $imageName);

        return back()->with('success', 'Image uploaded successfully.')->with('image', $imageName);
    }
}
