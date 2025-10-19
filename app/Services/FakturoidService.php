<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FakturoidService
{
    private string $apiUrl;
    private string $clientId;
    private string $clientSecret;
    private string $slug;
    private string $userAgent;
    private ?string $accessToken = null;

    public function __construct()
    {
        $this->apiUrl = 'https://app.fakturoid.cz/api/v3';
        $this->clientId = config('services.fakturoid.client_id');
        $this->clientSecret = config('services.fakturoid.client_secret');
        $this->slug = config('services.fakturoid.slug');
        $this->userAgent = config('services.fakturoid.user_agent', 'SyncMyDay (support@syncmyday.com)');
    }

    /**
     * Get OAuth access token (cached for 2 hours)
     */
    private function getAccessToken(): ?string
    {
        // Return cached token if available
        if ($this->accessToken) {
            return $this->accessToken;
        }

        // Check cache (tokens expire after 2 hours)
        $cacheKey = 'fakturoid_access_token';
        $cachedToken = Cache::get($cacheKey);
        
        if ($cachedToken) {
            $this->accessToken = $cachedToken;
            return $cachedToken;
        }

        try {
            // Request new access token using Client Credentials Flow
            $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
                ->withHeaders([
                    'User-Agent' => $this->userAgent,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post("{$this->apiUrl}/oauth/token", [
                    'grant_type' => 'client_credentials',
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $accessToken = $data['access_token'] ?? null;
                $expiresIn = $data['expires_in'] ?? 7200; // Default 2 hours

                if ($accessToken) {
                    // Cache token for slightly less than expiry time (to be safe)
                    Cache::put($cacheKey, $accessToken, now()->addSeconds($expiresIn - 60));
                    $this->accessToken = $accessToken;
                    
                    Log::info('Fakturoid access token obtained', [
                        'expires_in' => $expiresIn,
                    ]);

                    return $accessToken;
                }
            }

            Log::error('Failed to obtain Fakturoid access token', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Exception obtaining Fakturoid access token', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Create or find a subject (contact) in Fakturoid
     * 
     * @param \App\Models\User $user User to create subject for
     * @return int|null Subject ID or null on failure
     */
    private function createOrFindSubject(\App\Models\User $user): ?int
    {
        $accessToken = $this->getAccessToken();
        
        if (!$accessToken) {
            return null;
        }

        try {
            // Try to find existing subject by email
            $response = Http::withToken($accessToken)
                ->withHeaders([
                    'User-Agent' => $this->userAgent,
                ])
                ->get("{$this->apiUrl}/accounts/{$this->slug}/subjects/search.json", [
                    'query' => $user->email,
                ]);

            if ($response->successful()) {
                $subjects = $response->json();
                
                // If found, return first matching subject
                if (!empty($subjects) && isset($subjects[0]['id'])) {
                    Log::info('Found existing Fakturoid subject', [
                        'subject_id' => $subjects[0]['id'],
                        'email' => $user->email,
                    ]);
                    return $subjects[0]['id'];
                }
            }

            // Subject not found, create new one
            $subjectData = [
                'name' => $user->name ?: $user->email, // Use email as fallback name
                'email' => $user->email,
            ];

            $response = Http::withToken($accessToken)
                ->withHeaders([
                    'User-Agent' => $this->userAgent,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->apiUrl}/accounts/{$this->slug}/subjects.json", $subjectData);

            if ($response->successful()) {
                $subject = $response->json();
                
                Log::info('Created new Fakturoid subject', [
                    'subject_id' => $subject['id'] ?? null,
                    'email' => $user->email,
                ]);

                return $subject['id'] ?? null;
            }

            Log::error('Failed to create Fakturoid subject', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Exception creating Fakturoid subject', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Create an invoice in Fakturoid
     * 
     * @param array $invoiceData Invoice data
     * @return array|null Created invoice data or null on failure
     */
    public function createInvoice(array $invoiceData): ?array
    {
        $accessToken = $this->getAccessToken();
        
        if (!$accessToken) {
            Log::error('Cannot create invoice: No access token');
            return null;
        }

        try {
            $response = Http::withToken($accessToken) // Bearer token
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
        $accessToken = $this->getAccessToken();
        
        if (!$accessToken) {
            Log::error('Cannot get invoice: No access token');
            return null;
        }

        try {
            $response = Http::withToken($accessToken)
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
        $accessToken = $this->getAccessToken();
        
        if (!$accessToken) {
            Log::error('Cannot download PDF: No access token');
            return null;
        }

        try {
            $response = Http::withToken($accessToken)
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

        // First, create or find subject (contact) in Fakturoid
        $subjectId = $this->createOrFindSubject($user);
        
        if (!$subjectId) {
            Log::error('Cannot create invoice: Failed to create/find subject', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            return null;
        }

        // Calculate price without VAT (amount from Stripe is already with VAT included)
        // Formula: price_without_vat = price_with_vat / 1.21
        $priceWithoutVat = round($amount / 1.21, 2);

        // Use same date for issued and due
        $today = now()->format('Y-m-d');

        // Build invoice data according to Fakturoid API v3
        // Supported languages: cs, sk, en, de, fr, it, es, ru, pl, nl, hu, uk, ro
        // SyncMyDay languages (all supported): en, cs, sk, de, pl
        $invoiceData = [
            'subject_id' => $subjectId, // Required: ID of the contact/subject
            'number_format_id' => $numberFormatId, // Number series ID (numeric)
            'currency' => strtolower($currency), // Fakturoid uses lowercase currency codes
            'language' => $language, // Invoice language (en, cs, sk, de, pl supported)
            'issued_on' => $today, // Date of issue
            'taxable_fulfillment_due' => $today, // Date of taxable fulfillment
            'due' => 0, // Due in 0 days (same day as issued_on, will calculate due_on)
            'payment_method' => 'card', // Payment method: paid by card (Stripe)
            'hide_bank_account' => true, // Don't show bank account on invoice (already paid)
            'lines' => [
                [
                    'name' => $description,
                    'quantity' => 1,
                    'unit_name' => 'ks',
                    'unit_price' => $priceWithoutVat, // Price WITHOUT VAT
                    'vat_rate' => 21, // 21% DPH will be added
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

