#!/usr/bin/env php
<?php

/**
 * Cron Job: Retry Failed Fakturoid Invoices (Shared Hosting Compatible)
 * 
 * Runs daily to retry failed Fakturoid invoice creations.
 * Compatible with shared hosting where proc_open is disabled.
 * 
 * Usage:
 * 1. Via HTTP: https://syncmyday.cz/cron-fakturoid-retry.php?token=YOUR_CRON_SECRET
 * 2. Via cron: /usr/bin/php /path/to/syncmyday/public/cron-fakturoid-retry.php
 * 
 * Schedule: Daily (once per day)
 */

define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$cronSecret = config('app.cron_secret');

if (php_sapi_name() !== 'cli') {
    $providedToken = $_GET['token'] ?? '';
    if (empty($cronSecret) || !hash_equals($cronSecret, $providedToken)) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized', 'time' => date('Y-m-d H:i:s')]);
        exit(1);
    }
    header('Content-Type: application/json');
}

try {
    $startTime = microtime(true);
    
    $output = [];
    $output[] = '[' . date('Y-m-d H:i:s') . '] Checking for failed Fakturoid invoices...';
    
    $limit = 10; // Maximum number of invoices to retry per run
    
    // Get pending/failed invoices that need retry
    $invoices = \App\Models\FakturoidInvoice::needsRetry()
        ->orderBy('created_at', 'asc') // Oldest first
        ->limit($limit)
        ->get();

    $output[] = "Found {$invoices->count()} invoice(s) to retry";

    if ($invoices->isEmpty()) {
        $response = [
            'status' => 'success',
            'message' => 'No invoices need retrying',
            'retried' => 0,
            'output' => implode("\n", $output),
            'duration' => round(microtime(true) - $startTime, 2) . 's',
            'time' => date('Y-m-d H:i:s'),
        ];
        
        if (php_sapi_name() === 'cli') {
            echo implode("\n", $output) . "\n";
        } else {
            echo json_encode($response, JSON_PRETTY_PRINT);
        }
        
        exit(0);
    }

    $fakturoidService = new \App\Services\FakturoidService();
    $successCount = 0;
    $failCount = 0;

    foreach ($invoices as $invoice) {
        try {
            $output[] = "Retrying invoice #{$invoice->id} for user {$invoice->user->email} (attempt {$invoice->retry_count})...";

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

                $output[] = "✓ Success! Created invoice: {$createdInvoice['number']}";
                $successCount++;

                \Illuminate\Support\Facades\Log::info('Fakturoid invoice retry successful', [
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

                $output[] = "✗ Failed: API returned null response";
                $failCount++;

                \Illuminate\Support\Facades\Log::warning('Fakturoid invoice retry failed', [
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

            $output[] = "✗ Exception: {$e->getMessage()}";
            $failCount++;

            \Illuminate\Support\Facades\Log::error('Fakturoid invoice retry exception', [
                'local_invoice_id' => $invoice->id,
                'retry_attempt' => $invoice->retry_count,
                'error' => $e->getMessage(),
            ]);
        }

        // Small delay to respect rate limits (0.5 seconds)
        usleep(500000);
    }

    $output[] = "Finished! Total: {$successCount} succeeded, {$failCount} failed.";
    
    $response = [
        'status' => 'success',
        'retried' => $invoices->count(),
        'succeeded' => $successCount,
        'failed' => $failCount,
        'output' => implode("\n", $output),
        'duration' => round(microtime(true) - $startTime, 2) . 's',
        'time' => date('Y-m-d H:i:s'),
    ];
    
    if (php_sapi_name() === 'cli') {
        echo implode("\n", $output) . "\n";
    } else {
        echo json_encode($response, JSON_PRETTY_PRINT);
    }
    
    exit($successCount > 0 ? 0 : 1);
    
} catch (\Exception $e) {
    $error = 'Fakturoid retry error: ' . $e->getMessage();
    
    \Illuminate\Support\Facades\Log::error('Cron job failed: Fakturoid retry', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ]);
    
    if (php_sapi_name() === 'cli') {
        echo $error . "\n";
    } else {
        http_response_code(500);
        echo json_encode([
            'status' => 'error', 
            'error' => $error, 
            'time' => date('Y-m-d H:i:s')
        ], JSON_PRETTY_PRINT);
    }
    
    exit(1);
}

