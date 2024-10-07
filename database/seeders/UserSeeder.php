<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $count = $this->command->option('count') ?? 20;

        User::factory()->count($count)->create();

        $this->command->info("{$count} users created successfully.");
    }
}
