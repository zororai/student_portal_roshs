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
use App\Helpers\SmsHelper;

class StudentController extends Controller
{
    /**
     * Generate next roll number
     *
     * @return string
     */
    private function generateRollNumber()
    {
        // Get the last student's roll number
        $lastStudent = Student::orderBy('id', 'desc')->first();

        if (!$lastStudent || empty($lastStudent->roll_number)) {
            // First student
            return 'RSH0001';
        }

        // Extract the numeric part from the last roll number
        $lastNumber = (int) substr($lastStudent->roll_number, 3);

        // Increment and format with leading zeros
        $newNumber = $lastNumber + 1;

        return 'RSH' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $students = Student::with(['class', 'user', 'parents'])->latest()->paginate(10);

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
        $nextRollNumber = $this->generateRollNumber();

        // Store the roll number in session for this form
        session(['student_roll_number' => $nextRollNumber]);

        return view('backend.students.create', compact('classes','parents','nextRollNumber'));
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
            'gender'            => 'required|string',
            'phone'             => 'required|string|max:255',
            'dateofbirth'       => 'required|date',
            'current_address'   => 'required|string|max:255',
            'permanent_address' => 'required|string|max:255'
        ]);

        // Get the roll number from session (generated when form was opened)
        $rollNumber = session('student_roll_number', $this->generateRollNumber());

        // Clear the session roll number
        session()->forget('student_roll_number');

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
            'roll_number'       => $rollNumber,
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
        $nextRollNumber = $this->generateRollNumber();

        // Store the roll number in session for this form
        session(['student_roll_number' => $nextRollNumber]);

        return view('backend.students.create_with_parents', compact('classes', 'nextRollNumber'));
    }

    /**
     * Store a newly created student with multiple parents
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeWithParents(Request $request)
    {
        // Debug: Log all incoming request data
        \Log::info('=== STORE WITH PARENTS DEBUG ===');
        \Log::info('Request data:', $request->all());
        \Log::info('Parents array:', $request->input('parents', []));

        try {
            // Validate student data
            $request->validate([
            'student_name'              => 'required|string|max:255',
            'student_email'             => 'required|string|email|max:255|unique:users,email',
                'student_phone'             => 'required|string|max:255',
                'student_gender'            => 'required|string',
                'dateofbirth'               => 'required|date',
                'class_id'                  => 'required|numeric',
                'parents'                   => 'required|array|min:1',
                'parents.*.name'            => 'required|string|max:255',
                'parents.*.phone'           => 'required|string|max:255|regex:/^\+[0-9]{10,15}$/',
            ]);

        \Log::info('Validation passed successfully');

        // Create student user with default password
        \Log::info('Creating student user...');
        $studentUser = User::create([
            'name'      => $request->student_name,
            'email'     => $request->student_email,
            'password'  => Hash::make('12345678') // Default password
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

        \Log::info('Student user created:', ['id' => $studentUser->id, 'name' => $studentUser->name]);

        // Create parents and collect their IDs
        $parentIds = [];
        $parentPhones = [];

        foreach ($request->parents as $index => $parentData) {
            // Generate unique registration token
            $registrationToken = \Illuminate\Support\Str::random(60);

            // Create a temporary email for parent (will be updated when they register)
            $tempEmail = 'pending_' . time() . '_' . $index . '@temp.parent';

            // Create parent user with temporary data
            $parentUser = User::create([
                'name'      => $parentData['name'],
                'email'     => $tempEmail,
                'password'  => Hash::make(\Illuminate\Support\Str::random(16)) // Temporary password
            ]);

            // Set default profile picture
            $parentUser->update([
                'profile_picture' => 'avatar.png'
            ]);

            // Create parent record with registration token
            $parent = $parentUser->parent()->create([
                'gender'                    => 'male', // Temporary default, will be updated by parent during registration
                'phone'                     => $parentData['phone'],
                'current_address'           => 'Pending', // Will be filled by parent
                'permanent_address'         => 'Pending', // Will be filled by parent
                'registration_token'        => $registrationToken,
                'token_expires_at'          => now()->addDays(7), // Token valid for 7 days
                'registration_completed'    => false
            ]);

            $parentUser->assignRole('Parent');
            $parentIds[] = $parent->id;
            $parentPhones[] = $parentData['phone'];
        }

        // Get the roll number from session (generated when form was opened)
        $rollNumber = session('student_roll_number', $this->generateRollNumber());

        // Clear the session roll number
        session()->forget('student_roll_number');

        // Create student record with dummy values for fields that will be updated on password change
        $student = $studentUser->student()->create([
            'parent_id'         => $parentIds[0], // First parent for backward compatibility
            'class_id'          => $request->class_id,
            'roll_number'       => $rollNumber,
            'gender'            => $request->student_gender,
            'phone'             => $request->student_phone,
            'dateofbirth'       => $request->dateofbirth,
            'current_address'   => 'To be updated', // Dummy value - student will update on first login
            'permanent_address' => 'To be updated'  // Dummy value - student will update on first login
        ]);

        // Attach all parents to the student
        $student->parents()->attach($parentIds);

        \Log::info('Student record created:', ['student_id' => $student->id, 'roll_number' => $rollNumber]);
        \Log::info('Parents attached:', ['parent_ids' => $parentIds]);

        $studentUser->assignRole('Student');

        // Send SMS to all parents with registration link and student roll number
        $smsSentCount = 0;
        $smsFailedCount = 0;
        
        foreach ($request->parents as $index => $parentData) {
            $parent = \App\Parents::where('phone', $parentData['phone'])->latest()->first();

            if ($parent && $parent->registration_token) {
                $registrationUrl = url('/parent/register/' . $parent->registration_token);

                // Shorter message to avoid InboxIQ HTTP 500 error
                $message = "RSH School: {$request->student_name} registered. Complete parent registration: {$registrationUrl}";

                // Send SMS directly using SmsHelper (same as test page)
                $smsResult = SmsHelper::sendSms($parentData['phone'], $message);
                
                if ($smsResult['success']) {
                    $smsSentCount++;
                    \Log::info('SMS sent successfully to parent', [
                        'phone' => $parentData['phone'],
                        'parent_id' => $parent->id,
                        'student_id' => $student->id,
                        'response' => $smsResult['response'] ?? null
                    ]);
                } else {
                    $smsFailedCount++;
                    \Log::warning('Failed to send SMS to parent', [
                        'phone' => $parentData['phone'],
                        'parent_id' => $parent->id,
                        'student_id' => $student->id,
                        'error' => $smsResult['message'] ?? 'Unknown error'
                    ]);
                }
            }
        }

        \Log::info('Process completed successfully');
        \Log::info('=== END DEBUG ===');

        return redirect()->route('student.index')->with('success', 'Student created successfully! SMS sent to parent(s) for registration completion.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed:', ['errors' => $e->errors()]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('Error in storeWithParents:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create student: ' . $e->getMessage());
        }
    }

    /**
     * Show password change form
     */
    public function showChangePasswordForm()
    {
        return view('student.change_password');
    }

    /**
     * Update student password and phone number
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'phone' => 'required|string|max:20',
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        // Check if new password is same as default
        if ($request->new_password === '12345678') {
            return redirect()->back()->with('error', 'New password cannot be the default password (12345678).');
        }

        // Update phone number on student record
        if ($user->student) {
            $user->student->phone = $request->phone;
            $user->student->save();
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('home')->with('success', 'Profile updated successfully!');
    }

    /**
     * Resend SMS to pending parents
     */
    public function resendParentSms(Student $student)
    {
        try {
            $sentCount = 0;
            $failedCount = 0;
            $skippedCount = 0;
            
            // Get all pending parents for this student
            $pendingParents = $student->parents()->where('registration_completed', false)->get();
            
            if ($pendingParents->count() == 0) {
                return redirect()->back()->with('warning', 'No pending parent registrations found for this student.');
            }
            
            foreach ($pendingParents as $parent) {
                if ($parent->registration_token && $parent->token_expires_at && $parent->token_expires_at->isFuture()) {
                    $registrationUrl = url('/parent/register/' . $parent->registration_token);
                    
                    // Shorter message to avoid InboxIQ HTTP 500 error
                    $message = "RSH School: {$student->user->name} registered. Complete parent registration: {$registrationUrl}";
                    
                    // Send SMS directly using SmsHelper (same as test page)
                    $result = SmsHelper::sendSms($parent->phone, $message);
                    
                    if ($result['success']) {
                        $sentCount++;
                        \Log::info('SMS sent successfully to parent', [
                            'phone' => $parent->phone,
                            'student_id' => $student->id,
                            'parent_id' => $parent->id,
                            'response' => $result['response'] ?? null
                        ]);
                    } else {
                        $failedCount++;
                        \Log::warning('Failed to send SMS to parent', [
                            'phone' => $parent->phone,
                            'student_id' => $student->id,
                            'parent_id' => $parent->id,
                            'error' => $result['message'] ?? 'Unknown error'
                        ]);
                    }
                } else {
                    $skippedCount++;
                    \Log::warning('Cannot resend SMS - token expired or missing', [
                        'parent_id' => $parent->id,
                        'token_expires_at' => $parent->token_expires_at,
                        'has_token' => !empty($parent->registration_token)
                    ]);
                }
            }
            
            if ($sentCount > 0 && $failedCount == 0 && $skippedCount == 0) {
                return redirect()->back()->with('success', "SMS sent successfully to {$sentCount} parent(s)!");
            } elseif ($sentCount > 0 && ($failedCount > 0 || $skippedCount > 0)) {
                return redirect()->back()->with('warning', "SMS sent to {$sentCount} parent(s), failed: {$failedCount}, skipped: {$skippedCount}.");
            } elseif ($failedCount > 0) {
                return redirect()->back()->with('error', "Failed to send SMS to {$failedCount} parent(s). Check logs for details.");
            } else {
                return redirect()->back()->with('error', "No SMS sent - all parent tokens are expired or missing.");
            }
            
        } catch (\Exception $e) {
            \Log::error('Error in resendParentSms:', [
                'message' => $e->getMessage(),
                'student_id' => $student->id
            ]);
            
            return redirect()->back()->with('error', 'Failed to send SMS: ' . $e->getMessage());
        }
    }
}
