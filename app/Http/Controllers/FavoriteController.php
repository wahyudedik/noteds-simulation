<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Simulation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    /**
     * Toggle favorite/unfavorite a simulation.
     */
    public function toggle(Request $request, int $simulationId): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Silakan login terlebih dahulu.'], 401);
            }

            return redirect()->route('login');
        }

        $simulation = Simulation::findOrFail($simulationId);

        $existing = Favorite::where('user_id', $user->id)
            ->where('simulation_id', $simulation->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $favorited = false;
            $message = 'Dihapus dari favorit.';
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'simulation_id' => $simulation->id,
            ]);
            $favorited = true;
            $message = 'Ditambahkan ke favorit.';
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'favorited' => $favorited,
                'favorite_count' => $simulation->favorites()->count(),
                'message' => $message,
            ]);
        }

        return redirect()->back();
    }
}
