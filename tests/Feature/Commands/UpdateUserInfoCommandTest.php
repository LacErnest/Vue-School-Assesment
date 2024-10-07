<?php

namespace Tests\Feature\Commands;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\SetupUsers;

class UpdateUserInfoCommandTest extends TestCase
{
    use RefreshDatabase, SetupUsers;

    protected $customTimezones = ['CEST', 'CST', 'GMT+1'];

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUsers();
    }

    #[Test]
    public function it_updates_all_users_when_no_count_is_provided()
    {
        $initialUsers = User::count();

        $this->artisan('users:update')
             ->expectsOutput("{$initialUsers} users updated successfully!")
             ->assertExitCode(0);
    }

    #[Test]
    public function it_updates_specified_number_of_users()
    {
        $this->artisan('users:update', ['count' => 100])
             ->expectsOutput('100 users updated successfully!')
             ->assertExitCode(0);
    }

    #[Test]
    public function it_handles_count_greater_than_total_users()
    {
        $totalUsers = User::count();

        $this->artisan('users:update', ['count' => $totalUsers + 1000])
             ->expectsOutput("{$totalUsers} users updated successfully!")
             ->assertExitCode(0);
    }


    #[Test]
    public function it_doesnt_update_users_when_count_is_zero()
    {
        $this->artisan('users:update', ['count' => 0])
             ->expectsOutput('0 users updated successfully!')
             ->assertExitCode(0);
    }

    #[Test]
    public function it_uses_valid_timezones()
    {

        $this->artisan('users:update', ['count' => 100]);

        $updatedUsers = User::whereNotIn('timezone', $this->customTimezones)->take(10)->get();
        foreach ($updatedUsers as $user) {
            $this->assertTrue(in_array($user->timezone, \DateTimeZone::listIdentifiers()));
        }
    }

    protected function tearDown(): void
    {
        $this->artisan('cache:clear');
        parent::tearDown();
    }
}