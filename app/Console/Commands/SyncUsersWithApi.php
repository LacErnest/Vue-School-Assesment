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

class SyncUsersWithApi extends Command
{
    protected $signature = 'users:sync';
    protected $description = 'Simulate syncing user attributes with third-party API';

    private const BATCH_SIZE = 1000;
    private const BATCH_LIMIT_PER_HOUR = 50;
    private const INDIVIDUAL_LIMIT_PER_HOUR = 3600;

    public function handle()
    {
        $users = User::where('needs_sync', true)
                     ->take(self::INDIVIDUAL_LIMIT_PER_HOUR)
                     ->get();

        $batches = $users->chunk(self::BATCH_SIZE);
        $batchCount = 0;
        $totalUpdated = 0;

        foreach ($batches as $batch) {
            if ($batchCount >= self::BATCH_LIMIT_PER_HOUR) {
                $this->info("Batch limit reached. Stopping for this run.");
                break;
            }

            $this->processBatch($batch, $totalUpdated);

            $batchCount++;
        }
        $this->info("Total batches simulated: $batchCount");
        $this->info("Sync simulation completed. Total users updated: $totalUpdated");
    }

    private function processBatch($batch, &$totalUpdated)
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

            $userData['name'] = $user->name;
            $userData['time_zone'] = $user->timezone;

            $batchPayload['batches']['subscribers'][] = $userData;
            $updateInfo = "[" . ++$totalUpdated . "] ";
            $updateInfo .= isset($userData['name']) ? "firstname: {$userData['name']}, " : "";
            $updateInfo .= isset($userData['time_zone']) ? "timezone: '{$userData['time_zone']}'" : "";
            Log::info($updateInfo);
            $this->info($updateInfo);

            $user->needs_sync = false;
            $user->save();
        }
        $this->info("Batch of " . $batch->count() . " users processed.");
        Log::info("Simulated API call for batch of " . $batch->count() . " users.");
        $this->info("Simulated API call with payload: " . json_encode($batchPayload));
        Log::info("Simulated API call with payload: " . json_encode($batchPayload));
    }
}