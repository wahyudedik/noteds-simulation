<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdNetworkSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdNetworkController extends Controller
{
    /**
     * Display all ad network settings.
     */
    public function index(): View
    {
        $networks = AdNetworkSetting::orderBy('network')->get();
        $enabledCount = AdNetworkSetting::enabled()->count();

        return view('admin.ad-networks.index', compact('networks', 'enabledCount'));
    }

    /**
     * Show edit form for a specific ad network.
     */
    public function edit(AdNetworkSetting $network): View
    {
        return view('admin.ad-networks.edit', ['network' => $network]);
    }

    /**
     * Update ad network settings.
     */
    public function update(Request $request, AdNetworkSetting $network): RedirectResponse
    {
        $validated = $request->validate([
            'is_enabled' => 'nullable|boolean',
            'publisher_id' => 'nullable|string|max:200',
            'site_id' => 'nullable|string|max:200',
            'script_tag' => 'nullable|string|max:5000',
            'ads_txt_entry' => 'nullable|string|max:500',
            'allow_banner' => 'nullable|boolean',
            'allow_native' => 'nullable|boolean',
            'allow_interstitial' => 'nullable|boolean',
            'allow_popunder' => 'nullable|boolean',
            'allow_video' => 'nullable|boolean',
            'estimated_rpm' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Parse ad_unit_slots from repeated inputs
        $slots = [];
        if ($request->has('slot_keys')) {
            foreach ($request->input('slot_keys', []) as $index => $key) {
                $value = $request->input('slot_values', [])[$index] ?? null;
                if ($key && $value) {
                    $slots[$key] = $value;
                }
            }
        }

        $validated['ad_unit_slots'] = $slots ?: null;
        $validated['is_enabled'] = $request->boolean('is_enabled');

        $network->update($validated);

        return redirect()->route('admin.ad-networks.index')
            ->with('success', "Pengaturan {$network->display_name} berhasil diupdate.");
    }

    /**
     * Toggle network enabled status (AJAX-friendly).
     */
    public function toggle(AdNetworkSetting $network): RedirectResponse
    {
        $network->update(['is_enabled' => ! $network->is_enabled]);

        $status = $network->is_enabled ? 'diaktifkan' : 'dinonaktifkan';

        return redirect()->route('admin.ad-networks.index')
            ->with('success', "{$network->display_name} berhasil {$status}.");
    }

    /**
     * Generate and display the current ads.txt content.
     */
    public function adsTxt(): View
    {
        $entries = $this->generateAdsTxt();

        return view('admin.ad-networks.ads-txt', ['entries' => $entries]);
    }

    /**
     * Save ads.txt content to public/ads.txt.
     */
    public function saveAdsTxt(Request $request): RedirectResponse
    {
        $content = $request->input('content', '');

        // Security: only allow lines matching ads.txt format
        $lines = explode("\n", $content);
        $safe = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed === '' || str_starts_with($trimmed, '#')) {
                $safe[] = $trimmed;
            } elseif (preg_match('/^[a-z0-9.-]+,\s*\S+,\s*(DIRECT|RESELLER)/i', $trimmed)) {
                $safe[] = $trimmed;
            }
        }

        $finalContent = implode("\n", $safe)."\n";
        file_put_contents(public_path('ads.txt'), $finalContent);

        return redirect()->route('admin.ad-networks.ads-txt')
            ->with('success', 'ads.txt berhasil diperbarui.');
    }

    /**
     * Generate ads.txt content from all enabled networks.
     */
    private function generateAdsTxt(): string
    {
        $entries = AdNetworkSetting::getAdsTxtEntries();

        $lines = [
            '# ads.txt — Noteds Interactive Experience Platform',
            '# Generated automatically. Do not edit manually unless needed.',
            '',
        ];

        foreach ($entries as $network => $entry) {
            $lines[] = "# {$network}";
            $lines[] = $entry;
            $lines[] = '';
        }

        return implode("\n", $lines);
    }
}
