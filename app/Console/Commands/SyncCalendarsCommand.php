<?php

namespace App\Console\Commands;

use App\Models\SyncRule;
use App\Services\Sync\SyncEngine;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncCalendarsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendars:sync 
                            {--rule_id= : Sync specific rule only}
                            {--user_id= : Sync rules for specific user only}
                            {--force : Force sync even if recently synced}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync calendars according to active sync rules';

    /**
     * Execute the console command.
     */
    public function handle(SyncEngine $syncEngine)
    {
        $this->info('🔄 Starting calendar synchronization...');
        
        // Build query for active sync rules
        $query = SyncRule::where('is_active', true)
            ->with(['sourceConnection', 'targets.targetConnection']);

        // Filter by specific rule
        if ($ruleId = $this->option('rule_id')) {
            $query->where('id', $ruleId);
            $this->info("Filtering: Rule ID = {$ruleId}");
        }

        // Filter by specific user
        if ($userId = $this->option('user_id')) {
            $query->where('user_id', $userId);
            $this->info("Filtering: User ID = {$userId}");
        }

        $rules = $query->get();

        if ($rules->isEmpty()) {
            $this->warn('⚠️  No active sync rules found.');
            return Command::SUCCESS;
        }

        $this->info("📋 Found {$rules->count()} active sync rule(s)");
        
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($rules as $rule) {
            try {
                $this->info("  → Syncing rule #{$rule->id}: {$rule->sourceConnection->provider_email}");
                
                // Check connection status
                if ($rule->sourceConnection->status !== 'active') {
                    $this->warn("    ⚠️  Source connection is not active. Skipping.");
                    continue;
                }
                
                // Sync the rule
                $syncEngine->syncRule($rule, $rule->sourceConnection);
                
                $this->info("    ✅ Synced successfully (check sync logs for details)");
                
                $successCount++;
                
            } catch (\Exception $e) {
                $this->error("    ❌ Error syncing rule #{$rule->id}: " . $e->getMessage());
                
                Log::error('Sync command failed for rule', [
                    'rule_id' => $rule->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                
                $errorCount++;
            }
        }
        
        // Summary
        $this->newLine();
        $this->info("✅ Sync completed:");
        $this->info("   • Success: {$successCount}");
        
        if ($errorCount > 0) {
            $this->error("   • Errors: {$errorCount}");
            return Command::FAILURE;
        }
        
        $this->info('🎉 All rules synced successfully!');
        
        return Command::SUCCESS;
    }
}

