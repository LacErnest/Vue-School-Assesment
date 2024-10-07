<?php

namespace Tests\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Artisan;

trait SetupUsers
{
    protected static $usersCreated = false;

    protected function setUp(): void
    {
        parent::setUp();

        if (!static::$usersCreated) {
            $this->createUsers();
            static::$usersCreated = true;
        }
    }

    protected function createUsers()
    {
        $this->artisan('db:refresh-and-seed --count=20000 --force');
    }

    protected function updateAllUsersNeedsSync(bool $needsSync)
    {
        User::query()->update(['needs_sync' => $needsSync]);
    }

    protected function refreshUsers()
    {
        $this->updateAllUsersNeedsSync(true);
    }
}