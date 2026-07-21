<?php

namespace App\Console\Commands;

use App\Models\Simulation;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     */
    protected $description = 'Generate the sitemap.xml file';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $sitemap = Sitemap::create()
            ->add(
                Url::create('/')
                    ->setPriority(1.0)
                    ->setChangeFrequency('daily')
            )
            ->add(
                Url::create('/explore')
                    ->setPriority(0.9)
                    ->setChangeFrequency('daily')
            )
            ->add(
                Url::create('/leaderboard')
                    ->setPriority(0.7)
                    ->setChangeFrequency('weekly')
            )
            ->add(
                Url::create('/login')
                    ->setPriority(0.3)
                    ->setChangeFrequency('monthly')
            )
            ->add(
                Url::create('/register')
                    ->setPriority(0.3)
                    ->setChangeFrequency('monthly')
            );

        // Add published simulations
        Simulation::published()
            ->select('slug', 'title', 'description', 'updated_at')
            ->chunk(200, function ($simulations) use ($sitemap) {
                foreach ($simulations as $simulation) {
                    $sitemap->add(
                        Url::create("/sim/{$simulation->slug}")
                            ->setPriority(0.8)
                            ->setChangeFrequency('weekly')
                            ->setLastModificationDate($simulation->updated_at)
                    );
                }
            });

        // Add public category pages
        $categories = Simulation::published()
            ->selectRaw('category')
            ->groupBy('category')
            ->pluck('category');

        foreach ($categories as $category) {
            $sitemap->add(
                Url::create('/category/'.strtolower($category))
                    ->setPriority(0.6)
                    ->setChangeFrequency('weekly')
            );
        }

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully at public/sitemap.xml');

        return Command::SUCCESS;
    }
}
