<?php

namespace App\Http\Controllers;

use App\Models\Simulation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SimulationController extends Controller
{
    /**
     * Display the landing page with simulation feed.
     */
    public function index(Request $request): View
    {
        $categories = Simulation::published()
            ->selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->get();

        // Trending simulations (most played in last 7 days)
        $trending = Simulation::published()
            ->orderByDesc('play_count')
            ->take(12)
            ->get();

        // Latest simulations
        $latest = Simulation::published()
            ->latest('published_at')
            ->take(12)
            ->get();

        // Most popular all time
        $popular = Simulation::published()
            ->orderByDesc('view_count')
            ->take(12)
            ->get();

        // Search
        $search = $request->input('search');
        $searchResults = null;
        if ($search) {
            $searchResults = Simulation::published()
                ->search($search)
                ->orderByDesc('play_count')
                ->paginate(20);
        }

        return view('landing', compact('categories', 'trending', 'latest', 'popular', 'search', 'searchResults'));
    }

    /**
     * Display a single simulation.
     */
    public function show(string $slug): View
    {
        $simulation = Simulation::published()
            ->where('slug', $slug)
            ->with('user')
            ->firstOrFail();

        // Increment view count
        $simulation->increment('view_count');

        // Related simulations (same category)
        $related = Simulation::published()
            ->where('id', '!=', $simulation->id)
            ->where('category', $simulation->category)
            ->orderByDesc('play_count')
            ->take(8)
            ->get();

        return view('simulations.show', compact('simulation', 'related'));
    }

    /**
     * Play a simulation (increment play count and serve the sim).
     */
    public function play(string $slug)
    {
        $simulation = Simulation::published()
            ->where('slug', $slug)
            ->firstOrFail();

        $simulation->increment('play_count');

        $zipPath = storage_path('app/' . $simulation->zip_path);

        if (! file_exists($zipPath)) {
            abort(404, 'Simulation file not found.');
        }

        return response()->download($zipPath);
    }

    /**
     * Display simulations by category.
     */
    public function category(string $category): View
    {
        $simulations = Simulation::published()
            ->where('category', $category)
            ->orderByDesc('play_count')
            ->paginate(20);

        return view('simulations.category', compact('simulations', 'category'));
    }
}
