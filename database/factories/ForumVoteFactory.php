<?php

namespace Database\Factories;

use App\Models\ForumThread;
use App\Models\ForumVote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ForumVote>
 */
class ForumVoteFactory extends Factory
{
    protected $model = ForumVote::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'votable_type' => ForumThread::class,
            'votable_id' => ForumThread::factory(),
            'value' => fake()->randomElement([-1, 1]),
        ];
    }

    public function upvote(): static
    {
        return $this->state(fn () => ['value' => 1]);
    }

    public function downvote(): static
    {
        return $this->state(fn () => ['value' => -1]);
    }
}
