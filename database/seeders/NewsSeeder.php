<?php

namespace Database\Seeders;

use App\Models\News;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    public function run()
    {
        News::query()->delete();
        \app\Models\News::factory(random_int(15, 25))->create();
    }
}
