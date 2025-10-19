<?php

namespace App\Console\Commands;

use App\Models\FakturoidInvoice;
use App\Services\FakturoidService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RetryFailedFakturoidInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fakturoid:retry-failed 
                            {--limit=10 : Maximum number of invoices to retry per run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retry failed Fakturoid invoice creations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = (int) $this->option('limit');

        $this->info('Looking for failed Fakturoid invoices to retry...');

        // Get pending/failed invoices that need retry
        $invoices = FakturoidInvoice::needsRetry()
            ->orderBy('created_at', 'asc') // Oldest first
            ->limit($limit)
            ->get();

        if ($invoices->isEmpty()) {
            $this->info('No invoices need retrying.');
            return 0;
        }

        $this->info("Found {$invoices->count()} invoice(s) to retry.");

        $fakturoidService = new FakturoidService();
        $successCount = 0;
        $failCount = 0;

        foreach ($invoices as $invoice) {
            $this->line("Retrying invoice #{$invoice->id} (attempt {$invoice->retry_count})...");

            try {
                // Build invoice data
                $invoiceData = $fakturoidService->buildInvoiceData(
                    $invoice->user,
                    $invoice->amount,
                    $invoice->currency,
                    $invoice->description ?? "SyncMyDay Pro - Subscription",
                    $invoice->stripe_invoice_id
                );

                // Try to create invoice
                $createdInvoice = $fakturoidService->createInvoice($invoiceData);

                if ($createdInvoice) {
                    // Success - update invoice
                    $invoice->update([
                        'fakturoid_id' => $createdInvoice['id'],
                        'fakturoid_number' => $createdInvoice['number'] ?? null,
                        'issued_at' => isset($createdInvoice['issued_on']) 
                            ? \Carbon\Carbon::parse($createdInvoice['issued_on']) 
                            : now(),
                        'status' => 'created',
                        'error_message' => null,
                    ]);

                    $this->info("  ✓ Success! Created invoice: {$createdInvoice['number']}");
                    $successCount++;

                    Log::info('Fakturoid invoice retry successful', [
                        'local_invoice_id' => $invoice->id,
                        'fakturoid_id' => $createdInvoice['id'],
                        'fakturoid_number' => $createdInvoice['number'],
                        'retry_attempt' => $invoice->retry_count,
                    ]);
                } else {
                    // Failed - increment retry count
                    $invoice->increment('retry_count');
                    $invoice->update([
                        'status' => 'failed',
                        'error_message' => 'Fakturoid API returned null response',
                    ]);

                    $this->error("  ✗ Failed: API returned null response");
                    $failCount++;

                    Log::warning('Fakturoid invoice retry failed', [
                        'local_invoice_id' => $invoice->id,
                        'retry_attempt' => $invoice->retry_count,
                        'reason' => 'null_response',
                    ]);
                }

            } catch (\Exception $e) {
                // Exception - increment retry count and log error
                $invoice->increment('retry_count');
                $invoice->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                ]);

                $this->error("  ✗ Exception: {$e->getMessage()}");
                $failCount++;

                Log::error('Fakturoid invoice retry exception', [
                    'local_invoice_id' => $invoice->id,
                    'retry_attempt' => $invoice->retry_count,
                    'error' => $e->getMessage(),
                ]);
            }

            // Small delay to respect rate limits
            usleep(500000); // 0.5 seconds
        }

        $this->newLine();
        $this->info("Retry complete: {$successCount} succeeded, {$failCount} failed.");

        return $successCount > 0 ? 0 : 1;
    }
}
