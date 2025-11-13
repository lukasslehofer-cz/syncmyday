<?php

namespace App\Console\Commands;

use App\Models\SyncRule;
use App\Models\SyncRuleTarget;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CleanOrphanedSyncRulesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:clean-orphaned-rules 
                            {--dry-run : Show what would be deleted without actually deleting}
                            {--force : Skip confirmation prompt}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up orphaned sync rules (rules with missing source/target connections)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        $this->info('🔍 Scanning for orphaned sync rules...');
        $this->newLine();
        
        // 1. Find rules with missing source connections
        $orphanedSourceRules = $this->findOrphanedSourceRules();
        
        // 2. Find rules with missing target connections
        $orphanedTargetRules = $this->findOrphanedTargetRules();
        
        // 3. Find rules with NO targets at all
        $rulesWithoutTargets = $this->findRulesWithoutTargets();
        
        $totalOrphaned = $orphanedSourceRules->count() + $orphanedTargetRules->count() + $rulesWithoutTargets->count();
        
        if ($totalOrphaned === 0) {
            $this->info('✅ No orphaned sync rules found. Database is clean!');
            return Command::SUCCESS;
        }
        
        // Display summary
        $this->warn("⚠️  Found {$totalOrphaned} orphaned sync rule(s):");
        $this->table(
            ['Type', 'Count'],
            [
                ['Rules with missing source connection', $orphanedSourceRules->count()],
                ['Rules with missing target connections', $orphanedTargetRules->count()],
                ['Rules with no targets', $rulesWithoutTargets->count()],
            ]
        );
        
        if ($isDryRun) {
            $this->newLine();
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
            $this->displayDetails($orphanedSourceRules, $orphanedTargetRules, $rulesWithoutTargets);
            return Command::SUCCESS;
        }
        
        // Confirmation
        if (!$this->option('force')) {
            if (!$this->confirm('Do you want to delete these orphaned rules?', false)) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }
        }
        
        // Delete orphaned rules
        $this->newLine();
        $this->info('🗑️  Cleaning up...');
        
        $deletedCount = 0;
        
        // Delete rules with missing source
        foreach ($orphanedSourceRules as $rule) {
            try {
                Log::warning('Deleting orphaned sync rule - missing source connection', [
                    'rule_id' => $rule->id,
                    'source_connection_id' => $rule->source_connection_id,
                    'user_id' => $rule->user_id,
                ]);
                
                $rule->delete();
                $deletedCount++;
            } catch (\Exception $e) {
                $this->error("Failed to delete rule #{$rule->id}: {$e->getMessage()}");
            }
        }
        
        // Delete rules without targets
        foreach ($rulesWithoutTargets as $rule) {
            try {
                Log::warning('Deleting orphaned sync rule - no targets', [
                    'rule_id' => $rule->id,
                    'source_connection_id' => $rule->source_connection_id,
                    'user_id' => $rule->user_id,
                ]);
                
                $rule->delete();
                $deletedCount++;
            } catch (\Exception $e) {
                $this->error("Failed to delete rule #{$rule->id}: {$e->getMessage()}");
            }
        }
        
        // For rules with missing targets, just delete the orphaned target records
        $deletedTargets = 0;
        foreach ($orphanedTargetRules as $target) {
            try {
                Log::info('Deleting orphaned sync rule target', [
                    'target_id' => $target->id,
                    'rule_id' => $target->sync_rule_id,
                    'target_connection_id' => $target->target_connection_id,
                ]);
                
                $target->delete();
                $deletedTargets++;
            } catch (\Exception $e) {
                $this->error("Failed to delete target #{$target->id}: {$e->getMessage()}");
            }
        }
        
        $this->newLine();
        $this->info("✅ Cleanup completed:");
        $this->info("   • {$deletedCount} orphaned rules deleted");
        $this->info("   • {$deletedTargets} orphaned targets deleted");
        
        return Command::SUCCESS;
    }
    
    /**
     * Find sync rules where source_connection_id doesn't exist in calendar_connections
     */
    private function findOrphanedSourceRules()
    {
        return SyncRule::whereNotNull('source_connection_id')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('calendar_connections')
                    ->whereColumn('calendar_connections.id', 'sync_rules.source_connection_id');
            })
            ->get();
    }
    
    /**
     * Find sync rule targets where target_connection_id doesn't exist
     */
    private function findOrphanedTargetRules()
    {
        return SyncRuleTarget::whereNotExists(function ($query) {
            $query->select(DB::raw(1))
                ->from('calendar_connections')
                ->whereColumn('calendar_connections.id', 'sync_rule_targets.target_connection_id');
        })
        ->get();
    }
    
    /**
     * Find sync rules that have no targets at all
     */
    private function findRulesWithoutTargets()
    {
        return SyncRule::whereDoesntHave('targets')->get();
    }
    
    /**
     * Display detailed information about orphaned rules
     */
    private function displayDetails($orphanedSourceRules, $orphanedTargetRules, $rulesWithoutTargets)
    {
        if ($orphanedSourceRules->isNotEmpty()) {
            $this->newLine();
            $this->warn('📋 Rules with missing source connection:');
            $this->table(
                ['Rule ID', 'User ID', 'Missing Source Connection ID', 'Created At'],
                $orphanedSourceRules->map(fn($rule) => [
                    $rule->id,
                    $rule->user_id,
                    $rule->source_connection_id,
                    $rule->created_at->format('Y-m-d H:i:s'),
                ])->toArray()
            );
        }
        
        if ($orphanedTargetRules->isNotEmpty()) {
            $this->newLine();
            $this->warn('📋 Targets with missing connection:');
            $this->table(
                ['Target ID', 'Rule ID', 'Missing Connection ID', 'Created At'],
                $orphanedTargetRules->map(fn($target) => [
                    $target->id,
                    $target->sync_rule_id,
                    $target->target_connection_id,
                    $target->created_at->format('Y-m-d H:i:s'),
                ])->toArray()
            );
        }
        
        if ($rulesWithoutTargets->isNotEmpty()) {
            $this->newLine();
            $this->warn('📋 Rules without any targets:');
            $this->table(
                ['Rule ID', 'User ID', 'Source Connection ID', 'Created At'],
                $rulesWithoutTargets->map(fn($rule) => [
                    $rule->id,
                    $rule->user_id,
                    $rule->source_connection_id ?? 'N/A',
                    $rule->created_at->format('Y-m-d H:i:s'),
                ])->toArray()
            );
        }
    }
}

