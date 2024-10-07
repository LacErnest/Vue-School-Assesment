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
    protected $signature = 'users:sync-with-api';
    protected $description = 'Simulate syncing changed user attributes with the third-party API';

    private int $batchSize = 1000;
    private int $batchLimit = 50;
    private int $individualLimit = 3800;

    public function handle(): int
    {
        $changedUsers = User::where('needs_sync', true)
            ->take($this->individualLimit)
            ->get();
        
        $batches = $changedUsers->chunk($this->batchSize);
        
        $totalUpdates = 0;
        $batchCount = 0;
        
        foreach ($batches as $batch) {
            if ($batchCount >= $this->batchLimit) {
                $this->info("Batch limit reached. Stopping for this run.");
                break;
            }
            
            $this->processBatch($batch, $batchCount);
            
            $totalUpdates += $batch->count();
            $this->markUsersAsSynced($batch);
            
            $batchCount++;
        }
        
        $this->info("User synchronization simulation completed. Total updates: {$totalUpdates}");
        
        return Command::SUCCESS;
    }

    /**
     * Process a batch of users and log their updates.
     *
     * @param \Illuminate\Support\Collection $batch
     * @param int $batchNumber
     */
    private function processBatch($batch, int $batchNumber): void
    {
        foreach ($batch as $index => $user) {
            $userIndex = ($batchNumber * $this->batchSize) + $index + 1;
            $updates = [];

            if ($user->isDirty('name')) {
                $updates[] = "firstname: {$user->name}";
            }
            if ($user->isDirty('timezone')) {
                $updates[] = "timezone: '{$user->timezone}'";
            }

            if (!empty($updates)) {
                $updateString = implode(', ', $updates);
                Log::info("[{$userIndex}] {$updateString}");
                $this->info("[{$userIndex}] {$updateString}");
            }
        }
    }

    /**
     * Mark users as synced in the database.
     *
     * @param \Illuminate\Support\Collection $batch
     */
    private function markUsersAsSynced($batch): void
    {
        User::whereIn('email', $batch->pluck('email'))->update(['needs_sync' => false]);
    }
}