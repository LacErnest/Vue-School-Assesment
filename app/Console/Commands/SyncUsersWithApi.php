<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncUsersCommand extends Command
{
    protected $signature = 'users:sync';
    protected $description = 'Simulate syncing user attributes with third-party API';

    private const BATCH_SIZE = 1000;
    private const BATCH_LIMIT_PER_HOUR = 50;
    private const INDIVIDUAL_LIMIT_PER_HOUR = 3800;

    public function handle()
    {
        $users = User::where('needs_sync', true)
                     ->take(self::INDIVIDUAL_LIMIT_PER_HOUR)
                     ->get();

        $totalUpdates = 0;
        $batchCount = 0;

        foreach ($users->chunk(self::BATCH_SIZE) as $batch) {
            if ($batchCount >= self::BATCH_LIMIT_PER_HOUR) {
                $this->info("Batch limit reached. Stopping for this run.");
                break;
            }

            $this->processBatch($batch, $totalUpdates);

            $batchCount++;
            $totalUpdates += $batch->count();
        }

        $this->info("Total updates simulated: $totalUpdates");
    }

    private function processBatch($batch, &$totalUpdates)
    {
        $batchPayload = [
            'batches' => [
                'subscribers' => []
            ]
        ];

        foreach ($batch as $user) {
            $userData = [
                'email' => $user->email
            ];

            if ($user->isDirty('name')) {
                $userData['name'] = $user->name;
            }

            if ($user->isDirty('timezone')) {
                $userData['time_zone'] = $user->timezone;
            }

            $batchPayload['batches']['subscribers'][] = $userData;
            $updateInfo = "[" . ++$totalUpdates . "] ";
            $updateInfo .= isset($userData['name']) ? "firstname: {$userData['name']}, " : "";
            $updateInfo .= isset($userData['time_zone']) ? "timezone: '{$userData['time_zone']}'" : "";
            Log::info($updateInfo);
            $this->info($updateInfo);

            $user->needs_sync = false;
            $user->save();
        }
        Log::info("Simulated API call with payload: " . json_encode($batchPayload));
    }
}