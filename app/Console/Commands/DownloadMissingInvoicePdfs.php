<?php

namespace App\Console\Commands;

use App\Models\FakturoidInvoice;
use App\Services\FakturoidService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DownloadMissingInvoicePdfs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:download-missing 
                            {--limit=50 : Maximum number of PDFs to download per run}
                            {--force : Re-download PDFs even if they already exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download missing invoice PDFs from Fakturoid and store them locally';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = (int) $this->option('limit');
        $force = $this->option('force');

        $this->info('Looking for invoices without local PDF copies...');

        // Get created invoices that don't have PDF stored locally
        $query = FakturoidInvoice::where('status', 'created')
            ->whereNotNull('fakturoid_id');

        if (!$force) {
            // Only get invoices without pdf_url OR where file doesn't exist
            $query->where(function ($q) {
                $q->whereNull('pdf_url')
                  ->orWhere('pdf_url', '');
            });
        }

        $invoices = $query->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();

        // Filter out invoices that already have PDF files (if not forcing)
        if (!$force) {
            $invoices = $invoices->filter(function ($invoice) {
                return !$invoice->pdf_url || !Storage::exists($invoice->pdf_url);
            });
        }

        if ($invoices->isEmpty()) {
            $this->info('No invoices need PDF download.');
            return 0;
        }

        $this->info("Found {$invoices->count()} invoice(s) to download.");

        $fakturoidService = new FakturoidService();
        $successCount = 0;
        $failCount = 0;

        foreach ($invoices as $invoice) {
            $this->line("Downloading PDF for invoice #{$invoice->id} (Fakturoid ID: {$invoice->fakturoid_id})...");

            try {
                // Download PDF content from Fakturoid API
                $pdfContent = $fakturoidService->downloadPdfContent($invoice->fakturoid_id);

                if (!$pdfContent) {
                    $this->error("  ✗ Failed: Could not download PDF from Fakturoid API");
                    $failCount++;
                    
                    Log::warning('Failed to download PDF for invoice', [
                        'invoice_id' => $invoice->id,
                        'fakturoid_id' => $invoice->fakturoid_id,
                    ]);
                    
                    continue;
                }

                // Generate filename
                $filename = $invoice->fakturoid_number 
                    ? "invoice-{$invoice->fakturoid_number}.pdf"
                    : "invoice-{$invoice->id}.pdf";

                // Store PDF in storage/app/invoices/ directory
                $storagePath = "invoices/{$filename}";
                Storage::put($storagePath, $pdfContent);

                // Update invoice with PDF path
                $invoice->update([
                    'pdf_url' => $storagePath,
                ]);

                $this->info("  ✓ Success! PDF saved to: {$storagePath}");
                $successCount++;

                Log::info('Invoice PDF downloaded and stored locally', [
                    'invoice_id' => $invoice->id,
                    'fakturoid_id' => $invoice->fakturoid_id,
                    'storage_path' => $storagePath,
                ]);

            } catch (\Exception $e) {
                $this->error("  ✗ Exception: {$e->getMessage()}");
                $failCount++;

                Log::error('Exception downloading invoice PDF', [
                    'invoice_id' => $invoice->id,
                    'fakturoid_id' => $invoice->fakturoid_id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Small delay to respect rate limits
            usleep(500000); // 0.5 seconds
        }

        $this->newLine();
        $this->info("Download complete: {$successCount} succeeded, {$failCount} failed.");

        return $successCount > 0 || $failCount === 0 ? 0 : 1;
    }
}

