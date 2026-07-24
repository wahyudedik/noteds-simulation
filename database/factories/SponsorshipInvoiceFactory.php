<?php

namespace Database\Factories;

use App\Models\Sponsorship;
use App\Models\SponsorshipInvoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SponsorshipInvoice>
 */
class SponsorshipInvoiceFactory extends Factory
{
    protected $model = SponsorshipInvoice::class;

    public function definition(): array
    {
        return [
            'sponsorship_id' => Sponsorship::factory(),
            'invoice_number' => 'INV-'.date('Y').'-'.str_pad(fake()->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'amount' => fake()->randomFloat(2, 100000, 5000000),
            'status' => 'draft',
            'due_date' => fake()->dateTimeBetween('+7 days', '+30 days'),
            'paid_at' => null,
            'payment_method' => null,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sent',
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => fake()->randomElement(['bank_transfer', 'ewallet', 'credit_card']),
        ]);
    }

    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'overdue',
        ]);
    }

    public function withAmount(float $amount): static
    {
        return $this->state(fn (array $attributes) => [
            'amount' => $amount,
        ]);
    }

    public function dueInPast(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => now()->subDays(5),
        ]);
    }
}
