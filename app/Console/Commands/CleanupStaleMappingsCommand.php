<?php

namespace App\Console\Commands;

use App\Models\SyncEventMapping;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupStaleMappingsCommand extends Command
{
    protected $signature = 'sync:cleanup-stale-mappings
                            {--dry-run : Show what would be deleted without actually deleting}
                            {--user_id= : Limit cleanup to one user (via sync_rules.user_id)}
                            {--days=1 : Delete mappings whose event_end is older than this many days}';

    protected $description = 'Delete sync_event_mappings for past events to prevent stale-CANCEL spam';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');
        $userId = $this->option('user_id');
        $days = max(0, (int) $this->option('days'));
        $threshold = now()->subDays($days);

        $query = SyncEventMapping::whereNotNull('event_end')
            ->where('event_end', '<', $threshold);

        if ($userId) {
            $query->whereHas('syncRule', fn ($q) => $q->where('user_id', $userId));
        }

        $count = $query->count();

        if ($count === 0) {
            $this->info("No stale mappings found (threshold: {$threshold->toDateTimeString()}).");

            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->warn("🔍 DRY RUN: would delete {$count} stale mappings (event_end < {$threshold->toDateTimeString()})");

            return self::SUCCESS;
        }

        // Raw delete bypasses observers — no CANCEL emails, no SyncLog entries.
        // This is intentional: events are already past, blockers are no longer relevant.
        $deleted = $query->delete();

        $this->info("✓ Deleted {$deleted} stale mappings.");
        Log::info('Stale sync_event_mappings cleaned up', [
            'deleted' => $deleted,
            'threshold' => $threshold->toIso8601String(),
            'user_id' => $userId,
        ]);

        return self::SUCCESS;
    }
}
