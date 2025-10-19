<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FakturoidService
{
    private string $apiUrl;
    private string $email;
    private string $apiToken;
    private string $slug;
    private string $userAgent;

    public function __construct()
    {
        $this->apiUrl = 'https://app.fakturoid.cz/api/v3';
        $this->email = config('services.fakturoid.email');
        $this->apiToken = config('services.fakturoid.api_token');
        $this->slug = config('services.fakturoid.slug');
        $this->userAgent = config('services.fakturoid.user_agent', 'SyncMyDay (support@syncmyday.com)');
    }

    /**
     * Create an invoice in Fakturoid
     * 
     * @param array $invoiceData Invoice data
     * @return array|null Created invoice data or null on failure
     */
    public function createInvoice(array $invoiceData): ?array
    {
        try {
            $response = Http::withBasicAuth($this->email, $this->apiToken)
                ->withHeaders([
                    'User-Agent' => $this->userAgent,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->apiUrl}/accounts/{$this->slug}/invoices.json", $invoiceData);

            if ($response->successful()) {
                $invoice = $response->json();
                
                Log::info('Fakturoid invoice created', [
                    'fakturoid_id' => $invoice['id'] ?? null,
                    'number' => $invoice['number'] ?? null,
                ]);

                return $invoice;
            }

            Log::error('Failed to create Fakturoid invoice', [
                'status' => $response->status(),
                'body' => $response->body(),
                'data' => $invoiceData,
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Exception creating Fakturoid invoice', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    /**
     * Get invoice details
     * 
     * @param int $invoiceId Fakturoid invoice ID
     * @return array|null Invoice data or null on failure
     */
    public function getInvoice(int $invoiceId): ?array
    {
        try {
            $response = Http::withBasicAuth($this->email, $this->apiToken)
                ->withHeaders([
                    'User-Agent' => $this->userAgent,
                ])
                ->get("{$this->apiUrl}/accounts/{$this->slug}/invoices/{$invoiceId}.json");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Failed to get Fakturoid invoice', [
                'invoice_id' => $invoiceId,
                'status' => $response->status(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Exception getting Fakturoid invoice', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Download invoice PDF and stream it
     * 
     * @param int $invoiceId Fakturoid invoice ID
     * @return \Illuminate\Http\Response|null PDF response or null on failure
     */
    public function downloadPdf(int $invoiceId): ?\Illuminate\Http\Response
    {
        try {
            $response = Http::withBasicAuth($this->email, $this->apiToken)
                ->withHeaders([
                    'User-Agent' => $this->userAgent,
                ])
                ->get("{$this->apiUrl}/accounts/{$this->slug}/invoices/{$invoiceId}/download.pdf");

            if ($response->successful()) {
                return response($response->body(), 200)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'attachment; filename="invoice-' . $invoiceId . '.pdf"');
            }

            Log::error('Failed to download Fakturoid PDF', [
                'invoice_id' => $invoiceId,
                'status' => $response->status(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Exception downloading Fakturoid PDF', [
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Build invoice data for Fakturoid API
     * 
     * @param \App\Models\User $user User who made the payment
     * @param float $amount Payment amount
     * @param string $currency Currency code (CZK, EUR, etc.)
     * @param string $description Invoice description
     * @param string|null $stripeInvoiceId Stripe invoice ID for reference
     * @return array Invoice data for Fakturoid API
     */
    public function buildInvoiceData(
        \App\Models\User $user,
        float $amount,
        string $currency,
        string $description,
        ?string $stripeInvoiceId = null
    ): array {
        // Determine language based on user's locale
        $language = $this->getLanguageFromLocale($user->locale);
        
        // Get number format ID (for specific series)
        // This should be the numeric ID of the number format from Fakturoid settings
        $numberFormatId = config('services.fakturoid.number_format');
        
        // Convert to integer if it's numeric, otherwise leave as string (for backward compatibility)
        if (is_numeric($numberFormatId)) {
            $numberFormatId = (int) $numberFormatId;
        }

        // Build invoice data according to Fakturoid API v3
        $invoiceData = [
            'subject_name' => $user->name,
            'subject_email' => $user->email,
            'number_format_id' => $numberFormatId, // Number series ID (numeric)
            'currency' => strtolower($currency), // Fakturoid uses lowercase currency codes
            'language' => $language,
            'issued_on' => now()->format('Y-m-d'),
            'due_on' => now()->format('Y-m-d'), // Immediate due date since already paid
            'paid_on' => now()->format('Y-m-d'), // Mark as paid
            'lines' => [
                [
                    'name' => $description,
                    'quantity' => 1,
                    'unit_name' => 'ks',
                    'unit_price' => $amount,
                    'vat_rate' => 21, // 21% DPH
                ],
            ],
            'note' => __('messages.invoice_note', [], $user->locale),
        ];

        // Add custom ID for reference
        if ($stripeInvoiceId) {
            $invoiceData['custom_id'] = $stripeInvoiceId;
        }

        return $invoiceData;
    }

    /**
     * Get Fakturoid language code from app locale
     * 
     * @param string $locale App locale (cs, en, de, pl, sk)
     * @return string Fakturoid language code
     */
    private function getLanguageFromLocale(string $locale): string
    {
        // Fakturoid supported languages: cz, sk, en, de, fr, it, es, ru, pl, hu, nl, ro
        return match($locale) {
            'cs' => 'cz',
            'sk' => 'sk',
            'de' => 'de',
            'pl' => 'pl',
            'en' => 'en',
            default => 'en',
        };
    }
}

