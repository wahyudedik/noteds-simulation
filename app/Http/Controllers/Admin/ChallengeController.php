<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Models\ChallengeEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ChallengeController extends Controller
{
    /**
     * Display all challenges.
     */
    public function index(Request $request): View
    {
        $query = Challenge::withCount('entries');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $challenges = $query->latest()->paginate(15)->withQueryString();

        $totalChallenges = Challenge::count();
        $activeChallenges = Challenge::where('status', 'active')->count();
        $totalEntries = ChallengeEntry::count();

        return view('admin.challenges.index', compact(
            'challenges',
            'totalChallenges',
            'activeChallenges',
            'totalEntries'
        ));
    }

    /**
     * Show the form for creating a new challenge.
     */
    public function create(): View
    {
        return view('admin.challenges.create');
    }

    /**
     * Store a newly created challenge.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:weekly,monthly,annual',
            'theme' => 'required|string|max:255',
            'prize_description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        $validated['slug'] = Str::slug($validated['title']);
        $validated['created_by'] = auth()->id();
        $validated['criteria'] = [
            ['name' => 'Akurasi Ilmiah', 'weight' => 30],
            ['name' => 'Interaktivitas & UX', 'weight' => 25],
            ['name' => 'Visual & Desain', 'weight' => 20],
            ['name' => 'Kreativitas', 'weight' => 15],
            ['name' => 'Popularitas', 'weight' => 10],
        ];

        Challenge::create($validated);

        return redirect()->route('admin.challenges.index')->with('success', 'Challenge berhasil dibuat.');
    }

    /**
     * Display a specific challenge with entries.
     */
    public function show(Challenge $challenge): View
    {
        $entries = $challenge->entries()
            ->with(['simulation', 'user'])
            ->orderByDesc('total_score')
            ->get();

        return view('admin.challenges.show', compact('challenge', 'entries'));
    }

    /**
     * Show the edit form.
     */
    public function edit(Challenge $challenge): View
    {
        return view('admin.challenges.edit', compact('challenge'));
    }

    /**
     * Update the challenge.
     */
    public function update(Request $request, Challenge $challenge): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:weekly,monthly,annual',
            'theme' => 'required|string|max:255',
            'prize_description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:upcoming,active,judging,completed',
        ]);

        if ($challenge->status !== $validated['status']) {
            // Set winner if moving to completed
            if ($validated['status'] === 'completed') {
                $topEntry = $challenge->entries()->orderByDesc('total_score')->first();
                if ($topEntry) {
                    $validated['winner_simulation_id'] = $topEntry->simulation_id;
                    $topEntry->update(['status' => 'winner', 'rank' => 1]);

                    // Mark runner-up
                    $runnerUp = $challenge->entries()
                        ->where('id', '!=', $topEntry->id)
                        ->orderByDesc('total_score')
                        ->first();
                    if ($runnerUp) {
                        $runnerUp->update(['status' => 'runner_up', 'rank' => 2]);
                    }
                }
            }
        }

        $challenge->update($validated);

        return back()->with('success', 'Challenge berhasil diperbarui.');
    }

    /**
     * Delete the challenge.
     */
    public function destroy(Challenge $challenge): RedirectResponse
    {
        $challenge->entries()->delete();
        $challenge->delete();

        return redirect()->route('admin.challenges.index')->with('success', 'Challenge berhasil dihapus.');
    }

    /**
     * Score a challenge entry.
     */
    public function scoreEntry(Request $request, Challenge $challenge, ChallengeEntry $entry): RedirectResponse
    {
        $validated = $request->validate([
            'scientific_accuracy' => 'required|numeric|min:0|max:30',
            'interactivity' => 'required|numeric|min:0|max:25',
            'visual_design' => 'required|numeric|min:0|max:20',
            'creativity' => 'required|numeric|min:0|max:15',
            'popularity' => 'required|numeric|min:0|max:10',
            'notes' => 'nullable|string',
        ]);

        $scores = [
            'scientific_accuracy' => (float) $validated['scientific_accuracy'],
            'interactivity' => (float) $validated['interactivity'],
            'visual_design' => (float) $validated['visual_design'],
            'creativity' => (float) $validated['creativity'],
            'popularity' => (float) $validated['popularity'],
        ];

        $totalScore = array_sum($scores);

        $entry->update([
            'scores' => $scores,
            'total_score' => $totalScore,
            'status' => 'scored',
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', "Skor berhasil diberikan: {$totalScore}");
    }
}
