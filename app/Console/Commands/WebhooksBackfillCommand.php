<?php

namespace App\Console\Commands;

use App\Models\CalendarConnection;
use App\Services\WebhookSubscriptionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class WebhooksBackfillCommand extends Command
{
    protected $signature = 'webhooks:backfill
        {--provider= : Limit to a single provider (google, microsoft)}
        {--dry-run : List missing subscriptions without creating them}';

    protected $description = 'Create webhook subscriptions for active sync rules that lack one';

    public function handle(WebhookSubscriptionService $service): int
    {
        $provider = $this->option('provider');
        $dryRun = (bool) $this->option('dry-run');

        $missing = $this->findMissingSubscriptions($provider);

        if ($missing->isEmpty()) {
            $this->info('No missing webhook subscriptions found.');

            return self::SUCCESS;
        }

        $this->info(sprintf(
            '%s %d (connection, calendar) pair(s) without active webhook subscription.',
            $dryRun ? 'Found' : 'Processing',
            $missing->count()
        ));

        $stats = [
            WebhookSubscriptionService::RESULT_CREATED => 0,
            WebhookSubscriptionService::RESULT_EXISTS => 0,
            WebhookSubscriptionService::RESULT_SKIPPED_PROVIDER => 0,
            WebhookSubscriptionService::RESULT_SKIPPED_LOCALHOST => 0,
            WebhookSubscriptionService::RESULT_FAILED => 0,
        ];

        foreach ($missing as $row) {
            $connection = CalendarConnection::find($row->connection_id);
            if (! $connection) {
                continue;
            }

            $line = sprintf(
                '  conn #%d (%s) calendar=%s',
                $connection->id,
                $connection->provider,
                $row->calendar_id
            );

            if ($dryRun) {
                $this->line($line.' [dry-run]');

                continue;
            }

            $result = $service->ensureSubscription($connection, $row->calendar_id);
            $stats[$result] = ($stats[$result] ?? 0) + 1;
            $this->line($line.' -> '.$result);
        }

        if (! $dryRun) {
            $this->info(sprintf(
                'Done. created=%d exists=%d skipped_provider=%d skipped_localhost=%d failed=%d',
                $stats[WebhookSubscriptionService::RESULT_CREATED],
                $stats[WebhookSubscriptionService::RESULT_EXISTS],
                $stats[WebhookSubscriptionService::RESULT_SKIPPED_PROVIDER],
                $stats[WebhookSubscriptionService::RESULT_SKIPPED_LOCALHOST],
                $stats[WebhookSubscriptionService::RESULT_FAILED]
            ));
        }

        return self::SUCCESS;
    }

    /**
     * Find (connection_id, calendar_id) pairs that are referenced by an active
     * sync rule but have no active webhook_subscription row.
     */
    private function findMissingSubscriptions(?string $provider)
    {
        $query = DB::table('sync_rules as sr')
            ->join('calendar_connections as cc', 'cc.id', '=', 'sr.source_connection_id')
            ->select([
                'sr.source_connection_id as connection_id',
                'sr.source_calendar_id as calendar_id',
            ])
            ->where('sr.is_active', true)
            ->where('cc.status', 'active')
            ->whereNotNull('sr.source_connection_id')
            ->whereNotNull('sr.source_calendar_id')
            ->whereIn('cc.provider', ['google', 'microsoft'])
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('webhook_subscriptions as ws')
                    ->whereColumn('ws.calendar_connection_id', 'sr.source_connection_id')
                    ->whereColumn('ws.calendar_id', 'sr.source_calendar_id')
                    ->where('ws.status', 'active')
                    ->where(function ($inner) {
                        $inner->whereNull('ws.expires_at')
                            ->orWhere('ws.expires_at', '>', now());
                    });
            })
            ->distinct();

        if ($provider) {
            $query->where('cc.provider', $provider);
        }

        return $query->get();
    }
}
