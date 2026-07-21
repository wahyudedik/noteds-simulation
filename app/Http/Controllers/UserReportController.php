<?php

namespace App\Http\Controllers;

use App\Models\Simulation;
use App\Models\UserReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserReportController extends Controller
{
    /**
     * Store a new report for a simulation.
     */
    public function store(Request $request, string $slug): JsonResponse|RedirectResponse
    {
        $simulation = Simulation::published()->where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'reason' => 'required|in:malware,spam_ads,inappropriate,other',
            'description' => 'nullable|string|max:1000',
        ]);

        // Check if user already reported this simulation
        $existingReport = UserReport::where('user_id', Auth::id())
            ->where('simulation_id', $simulation->id)
            ->whereIn('status', ['pending', 'reviewed'])
            ->exists();

        if ($existingReport) {
            $message = 'Anda sudah melaporkan simulasi ini.';
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return back()->withErrors(['reason' => $message]);
        }

        $report = UserReport::create([
            'user_id' => Auth::id(),
            'simulation_id' => $simulation->id,
            'reason' => $validated['reason'],
            'description' => $validated['description'] ?? null,
            'status' => 'pending',
        ]);

        // Auto-pend simulation if 3+ active reports
        $activeReportCount = UserReport::where('simulation_id', $simulation->id)
            ->whereIn('status', ['pending', 'reviewed'])
            ->count();

        if ($activeReportCount >= 3 && $simulation->status === 'published') {
            $simulation->update(['status' => 'pending']);
        }

        $message = 'Laporan berhasil dikirim. Terima kasih!';

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('success', $message);
    }
}
