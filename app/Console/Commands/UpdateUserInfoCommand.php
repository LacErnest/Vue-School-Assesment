<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Faker\Factory as Faker;

class UpdateUserInfoCommand extends Command
{
    protected $signature = 'users:update';
    protected $description = 'Update users with random names and timezones';

    public function handle()
    {
        $faker = Faker::create();
        $timezones = ['CEST', 'CST', 'GMT+1'];

        User::all()->each(function ($user) use ($faker, $timezones) {
            $firstName = $faker->firstName;
            $lastName = $faker->lastName;

            $user->update([
                'name' => "$firstName $lastName",
                'timezone' => $timezones[array_rand($timezones)],
            ]);
        });

        $this->info('Users updated successfully!');
    }
}
