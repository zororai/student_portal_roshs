<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Reading;
use App\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AddsubjectController extends Controller
{
 
    // Display a list of readings
    public function show($subject_id)
    {
        // Retrieve readings associated with the given subject_id
        $readings = Reading::where('subject_id', $subject_id)->get();

        return view('backend.Reading.index', compact('readings', 'subject_id'));
    }

    public function showread($subject_id)
    {
        // Retrieve readings associated with the given subject_id
        $readings = Reading::where('subject_id', $subject_id)->get();
    
        return view('student.viewreading', compact('readings', 'subject_id'));
    }

    public function download($id)
    {
        $reading = Reading::findOrFail($id);
        
        // Check if file exists in public disk
        if (!Storage::disk('public')->exists($reading->path)) {
            abort(404, 'File not found');
        }
        
        // Get the full path and original filename
        $filePath = storage_path('app/public/' . $reading->path);
        $fileName = $reading->name . '.' . pathinfo($reading->path, PATHINFO_EXTENSION);
        
        return response()->download($filePath, $fileName);
    }

      
   public function studentviewsubject()
   {
    $user = Auth::user();

    $student = Student::with(['user','parent','class','attendances'])->findOrFail($user->student->id); 

    return view('student.Subject', compact('student'));
   }

   public function studentattendance()
   {
    $user = Auth::user();

    $student = Student::with(['user','parent','class','attendances'])->findOrFail($user->student->id); 

    return view('student.Attendance', compact('student'));
   }

    

    // Show the form to create a new reading
    public function create(Request $request)
    {
        $subject_id = $request->query('subject_id'); // Get the subject_id from the query parameters
        return view('backend.Reading.create', compact('subject_id'));
    }

    // Store a new reading
    public function store(Request $request)
    {
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'path' => 'required|file|mimes:pdf,doc,docx|max:2048', // Example for file type
            'youtube_link' => 'nullable|url',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        $path = $request->file('path')->store('uploads', 'public');

        Reading::create([
            'name' => $request->name,
            'description' => $request->description,
            'path' => $path,
            'youtube_link' => $request->youtube_link,
            'subject_id' => $request->subject_id,
        ]);
        $subject_id =  $request->subject_id;

        $readings = Reading::where('subject_id', $subject_id)->get();
    
        return view('backend.Reading.index', compact('readings', 'subject_id'));
    }

    // Show the form to edit a reading
    public function edit($id)
    {
        $reading = Reading::where('subject_id', $id)->get();
        return view('backend.Reading.edit', compact('reading'));
    }

    // Update a reading
    public function update(Request $request, $id)
    {
        $reading = Reading::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'path' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'youtube_link' => 'nullable|url',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        if ($request->hasFile('path')) {
            // Delete the old file
            Storage::disk('public')->delete($reading->path);
            // Store the new file
            $path = $request->file('path')->store('uploads', 'public');
            $reading->path = $path;
        }

        $reading->name = $request->name;
        $reading->description = $request->description;
        $reading->youtube_link = $request->youtube_link;
        $reading->subject_id = $request->subject_id;
        $reading->save();

        return redirect()->route('backend.Reading.index')->with('success', 'Reading updated successfully.');
    }
    public function destroy($id)
    {
        $readings = Reading::where('subject_id', $id)->get(); 
    
        foreach ($readings as $reading) {
            Storage::disk('public')->delete($reading->path);
            $reading->delete();
        }
    
        $subject_id = $id;
        $readings = Reading::all(); // Fetch updated readings
    
        return view('backend.Reading.index', compact('readings', 'subject_id'));
    }
}


