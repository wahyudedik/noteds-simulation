<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sponsor;
use App\Models\Sponsorship;
use App\Models\SponsorshipInvoice;
use App\Services\InvoiceService;
use App\Services\SponsorshipService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SponsorshipController extends Controller
{
    public function __construct(
        protected SponsorshipService $sponsorshipService,
        protected InvoiceService $invoiceService,
    ) {}

    /**
     * Display a listing of sponsorships.
     */
    public function index(Request $request): View
    {
        $sponsorships = Sponsorship::query()
            ->with('sponsor')
            ->when($request->input('status'), fn ($q, $s) => $q->where('status', $s))
            ->when($request->input('search'), fn ($q, $s) => $q->where('title', 'like', "%{$s}%"))
            ->orderByDesc('created_at')
            ->paginate(20);

        $stats = $this->sponsorshipService->getDashboardStats();

        return view('admin.sponsorships.index', compact('sponsorships', 'stats'));
    }

    /**
     * Show the form for creating a new sponsorship.
     */
    public function create(): View
    {
        $sponsors = Sponsor::where('is_active', true)->orderBy('company_name')->get();

        return view('admin.sponsorships.create', compact('sponsors'));
    }

    /**
     * Store a newly created sponsorship.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sponsor_id' => 'required|exists:sponsors,id',
            'title' => 'required|string|max:255',
            'package_type' => 'required|in:basic,standard,premium,custom',
            'budget' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'positions' => 'required|array|min:1',
            'positions.*' => 'in:header,sidebar,pre_roll,mid_roll,post_simulation,feed_sponsored,search_sponsored',
            'category_filter' => 'nullable|array',
            'target_impressions' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();

        $sponsor = Sponsor::findOrFail($validated['sponsor_id']);
        unset($validated['sponsor_id']);

        $this->sponsorshipService->create($sponsor, $validated, auth()->user());

        return redirect()->route('admin.sponsorships.index')
            ->with('success', 'Sponsorship berhasil ditambahkan.');
    }

    /**
     * Display the specified sponsorship.
     */
    public function show(Sponsorship $sponsorship): View
    {
        $sponsorship->load(['sponsor', 'platformAds', 'invoices']);
        $stats = $this->sponsorshipService->getSponsorshipStats($sponsorship);
        $invoices = $this->invoiceService->getForSponsorship($sponsorship);

        return view('admin.sponsorships.show', compact('sponsorship', 'stats', 'invoices'));
    }

    /**
     * Show the form for editing the specified sponsorship.
     */
    public function edit(Sponsorship $sponsorship): View
    {
        $sponsors = Sponsor::where('is_active', true)->orderBy('company_name')->get();

        return view('admin.sponsorships.edit', compact('sponsorship', 'sponsors'));
    }

    /**
     * Update the specified sponsorship.
     */
    public function update(Request $request, Sponsorship $sponsorship)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'package_type' => 'required|in:basic,standard,premium,custom',
            'budget' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'positions' => 'required|array|min:1',
            'positions.*' => 'in:header,sidebar,pre_roll,mid_roll,post_simulation,feed_sponsored,search_sponsored',
            'category_filter' => 'nullable|array',
            'target_impressions' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $this->sponsorshipService->update($sponsorship, $validated);

        return redirect()->route('admin.sponsorships.show', $sponsorship)
            ->with('success', 'Sponsorship berhasil diperbarui.');
    }

    /**
     * Approve a sponsorship.
     */
    public function approve(Sponsorship $sponsorship)
    {
        $this->sponsorshipService->approve($sponsorship, auth()->user());

        return redirect()->route('admin.sponsorships.show', $sponsorship)
            ->with('success', 'Sponsorship berhasil disetujui dan diaktifkan.');
    }

    /**
     * Pause a sponsorship.
     */
    public function pause(Sponsorship $sponsorship)
    {
        $this->sponsorshipService->pause($sponsorship);

        return redirect()->route('admin.sponsorships.show', $sponsorship)
            ->with('success', 'Sponsorship berhasil dijeda.');
    }

    /**
     * Resume a paused sponsorship.
     */
    public function resume(Sponsorship $sponsorship)
    {
        $this->sponsorshipService->resume($sponsorship);

        return redirect()->route('admin.sponsorships.show', $sponsorship)
            ->with('success', 'Sponsorship berhasil dilanjutkan.');
    }

    /**
     * Mark a sponsorship as completed.
     */
    public function complete(Sponsorship $sponsorship)
    {
        $this->sponsorshipService->complete($sponsorship);

        return redirect()->route('admin.sponsorships.show', $sponsorship)
            ->with('success', 'Sponsorship berhasil ditandai selesai.');
    }

    // ─── Invoice Methods ──────────────────────────────────────────

    /**
     * Display invoices for a sponsorship.
     */
    public function invoices(Sponsorship $sponsorship): View
    {
        $invoices = $this->invoiceService->getForSponsorship($sponsorship);

        return view('admin.sponsorships.invoices', compact('sponsorship', 'invoices'));
    }

    /**
     * Create a new invoice for a sponsorship.
     */
    public function createInvoice(Request $request, Sponsorship $sponsorship)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $this->invoiceService->create(
            $sponsorship,
            $validated['amount'],
            Carbon::parse($validated['due_date']),
            $validated['notes'] ?? null,
        );

        return redirect()->route('admin.sponsorships.show', $sponsorship)
            ->with('success', 'Invoice berhasil dibuat.');
    }

    /**
     * Mark an invoice as sent.
     */
    public function sendInvoice(SponsorshipInvoice $invoice)
    {
        $this->invoiceService->markSent($invoice);

        return redirect()->back()->with('success', 'Invoice berhasil ditandai terkirim.');
    }

    /**
     * Mark an invoice as paid.
     */
    public function markInvoicePaid(Request $request, SponsorshipInvoice $invoice)
    {
        $validated = $request->validate([
            'payment_method' => 'required|string|max:100',
        ]);

        $this->invoiceService->markPaid($invoice, $validated['payment_method']);

        return redirect()->back()->with('success', 'Invoice berhasil ditandai lunas.');
    }
}
