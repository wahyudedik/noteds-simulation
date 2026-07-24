<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;

class SeoController extends Controller
{
    /**
     * Display a listing of SEO settings.
     */
    public function index(Request $request): View
    {
        $query = SeoSetting::with('updater');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('page_key', 'like', "%{$search}%")
                    ->orWhere('meta_title', 'like', "%{$search}%");
            });
        }

        $seoSettings = $query->latest()->paginate(15)->withQueryString();

        return view('admin.seo.index', compact('seoSettings'));
    }

    /**
     * Show the form for creating a new SEO setting.
     */
    public function create(): View
    {
        return view('admin.seo.create');
    }

    /**
     * Store a newly created SEO setting.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'page_key' => 'required|string|max:100|unique:seo_settings,page_key',
            'meta_title' => 'required|string|max:255',
            'meta_description' => 'required|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|string|max:500',
            'structured_data' => 'nullable|string',
        ]);

        if (isset($validated['structured_data']) && $validated['structured_data']) {
            $decoded = json_decode($validated['structured_data'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['structured_data' => 'JSON tidak valid.'])->withInput();
            }
            $validated['structured_data'] = $decoded;
        } else {
            unset($validated['structured_data']);
        }

        $validated['updated_by'] = auth()->id();

        SeoSetting::create($validated);

        return redirect()->route('admin.seo.index')
            ->with('success', 'Pengaturan SEO berhasil dibuat.');
    }

    /**
     * Show the form for editing the specified SEO setting.
     */
    public function edit(SeoSetting $seo): View
    {
        return view('admin.seo.edit', compact('seo'));
    }

    /**
     * Update the specified SEO setting.
     */
    public function update(Request $request, SeoSetting $seo): RedirectResponse
    {
        $validated = $request->validate([
            'meta_title' => 'required|string|max:255',
            'meta_description' => 'required|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'og_image' => 'nullable|string|max:500',
            'canonical_url' => 'nullable|string|max:500',
            'structured_data' => 'nullable|string',
        ]);

        if (isset($validated['structured_data']) && $validated['structured_data']) {
            $decoded = json_decode($validated['structured_data'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return back()->withErrors(['structured_data' => 'JSON tidak valid.'])->withInput();
            }
            $validated['structured_data'] = $decoded;
        } else {
            $validated['structured_data'] = null;
        }

        $validated['updated_by'] = auth()->id();

        $seo->update($validated);

        return redirect()->route('admin.seo.index')
            ->with('success', 'Pengaturan SEO berhasil diupdate.');
    }

    /**
     * Remove the specified SEO setting.
     */
    public function destroy(SeoSetting $seo): RedirectResponse
    {
        $seo->delete();

        return redirect()->route('admin.seo.index')
            ->with('success', 'Pengaturan SEO berhasil dihapus.');
    }

    /**
     * Regenerate the sitemap.xml file via Artisan command.
     */
    public function regenerateSitemap(): RedirectResponse
    {
        Artisan::call('sitemap:generate');

        $output = Artisan::output();

        return redirect()->route('admin.seo.index')
            ->with('success', 'Sitemap berhasil diregenerasi. '.$output);
    }
}
