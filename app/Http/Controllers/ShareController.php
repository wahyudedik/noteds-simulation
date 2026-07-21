<?php

namespace App\Http\Controllers;

use App\Models\Share;
use App\Models\Simulation;
use App\Services\GamificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShareController extends Controller
{
    public function __construct(
        private GamificationService $gamification,
    ) {}

    /**
     * Track a share action.
     */
    public function track(Request $request, int $simulationId): JsonResponse
    {
        $simulation = Simulation::findOrFail($simulationId);

        $validated = $request->validate([
            'platform' => 'required|in:copy_link,whatsapp,telegram,twitter,facebook',
        ]);

        Share::create([
            'user_id' => $request->user()?->id,
            'simulation_id' => $simulation->id,
            'platform' => $validated['platform'],
        ]);

        $simulation->increment('share_count');

        // Gamification: award share points
        $user = $request->user();
        if ($user) {
            $this->gamification->awardPoints($user, 'share', 'Share: '.$simulation->title);
            $this->gamification->checkBadges($user);
        }

        return response()->json([
            'success' => true,
            'share_count' => $simulation->fresh()->share_count,
            'message' => 'Share tercatat.',
        ]);
    }
}
