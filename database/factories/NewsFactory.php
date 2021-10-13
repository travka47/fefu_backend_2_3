<?php

namespace Database\Factories;

use App\Models\News;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsFactory extends Factory
{
    protected $model = News::class;

    public function definition()
    {
        return [
            'title' => $this->faker->unique->realTextBetween(5, 30),
            'description' => $this->faker->realTextBetween(100, 150),
            'text' => $this->faker->realTextBetween(250, 500),
            'is_published' => $this->faker->boolean(70),
            'published_at' => $this->faker->dateTimeBetween('-2 months', '+2 weeks'),
            ];
    }
}
