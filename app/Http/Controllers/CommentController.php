<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Notification;
use App\Models\Simulation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Store a new comment on a simulation.
     */
    public function store(Request $request, string $slug): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'body' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $simulation = Simulation::published()->where('slug', $slug)->firstOrFail();

        /** @var User $user */
        $user = Auth::user();

        $comment = Comment::create([
            'user_id' => $user->id,
            'simulation_id' => $simulation->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'body' => $validated['body'],
        ]);

        // Notify the simulation owner if someone else commented
        if ($simulation->user_id !== $user->id) {
            Notification::create([
                'user_id' => $simulation->user_id,
                'type' => 'comment',
                'title' => 'Komentar Baru',
                'body' => $user->name.' mengomentari simulasi '.$simulation->title,
                'data' => [
                    'simulation_slug' => $simulation->slug,
                    'comment_id' => $comment->id,
                    'url' => route('simulations.show', $simulation->slug).'#comment-'.$comment->id,
                ],
            ]);
        }

        // Notify parent comment author if this is a reply
        if ($comment->parent_id && $comment->parent && $comment->parent->user_id !== $user->id) {
            Notification::create([
                'user_id' => $comment->parent->user_id,
                'type' => 'comment',
                'title' => 'Balasan Komentar',
                'body' => $user->name.' membalas komentar Anda di '.$simulation->title,
                'data' => [
                    'simulation_slug' => $simulation->slug,
                    'comment_id' => $comment->id,
                    'url' => route('simulations.show', $simulation->slug).'#comment-'.$comment->id,
                ],
            ]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'comment' => $comment->load('user'),
            ]);
        }

        return redirect()->route('simulations.show', $slug);
    }

    /**
     * Delete a comment (only by its author or admin).
     */
    public function destroy(Request $request, Comment $comment): JsonResponse|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if ($comment->user_id !== $user->id && ! $user->isAdmin()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki akses.'], 403);
            }

            return redirect()->back()->with('error', 'Anda tidak memiliki akses.');
        }

        $slug = $comment->simulation->slug;
        $comment->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Komentar berhasil dihapus.']);
        }

        return redirect()->route('simulations.show', $slug);
    }
}
