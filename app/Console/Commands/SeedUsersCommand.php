<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class SeedUsersCommand extends Command
{
    protected $signature = 'db:refresh-and-seed {--count=100 : The number of users to seed}';

    protected $description = 'Refresh the database and seed users';

    public function handle()
    {
        $count = (int) $this->option('count');

        if (!$this->confirm("This will refresh your database and seed {$count} users. Do you wish to continue?")) {
            $this->info('Command cancelled.');
            return;
        }

        $this->call('migrate:fresh');

        $this->info("Seeding {$count} users...");
        User::factory()->count($count)->create();

        $this->info('Database refreshed and seeded successfully!');
    }
}