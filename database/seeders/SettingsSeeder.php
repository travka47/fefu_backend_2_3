<?php

namespace Database\Seeders;

use App\Models\Settings;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        Settings::query()->truncate();
        DB::table('settings')->insert([
            'frequency' => 5,
            'max' => 5,
        ]);
    }
}
