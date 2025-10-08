<?php

namespace App\Console\Commands;

use App\Models\SyncLog;
use Illuminate\Console\Command;

class LogsCleanCommand extends Command
{
    protected $signature = 'logs:clean {--days=30 : Number of days to keep}';
    protected $description = 'Clean up old sync logs';

    public function handle()
    {
        $days = $this->option('days');
        
        $this->info("Cleaning up sync logs older than {$days} days...");
        
        $deleted = SyncLog::where('created_at', '<', now()->subDays($days))->delete();
        
        $this->info("Deleted {$deleted} log entries.");
        return 0;
    }
}

