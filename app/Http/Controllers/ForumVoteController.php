<?php

namespace App\Http\Controllers;

use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\ForumVote;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumVoteController extends Controller
{
    /**
     * Toggle upvote/downvote on a thread or reply.
     */
    public function toggle(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'votable_type' => 'required|in:thread,reply',
            'votable_id' => 'required|integer',
            'value' => 'required|in:1,-1',
        ]);

        /** @var User $user */
        $user = Auth::user();

        $votableClass = $validated['votable_type'] === 'thread'
            ? ForumThread::class
            : ForumReply::class;

        $votable = $votableClass::findOrFail($validated['votable_id']);

        // Cannot vote on own content
        if ($votable->user_id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak bisa memberikan vote pada konten sendiri.',
            ], 422);
        }

        $existingVote = ForumVote::where('user_id', $user->id)
            ->where('votable_type', $votableClass)
            ->where('votable_id', $validated['votable_id'])
            ->first();

        if ($existingVote) {
            if ($existingVote->value == $validated['value']) {
                // Same vote direction — remove vote (toggle off)
                $existingVote->delete();
                $votable->decrement('votes_count', $validated['value']);

                return response()->json([
                    'success' => true,
                    'action' => 'removed',
                    'votes_count' => $votable->fresh()->votes_count,
                ]);
            }

            // Different vote direction — switch vote
            $oldValue = $existingVote->value;
            $existingVote->update(['value' => $validated['value']]);

            // Net change: remove old vote, add new vote
            $netChange = $validated['value'] - $oldValue;
            $votable->increment('votes_count', $netChange);

            return response()->json([
                'success' => true,
                'action' => 'switched',
                'votes_count' => $votable->fresh()->votes_count,
            ]);
        }

        // New vote
        ForumVote::create([
            'user_id' => $user->id,
            'votable_type' => $votableClass,
            'votable_id' => $validated['votable_id'],
            'value' => $validated['value'],
        ]);

        $votable->increment('votes_count', $validated['value']);

        return response()->json([
            'success' => true,
            'action' => 'added',
            'votes_count' => $votable->fresh()->votes_count,
        ]);
    }
}
