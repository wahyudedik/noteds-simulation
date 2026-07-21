<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CodeScanLog;
use App\Models\CreatorReputation;
use App\Models\Simulation;
use App\Services\SecurityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScanController extends Controller
{
    public function __construct(
        private SecurityService $securityService,
    ) {}

    /**
     * Display scan logs.
     */
    public function index(Request $request): View
    {
        $query = CodeScanLog::with(['simulation', 'reviewer'])
            ->orderByDesc('created_at');

        $scanType = $request->get('scan_type');
        if ($scanType) {
            $query->where('scan_type', $scanType);
        }

        $result = $request->get('result');
        if ($result) {
            $query->where('result', $result);
        }

        $logs = $query->paginate(20)->withQueryString();

        return view('admin.scans.index', [
            'logs' => $logs,
            'counts' => [
                'total' => CodeScanLog::count(),
                'pass' => CodeScanLog::where('result', 'pass')->count(),
                'flag' => CodeScanLog::where('result', 'flag')->count(),
                'reject' => CodeScanLog::where('result', 'reject')->count(),
            ],
        ]);
    }

    /**
     * Show scan log detail.
     */
    public function show(CodeScanLog $log): View
    {
        $log->load(['simulation', 'reviewer']);

        $allScans = CodeScanLog::where('simulation_id', $log->simulation_id)
            ->orderByDesc('created_at')
            ->get();

        return view('admin.scans.show', [
            'log' => $log,
            'allScans' => $allScans,
        ]);
    }

    /**
     * Trigger auto-scan on a simulation.
     */
    public function autoScan(Simulation $simulation): RedirectResponse
    {
        $zipPath = storage_path('app/simulations/'.$simulation->slug.'.zip');

        if (! file_exists($zipPath)) {
            return back()->withErrors(['scan' => 'File ZIP simulasi tidak ditemukan.']);
        }

        $this->securityService->autoScan($simulation, $zipPath);

        return back()->with('success', 'Auto-scan selesai untuk simulasi: '.$simulation->title);
    }

    /**
     * Perform manual review on a simulation.
     */
    public function manualReview(Request $request, Simulation $simulation): RedirectResponse
    {
        $validated = $request->validate([
            'result' => 'required|in:pass,flag,reject',
            'notes' => 'nullable|string|max:1000',
        ]);

        $this->securityService->manualReview(
            $simulation,
            auth()->id(),
            $validated['result'],
            $validated['notes'] ?? null,
        );

        // Update simulation status based on review result
        if ($validated['result'] === 'reject') {
            $simulation->update(['status' => 'rejected']);
        } elseif ($validated['result'] === 'pass') {
            $simulation->update(['status' => 'published']);
            $this->updateCreatorReputation($simulation->user_id, 'approved');
        } elseif ($validated['result'] === 'flag') {
            $this->updateCreatorReputation($simulation->user_id, 'flagged');
        }

        return back()->with('success', 'Review manual berhasil disimpan.');
    }

    /**
     * Update creator reputation after scan/review.
     */
    private function updateCreatorReputation(int $userId, string $action): void
    {
        $reputation = CreatorReputation::firstOrCreate(['user_id' => $userId]);

        match ($action) {
            'approved' => $reputation->update([
                'approved_count' => $reputation->approved_count + 1,
                'score' => min(100, $reputation->score + 2),
            ]),
            'rejected' => $reputation->update([
                'rejected_count' => $reputation->rejected_count + 1,
                'score' => max(0, $reputation->score - 5),
            ]),
            'flagged' => $reputation->update([
                'flagged_count' => $reputation->flagged_count + 1,
                'score' => max(0, $reputation->score - 2),
            ]),
            'upload' => $reputation->update([
                'total_uploads' => $reputation->total_uploads + 1,
            ]),
            default => null,
        };

        // Update revenue tier based on score
        $tier = match (true) {
            $reputation->score >= 90 => 'platinum',
            $reputation->score >= 70 => 'expert',
            $reputation->score >= 50 => 'verified',
            default => 'basic',
        };
        $reputation->update(['revenue_tier' => $tier]);
    }
}
