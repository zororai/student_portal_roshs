<?php

namespace App\Http\Controllers;

use App\LeaveApplication;
use App\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AdminLeaveController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaveApplication::with(['teacher.user', 'approver']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by teacher
        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->where('start_date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('end_date', '<=', $request->to_date);
        }

        $leaveApplications = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistics
        $pendingCount = LeaveApplication::where('status', 'pending')->count();
        $approvedCount = LeaveApplication::where('status', 'approved')
            ->whereYear('start_date', now()->year)->count();
        $rejectedCount = LeaveApplication::where('status', 'rejected')
            ->whereYear('start_date', now()->year)->count();

        // Teachers on leave today
        $onLeaveToday = LeaveApplication::where('status', 'approved')
            ->where('start_date', '<=', today())
            ->where('end_date', '>=', today())
            ->with('teacher.user')
            ->get();

        $teachers = Teacher::with('user')->get();
        $leaveTypes = LeaveApplication::getLeaveTypes();

        return view('backend.admin.leave.index', compact(
            'leaveApplications',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'onLeaveToday',
            'teachers',
            'leaveTypes'
        ));
    }

    public function show($id)
    {
        $leave = LeaveApplication::with(['teacher.user', 'approver'])->findOrFail($id);
        
        // Get teacher's leave history
        $leaveHistory = LeaveApplication::where('teacher_id', $leave->teacher_id)
            ->where('id', '!=', $id)
            ->where('status', 'approved')
            ->orderBy('start_date', 'desc')
            ->limit(5)
            ->get();

        return view('backend.admin.leave.show', compact('leave', 'leaveHistory'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'admin_remarks' => 'nullable|string|max:500',
        ]);

        $leave = LeaveApplication::findOrFail($id);

        if (!$leave->isPending()) {
            return redirect()->back()->with('error', 'This leave request has already been processed.');
        }

        $leave->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'admin_remarks' => $request->admin_remarks,
        ]);

        return redirect()->route('admin.leave.index')
            ->with('success', 'Leave application approved successfully.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_remarks' => 'required|string|max:500',
        ]);

        $leave = LeaveApplication::findOrFail($id);

        if (!$leave->isPending()) {
            return redirect()->back()->with('error', 'This leave request has already been processed.');
        }

        $leave->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'admin_remarks' => $request->admin_remarks,
        ]);

        return redirect()->route('admin.leave.index')
            ->with('success', 'Leave application rejected.');
    }

    public function calendar()
    {
        $leaves = LeaveApplication::where('status', 'approved')
            ->with('teacher.user')
            ->get()
            ->map(function ($leave) {
                return [
                    'id' => $leave->id,
                    'title' => $leave->teacher->user->name . ' - ' . ucfirst($leave->leave_type),
                    'start' => $leave->start_date->format('Y-m-d'),
                    'end' => $leave->end_date->addDay()->format('Y-m-d'),
                    'color' => $this->getLeaveColor($leave->leave_type),
                ];
            });

        return response()->json($leaves);
    }

    private function getLeaveColor($type)
    {
        $colors = [
            'sick' => '#ef4444',
            'personal' => '#3b82f6',
            'family' => '#8b5cf6',
            'vacation' => '#10b981',
            'maternity' => '#ec4899',
            'paternity' => '#06b6d4',
            'bereavement' => '#6b7280',
        ];
        
        return isset($colors[$type]) ? $colors[$type] : '#f59e0b';
    }
}
