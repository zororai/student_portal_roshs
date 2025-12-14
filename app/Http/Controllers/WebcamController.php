<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Student;
use App\Grade;
use Illuminate\Support\Facades\Storage;

class WebcamController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $classes = Grade::orderBy('class_name')->get();
        $students = Student::with(['user', 'class'])->get();
        return view('Webcam.index', compact('classes', 'students'));
    }

    public function getStudentsByClass($classId)
    {
        $students = Student::where('class_id', $classId)
            ->with('user')
            ->get()
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->user->name ?? 'Unknown',
                    'roll_number' => $student->roll_number,
                ];
            });
        
        return response()->json($students);
    }

    public function getStudent($id)
    {
        $student = Student::with(['user', 'class'])->find($id);
        
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        return response()->json([
            'id' => $student->id,
            'name' => $student->user->name ?? 'Unknown',
            'roll_number' => $student->roll_number,
            'class_name' => $student->class->class_name ?? 'N/A',
            'gender' => $student->gender,
            'dateofbirth' => $student->dateofbirth,
            'photo' => $student->user->profile_picture ?? null,
        ]);
    }

    public function capturePhoto(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'image' => 'required|string',
        ]);

        $student = Student::with('user')->find($request->student_id);
        
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        // Decode base64 image
        $imageData = $request->image;
        $imageData = str_replace('data:image/png;base64,', '', $imageData);
        $imageData = str_replace('data:image/jpeg;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);
        $imageDecoded = base64_decode($imageData);

        // Create directory if not exists
        $directory = public_path('images/student_photos');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        // Save image
        $imageName = 'student_' . $student->id . '_' . time() . '.png';
        $imagePath = $directory . '/' . $imageName;
        file_put_contents($imagePath, $imageDecoded);

        // Update student's profile picture in user table
        if ($student->user) {
            $student->user->profile_picture = 'images/student_photos/' . $imageName;
            $student->user->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Photo captured successfully',
            'photo_url' => asset('images/student_photos/' . $imageName),
        ]);
    }

    public function generateIdCard($studentId)
    {
        $student = Student::with(['user', 'class'])->find($studentId);
        
        if (!$student) {
            return redirect()->back()->with('error', 'Student not found');
        }

        return view('Webcam.id-card', compact('student'));
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
