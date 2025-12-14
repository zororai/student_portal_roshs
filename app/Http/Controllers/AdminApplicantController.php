<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StudentApplication;
use App\Mail\ApplicationStatusMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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

        // Send email notifications
        $emailsSent = [];
        
        // Send to guardian email
        if ($application->guardian_email) {
            try {
                Mail::to($application->guardian_email)->send(new ApplicationStatusMail($application, 'guardian'));
                $emailsSent[] = 'guardian';
            } catch (\Exception $e) {
                Log::error('Failed to send email to guardian: ' . $e->getMessage());
            }
        }
        
        // Send to student email
        if ($application->student_email) {
            try {
                Mail::to($application->student_email)->send(new ApplicationStatusMail($application, 'student'));
                $emailsSent[] = 'student';
            } catch (\Exception $e) {
                Log::error('Failed to send email to student: ' . $e->getMessage());
            }
        }

        $message = 'Application status updated successfully.';
        if (!empty($emailsSent)) {
            $message .= ' Email notifications sent to: ' . implode(', ', $emailsSent) . '.';
        }

        return redirect()->back()->with('success', $message);
    }

    public function destroy($id)
    {
        $application = StudentApplication::findOrFail($id);
        $application->delete();

        return redirect()->route('admin.applicants.index')->with('success', 'Application deleted successfully.');
    }
}
