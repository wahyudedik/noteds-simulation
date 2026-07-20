<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CollectionController extends Controller
{
    /**
     * Display user's collections.
     */
    public function index(Request $request): View
    {
        $collections = $request->user()
            ->collections()
            ->withCount('simulations')
            ->latest()
            ->paginate(12);

        return view('collections.index', compact('collections'));
    }

    /**
     * Show a single collection (public).
     */
    public function show(string $slug): View
    {
        $collection = Collection::where('slug', $slug)
            ->where('is_public', true)
            ->with(['user', 'simulations.user'])
            ->firstOrFail();

        $collection->increment('view_count');

        return view('collections.show', compact('collection'));
    }

    /**
     * Show create form.
     */
    public function create(): View
    {
        return view('collections.create');
    }

    /**
     * Store a new collection.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_public' => ['boolean'],
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['slug'] = Str::slug($validated['title']);

        // Ensure unique slug
        $originalSlug = $validated['slug'];
        $counter = 1;
        while (Collection::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $originalSlug.'-'.$counter;
            $counter++;
        }

        $validated['is_public'] = $request->boolean('is_public', true);

        Collection::create($validated);

        return redirect()->route('collections.index')
            ->with('status', 'collection-created');
    }

    /**
     * Show edit form.
     */
    public function edit(Collection $collection): View
    {
        if ($collection->user_id !== auth()->id()) {
            abort(403);
        }

        $collection->load('simulations');

        return view('collections.edit', compact('collection'));
    }

    /**
     * Update a collection.
     */
    public function update(Request $request, Collection $collection): RedirectResponse
    {
        if ($collection->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_public' => ['boolean'],
        ]);

        $validated['is_public'] = $request->boolean('is_public', true);

        // Only update slug if title changed
        if ($validated['title'] !== $collection->title) {
            $validated['slug'] = Str::slug($validated['title']);
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Collection::where('slug', $validated['slug'])->where('id', '!=', $collection->id)->exists()) {
                $validated['slug'] = $originalSlug.'-'.$counter;
                $counter++;
            }
        }

        $collection->update($validated);

        return redirect()->route('collections.index')
            ->with('status', 'collection-updated');
    }

    /**
     * Delete a collection.
     */
    public function destroy(Collection $collection): RedirectResponse
    {
        if ($collection->user_id !== auth()->id()) {
            abort(403);
        }

        $collection->delete();

        return redirect()->route('collections.index')
            ->with('status', 'collection-deleted');
    }

    /**
     * Add a simulation to a collection (AJAX).
     */
    public function addSimulation(Request $request): JsonResponse
    {
        $request->validate([
            'collection_id' => ['required', 'exists:collections,id'],
            'simulation_id' => ['required', 'exists:simulations,id'],
        ]);

        $collection = Collection::findOrFail($request->collection_id);

        if ($collection->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        // Check if already in collection
        if ($collection->simulations()->where('simulation_id', $request->simulation_id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Simulasi sudah ada di collection ini']);
        }

        $position = $collection->simulations()->count();

        $collection->simulations()->attach($request->simulation_id, ['position' => $position]);

        return response()->json([
            'success' => true,
            'message' => 'Simulasi berhasil ditambahkan ke collection',
        ]);
    }

    /**
     * Remove a simulation from a collection (AJAX).
     */
    public function removeSimulation(Request $request): JsonResponse
    {
        $request->validate([
            'collection_id' => ['required', 'exists:collections,id'],
            'simulation_id' => ['required', 'exists:simulations,id'],
        ]);

        $collection = Collection::findOrFail($request->collection_id);

        if ($collection->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $collection->simulations()->detach($request->simulation_id);

        return response()->json([
            'success' => true,
            'message' => 'Simulasi berhasil dihapus dari collection',
        ]);
    }
}
