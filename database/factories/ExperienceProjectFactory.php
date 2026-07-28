<?php

namespace Database\Factories;

use App\Models\ExperienceProject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ExperienceProject>
 */
class ExperienceProjectFactory extends Factory
{
    protected $model = ExperienceProject::class;

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
            'template_id' => null,
            'title' => $title,
            'description' => fake()->paragraph(),
            'config' => ['components' => []],
            'status' => 'draft',
            'version' => 1,
            'slug' => Str::slug($title).'-'.Str::random(5),
            'thumbnail_path' => null,
            'published_at' => null,
        ];
    }
}
