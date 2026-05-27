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
                    'target_email_connection_id' => $target->target_email_connection_id,
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
     * Find sync rules whose source connection no longer exists.
     *
     * A rule's source is either an API CalendarConnection (source_connection_id)
     * or an EmailCalendarConnection (source_email_connection_id). Treat the rule
     * as orphaned when the FK that IS set points to a missing row, or when both
     * FKs are NULL (malformed row).
     */
    private function findOrphanedSourceRules()
    {
        return SyncRule::where(function ($q) {
                // API source set but the calendar_connection is gone
                $q->whereNotNull('source_connection_id')
                  ->whereNotExists(function ($sub) {
                      $sub->select(DB::raw(1))
                          ->from('calendar_connections')
                          ->whereColumn('calendar_connections.id', 'sync_rules.source_connection_id');
                  });
            })
            ->orWhere(function ($q) {
                // Email source set but the email_calendar_connection is gone
                $q->whereNotNull('source_email_connection_id')
                  ->whereNotExists(function ($sub) {
                      $sub->select(DB::raw(1))
                          ->from('email_calendar_connections')
                          ->whereColumn('email_calendar_connections.id', 'sync_rules.source_email_connection_id');
                  });
            })
            ->orWhere(function ($q) {
                // Neither source set — malformed row
                $q->whereNull('source_connection_id')
                  ->whereNull('source_email_connection_id');
            })
            ->get();
    }

    /**
     * Find sync rule targets whose target connection no longer exists.
     *
     * Same logic as sources: a target is either an API CalendarConnection
     * (target_connection_id) or an EmailCalendarConnection (target_email_connection_id).
     * Only the FK that IS set must be validated — otherwise email targets, which
     * leave target_connection_id NULL by design, get incorrectly flagged as orphans.
     */
    private function findOrphanedTargetRules()
    {
        return SyncRuleTarget::where(function ($q) {
                $q->whereNotNull('target_connection_id')
                  ->whereNotExists(function ($sub) {
                      $sub->select(DB::raw(1))
                          ->from('calendar_connections')
                          ->whereColumn('calendar_connections.id', 'sync_rule_targets.target_connection_id');
                  });
            })
            ->orWhere(function ($q) {
                $q->whereNotNull('target_email_connection_id')
                  ->whereNotExists(function ($sub) {
                      $sub->select(DB::raw(1))
                          ->from('email_calendar_connections')
                          ->whereColumn('email_calendar_connections.id', 'sync_rule_targets.target_email_connection_id');
                  });
            })
            ->orWhere(function ($q) {
                $q->whereNull('target_connection_id')
                  ->whereNull('target_email_connection_id');
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
                ['Rule ID', 'User ID', 'Source Type', 'Missing ID', 'Created At'],
                $orphanedSourceRules->map(function ($rule) {
                    [$type, $missingId] = $rule->source_connection_id
                        ? ['api', $rule->source_connection_id]
                        : ($rule->source_email_connection_id
                            ? ['email', $rule->source_email_connection_id]
                            : ['none', 'N/A']);
                    return [
                        $rule->id,
                        $rule->user_id,
                        $type,
                        $missingId,
                        $rule->created_at->format('Y-m-d H:i:s'),
                    ];
                })->toArray()
            );
        }

        if ($orphanedTargetRules->isNotEmpty()) {
            $this->newLine();
            $this->warn('📋 Targets with missing connection:');
            $this->table(
                ['Target ID', 'Rule ID', 'Target Type', 'Missing ID', 'Created At'],
                $orphanedTargetRules->map(function ($target) {
                    [$type, $missingId] = $target->target_connection_id
                        ? ['api', $target->target_connection_id]
                        : ($target->target_email_connection_id
                            ? ['email', $target->target_email_connection_id]
                            : ['none', 'N/A']);
                    return [
                        $target->id,
                        $target->sync_rule_id,
                        $type,
                        $missingId,
                        $target->created_at->format('Y-m-d H:i:s'),
                    ];
                })->toArray()
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

