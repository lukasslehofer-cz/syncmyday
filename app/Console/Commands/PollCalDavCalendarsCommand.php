<?php

namespace App\Console\Commands;

use App\Models\CalendarConnection;
use App\Services\Sync\SyncEngine;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PollCalDavCalendarsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'caldav:poll {--connection=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Poll CalDAV calendars for changes and trigger sync';

    private SyncEngine $syncEngine;

    public function __construct(SyncEngine $syncEngine)
    {
        parent::__construct();
        $this->syncEngine = $syncEngine;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting CalDAV polling...');
        
        // Get all active CalDAV connections
        $query = CalendarConnection::where('provider', 'caldav')
            ->where('status', 'active');
        
        // If specific connection specified
        if ($connectionId = $this->option('connection')) {
            $query->where('id', $connectionId);
        }
        
        $connections = $query->get();
        
        if ($connections->isEmpty()) {
            $this->info('No active CalDAV connections to poll.');
            return 0;
        }
        
        $this->info("Found {$connections->count()} CalDAV connection(s) to poll");
        
        $successCount = 0;
        $errorCount = 0;
        
        foreach ($connections as $connection) {
            try {
                $this->line("Polling: {$connection->account_email} (ID: {$connection->id})");
                
                Log::channel('sync')->info('Polling CalDAV connection', [
                    'connection_id' => $connection->id,
                    'email' => $connection->account_email,
                ]);
                
                // Trigger sync for this connection
                $this->syncEngine->syncConnection($connection);
                
                $this->info("  ✓ Successfully polled {$connection->account_email}");
                $successCount++;
                
            } catch (\Exception $e) {
                $this->error("  ✗ Failed to poll {$connection->account_email}: {$e->getMessage()}");
                $errorCount++;
                
                Log::channel('sync')->error('CalDAV polling failed', [
                    'connection_id' => $connection->id,
                    'email' => $connection->account_email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
                
                // Update connection status if repeated failures
                $connection->update([
                    'last_error' => $e->getMessage(),
                ]);
            }
        }
        
        $this->newLine();
        $this->info("Polling complete: {$successCount} successful, {$errorCount} failed");
        
        return 0;
    }
}
