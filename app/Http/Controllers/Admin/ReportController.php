<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display all user reports.
     */
    public function index(Request $request): View
    {
        $query = UserReport::with(['user', 'simulation', 'reviewer'])
            ->orderByDesc('created_at');

        $status = $request->get('status', 'pending');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $reports = $query->paginate(20)->withQueryString();

        return view('admin.reports.index', [
            'reports' => $reports,
            'activeStatus' => $status,
            'counts' => [
                'pending' => UserReport::where('status', 'pending')->count(),
                'reviewed' => UserReport::where('status', 'reviewed')->count(),
                'resolved' => UserReport::where('status', 'resolved')->count(),
                'dismissed' => UserReport::where('status', 'dismissed')->count(),
            ],
        ]);
    }

    /**
     * Show report detail.
     */
    public function show(UserReport $report): View
    {
        $report->load(['user', 'simulation', 'reviewer']);

        $previousReports = UserReport::where('simulation_id', $report->simulation_id)
            ->where('id', '!=', $report->id)
            ->with('user')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.reports.show', [
            'report' => $report,
            'previousReports' => $previousReports,
        ]);
    }

    /**
     * Review a report — update status and take action.
     */
    public function review(Request $request, UserReport $report): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:reviewed,resolved,dismissed',
            'action_taken' => 'nullable|string|max:500',
        ]);

        $report->update([
            'status' => $validated['status'],
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'action_taken' => $validated['action_taken'] ?? null,
        ]);

        return back()->with('success', 'Laporan berhasil di-review.');
    }

    /**
     * Bulk action on multiple reports.
     */
    public function bulkAction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'report_ids' => 'required|array',
            'action' => 'required|in:resolve,dismiss',
        ]);

        $action = match ($validated['action']) {
            'resolve' => 'resolved',
            'dismiss' => 'dismissed',
        };

        UserReport::whereIn('id', $validated['report_ids'])
            ->update([
                'status' => $action,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

        return back()->with('success', count($validated['report_ids']).' laporan berhasil diproses.');
    }
}
