<?php

namespace Database\Factories;

use App\Models\ExperienceProject;
use App\Models\Simulation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Simulation>
 */
class SimulationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(3);

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::random(5),
            'description' => fake()->paragraph(),
            'category' => fake()->randomElement(['Science', 'Mathematics', 'Biology', 'Physics', 'Chemistry']),
            'subcategory' => null,
            'tags' => null,
            'thumbnail' => null,
            'version' => '1.0.0',
            'zip_path' => 'simulations/1/test.zip',
            'entry_point' => 'index.html',
            'is_published' => true,
            'is_featured' => false,
            'play_count' => 0,
            'view_count' => 0,
            'like_count' => 0,
            'bookmark_count' => 0,
            'share_count' => 0,
            'average_rating' => 0,
            'rating_count' => 0,
            'published_at' => now(),
        ];
    }

    /**
     * Simulation that is unpublished.
     */
    public function unpublished(): static
    {
        return $this->state(fn () => [
            'is_published' => false,
            'published_at' => null,
        ]);
    }

    /**
     Simulation created from a Builder project.
     */
    public function fromBuilder(ExperienceProject $project): static
    {
        return $this->state(fn () => [
            'experience_project_id' => $project->id,
        ]);
    }
}
