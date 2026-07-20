<?php

namespace App\Http\Controllers;

use App\Models\Reaction;
use App\Models\Simulation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReactionController extends Controller
{
    /**
     * Toggle a reaction on a simulation.
     */
    public function toggle(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'simulation_id' => 'required|exists:simulations,id',
            'type' => 'required|string|in:mudah_dipahami,membuka_wawasan,sangat_membantu,interaktif,favorit',
        ]);

        $simulation = Simulation::findOrFail($validated['simulation_id']);

        $existing = Reaction::where('user_id', Auth::id())
            ->where('simulation_id', $simulation->id)
            ->where('type', $validated['type'])
            ->first();

        if ($existing) {
            $existing->delete();
            $active = false;
        } else {
            Reaction::create([
                'user_id' => Auth::id(),
                'simulation_id' => $simulation->id,
                'type' => $validated['type'],
            ]);
            $active = true;
        }

        // Get updated count for this specific reaction type
        $count = Reaction::where('simulation_id', $simulation->id)
            ->where('type', $validated['type'])
            ->count();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'active' => $active,
                'type' => $validated['type'],
                'count' => $count,
            ]);
        }

        return redirect()->back();
    }
}
