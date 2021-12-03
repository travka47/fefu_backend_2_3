<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::query()->truncate();
        User::factory(random_int(3, 5))->create();
    }
}
