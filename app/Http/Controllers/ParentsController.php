<?php

namespace App\Http\Controllers;

use App\User;
use App\Parents;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ParentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $parents = Parents::with(['user','children'])->latest()->paginate(10);

        return view('backend.parents.index', compact('parents'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $students = \App\Student::with('user')->latest()->get();
        return view('backend.parents.create', compact('students'));
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
            'gender'            => 'required|string|max:255',
            'phone'             => 'required|string|max:255',
            'current_address'   => 'required|string|max:255',
            'permanent_address' => 'required|string|max:255',
            'student_ids'       => 'nullable|array',
            'student_ids.*'     => 'exists:students,id'
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password)
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

        $parent = $user->parent()->create([
            'gender'            => $request->gender,
            'phone'             => $request->phone,
            'current_address'   => $request->current_address,
            'permanent_address' => $request->permanent_address
        ]);

        // Attach selected students to this parent
        if ($request->has('student_ids') && is_array($request->student_ids)) {
            $parent->students()->attach($request->student_ids);
        }

        $user->assignRole('Parent');

        return redirect()->route('parents.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Parents  $parents
     * @return \Illuminate\Http\Response
     */
    public function show(Parents $parents)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Parents  $parents
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $parent = Parents::with(['user', 'students'])->findOrFail($id);
        $students = \App\Student::with('user')->latest()->get();

        return view('backend.parents.edit', compact('parent', 'students'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Parents  $parents
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $parents = Parents::findOrFail($id);

        $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|string|email|max:255|unique:users,email,'.$parents->user_id,
            'gender'            => 'required|string',
            'phone'             => 'required|string|max:255',
            'current_address'   => 'required|string|max:255',
            'permanent_address' => 'required|string|max:255',
            'student_ids'       => 'nullable|array',
            'student_ids.*'     => 'exists:students,id'
        ]);

        if ($request->hasFile('profile_picture')) {
            $profile = Str::slug($parents->user->name).'-'.$parents->user->id.'.'.$request->profile_picture->getClientOriginalExtension();
            $request->profile_picture->move(public_path('images/profile'), $profile);
        } else {
            $profile = $parents->user->profile_picture;
        }

        $parents->user()->update([
            'name'              => $request->name,
            'email'             => $request->email,
            'profile_picture'   => $profile
        ]);

        $parents->update([
            'gender'            => $request->gender,
            'phone'             => $request->phone,
            'current_address'   => $request->current_address,
            'permanent_address' => $request->permanent_address
        ]);

        // Sync student relationships
        if ($request->has('student_ids')) {
            $parents->students()->sync($request->student_ids);
        } else {
            $parents->students()->detach();
        }

        return redirect()->route('parents.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Parents  $parents
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $parent = Parents::findOrFail($id);

        $user = User::findOrFail($parent->user_id);
        $user->removeRole('Parent');

        if ($user->delete()) {
            if($user->profile_picture != 'avatar.png') {
                $image_path = public_path() . '/images/profile/' . $user->profile_picture;
                if (is_file($image_path) && file_exists($image_path)) {
                    unlink($image_path);
                }
            }
        }

        $parent->delete();

        return back();
    }

    /**
     * Show the registration form for parent to complete their profile
     *
     * @param string $token
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm($token)
    {
        $parent = Parents::where('registration_token', $token)
                        ->where('registration_completed', false)
                        ->where('token_expires_at', '>', now())
                        ->first();

        if (!$parent) {
            return redirect('/')->with('error', 'Invalid or expired registration link.');
        }

        $parent->load('user', 'students.user');

        return view('backend.parents.complete_registration', compact('parent'));
    }

    /**
     * Complete parent registration
     *
     * @param \Illuminate\Http\Request $request
     * @param string $token
     * @return \Illuminate\Http\Response
     */
    public function completeRegistration(Request $request, $token)
    {
        $parent = Parents::where('registration_token', $token)
                        ->where('registration_completed', false)
                        ->where('token_expires_at', '>', now())
                        ->first();

        if (!$parent) {
            return redirect('/')->with('error', 'Invalid or expired registration link.');
        }

        // Ensure parent has a valid user
        if (!$parent->user) {
            return redirect('/')->with('error', 'Parent account not properly set up. Please contact the school.');
        }

        $request->validate([
            'email'             => 'required|string|email|max:255|unique:users,email,' . $parent->user_id,
            'password'          => 'required|string|min:8|confirmed',
            'gender'            => 'required|string',
            'current_address'   => 'required|string|max:255',
            'permanent_address' => 'required|string|max:255',
            'profile_picture'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            // Load the user relationship if not already loaded
            $parent->load('user');

            // Update user record
            $parent->user->update([
                'email'    => $request->email,
                'password' => Hash::make($request->password)
            ]);

            // Handle profile picture
            if ($request->hasFile('profile_picture')) {
                $profile = Str::slug($parent->user->name) . '-' . $parent->user_id . '.' . $request->profile_picture->getClientOriginalExtension();
                $request->profile_picture->move(public_path('images/profile'), $profile);

                $parent->user->update([
                    'profile_picture' => $profile
                ]);
            }

            // Update parent record
            $parent->update([
                'gender'                => $request->gender,
                'current_address'       => $request->current_address,
                'permanent_address'     => $request->permanent_address,
                'registration_completed' => true,
                'registration_token'    => null,
                'token_expires_at'      => null
            ]);

            return redirect()->route('parent.register.success');
        } catch (\Exception $e) {
            Log::error('Parent registration error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'An error occurred while saving your registration. Please try again.');
        }
    }

    /**
     * Show registration success page
     *
     * @return \Illuminate\Http\Response
     */
    public function registrationSuccess()
    {
        return view('backend.parents.registration_success');
    }
}

