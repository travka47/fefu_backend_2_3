<?php

namespace Database\Seeders;

use App\Models\Comment;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run()
    {
        Comment::query()->truncate();
        Comment::factory(random_int(10, 20))->create();
    }
}
