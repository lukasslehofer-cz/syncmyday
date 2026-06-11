<?php

namespace App\Console\Commands;

use App\Mail\CalendarConnectionExpiredMail;
use App\Models\CalendarConnection;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
            if ($connection->isTokenExpired() && ! $connection->getRefreshToken()) {
                // Token expired and no refresh token available
                $connection->update(['status' => 'expired']);
                $issuesFound++;

                Log::warning('Connection expired without refresh token', [
                    'connection_id' => $connection->id,
                    'user_id' => $connection->user_id,
                ]);
            }
        }

        $emailsSent = $this->notifyBrokenConnections();

        if ($issuesFound > 0 || $emailsSent > 0) {
            $this->warn("Found {$issuesFound} newly expired connection(s); sent {$emailsSent} reconnection email(s).");
        } else {
            $this->info('All connections healthy.');
        }

        return 0;
    }

    /**
     * Send a one-time "reconnect your calendar" email for connections that are
     * permanently broken (expired/revoked) and haven't been notified yet.
     */
    private function notifyBrokenConnections(): int
    {
        $needReconnect = CalendarConnection::whereIn('status', ['expired', 'revoked'])
            ->whereNull('reconnection_email_sent_at')
            ->with('user')
            ->get();

        $sent = 0;

        foreach ($needReconnect as $connection) {
            if (! $connection->user) {
                continue;
            }

            try {
                Mail::to($connection->user->email)
                    ->send(new CalendarConnectionExpiredMail($connection->user, $connection));

                $connection->update(['reconnection_email_sent_at' => now()]);
                $sent++;
            } catch (\Throwable $e) {
                // Don't let one failed email block the rest; retry on the next run.
                Log::warning('Failed to send reconnection email', [
                    'connection_id' => $connection->id,
                    'user_id' => $connection->user_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $sent;
    }
}
