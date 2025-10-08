<?php

namespace App\Console\Commands;

use App\Models\SyncRule;
use App\Services\Calendar\GoogleCalendarService;
use App\Services\Calendar\MicrosoftCalendarService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupDuplicateBlockersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'calendars:cleanup-duplicates 
                            {--dry-run : Show what would be deleted without actually deleting}
                            {--calendar_id= : Clean specific calendar only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up duplicate blocker events in target calendars';

    /**
     * Execute the console command.
     */
    public function handle(GoogleCalendarService $googleService, MicrosoftCalendarService $microsoftService)
    {
        $dryRun = $this->option('dry-run');
        $calendarIdFilter = $this->option('calendar_id');
        
        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No events will be deleted');
        } else {
            $this->warn('⚠️  WARNING: This will DELETE duplicate blocker events!');
            if (!$this->option('no-interaction')) {
                if (!$this->confirm('Do you want to continue?')) {
                    $this->info('Cancelled.');
                    return Command::SUCCESS;
                }
            }
        }
        
        $this->info('🧹 Starting cleanup of duplicate blockers...');
        
        // Get all active sync rules
        $rules = SyncRule::where('is_active', true)
            ->with(['sourceConnection', 'targets.targetConnection'])
            ->get();
            
        if ($rules->isEmpty()) {
            $this->warn('No active sync rules found.');
            return Command::SUCCESS;
        }
        
        $totalDeleted = 0;
        
        foreach ($rules as $rule) {
            foreach ($rule->targets as $target) {
                $targetConnection = $target->targetConnection;
                $calendarId = $target->target_calendar_id;
                
                if ($calendarIdFilter && $calendarId !== $calendarIdFilter) {
                    continue;
                }
                
                $this->info("  → Processing calendar: {$targetConnection->provider_email} ({$calendarId})");
                
                try {
                    // Initialize service
                    $service = $targetConnection->provider === 'google' 
                        ? $googleService 
                        : $microsoftService;
                    $service->initializeWithConnection($targetConnection);
                    
                    // Get all events in calendar (use getChangedEvents without sync token to get all)
                    $eventsData = $service->getChangedEvents($calendarId, null);
                    $events = $eventsData['events'] ?? $eventsData;
                    
                    // Group blockers by start time and title
                    $blockers = [];
                    foreach ($events as $event) {
                        if ($service->isOurBlocker($event)) {
                            $key = $this->getEventKey($event, $targetConnection->provider);
                            
                            if (!isset($blockers[$key])) {
                                $blockers[$key] = [];
                            }
                            
                            $blockers[$key][] = [
                                'id' => $this->getEventId($event, $targetConnection->provider),
                                'title' => $this->getEventTitle($event, $targetConnection->provider),
                                'start' => $this->getEventStart($event, $targetConnection->provider),
                            ];
                        }
                    }
                    
                    // Find and delete duplicates
                    $deletedInCalendar = 0;
                    foreach ($blockers as $key => $group) {
                        if (count($group) > 1) {
                            $this->warn("    Found " . count($group) . " duplicates for: {$group[0]['title']} @ {$group[0]['start']}");
                            
                            // Keep first one, delete the rest
                            for ($i = 1; $i < count($group); $i++) {
                                $eventId = $group[$i]['id'];
                                
                                if ($dryRun) {
                                    $this->line("      [DRY RUN] Would delete: {$eventId}");
                                } else {
                                    try {
                                        $service->deleteBlocker($calendarId, $eventId);
                                        $this->info("      ✅ Deleted: {$eventId}");
                                        $deletedInCalendar++;
                                    } catch (\Exception $e) {
                                        $this->error("      ❌ Failed to delete {$eventId}: " . $e->getMessage());
                                    }
                                }
                            }
                        }
                    }
                    
                    if ($deletedInCalendar > 0) {
                        $this->info("    Deleted {$deletedInCalendar} duplicate(s) from this calendar");
                        $totalDeleted += $deletedInCalendar;
                    } else {
                        $this->info("    No duplicates found in this calendar ✅");
                    }
                    
                } catch (\Exception $e) {
                    $this->error("    ❌ Error processing calendar: " . $e->getMessage());
                    Log::error('Cleanup duplicates failed', [
                        'calendar_id' => $calendarId,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
        
        $this->newLine();
        if ($dryRun) {
            $this->info("🔍 DRY RUN completed. Would have deleted {$totalDeleted} duplicate(s).");
            $this->info("Run without --dry-run to actually delete duplicates.");
        } else {
            $this->info("✅ Cleanup completed. Deleted {$totalDeleted} duplicate blocker(s).");
        }
        
        return Command::SUCCESS;
    }
    
    private function getEventKey($event, string $provider): string
    {
        $start = $this->getEventStart($event, $provider);
        $title = $this->getEventTitle($event, $provider);
        return md5($start . '|' . $title);
    }
    
    private function getEventId($event, string $provider): string
    {
        return $provider === 'google' ? $event->getId() : $event['id'];
    }
    
    private function getEventTitle($event, string $provider): string
    {
        return $provider === 'google' ? $event->getSummary() : ($event['subject'] ?? '');
    }
    
    private function getEventStart($event, string $provider): string
    {
        if ($provider === 'google') {
            return $event->getStart()->getDateTime() ?? $event->getStart()->getDate();
        } else {
            return $event['start']['dateTime'];
        }
    }
}

