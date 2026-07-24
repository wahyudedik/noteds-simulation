<?php

namespace Database\Factories;

use App\Models\ForumCategory;
use App\Models\ForumThread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ForumThread>
 */
class ForumThreadFactory extends Factory
{
    protected $model = ForumThread::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);

        return [
            'user_id' => User::factory(),
            'forum_category_id' => ForumCategory::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::random(5),
            'body' => fake()->paragraphs(3, true),
            'is_pinned' => false,
            'is_locked' => false,
            'is_solved' => false,
            'views_count' => 0,
            'replies_count' => 0,
            'votes_count' => 0,
        ];
    }

    public function pinned(): static
    {
        return $this->state(fn () => ['is_pinned' => true]);
    }

    public function locked(): static
    {
        return $this->state(fn () => ['is_locked' => true]);
    }

    public function solved(): static
    {
        return $this->state(fn () => ['is_solved' => true]);
    }
}
