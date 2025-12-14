<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StudentApplication;

class AdminApplicantController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:Admin']);
    }

    public function index()
    {
        $applications = StudentApplication::orderBy('created_at', 'desc')->get();
        return view('backend.admin.applicants.index', compact('applications'));
    }

    public function show($id)
    {
        $application = StudentApplication::findOrFail($id);
        return view('backend.admin.applicants.show', compact('application'));
    }

    public function updateStatus(Request $request, $id)
    {
        $application = StudentApplication::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'admin_notes' => 'nullable|string'
        ]);

        $application->update($validated);

        return redirect()->back()->with('success', 'Application status updated successfully.');
    }

    public function destroy($id)
    {
        $application = StudentApplication::findOrFail($id);
        $application->delete();

        return redirect()->route('admin.applicants.index')->with('success', 'Application deleted successfully.');
    }
}
