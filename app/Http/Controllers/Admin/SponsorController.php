<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use App\Services\SponsorshipService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SponsorController extends Controller
{
    public function __construct(
        protected SponsorshipService $sponsorshipService,
    ) {}

    /**
     * Display a listing of sponsors.
     */
    public function index(Request $request): View
    {
        $sponsors = Sponsor::query()
            ->when($request->input('search'), fn ($q, $s) => $q->where('company_name', 'like', "%{$s}%"))
            ->withCount('sponsorships')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.sponsors.index', compact('sponsors'));
    }

    /**
     * Show the form for creating a new sponsor.
     */
    public function create(): View
    {
        return view('admin.sponsors.create');
    }

    /**
     * Store a newly created sponsor.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'logo' => 'nullable|image|max:512',
            'website_url' => 'nullable|url|max:500',
            'industry' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $request->file('logo')->store('sponsors', 'public');
        }

        unset($validated['logo']);
        Sponsor::create($validated);

        return redirect()->route('admin.sponsors.index')
            ->with('success', 'Sponsor berhasil ditambahkan.');
    }

    /**
     * Display the specified sponsor.
     */
    public function show(Sponsor $sponsor): View
    {
        $sponsor->load(['sponsorships' => fn ($q) => $q->orderByDesc('created_at')]);
        $stats = $this->sponsorshipService->getBrandStats($sponsor);

        return view('admin.sponsors.show', compact('sponsor', 'stats'));
    }

    /**
     * Show the form for editing the specified sponsor.
     */
    public function edit(Sponsor $sponsor): View
    {
        return view('admin.sponsors.edit', compact('sponsor'));
    }

    /**
     * Update the specified sponsor.
     */
    public function update(Request $request, Sponsor $sponsor)
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'contact_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'logo' => 'nullable|image|max:512',
            'website_url' => 'nullable|url|max:500',
            'industry' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $request->file('logo')->store('sponsors', 'public');
        }

        unset($validated['logo']);
        $validated['is_active'] = $request->boolean('is_active');
        $sponsor->update($validated);

        return redirect()->route('admin.sponsors.show', $sponsor)
            ->with('success', 'Sponsor berhasil diperbarui.');
    }

    /**
     * Show performance report for a sponsor.
     */
    public function report(Sponsor $sponsor): View
    {
        $stats = $this->sponsorshipService->getBrandStats($sponsor);
        $sponsorships = $sponsor->sponsorships()->with('platformAds')->orderByDesc('created_at')->get();

        return view('admin.sponsors.report', compact('sponsor', 'stats', 'sponsorships'));
    }
}
