<?php

namespace Database\Factories;

use App\Models\Sponsor;
use App\Models\Sponsorship;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sponsorship>
 */
class SponsorshipFactory extends Factory
{
    protected $model = Sponsorship::class;

    public function definition(): array
    {
        return [
            'sponsor_id' => Sponsor::factory(),
            'title' => fake()->sentence(4),
            'package_type' => fake()->randomElement(['basic', 'standard', 'premium', 'custom']),
            'status' => 'draft',
            'budget' => fake()->randomFloat(2, 1000000, 50000000),
            'spent' => 0,
            'start_date' => fake()->dateTimeBetween('-1 month', '+1 month'),
            'end_date' => fake()->dateTimeBetween('+1 month', '+6 months'),
            'positions' => ['header'],
            'category_filter' => null,
            'target_impressions' => fake()->optional()->numberBetween(10000, 1000000),
            'notes' => fake()->optional()->sentence(),
            'approved_by' => null,
            'approved_at' => null,
            'created_by' => User::factory(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'start_date' => now()->subMonth(),
            'end_date' => now()->addMonth(),
        ]);
    }

    public function running(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'start_date' => now()->subWeek(),
            'end_date' => now()->addWeek(),
        ]);
    }

    public function paused(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paused',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'start_date' => now()->subMonths(3),
            'end_date' => now()->subDay(),
        ]);
    }

    public function withBudget(float $budget): static
    {
        return $this->state(fn (array $attributes) => [
            'budget' => $budget,
        ]);
    }
}
