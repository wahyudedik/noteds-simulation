<?php

namespace App\View\Composers;

use App\Models\SeoSetting;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class SeoComposer
{
    /**
     * Bind SEO data to the view.
     *
     * Resolves the current page_key from the route, queries seo_settings
     * for matching entry, then merges with defaults and view-level overrides.
     */
    public function compose(View $view): void
    {
        $appName = config('app.name', 'Noteds');
        $defaultTitle = $appName.' — Platform Simulasi Interaktif';
        $defaultDescription = 'Platform simulasi interaktif untuk pembelajaran. Temukan, mainkan, dan bagikan simulasi edukatif seperti YouTube untuk simulasi.';
        $defaultImage = asset('logo.jpeg');
        $currentUrl = request()->url();

        // Resolve page_key from current route
        $pageKey = $this->resolvePageKey();

        // Query seo_settings for matching entry
        $seoSetting = $pageKey ? SeoSetting::findByKey($pageKey) : null;

        // Build SEO data: defaults < database < view-level overrides
        $seoData = [];

        if ($view->offsetExists('seo')) {
            $seoData = (array) $view->offsetGet('seo');
        }

        $view->with('seo', [
            'title' => $seoData['title'] ?? ($seoSetting?->meta_title ?? $defaultTitle),
            'description' => $seoData['description'] ?? ($seoSetting?->meta_description ?? $defaultDescription),
            'image' => $seoData['image'] ?? ($seoSetting?->og_image ?? $defaultImage),
            'url' => $seoData['url'] ?? ($seoSetting?->canonical_url ?? $currentUrl),
            'type' => $seoData['type'] ?? 'website',
            'site_name' => $appName,
            'og_title' => $seoSetting?->og_title,
            'og_description' => $seoSetting?->og_description,
            'og_image' => $seoSetting?->og_image,
            'meta_keywords' => $seoSetting?->meta_keywords,
            'structured_data' => $seoSetting?->structured_data,
            'canonical_url' => $seoSetting?->canonical_url,
            'seo_setting' => $seoSetting,
        ]);
    }

    /**
     * Resolve the page_key from the current route.
     */
    private function resolvePageKey(): ?string
    {
        $currentRoute = Route::getCurrentRoute();

        if (! $currentRoute) {
            return null;
        }

        $routeName = $currentRoute->getName();

        if (! $routeName) {
            return null;
        }

        return match (true) {
            // Simulation detail page: simulation:{slug}
            $routeName === 'simulations.show' => 'simulation:'.$currentRoute->parameter('slug'),
            // Category page: category:{name}
            $routeName === 'simulations.category' => 'category:'.$currentRoute->parameter('category'),
            // Creator profile page: creator:{id}
            $routeName === 'creators.show' => 'creator:'.$currentRoute->parameter('creator'),
            // Static pages by route name
            in_array($routeName, ['home', 'simulations.index']) => 'home',
            $routeName === 'simulations.explore' => 'explore',
            $routeName === 'leaderboard.index' => 'leaderboard',
            $routeName === 'collections.index' => 'collections',
            // Default: use route name as page key
            default => $routeName,
        };
    }
}
