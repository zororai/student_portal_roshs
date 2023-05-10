<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Webresult;

class ResultController extends Controller
{
    public function index()
    {
        $results = Webresult::all();
        return view('results.index', compact('results'));
    }

    public function create()
    {
        return view('results.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        $path = $request->file('image')->store('results', 'public');

        Webresult::create(['image_path' => $path]);

        return redirect()->route('results.index')->with('success', 'Image uploaded successfully.');
    }

    public function edit(Webresult $result)
    {
        return view('results.edit', compact('result'));
    }

    public function update(Request $request, Webresult $result)
    {
        if ($request->hasFile('image')) {
            $request->validate(['image' => 'image|max:2048']);
            Storage::delete('public/' . $result->image_path);
            $result->image_path = $request->file('image')->store('results', 'public');
        }

        $result->save();
        return redirect()->route('results.index')->with('success', 'Image updated successfully.');
    }

    public function destroy(Webresult $result)
    {
        Storage::delete('public/' . $result->image_path);
        $result->delete();
        return redirect()->route('results.index')->with('success', 'Image deleted successfully.');
    }
}
