<?php

namespace App\Http\Controllers;

use App\LeaveApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TeacherLeaveController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        
        if (!$teacher) {
            return redirect()->back()->with('error', 'Teacher profile not found.');
        }

        $leaveApplications = LeaveApplication::where('teacher_id', $teacher->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Calculate leave statistics for current year
        $currentYear = now()->year;
        $approvedLeaves = LeaveApplication::where('teacher_id', $teacher->id)
            ->where('status', 'approved')
            ->whereYear('start_date', $currentYear)
            ->get();

        $totalDaysUsed = $approvedLeaves->sum('total_days');
        $pendingRequests = LeaveApplication::where('teacher_id', $teacher->id)
            ->where('status', 'pending')
            ->count();

        $leaveTypes = LeaveApplication::getLeaveTypes();

        return view('backend.teacher.leave.index', compact(
            'leaveApplications',
            'totalDaysUsed',
            'pendingRequests',
            'leaveTypes'
        ));
    }

    public function create()
    {
        $leaveTypes = LeaveApplication::getLeaveTypes();
        return view('backend.teacher.leave.create', compact('leaveTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|min:10',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $teacher = Auth::user()->teacher;

        if (!$teacher) {
            return redirect()->back()->with('error', 'Teacher profile not found.');
        }

        // Calculate total days
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $totalDays = $startDate->diffInDays($endDate) + 1;

        // Check for overlapping leave requests
        $overlapping = LeaveApplication::where('teacher_id', $teacher->id)
            ->where('status', '!=', 'rejected')
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();

        if ($overlapping) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'You already have a leave request for these dates.');
        }

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('leave-attachments', 'public');
        }

        LeaveApplication::create([
            'teacher_id' => $teacher->id,
            'leave_type' => $request->leave_type,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_days' => $totalDays,
            'reason' => $request->reason,
            'attachment' => $attachmentPath,
            'status' => 'pending',
        ]);

        return redirect()->route('teacher.leave.index')
            ->with('success', 'Leave application submitted successfully. Awaiting approval.');
    }

    public function show($id)
    {
        $teacher = Auth::user()->teacher;
        $leave = LeaveApplication::where('teacher_id', $teacher->id)
            ->findOrFail($id);

        return view('backend.teacher.leave.show', compact('leave'));
    }

    public function destroy($id)
    {
        $teacher = Auth::user()->teacher;
        $leave = LeaveApplication::where('teacher_id', $teacher->id)
            ->where('status', 'pending')
            ->findOrFail($id);

        if ($leave->attachment) {
            Storage::disk('public')->delete($leave->attachment);
        }

        $leave->delete();

        return redirect()->route('teacher.leave.index')
            ->with('success', 'Leave application cancelled successfully.');
    }
}
