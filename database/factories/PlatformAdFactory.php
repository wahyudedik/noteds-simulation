<?php

namespace Database\Factories;

use App\Models\PlatformAd;
use App\Models\Sponsor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PlatformAd>
 */
class PlatformAdFactory extends Factory
{
    protected $model = PlatformAd::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'type' => fake()->randomElement(['banner', 'video', 'native']),
            'position' => fake()->randomElement(['header', 'sidebar', 'pre_roll', 'mid_roll', 'post_simulation']),
            'content' => fake()->paragraph(),
            'image_path' => null,
            'video_path' => null,
            'target_url' => fake()->url(),
            'adsense_publisher_id' => null,
            'adsense_ad_slot' => null,
            'category_filter' => null,
            'weight' => 1,
            'is_active' => true,
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonth(),
            'impressions' => 0,
            'clicks' => 0,
            'revenue' => 0,
            'created_by' => User::factory(),
            'sponsor_id' => null,
            'sponsorship_id' => null,
            'is_sponsored' => false,
            'sponsored_label' => null,
        ];
    }

    public function sponsored(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_sponsored' => true,
            'sponsor_id' => Sponsor::factory(),
            'sponsored_label' => 'Sponsored',
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function forPosition(string $position): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => $position,
        ]);
    }
}
