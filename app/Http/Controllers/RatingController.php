<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Simulation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    /**
     * Store or update a rating for a simulation.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'simulation_id' => 'required|exists:simulations,id',
            'rating' => 'required|integer|min:1|max:5',
        ]);

        $simulation = Simulation::findOrFail($validated['simulation_id']);

        $existing = Rating::where('user_id', Auth::id())
            ->where('simulation_id', $simulation->id)
            ->first();

        if ($existing) {
            $existing->update(['rating' => $validated['rating']]);
        } else {
            Rating::create([
                'user_id' => Auth::id(),
                'simulation_id' => $simulation->id,
                'rating' => $validated['rating'],
            ]);
        }

        // Recalculate average rating
        $avgRating = $simulation->ratings()->avg('rating');
        $ratingCount = $simulation->ratings()->count();
        $simulation->update([
            'average_rating' => round($avgRating, 1),
            'rating_count' => $ratingCount,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'rating' => $validated['rating'],
                'average_rating' => round($avgRating, 1),
                'rating_count' => $ratingCount,
                'message' => 'Rating berhasil dikirim.',
            ]);
        }

        return redirect()->back();
    }
}
