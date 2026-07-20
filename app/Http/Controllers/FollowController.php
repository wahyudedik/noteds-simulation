<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FollowController extends Controller
{
    /**
     * Toggle follow/unfollow a creator.
     */
    public function toggle(Request $request, int $id): JsonResponse|RedirectResponse
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
            ->first();

        if ($existing) {
            $existing->delete();
            $following = false;
            $message = 'Berhenti mengikuti '.$creator->name.'.';
        } else {
            Follow::create([
                'follower_id' => Auth::id(),
                'followable_id' => $creator->id,
            ]);
            $following = true;
            $message = 'Mengikuti '.$creator->name.'.';

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
