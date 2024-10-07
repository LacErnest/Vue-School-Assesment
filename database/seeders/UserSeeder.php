<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $timezones = ['CEST', 'CST', 'GMT+1'];

        $email = $faker->unique()->safeEmail;

        if ($user = User::where('email', $email)->first()) {
            $email = Str::random(5).$email;
        }

        for ($i = 0; $i < 20; $i++) {
            User::create([
                'name' => $faker->firstName . ' ' . $faker->lastName,
                'email' => $email,
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'timezone' => $timezones[array_rand($timezones)],
            ]);
        }
    }
}
