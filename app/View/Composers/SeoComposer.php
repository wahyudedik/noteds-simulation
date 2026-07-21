<?php

namespace App\View\Composers;

use Illuminate\View\View;

class SeoComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $defaultTitle = config('app.name', 'Noteds').' — Platform Simulasi Interaktif';
        $defaultDescription = 'Platform simulasi interaktif untuk pembelajaran. Temukan, mainkan, dan bagikan simulasi edukatif seperti YouTube untuk simulasi.';
        $defaultImage = asset('logo.jpeg');
        $currentUrl = request()->url();

        $seoData = [];

        if ($view->offsetExists('seo')) {
            $seoData = (array) $view->offsetGet('seo');
        }

        $view->with('seo', [
            'title' => $seoData['title'] ?? $defaultTitle,
            'description' => $seoData['description'] ?? $defaultDescription,
            'image' => $seoData['image'] ?? $defaultImage,
            'url' => $seoData['url'] ?? $currentUrl,
            'type' => $seoData['type'] ?? 'website',
            'site_name' => config('app.name', 'Noteds'),
        ]);
    }
}
