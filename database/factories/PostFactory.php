<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition() : array
    {
        return [
            'title' => $this->faker->realTextBetween(5, 30),
            'text' => $this->faker->realTextBetween(250, 500),
            'user_id' => $this->faker->randomElement(User::query()->pluck('id')),
            'created_at' => $this->faker->dateTimeBetween('-1 week')
        ];
    }
}
