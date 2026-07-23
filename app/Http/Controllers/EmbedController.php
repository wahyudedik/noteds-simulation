<?php

namespace App\Http\Controllers;

use App\Models\EmbedTrack;
use App\Models\Simulation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmbedController extends Controller
{
    /**
     * Show the embed page for a simulation.
     * Renders the simulation in a sandboxed iframe for external embedding.
     */
    public function show(Request $request, string $slug): View
    {
        $simulation = Simulation::published()->where('slug', $slug)->firstOrFail();

        $playUrl = route('simulations.serve', $slug);

        // Track embed view
        $this->trackEmbed($request, $simulation, route('embed.show', $slug));

        return view('simulations.embed', [
            'simulation' => $simulation,
            'playUrl' => $playUrl,
        ]);
    }

    /**
     * Get the embed code snippet for copying.
     */
    public function code(string $slug): View
    {
        $simulation = Simulation::published()->where('slug', $slug)->firstOrFail();

        $embedUrl = route('embed.show', $slug);

        $embedCode = '<iframe '
            .'src="'.e($embedUrl).'" '
            .'width="800" '
            .'height="600" '
            .'style="border:none;border-radius:12px;max-width:100%;" '
            .'allowfullscreen '
            .'sandbox="allow-scripts allow-same-origin allow-popups allow-forms" '
            .'loading="lazy" '
            .'title="'.e($simulation->title).'">'
            .'</iframe>';

        return view('simulations.embed-code', [
            'simulation' => $simulation,
            'embedCode' => $embedCode,
            'embedUrl' => $embedUrl,
        ]);
    }

    /**
     * Record an EmbedTrack entry for analytics.
     */
    private function trackEmbed(Request $request, Simulation $simulation, string $embedUrl): void
    {
        $referrer = $request->headers->get('referer', '');
        $referrerDomain = $referrer ? parse_url($referrer, PHP_URL_HOST) : null;

        EmbedTrack::create([
            'simulation_id' => $simulation->id,
            'embed_url' => $embedUrl,
            'referrer_domain' => $referrerDomain,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
