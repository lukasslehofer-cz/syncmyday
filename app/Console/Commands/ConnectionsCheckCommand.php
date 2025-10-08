<?php

namespace App\Console\Commands;

use App\Models\CalendarConnection;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ConnectionsCheckCommand extends Command
{
    protected $signature = 'connections:check';
    protected $description = 'Check calendar connections health and notify users of issues';

    public function handle()
    {
        $this->info('Checking calendar connections...');
        
        $connections = CalendarConnection::where('status', 'active')->get();
        $issuesFound = 0;

        foreach ($connections as $connection) {
            if ($connection->isTokenExpired() && !$connection->getRefreshToken()) {
                // Token expired and no refresh token available
                $connection->update(['status' => 'expired']);
                $issuesFound++;

                Log::warning('Connection expired without refresh token', [
                    'connection_id' => $connection->id,
                    'user_id' => $connection->user_id,
                ]);

                // TODO: Send email notification to user
            }
        }

        if ($issuesFound > 0) {
            $this->warn("Found {$issuesFound} connection(s) with issues.");
        } else {
            $this->info('All connections healthy.');
        }

        return 0;
    }
}

