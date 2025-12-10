<?php

namespace App\Http\Controllers;

use App\User;
use App\Grade;
use App\Parents;
use App\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade as PDF;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $students = Student::with(['class', 'user'])->latest()->paginate(10);

        return view('backend.students.index', compact('students'));
    }

    public function downloadIdCard($id)
    {
        $student = Student::with(['user', 'parent', 'class'])->findOrFail($id);
        $pdf = PDF::loadView('student.id_card', compact('student'));

        return $pdf->download('student_id_card_' . $student->roll_number . '.pdf');
    }

    public function showid($id)
    {
        $student = Student::with(['user', 'parent', 'class', 'attendances', 'subjects'])->findOrFail($id);

        return view('student.id_card', compact('student'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $classes = Grade::latest()->get();
        $parents = Parents::with('user')->latest()->get();

        return view('backend.students.create', compact('classes','parents'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|string|email|max:255|unique:users',
            'password'          => 'required|string|min:8',
            'parent_id'         => 'required|array|min:1',
            'parent_id.*'       => 'required|numeric|exists:parents,id',
            'class_id'          => 'required|numeric',
            'roll_number'       => [
                'required',
                'numeric',
                Rule::unique('students')->where(function ($query) use ($request) {
                    return $query->where('class_id', $request->class_id);
                })
            ],
            'gender'            => 'required|string',
            'phone'             => 'required|string|max:255',
            'dateofbirth'       => 'required|date',
            'current_address'   => 'required|string|max:255',
            'permanent_address' => 'required|string|max:255'
        ]);

        $user = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => Hash::make($request->password)
        ]);

        if ($request->hasFile('profile_picture')) {
            $profile = Str::slug($user->name).'-'.$user->id.'.'.$request->profile_picture->getClientOriginalExtension();
            $request->profile_picture->move(public_path('images/profile'), $profile);
        } else {
            $profile = 'avatar.png';
        }
        $user->update([
            'profile_picture' => $profile
        ]);

        $student = $user->student()->create([
            'parent_id'         => $request->parent_id[0], // Keep first parent for backward compatibility
            'class_id'          => $request->class_id,
            'roll_number'       => $request->roll_number,
            'gender'            => $request->gender,
            'phone'             => $request->phone,
            'dateofbirth'       => $request->dateofbirth,
            'current_address'   => $request->current_address,
            'permanent_address' => $request->permanent_address
        ]);

        // Attach all parents to the student
        $student->parents()->attach($request->parent_id);

        $user->assignRole('Student');

        return redirect()->route('student.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show(Student $student)
    {
        $class = Grade::with('subjects')->where('id', $student->class_id)->first();

        return view('backend.students.show', compact('class','student'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function edit(Student $student)
    {
        $classes = Grade::latest()->get();
        $parents = Parents::with('user')->latest()->get();

        return view('backend.students.edit', compact('classes','parents','student'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Student $student)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|string|email|max:255|unique:users,email,'.$student->user_id,
            'parent_id'         => 'required|array|min:1',
            'parent_id.*'       => 'required|numeric|exists:parents,id',
            'class_id'          => 'required|numeric',
            'roll_number'       => [
                'required',
                'numeric',
                Rule::unique('students')->ignore($student->id)->where(function ($query) use ($request) {
                    return $query->where('class_id', $request->class_id);
                })
            ],
            'gender'            => 'required|string',
            'phone'             => 'required|string|max:255',
            'dateofbirth'       => 'required|date',
            'current_address'   => 'required|string|max:255',
            'permanent_address' => 'required|string|max:255'
        ]);

        if ($request->hasFile('profile_picture')) {
            $profile = Str::slug($student->user->name).'-'.$student->user->id.'.'.$request->profile_picture->getClientOriginalExtension();
            $request->profile_picture->move(public_path('images/profile'), $profile);
        } else {
            $profile = $student->user->profile_picture;
        }

        $student->user()->update([
            'name'              => $request->name,
            'email'             => $request->email,
            'profile_picture'   => $profile
        ]);

        $student->update([
            'parent_id'         => $request->parent_id[0], // Keep first parent for backward compatibility
            'class_id'          => $request->class_id,
            'roll_number'       => $request->roll_number,
            'gender'            => $request->gender,
            'phone'             => $request->phone,
            'dateofbirth'       => $request->dateofbirth,
            'current_address'   => $request->current_address,
            'permanent_address' => $request->permanent_address
        ]);

        // Sync parents (removes old, adds new)
        $student->parents()->sync($request->parent_id);

        return redirect()->route('student.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy(Student $student)
    {
        $user = User::findOrFail($student->user_id);
        $user->student()->delete();
        $user->removeRole('Student');

        if ($user->delete()) {
            if($user->profile_picture != 'avatar.png') {
                $image_path = public_path() . '/images/profile/' . $user->profile_picture;
                if (is_file($image_path) && file_exists($image_path)) {
                    unlink($image_path);
                }
            }
        }

        return back();
    }

    /**
     * Show the form for creating a student with parents (stepper form)
     *
     * @return \Illuminate\Http\Response
     */
    public function createWithParents()
    {
        $classes = Grade::latest()->get();

        return view('backend.students.create_with_parents', compact('classes'));
    }

    /**
     * Store a newly created student with multiple parents
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeWithParents(Request $request)
    {
        // Validate student data
        $request->validate([
            'student_name'              => 'required|string|max:255',
            'student_email'             => 'required|string|email|max:255|unique:users,email',
            'student_password'          => 'required|string|min:8',
            'student_phone'             => 'required|string|max:255',
            'student_gender'            => 'required|string',
            'dateofbirth'               => 'required|date',
            'student_current_address'   => 'required|string|max:255',
            'student_permanent_address' => 'required|string|max:255',
            'class_id'                  => 'required|numeric',
            'roll_number'               => [
                'required',
                'numeric',
                Rule::unique('students')->where(function ($query) use ($request) {
                    return $query->where('class_id', $request->class_id);
                })
            ],
            'parents'                   => 'required|array|min:1',
            'parents.*.name'            => 'required|string|max:255',
            'parents.*.email'           => 'required|string|email|max:255|unique:users,email',
            'parents.*.password'        => 'required|string|min:8',
            'parents.*.phone'           => 'required|string|max:255',
            'parents.*.gender'          => 'required|string',
            'parents.*.current_address' => 'required|string|max:255',
            'parents.*.permanent_address' => 'required|string|max:255',
        ]);

        // Create student user
        $studentUser = User::create([
            'name'      => $request->student_name,
            'email'     => $request->student_email,
            'password'  => Hash::make($request->student_password)
        ]);

        // Handle student profile picture
        if ($request->hasFile('student_profile_picture')) {
            $profile = Str::slug($studentUser->name).'-'.$studentUser->id.'.'.$request->student_profile_picture->getClientOriginalExtension();
            $request->student_profile_picture->move(public_path('images/profile'), $profile);
        } else {
            $profile = 'avatar.png';
        }
        $studentUser->update([
            'profile_picture' => $profile
        ]);

        // Create parents and collect their IDs
        $parentIds = [];
        foreach ($request->parents as $index => $parentData) {
            // Create parent user
            $parentUser = User::create([
                'name'      => $parentData['name'],
                'email'     => $parentData['email'],
                'password'  => Hash::make($parentData['password'])
            ]);

            // Handle parent profile picture
            if ($request->hasFile("parents.{$index}.profile_picture")) {
                $parentProfile = Str::slug($parentUser->name).'-'.$parentUser->id.'.'.$request->file("parents.{$index}.profile_picture")->getClientOriginalExtension();
                $request->file("parents.{$index}.profile_picture")->move(public_path('images/profile'), $parentProfile);
            } else {
                $parentProfile = 'avatar.png';
            }
            $parentUser->update([
                'profile_picture' => $parentProfile
            ]);

            // Create parent record
            $parent = $parentUser->parent()->create([
                'gender'            => $parentData['gender'],
                'phone'             => $parentData['phone'],
                'current_address'   => $parentData['current_address'],
                'permanent_address' => $parentData['permanent_address']
            ]);

            $parentUser->assignRole('Parent');
            $parentIds[] = $parent->id;
        }

        // Create student record
        $student = $studentUser->student()->create([
            'parent_id'         => $parentIds[0], // First parent for backward compatibility
            'class_id'          => $request->class_id,
            'roll_number'       => $request->roll_number,
            'gender'            => $request->student_gender,
            'phone'             => $request->student_phone,
            'dateofbirth'       => $request->dateofbirth,
            'current_address'   => $request->student_current_address,
            'permanent_address' => $request->student_permanent_address
        ]);

        // Attach all parents to the student
        $student->parents()->attach($parentIds);

        $studentUser->assignRole('Student');

        return redirect()->route('student.index')->with('success', 'Student and parent(s) created successfully!');
    }
}
