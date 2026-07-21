<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\SavedCollection;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SavedCollectionController extends Controller
{
    /**
     * Toggle save/unsave a collection from another user.
     */
    public function toggle(Request $request, int $collectionId): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Silakan login terlebih dahulu.'], 401);
            }

            return redirect()->route('login');
        }

        $collection = Collection::findOrFail($collectionId);

        // Prevent saving own collection
        if ($collection->user_id === $user->id) {
            $message = 'Anda tidak dapat menyimpan collection sendiri.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'saved' => false, 'message' => $message]);
            }

            return redirect()->back()->with('error', $message);
        }

        $existing = SavedCollection::where('user_id', $user->id)
            ->where('collection_id', $collection->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $saved = false;
            $message = 'Collection dihapus dari tersimpan.';
        } else {
            SavedCollection::create([
                'user_id' => $user->id,
                'collection_id' => $collection->id,
            ]);
            $saved = true;
            $message = 'Collection berhasil disimpan.';
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'saved' => $saved,
                'save_count' => $collection->savedByUsers()->count(),
                'message' => $message,
            ]);
        }

        return redirect()->back();
    }

    /**
     * List all saved collections for the current user.
     */
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $savedCollections = SavedCollection::where('user_id', $user->id)
            ->with('collection.user', 'collection.simulations')
            ->orderByDesc('id')
            ->paginate(12);

        return view('collections.saved-index', compact('savedCollections'));
    }
}
