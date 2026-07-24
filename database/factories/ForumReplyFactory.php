<?php

namespace Database\Factories;

use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ForumReply>
 */
class ForumReplyFactory extends Factory
{
    protected $model = ForumReply::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'forum_thread_id' => ForumThread::factory(),
            'parent_id' => null,
            'body' => fake()->paragraphs(2, true),
            'is_accepted' => false,
            'votes_count' => 0,
        ];
    }

    public function accepted(): static
    {
        return $this->state(fn () => ['is_accepted' => true]);
    }

    public function nested(): static
    {
        return $this->state(fn () => ['parent_id' => ForumReply::factory()]);
    }
}
