<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;


    public function definition() : array
    {
        return [
            'text' => $this->faker->realTextBetween(10, 60),
            'user_id' => $this->faker->randomElement(User::query()->pluck('id')),
            'post_id' => $this->faker->randomElement(Post::query()->pluck('id')),
            'created_at' => $this->faker->dateTimeBetween('-1 day'),
        ];
    }
}
