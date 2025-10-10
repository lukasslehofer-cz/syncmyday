<?php

namespace App\Http\Controllers;

use App\Models\EmailCalendarConnection;
use App\Services\Email\EmailCalendarSyncService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Email Webhook Controller
 * 
 * Handles inbound email webhooks from mail providers:
 * - Mailgun
 * - SendGrid
 * - Postmark
 * - Others
 * 
 * Each provider sends webhooks in different formats, so we need to
 * normalize them before processing.
 */
class EmailWebhookController extends Controller
{
    private EmailCalendarSyncService $syncService;

    public function __construct(EmailCalendarSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    /**
     * Handle Mailgun inbound webhook
     * 
     * POST /webhook/email/mailgun
     */
    public function mailgun(Request $request)
    {
        // Verify webhook signature
        if (!$this->verifyMailgunSignature($request)) {
            Log::warning('Mailgun webhook signature verification failed');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        try {
            // Extract recipient
            $recipient = $request->input('recipient');
            $token = $this->extractToken($recipient);
            
            if (!$token) {
                Log::info('No valid token in recipient', ['recipient' => $recipient]);
                return response()->json(['message' => 'No valid recipient'], 200);
            }

            // Get raw email (Mailgun sends MIME)
            $rawEmail = $request->input('body-mime');

            // Process
            $result = $this->syncService->processIncomingEmail($token, $rawEmail);

            Log::info('Mailgun webhook processed', [
                'token' => $token,
                'result' => $result,
            ]);

            return response()->json([
                'message' => 'Email processed',
                'result' => $result,
            ]);

        } catch (\Exception $e) {
            Log::error('Mailgun webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    /**
     * Handle SendGrid inbound webhook
     * 
     * POST /webhook/email/sendgrid
     */
    public function sendgrid(Request $request)
    {
        try {
            // SendGrid sends email as form data
            $to = $request->input('to');
            $token = $this->extractToken($to);
            
            if (!$token) {
                Log::info('No valid token in recipient', ['to' => $to]);
                return response()->json(['message' => 'No valid recipient'], 200);
            }

            // Reconstruct raw email from SendGrid data
            $rawEmail = $this->reconstructEmailFromSendGrid($request);

            // Process
            $result = $this->syncService->processIncomingEmail($token, $rawEmail);

            Log::info('SendGrid webhook processed', [
                'token' => $token,
                'result' => $result,
            ]);

            return response()->json([
                'message' => 'Email processed',
                'result' => $result,
            ]);

        } catch (\Exception $e) {
            Log::error('SendGrid webhook error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    /**
     * Handle Postmark inbound webhook
     * 
     * POST /webhook/email/postmark
     */
    public function postmark(Request $request)
    {
        try {
            // Postmark sends JSON
            $to = $request->input('To');
            $token = $this->extractToken($to);
            
            if (!$token) {
                Log::info('No valid token in recipient', ['to' => $to]);
                return response()->json(['message' => 'No valid recipient'], 200);
            }

            // Reconstruct raw email from Postmark data
            $rawEmail = $this->reconstructEmailFromPostmark($request);

            // Process
            $result = $this->syncService->processIncomingEmail($token, $rawEmail);

            Log::info('Postmark webhook processed', [
                'token' => $token,
                'result' => $result,
            ]);

            return response()->json([
                'message' => 'Email processed',
                'result' => $result,
            ]);

        } catch (\Exception $e) {
            Log::error('Postmark webhook error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    /**
     * Extract token from email address
     */
    private function extractToken(string $emailAddress): ?string
    {
        $emailDomain = config('app.email_domain');
        $emailAddress = strtolower(trim($emailAddress));

        // Extract email from "Name <email@domain.com>" format
        if (preg_match('/<([^>]+)>/', $emailAddress, $matches)) {
            $emailAddress = $matches[1];
        }

        // Check if it's our domain
        if (str_ends_with($emailAddress, '@' . $emailDomain)) {
            return explode('@', $emailAddress)[0];
        }

        return null;
    }

    /**
     * Verify Mailgun webhook signature
     */
    private function verifyMailgunSignature(Request $request): bool
    {
        $secret = config('inbound_email.webhook.secret');
        
        if (!$secret) {
            // If no secret configured, skip verification (not recommended for production)
            return true;
        }

        $timestamp = $request->input('timestamp');
        $token = $request->input('token');
        $signature = $request->input('signature');

        $expectedSignature = hash_hmac('sha256', $timestamp . $token, $secret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Reconstruct email from SendGrid webhook data
     */
    private function reconstructEmailFromSendGrid(Request $request): string
    {
        // SendGrid provides structured data, we need to reconstruct a basic email
        $from = $request->input('from', 'unknown@example.com');
        $to = $request->input('to', '');
        $subject = $request->input('subject', '');
        $text = $request->input('text', '');
        $html = $request->input('html', '');
        
        // Check for attachments
        $attachmentCount = $request->input('attachments', 0);
        
        $email = "From: {$from}\r\n";
        $email .= "To: {$to}\r\n";
        $email .= "Subject: {$subject}\r\n";
        $email .= "Content-Type: text/plain\r\n\r\n";
        $email .= $text;

        // Add attachments (simplified - SendGrid sends them as separate files)
        for ($i = 1; $i <= $attachmentCount; $i++) {
            $attachmentInfo = $request->input("attachment-info", "{}");
            $attachmentData = $request->input("attachment{$i}", "");
            
            if ($attachmentData) {
                $email .= "\r\n\r\n--ATTACHMENT--\r\n";
                $email .= $attachmentData;
            }
        }

        return $email;
    }

    /**
     * Reconstruct email from Postmark webhook data
     */
    private function reconstructEmailFromPostmark(Request $request): string
    {
        $from = $request->input('From', 'unknown@example.com');
        $to = $request->input('To', '');
        $subject = $request->input('Subject', '');
        $textBody = $request->input('TextBody', '');
        
        $email = "From: {$from}\r\n";
        $email .= "To: {$to}\r\n";
        $email .= "Subject: {$subject}\r\n";
        $email .= "Content-Type: text/plain\r\n\r\n";
        $email .= $textBody;

        // Add .ics attachments
        $attachments = $request->input('Attachments', []);
        foreach ($attachments as $attachment) {
            if (isset($attachment['ContentType']) && 
                (str_contains($attachment['ContentType'], 'calendar') || 
                 str_ends_with($attachment['Name'], '.ics'))) {
                
                $email .= "\r\n\r\n--ATTACHMENT--\r\n";
                $email .= "Content-Type: {$attachment['ContentType']}\r\n";
                $email .= "Content-Disposition: attachment; filename=\"{$attachment['Name']}\"\r\n\r\n";
                $email .= base64_decode($attachment['Content']);
            }
        }

        return $email;
    }
}

