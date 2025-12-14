<?php

namespace App\Http\Controllers;

use App\AuditTrail;
use Illuminate\Http\Request;

class AuditTrailController extends Controller
{
    /**
     * Display the audit trail index page.
     */
    public function index(Request $request)
    {
        $query = AuditTrail::query()->orderBy('created_at', 'desc');

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by model type
        if ($request->filled('model_type')) {
            $query->where('model_type', 'like', '%' . $request->model_type . '%');
        }

        // Search in description
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                  ->orWhere('user_name', 'like', '%' . $request->search . '%');
            });
        }

        $audits = $query->paginate(25)->appends($request->query());

        // Get unique actions for filter dropdown
        $actions = AuditTrail::select('action')->distinct()->pluck('action');

        // Get unique users for filter dropdown
        $users = \App\User::select('id', 'name')->orderBy('name')->get();

        // Get statistics
        $stats = [
            'total' => AuditTrail::count(),
            'today' => AuditTrail::whereDate('created_at', today())->count(),
            'logins_today' => AuditTrail::where('action', 'login')->whereDate('created_at', today())->count(),
            'changes_today' => AuditTrail::whereIn('action', ['create', 'update', 'delete'])->whereDate('created_at', today())->count(),
        ];

        return view('backend.admin.audit-trail.index', compact('audits', 'actions', 'users', 'stats'));
    }

    /**
     * Show details of a specific audit entry.
     */
    public function show($id)
    {
        $audit = AuditTrail::findOrFail($id);
        return view('backend.admin.audit-trail.show', compact('audit'));
    }

    /**
     * Export audit trail to CSV.
     */
    public function export(Request $request)
    {
        $query = AuditTrail::query()->orderBy('created_at', 'desc');

        // Apply the same filters as index
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $audits = $query->get();

        $filename = 'audit_trail_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($audits) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, ['Date/Time', 'User', 'Role', 'Action', 'Description', 'Model', 'IP Address']);
            
            foreach ($audits as $audit) {
                fputcsv($file, [
                    $audit->created_at->format('Y-m-d H:i:s'),
                    $audit->user_name,
                    $audit->user_role,
                    $audit->action,
                    $audit->description,
                    $audit->model_type ? class_basename($audit->model_type) : '-',
                    $audit->ip_address,
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Clear old audit records (older than specified days).
     */
    public function clear(Request $request)
    {
        $days = $request->input('days', 90);
        
        $deleted = AuditTrail::where('created_at', '<', now()->subDays($days))->delete();

        AuditTrail::log('delete', "Cleared $deleted audit records older than $days days");

        return redirect()->route('admin.audit-trail.index')
            ->with('success', "Successfully deleted $deleted audit records older than $days days.");
    }
}
