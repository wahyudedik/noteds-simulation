<?php

namespace App\Http\Controllers;

use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\Notification;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumReplyController extends Controller
{
    /**
     * Store a new reply to a thread.
     */
    public function store(Request $request, string $slug): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'body' => 'required|string|max:5000',
            'parent_id' => 'nullable|exists:forum_replies,id',
        ]);

        $thread = ForumThread::where('slug', $slug)->firstOrFail();

        if ($thread->is_locked) {
            $message = 'Thread ini sudah dikunci dan tidak bisa dibalas.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }

            return redirect()->back()->with('error', $message);
        }

        /** @var User $user */
        $user = Auth::user();

        $reply = ForumReply::create([
            'user_id' => $user->id,
            'forum_thread_id' => $thread->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'body' => $validated['body'],
        ]);

        // Update thread reply count and last reply info
        $thread->update([
            'replies_count' => $thread->replies()->count(),
            'last_reply_at' => now(),
            'last_reply_user_id' => $user->id,
        ]);

        // Notify the thread author if someone else replied
        if (! $thread->isOwnedBy($user)) {
            Notification::create([
                'user_id' => $thread->user_id,
                'type' => 'forum_reply',
                'title' => 'Balasan di Forum',
                'body' => $user->name.' membalas thread "'.$thread->title.'"',
                'data' => [
                    'thread_slug' => $thread->slug,
                    'reply_id' => $reply->id,
                    'url' => route('forum.show', $thread->slug).'#reply-'.$reply->id,
                ],
            ]);
        }

        // Notify parent reply author if this is a nested reply
        if ($reply->parent_id && $reply->parent && $reply->parent->user_id !== $user->id) {
            Notification::create([
                'user_id' => $reply->parent->user_id,
                'type' => 'forum_reply',
                'title' => 'Balasan di Forum',
                'body' => $user->name.' membalas komentar Anda di "'.$thread->title.'"',
                'data' => [
                    'thread_slug' => $thread->slug,
                    'reply_id' => $reply->id,
                    'url' => route('forum.show', $thread->slug).'#reply-'.$reply->id,
                ],
            ]);
        }

        // Notify mentioned users (@username)
        $mentionedNames = $this->extractMentions($reply->body);
        if (! empty($mentionedNames)) {
            $mentionedUsers = User::whereIn('name', $mentionedNames)
                ->where('id', '!=', $user->id)
                ->where('id', '!=', $thread->user_id)
                ->get();

            foreach ($mentionedUsers as $mentionedUser) {
                Notification::create([
                    'user_id' => $mentionedUser->id,
                    'type' => 'forum_mention',
                    'title' => 'Anda Disebut di Forum',
                    'body' => $user->name.' menyebut Anda di "'.$thread->title.'"',
                    'data' => [
                        'thread_slug' => $thread->slug,
                        'reply_id' => $reply->id,
                        'url' => route('forum.show', $thread->slug).'#reply-'.$reply->id,
                    ],
                ]);
            }
        }

        // Gamification: award points for replying
        $gamification = app(GamificationService::class);
        $gamification->awardPoints($user, 'forum_reply', 'Reply di: '.$thread->title);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'reply' => $reply->load('user'),
            ]);
        }

        return redirect(route('forum.show', $thread->slug).'#reply-'.$reply->id);
    }

    /**
     * Remove the specified reply.
     */
    public function destroy(Request $request, ForumReply $reply): JsonResponse|RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $reply->isOwnedBy($user) && ! $user->isAdmin()) {
            abort(403);
        }

        $thread = $reply->thread;

        // Update thread reply count before deleting
        $reply->delete();
        $thread->update(['replies_count' => $thread->replies()->count()]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('forum.show', $thread->slug)->with('success', 'Balasan berhasil dihapus.');
    }

    /**
     * Accept a reply as the best answer (thread author only).
     */
    public function accept(ForumReply $reply): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $thread = $reply->thread;

        // Only thread author can accept
        if (! $thread->isOwnedBy($user)) {
            abort(403, 'Hanya penulis thread yang bisa menerima jawaban.');
        }

        // Uncheck any previously accepted reply
        $thread->replies()->where('is_accepted', true)->update(['is_accepted' => false]);

        // Accept this reply
        $reply->update(['is_accepted' => true]);
        $thread->update(['is_solved' => true]);

        // Notify the reply author
        if (! $reply->isOwnedBy($user)) {
            Notification::create([
                'user_id' => $reply->user_id,
                'type' => 'forum_accepted',
                'title' => 'Jawaban Diterima',
                'body' => 'Jawaban Anda di "'.$thread->title.'" telah diterima sebagai solusi terbaik!',
                'data' => [
                    'thread_slug' => $thread->slug,
                    'reply_id' => $reply->id,
                    'url' => route('forum.show', $thread->slug).'#reply-'.$reply->id,
                ],
            ]);

            // Gamification: award best answer points
            $gamification = app(GamificationService::class);
            $gamification->awardPoints($reply->user, 'forum_best_answer', 'Jawaban terbaik di: '.$thread->title);
        }

        return redirect()->route('forum.show', $thread->slug)->with('success', 'Jawaban diterima sebagai solusi terbaik!');
    }

    /**
     * Extract @mentions from text.
     *
     * @return string[]
     */
    private function extractMentions(string $text): array
    {
        preg_match_all('/@(\w+)/', $text, $matches);

        return array_unique($matches[1] ?? []);
    }
}
