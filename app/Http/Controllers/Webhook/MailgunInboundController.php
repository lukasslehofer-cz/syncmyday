<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Services\Email\InboundEmailProcessor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Mailgun Inbound Webhook Controller
 * 
 * Receives and processes inbound emails from Mailgun Routes
 * Route: POST /api/webhook/mailgun-inbound
 */
class MailgunInboundController extends Controller
{
    public function __construct(
        private InboundEmailProcessor $processor
    ) {}

    /**
     * Handle Mailgun inbound email webhook
     * 
     * Mailgun sends POST data with:
     * - sender: From address
     * - recipient: To address (events@syncmyday.*)
     * - subject: Email subject
     * - body-plain: Plain text body
     * - body-html: HTML body
     * - stripped-text: Body without signature
     * - attachments: Array of attachments
     */
    public function handle(Request $request)
    {
        try {
            // Verify webhook signature (security)
            if (!$this->verifySignature($request)) {
                Log::warning('Mailgun webhook signature verification failed', [
                    'timestamp' => $request->input('timestamp'),
                    'token' => substr($request->input('token', ''), 0, 10) . '...',
                ]);
                return response()->json(['error' => 'Invalid signature'], 401);
            }

            // Extract email data
            $from = $request->input('sender');
            $to = $request->input('recipient');
            $subject = $request->input('subject', '');
            $body = $request->input('stripped-text') ?? $request->input('body-plain', '');

            // Check if this is a calendar response (iMIP)
            $contentType = $request->input('Content-Type', '');
            $isCalendarResponse = str_contains($contentType, 'text/calendar') 
                || str_contains($subject, 'Accepted:')
                || str_contains($subject, 'Declined:')
                || str_contains($subject, 'Tentative:');

            // Get .ics attachment if present
            $icsContent = null;
            $attachmentCount = $request->input('attachment-count', 0);
            
            for ($i = 1; $i <= $attachmentCount; $i++) {
                $attachmentName = $request->input("attachment-{$i}");
                if ($attachmentName && str_ends_with(strtolower($attachmentName), '.ics')) {
                    // Mailgun provides file via multipart/form-data
                    if ($request->hasFile("attachment-{$i}")) {
                        $icsContent = file_get_contents($request->file("attachment-{$i}")->getRealPath());
                        break;
                    }
                }
            }

            // Also check inline calendar data
            if (!$icsContent && str_contains($contentType, 'text/calendar')) {
                $icsContent = $request->input('body-calendar');
            }

            Log::info('Mailgun inbound email received', [
                'from' => $from,
                'to' => $to,
                'subject' => $subject,
                'is_calendar' => $isCalendarResponse,
                'has_ics' => !empty($icsContent),
            ]);

            // Process calendar response if applicable
            if ($isCalendarResponse && $icsContent) {
                $result = $this->processor->processCalendarResponse($from, $to, $icsContent);
                
                if ($result['success']) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Calendar response processed',
                        'data' => $result,
                    ]);
                } else {
                    Log::warning('Failed to process calendar response', $result);
                    return response()->json([
                        'success' => false,
                        'message' => $result['error'] ?? 'Processing failed',
                    ], 422);
                }
            }

            // Not a calendar response - just acknowledge
            return response()->json([
                'success' => true,
                'message' => 'Email received (not a calendar response)',
            ]);

        } catch (\Exception $e) {
            Log::error('Mailgun webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'error' => 'Internal server error',
            ], 500);
        }
    }

    /**
     * Verify Mailgun webhook signature
     * 
     * See: https://documentation.mailgun.com/en/latest/user_manual.html#webhooks
     */
    private function verifySignature(Request $request): bool
    {
        $signingKey = config('services.mailgun.webhook_signing_key');
        
        // Skip verification if key not configured (dev/testing)
        if (!$signingKey) {
            Log::warning('Mailgun webhook signing key not configured - skipping verification');
            return true;
        }

        $timestamp = $request->input('timestamp');
        $token = $request->input('token');
        $signature = $request->input('signature');

        if (!$timestamp || !$token || !$signature) {
            return false;
        }

        // Verify signature
        $expectedSignature = hash_hmac('sha256', $timestamp . $token, $signingKey);
        
        return hash_equals($expectedSignature, $signature);
    }
}


