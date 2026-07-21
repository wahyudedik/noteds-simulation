<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Services\GamificationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = GamificationService::getDefaultBadges();

        foreach ($badges as $badgeData) {
            Badge::updateOrCreate(
                ['slug' => Str::slug($badgeData['name'])],
                $badgeData
            );
        }
    }
}
