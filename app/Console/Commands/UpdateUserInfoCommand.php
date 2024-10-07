<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Faker\Factory as Faker;
use DateTimeZone;

class UpdateUserInfoCommand extends Command
{
    protected $signature = 'users:update {count? : Number of users to update}';
    protected $description = 'Update users with random names or timezones';

    protected $customTimezones = ['CEST', 'CST', 'GMT+1'];

    public function handle()
    {
        $faker = Faker::create();
        $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $allTimezones = array_merge($this->customTimezones, $timezones);

        $totalUsers = User::count();
        $count = $this->argument('count');

        if ($count === null) {
            $usersToUpdate = User::all();
            $this->info("Updating all {$totalUsers} users.");
        } else {
            $count = min((int)$count, $totalUsers);
            $usersToUpdate = User::inRandomOrder()->take($count)->get();
            $this->info("Updating {$count} out of {$totalUsers} users.");
        }

        $updatedCount = 0;

        foreach ($usersToUpdate as $user) {
            if (rand(0, 1) == 0) {
                $oldName = $user->name;
                $newName = $faker->name();
                $user->name = $newName;
                $this->info("Updated user {$user->id}: Name changed from '{$oldName}' to '{$newName}'");
            } else {
                $oldTimezone = $user->timezone;
                $newTimezone = $faker->randomElement($allTimezones);
                $user->timezone = $newTimezone;
                $this->info("Updated user {$user->id}: Timezone changed from '{$oldTimezone}' to '{$newTimezone}'");
            }

            $user->save();
            $updatedCount++;
        }

        $this->info("{$updatedCount} users updated successfully!");
    }
}
