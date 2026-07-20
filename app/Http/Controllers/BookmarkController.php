<?php

namespace App\Http\Controllers;

use App\Models\Bookmark;
use App\Models\Simulation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    /**
     * Toggle bookmark on a simulation.
     */
    public function toggle(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'simulation_id' => 'required|exists:simulations,id',
        ]);

        $simulation = Simulation::findOrFail($validated['simulation_id']);

        $existing = Bookmark::where('user_id', Auth::id())
            ->where('simulation_id', $simulation->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $bookmarked = false;
        } else {
            Bookmark::create([
                'user_id' => Auth::id(),
                'simulation_id' => $simulation->id,
            ]);
            $bookmarked = true;
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'bookmarked' => $bookmarked,
                'message' => $bookmarked ? 'Bookmark ditambahkan.' : 'Bookmark dihapus.',
            ]);
        }

        return redirect()->back();
    }
}
