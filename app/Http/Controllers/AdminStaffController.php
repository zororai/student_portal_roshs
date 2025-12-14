<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Teacher;

class AdminStaffController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin']);
    }

    public function index()
    {
        $teachers = Teacher::with('user')->orderBy('created_at', 'desc')->get();
        return view('backend.admin.staff.index', compact('teachers'));
    }

    public function show($id)
    {
        $teacher = Teacher::with('user')->findOrFail($id);
        return view('backend.admin.staff.show', compact('teacher'));
    }
}
