<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;

class RepairSubscriptionEndsAtCommand extends Command
{
    protected $signature = 'subscriptions:repair-ends-at
                            {--apply : Persist changes to the database (default is dry-run)}
                            {--user= : Repair a single user by ID}';

    protected $description = 'Recompute users.subscription_ends_at from Stripe (fixes the API basil current_period_end regression)';

    public function handle(): int
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $apply = (bool) $this->option('apply');
        $userId = $this->option('user');

        $query = User::where('subscription_tier', 'pro')
            ->whereNotNull('stripe_subscription_id');

        if ($userId) {
            $query->where('id', $userId);
        }

        $users = $query->get();
        $this->info("Found {$users->count()} pro users with Stripe subscription".($apply ? ' (apply mode)' : ' (DRY RUN — pass --apply to commit)'));

        $changed = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($users as $user) {
            try {
                $subscription = \Stripe\Subscription::retrieve($user->stripe_subscription_id);

                $periodEnd = $subscription->items->data[0]->current_period_end
                    ?? $subscription->current_period_end
                    ?? null;

                if (! $periodEnd || $periodEnd <= 0) {
                    $this->warn("user {$user->id} ({$user->email}): no period_end on Stripe subscription, skipping");
                    $skipped++;

                    continue;
                }

                $stripeEndsAt = Carbon::createFromTimestamp($periodEnd);
                $dbEndsAt = $user->subscription_ends_at;
                $diffDays = $dbEndsAt ? (int) $dbEndsAt->diffInDays($stripeEndsAt, false) : null;

                if ($dbEndsAt && abs($diffDays) <= 1) {
                    $this->line("user {$user->id} ({$user->email}): OK ({$dbEndsAt->toDateString()})");
                    $skipped++;

                    continue;
                }

                $this->line(sprintf(
                    'user %d (%s): db=%s  →  stripe=%s  (%+d days)',
                    $user->id,
                    $user->email,
                    $dbEndsAt ? $dbEndsAt->toDateString() : 'NULL',
                    $stripeEndsAt->toDateString(),
                    $diffDays ?? 0
                ));

                if ($apply) {
                    $user->update(['subscription_ends_at' => $stripeEndsAt]);
                    Log::info('subscription_ends_at repaired', [
                        'user_id' => $user->id,
                        'old' => $dbEndsAt ? $dbEndsAt->toDateTimeString() : null,
                        'new' => $stripeEndsAt->toDateTimeString(),
                    ]);
                }
                $changed++;
            } catch (\Throwable $e) {
                $this->error("user {$user->id} ({$user->email}): {$e->getMessage()}");
                $errors++;
            }
        }

        $this->newLine();
        $this->info(sprintf(
            '%s — changed: %d, skipped: %d, errors: %d',
            $apply ? 'Applied' : 'Would change',
            $changed,
            $skipped,
            $errors
        ));

        return $errors > 0 ? Command::FAILURE : Command::SUCCESS;
    }
}
