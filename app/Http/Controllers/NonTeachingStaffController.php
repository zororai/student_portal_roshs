<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class NonTeachingStaffController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin']);
    }

    public function index()
    {
        // Get users with Staff role
        $staff = User::role('Staff')->orderBy('name')->paginate(20);
        
        return view('backend.admin.non-teaching-staff.index', compact('staff'));
    }

    public function create()
    {
        $positions = [
            'Driver' => 'Driver',
            'Accountant' => 'Accountant',
            'Secretary' => 'Secretary',
            'Security' => 'Security Guard',
            'Cleaner' => 'Cleaner',
            'Cook' => 'Cook',
            'Groundskeeper' => 'Groundskeeper',
            'Maintenance' => 'Maintenance',
            'Librarian' => 'Librarian',
            'Lab Technician' => 'Lab Technician',
            'Nurse' => 'School Nurse',
            'Other' => 'Other',
        ];
        
        return view('backend.admin.non-teaching-staff.create', compact('positions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:255',
            'national_id' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date',
            'date_hired' => 'nullable|date',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
        ]);

        // Ensure Staff role exists
        $staffRole = Role::firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);

        // Create the user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password123'), // Default password
            'phone' => $request->phone,
            'position' => $request->position,
            'national_id' => $request->national_id,
            'address' => $request->address,
            'date_of_birth' => $request->date_of_birth,
            'date_hired' => $request->date_hired,
            'emergency_contact' => $request->emergency_contact,
            'emergency_phone' => $request->emergency_phone,
        ]);

        // Assign Staff role
        $user->assignRole('Staff');

        return redirect()->route('admin.non-teaching-staff.index')
            ->with('success', 'Staff member added successfully! Default password is: password123');
    }

    public function show($id)
    {
        $staff = User::role('Staff')->findOrFail($id);
        return view('backend.admin.non-teaching-staff.show', compact('staff'));
    }

    public function edit($id)
    {
        $staff = User::role('Staff')->findOrFail($id);
        
        $positions = [
            'Driver' => 'Driver',
            'Accountant' => 'Accountant',
            'Secretary' => 'Secretary',
            'Security' => 'Security Guard',
            'Cleaner' => 'Cleaner',
            'Cook' => 'Cook',
            'Groundskeeper' => 'Groundskeeper',
            'Maintenance' => 'Maintenance',
            'Librarian' => 'Librarian',
            'Lab Technician' => 'Lab Technician',
            'Nurse' => 'School Nurse',
            'Other' => 'Other',
        ];
        
        return view('backend.admin.non-teaching-staff.edit', compact('staff', 'positions'));
    }

    public function update(Request $request, $id)
    {
        $staff = User::role('Staff')->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'position' => 'required|string|max:255',
            'national_id' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'date_of_birth' => 'nullable|date',
            'date_hired' => 'nullable|date',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
        ]);

        $staff->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'position' => $request->position,
            'national_id' => $request->national_id,
            'address' => $request->address,
            'date_of_birth' => $request->date_of_birth,
            'date_hired' => $request->date_hired,
            'emergency_contact' => $request->emergency_contact,
            'emergency_phone' => $request->emergency_phone,
        ]);

        return redirect()->route('admin.non-teaching-staff.index')
            ->with('success', 'Staff member updated successfully!');
    }

    public function destroy($id)
    {
        $staff = User::role('Staff')->findOrFail($id);
        $staff->delete();

        return redirect()->route('admin.non-teaching-staff.index')
            ->with('success', 'Staff member deleted successfully!');
    }
}
