<?php

namespace Tests\Feature\Commands;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\SetupUsers;

class SyncUsersWithApiTest extends TestCase
{
    use RefreshDatabase, SetupUsers;

    protected function setUp(): void
    {
        parent::setUp();
        Log::spy();
        $this->createUsers();
    }

    // #[Test]
    // public function it_respects_individual_request_limit()
    // {
    //     $this->artisan('users:update', ['count' => 20000]);
    //     $this->artisan('users:sync');

    //     $this->assertLessThanOrEqual(3600, User::where('needs_sync', false)->count());
    // }

    #[Test]
    public function it_respects_batch_size_limit()
    {
        $this->artisan('users:update', ['count' => 10000]);
        $this->artisan('users:sync');

        Log::shouldHaveReceived('info')
           ->withArgs(function ($message) {
               return strpos($message, 'Simulated API call for batch of 1000 users') !== false;
           });
    }

    protected function tearDown(): void
    {
        $this->artisan('cache:clear');
        parent::tearDown();
    }
}