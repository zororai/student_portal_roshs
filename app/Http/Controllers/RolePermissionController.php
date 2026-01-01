<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    /**
     * ROLES CRUD
     */
    public function roles()
    {
        $roles = Role::with('permissions')->get();

        return view('backend.roles.index', compact('roles'));
    }

    public function createRole()
    {
        // Auto-sync sidebar permissions before showing the form
        $this->syncSidebarPermissions();
        
        $permissions = Permission::orderBy('name')->get();

        return view('backend.roles.create', compact('permissions'));
    }
    
    /**
     * Automatically scan sidebar and create missing permissions
     */
    private function syncSidebarPermissions()
    {
        $sidebarPath = resource_path('views/layouts/sidebar.blade.php');
        
        if (!file_exists($sidebarPath)) {
            return;
        }

        $content = file_get_contents($sidebarPath);
        
        // Find all @can('permission-name') directives
        preg_match_all("/@can\s*\(\s*['\"]([^'\"]+)['\"]\s*\)/", $content, $matches);
        
        $permissions = array_unique($matches[1]);

        foreach ($permissions as $permissionName) {
            if (!Permission::where('name', $permissionName)->exists()) {
                Permission::create(['name' => $permissionName, 'guard_name' => 'web']);
            }
        }
    }

    public function storeRole(Request $request)
    {
    	$request->validate([
            'name'	=> 'required|string|max:255|unique:roles'
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->givePermissionTo($request->selectedpermissions);

        return redirect()->route('roles-permissions');
        
    }

    public function editRole($id)
    {
        // Auto-sync sidebar permissions before showing the form
        $this->syncSidebarPermissions();
        
        $role = Role::with('permissions')->find($id);
        $permissions = Permission::orderBy('name')->get();

        return view('backend.roles.edit', compact('role','permissions'));
    }

    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'name'	=> 'required|string|max:255|unique:roles,name,'.$id
        ]);

    	$role = Role::findById($id); 
        $role->update(['name' => $request->name]);
        $role->syncPermissions($request->selectedpermissions);

        return redirect()->route('roles-permissions');
    }

    /**
     * PERMISSIONS CRUD
     */
    public function createPermission() 
    {
        $roles = Role::latest()->get();
        $permissions = Permission::latest()->get();

        return view('backend.permissions.create', compact('roles','permissions'));
    }

    public function storePermission(Request $request)
    {
        $request->validate([
            'name'	=> 'required|string|max:255|unique:permissions'
        ]);

        $permission = Permission::create(['name' => $request->name]);
        $permission->assignRole($request->selectedroles);

        $roles = Role::latest()->get();
        $permissions = Permission::latest()->get();

        return view('backend.permissions.create', compact('roles','permissions'));
    }

    public function editPermission($id)
    {
        $permission = Permission::with('roles')->find($id);
        $roles = Role::latest()->get();

        return view('backend.permissions.edit', compact('roles','permission'));
    }

    public function updatePermission(Request $request, $id)
    {
        $request->validate([
            'name'	=> 'required|string|max:255|unique:permissions,name,'.$id
        ]);

    	$permission = Permission::findById($id); 
        $permission->update(['name' => $request->name]);
        $permission->syncRoles($request->selectedroles);

        return redirect()->route('roles-permissions');
    }

    /**
     * SIDEBAR PERMISSIONS MANAGEMENT
     */
    public function manageSidebarPermissions()
    {
        // Define all available sidebar items
        $sidebarItems = [
            'sidebar-home' => 'Home/Dashboard',
            'sidebar-notifications' => 'Notifications',
            'sidebar-onboard' => 'OnBoard Section',
            'sidebar-teachers' => 'Teachers',
            'sidebar-students' => 'Students',
            'sidebar-subjects' => 'Subjects',
            'sidebar-classes' => 'Classes',
            'sidebar-parents' => 'Parents',
            'sidebar-student-section' => 'Student Section',
            'sidebar-student-record' => 'Student Record',
            'sidebar-applicants' => 'Applicants',
            'sidebar-logbook' => 'Teacher Log Book',
            'sidebar-leave-management' => 'Leave Management',
            'sidebar-teacher-attendance' => 'Teacher - My Attendance',
            'sidebar-teacher-leave' => 'Teacher - Leave Applications',
            'sidebar-marking-scheme' => 'Marking Scheme',
            'sidebar-attendance' => 'Attendance Register',
            'sidebar-school-staff' => 'School Staff Section',
            'sidebar-staff-members' => 'Staff Members',
            'sidebar-timetable' => 'Timetable',
            'sidebar-webcam' => 'Webcam',
            'sidebar-website' => 'Website Section',
            'sidebar-banner' => 'Banner',
            'sidebar-newsletter' => 'Newsletter',
            'sidebar-events' => 'Events',
            'sidebar-finance' => 'Finance & Accounting',
            'sidebar-student-payments' => 'Student Payments',
            'sidebar-parents-arrears' => 'Parents with Arrears',
            'sidebar-school-income' => 'School Income',
            'sidebar-school-expenses' => 'School Expenses',
        ];

        // Get all existing permissions
        $existingPermissions = Permission::whereIn('name', array_keys($sidebarItems))->pluck('name')->toArray();
        
        // Create missing sidebar permissions
        foreach ($sidebarItems as $permissionName => $displayName) {
            if (!in_array($permissionName, $existingPermissions)) {
                Permission::create(['name' => $permissionName, 'guard_name' => 'web']);
            }
        }

        // Get all users with roles
        $users = \App\User::with('permissions', 'roles.permissions')->get();
        
        return view('backend.permissions.sidebar', compact('sidebarItems', 'users'));
    }

    public function updateUserSidebarPermissions(Request $request)
    {
        $userId = $request->user_id;
        $permissions = $request->permissions ?? [];
        
        $user = \App\User::findOrFail($userId);
        
        // Define all sidebar permissions
        $sidebarPermissions = [
            'sidebar-home', 'sidebar-notifications', 'sidebar-onboard', 'sidebar-teachers',
            'sidebar-students', 'sidebar-subjects', 'sidebar-classes', 'sidebar-parents',
            'sidebar-student-section', 'sidebar-student-record', 'sidebar-applicants',
            'sidebar-disciplinary', 'sidebar-results-management', 'sidebar-marking-scheme',
            'sidebar-attendance', 'sidebar-school-staff', 'sidebar-staff-members', 'sidebar-logbook', 'sidebar-leave-management',
            'sidebar-teacher-attendance', 'sidebar-teacher-leave',
            'sidebar-timetable', 'sidebar-webcam', 'sidebar-website', 'sidebar-banner',
            'sidebar-newsletter', 'sidebar-events', 'sidebar-finance', 'sidebar-student-payments',
            'sidebar-parents-arrears', 'sidebar-school-income', 'sidebar-school-expenses'
        ];
        
        // Remove all existing sidebar permissions from user
        $user->revokePermissionTo($sidebarPermissions);
        
        // Give selected permissions to user
        if (!empty($permissions)) {
            $user->givePermissionTo($permissions);
        }
        
        return redirect()->back()->with('success', 'Sidebar permissions updated successfully!');
    }
}
