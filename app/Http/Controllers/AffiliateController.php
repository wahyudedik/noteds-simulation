<?php

namespace App\Http\Controllers;

use App\Models\AffiliateLink;
use App\Models\Simulation;
use App\Models\User;
use App\Services\AffiliateService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AffiliateController extends Controller
{
    public function __construct(
        private AffiliateService $affiliateService,
    ) {}

    /**
     * Display affiliate links management page for the creator.
     */
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();

        abort_unless($user->isCreator() || $user->isAdmin() || $user->isSuperAdmin(), 403);

        $links = $this->affiliateService->getCreatorLinks($user);
        $stats = $this->affiliateService->getCreatorStats($user);
        $simulations = Simulation::where('user_id', $user->id)->published()->get();
        $commissionRate = $this->affiliateService->getCommissionRate();

        return view('studio.affiliate', compact('links', 'stats', 'simulations', 'commissionRate'));
    }

    /**
     * Generate an affiliate link for a simulation.
     */
    public function generate(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'simulation_id' => ['required', 'integer', 'exists:simulations,id'],
        ]);

        $simulation = Simulation::findOrFail($validated['simulation_id']);

        // Ensure the simulation belongs to the creator
        if ($simulation->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke simulasi ini.');
        }

        $this->affiliateService->generateLink($user, $simulation);

        return redirect()->route('studio.affiliate')
            ->with('toast', ['type' => 'success', 'message' => 'Link afiliasi berhasil dibuat.']);
    }

    /**
     * Delete an affiliate link.
     */
    public function destroy(AffiliateLink $link): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if ($link->user_id !== $user->id) {
            abort(403, 'Anda tidak memiliki akses ke link ini.');
        }

        $link->delete();

        return redirect()->route('studio.affiliate')
            ->with('toast', ['type' => 'success', 'message' => 'Link afiliasi berhasil dihapus.']);
    }

    /**
     * Public tracking endpoint — record click and redirect to simulation.
     */
    public function track(string $code): RedirectResponse
    {
        $link = $this->affiliateService->trackClick($code);

        if (! $link) {
            abort(404, 'Link afiliasi tidak ditemukan.');
        }

        return redirect()->route('simulations.show', $link->simulation->slug)
            ->withCookie(cookie()->forever('aff_code', $code));
    }
}
