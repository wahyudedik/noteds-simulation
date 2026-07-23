<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\Notification;
use App\Models\Simulation;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FollowController extends Controller
{
    /**
     * Toggle follow/unfollow a creator or simulation.
     *
     * Supports two modes:
     * - User follow: POST /follows/{userId}/toggle
     * - Simulation follow: POST /follows/{userId}/toggle with body { followable_type: 'simulation', followable_id: N }
     */
    public function toggle(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $followableType = $request->input('followable_type', 'user');
        $followableId = $request->input('followable_id', $id);

        if ($followableType === 'simulation') {
            return $this->toggleSimulationFollow($request, (int) $followableId);
        }

        return $this->toggleUserFollow($request, $id);
    }

    /**
     * Toggle follow/unfollow a creator (user).
     */
    private function toggleUserFollow(Request $request, int $id): JsonResponse|RedirectResponse
    {
        $creator = User::findOrFail($id);

        if ($creator->id === Auth::id()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Anda tidak bisa mengikuti diri sendiri.'], 422);
            }

            return redirect()->back()->with('error', 'Anda tidak bisa mengikuti diri sendiri.');
        }

        $existing = Follow::where('follower_id', Auth::id())
            ->where('followable_id', $creator->id)
            ->where('followable_type', User::class)
            ->first();

        if ($existing) {
            $existing->delete();
            $following = false;
            $message = 'Berhenti mengikuti '.$creator->name.'.';
        } else {
            Follow::create([
                'follower_id' => Auth::id(),
                'followable_id' => $creator->id,
                'followable_type' => User::class,
            ]);
            $following = true;
            $message = 'Mengikuti '.$creator->name.'.';

            // Gamification: award follow points
            /** @var User $user */
            $user = Auth::user();
            $gamification = app(GamificationService::class);
            $gamification->awardPoints($user, 'follow_creator', 'Follow: '.$creator->name);
            $gamification->checkBadges($user);

            // Notify the creator
            Notification::create([
                'user_id' => $creator->id,
                'type' => 'follow',
                'title' => 'Pengikut Baru',
                'body' => Auth::user()->name.' mengikuti Anda.',
                'data' => [
                    'follower_id' => Auth::id(),
                    'url' => route('creators.show', Auth::id()),
                ],
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'following' => $following,
                'followers_count' => $creator->followers()->count(),
                'message' => $message,
            ]);
        }

        return redirect()->back();
    }

    /**
     * Toggle follow/unfollow a simulation.
     */
    private function toggleSimulationFollow(Request $request, int $simulationId): JsonResponse|RedirectResponse
    {
        $simulation = Simulation::findOrFail($simulationId);

        $existing = Follow::where('follower_id', Auth::id())
            ->where('followable_id', $simulation->id)
            ->where('followable_type', Simulation::class)
            ->first();

        if ($existing) {
            $existing->delete();
            $following = false;
            $message = 'Berhenti mengikuti simulasi '.$simulation->title.'.';
        } else {
            Follow::create([
                'follower_id' => Auth::id(),
                'followable_id' => $simulation->id,
                'followable_type' => Simulation::class,
            ]);
            $following = true;
            $message = 'Mengikuti simulasi '.$simulation->title.'.';

            /** @var User $user */
            $user = Auth::user();
            $gamification = app(GamificationService::class);
            $gamification->awardPoints($user, 'follow_simulation', 'Follow simulasi: '.$simulation->title);
            $gamification->checkBadges($user);

            // Notify the simulation owner
            if ($simulation->user_id !== Auth::id()) {
                Notification::create([
                    'user_id' => $simulation->user_id,
                    'type' => 'follow',
                    'title' => 'Simulasi Diikuti',
                    'body' => Auth::user()->name.' mengikuti simulasi '.$simulation->title.'.',
                    'data' => [
                        'simulation_slug' => $simulation->slug,
                        'url' => route('simulations.show', $simulation->slug),
                    ],
                ]);
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'following' => $following,
                'message' => $message,
            ]);
        }

        return redirect()->back();
    }

    /**
     * Display a creator's public profile page.
     */
    public function profile(int $id): View
    {
        $creator = User::findOrFail($id);

        $simulations = $creator->simulations()
            ->published()
            ->latest()
            ->paginate(12);

        $isFollowing = Auth::check() && Auth::user()->isFollowing($creator);

        return view('creators.show', compact('creator', 'simulations', 'isFollowing'));
    }
}
