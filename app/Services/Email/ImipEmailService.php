<?php

namespace App\Services\Email;

use App\Models\EmailCalendarConnection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * iMIP Email Service
 * 
 * Sends iCalendar Method Invitations/Requests via email (iMIP protocol)
 * RFC 6047: https://tools.ietf.org/html/rfc6047
 */
class ImipEmailService
{
    /**
     * Send blocker invitation via email
     *
     * @param EmailCalendarConnection $connection The email calendar connection (for logging)
     * @param string $targetEmail The recipient email address
     * @param string $eventUid Unique event ID
     * @param string $summary Event title
     * @param \DateTime $start Event start time
     * @param \DateTime $end Event end time
     * @param string $method iMIP method (REQUEST, CANCEL, etc.)
     * @param int $sequence Event sequence number
     * @return bool Success
     */
    public function sendBlockerInvitation(
        EmailCalendarConnection $connection,
        string $targetEmail,
        string $eventUid,
        string $summary,
        \DateTime $start,
        \DateTime $end,
        string $method = 'REQUEST',
        int $sequence = 0
    ): bool {
        if (empty($targetEmail)) {
            Log::error('No target email provided for iMIP', [
                'connection_id' => $connection->id,
            ]);
            return false;
        }

        try {
            // Generate .ics content
            $icsContent = $this->generateIcsContent(
                $eventUid,
                $summary,
                $start,
                $end,
                $method,
                $sequence,
                $targetEmail
            );

            // Create descriptive text body
            $textBody = $this->createTextBody($summary, $start, $end, $method);

            // Send email directly using Symfony Mailer (bypass Laravel Mailable)
            $this->sendCalendarEmail($targetEmail, $summary, $textBody, $icsContent, $method);

            Log::info('iMIP email sent', [
                'connection_id' => $connection->id,
                'target_email' => $targetEmail,
                'event_uid' => $eventUid,
                'method' => $method,
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send iMIP email', [
                'connection_id' => $connection->id,
                'target_email' => $targetEmail,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Send cancellation email
     */
    public function sendCancellation(
        EmailCalendarConnection $connection,
        string $targetEmail,
        string $eventUid,
        string $summary,
        \DateTime $start,
        \DateTime $end,
        int $sequence = 0
    ): bool {
        return $this->sendBlockerInvitation(
            $connection,
            $targetEmail,
            $eventUid,
            $summary,
            $start,
            $end,
            'CANCEL',
            $sequence
        );
    }

    /**
     * Generate iCalendar (.ics) content
     */
    private function generateIcsContent(
        string $eventUid,
        string $summary,
        \DateTime $start,
        \DateTime $end,
        string $method,
        int $sequence,
        string $targetEmail
    ): string {
        $now = new \DateTime();
        $organizerEmail = config('mail.from.address');
        $organizerName = config('mail.from.name', 'SyncMyDay');

        // Convert to UTC
        $startUtc = clone $start;
        $startUtc->setTimezone(new \DateTimeZone('UTC'));
        $endUtc = clone $end;
        $endUtc->setTimezone(new \DateTimeZone('UTC'));
        $nowUtc = clone $now;
        $nowUtc->setTimezone(new \DateTimeZone('UTC'));

        $ics = [];
        $ics[] = 'BEGIN:VCALENDAR';
        $ics[] = 'VERSION:2.0';
        $ics[] = 'PRODID:-//SyncMyDay//Calendar Sync//EN';
        $ics[] = 'METHOD:' . $method;
        $ics[] = 'CALSCALE:GREGORIAN';
        $ics[] = 'BEGIN:VEVENT';
        $ics[] = 'UID:' . $eventUid;
        $ics[] = 'DTSTAMP:' . $nowUtc->format('Ymd\THis\Z');
        $ics[] = 'DTSTART:' . $startUtc->format('Ymd\THis\Z');
        $ics[] = 'DTEND:' . $endUtc->format('Ymd\THis\Z');
        $ics[] = 'SUMMARY:' . $this->escapeIcsValue($summary);
        $ics[] = 'DESCRIPTION:Automatic blocker from SyncMyDay';
        $ics[] = 'STATUS:' . ($method === 'CANCEL' ? 'CANCELLED' : 'CONFIRMED');
        $ics[] = 'SEQUENCE:' . $sequence;
        $ics[] = 'TRANSP:OPAQUE'; // Show as busy
        $ics[] = 'CLASS:PRIVATE'; // Mark as private
        $ics[] = 'ORGANIZER;CN=' . $this->escapeIcsValue($organizerName) . ':mailto:' . $organizerEmail;
        $ics[] = 'ATTENDEE;CN=' . $this->escapeIcsValue($targetEmail) . ';RSVP=FALSE:mailto:' . $targetEmail;
        $ics[] = 'END:VEVENT';
        $ics[] = 'END:VCALENDAR';

        return implode("\r\n", $ics);
    }

    /**
     * Escape special characters in iCalendar values
     */
    private function escapeIcsValue(string $value): string
    {
        // Escape special characters according to RFC 5545
        $value = str_replace('\\', '\\\\', $value);
        $value = str_replace(';', '\\;', $value);
        $value = str_replace(',', '\\,', $value);
        $value = str_replace("\n", '\\n', $value);
        
        return $value;
    }

    /**
     * Create human-readable text body for email
     */
    private function createTextBody(
        string $summary,
        \DateTime $start,
        \DateTime $end,
        string $method
    ): string {
        $action = $method === 'CANCEL' ? 'CANCELLED' : 'INVITATION';
        
        $text = "CALENDAR {$action}\n\n";
        $text .= "Event: {$summary}\n";
        $text .= "Start: " . $start->format('l, F j, Y \a\t g:i A') . "\n";
        $text .= "End: " . $end->format('l, F j, Y \a\t g:i A') . "\n";
        $text .= "\n";
        
        if ($method === 'CANCEL') {
            $text .= "This event has been cancelled.\n";
        } else {
            $text .= "This is an automatic blocker from SyncMyDay.\n";
            $text .= "Your calendar should automatically process this invitation.\n";
        }
        
        return $text;
    }

    /**
     * Send calendar email using Laravel Mail with Symfony message manipulation
     */
    private function sendCalendarEmail(
        string $toEmail,
        string $subject,
        string $textBody,
        string $icsContent,
        string $method
    ): void {
        Mail::send([], [], function ($message) use ($toEmail, $subject, $textBody, $icsContent, $method) {
            $message->to($toEmail)
                ->subject("Calendar: {$subject}")
                ->from(config('mail.from.address'), config('mail.from.name'));
            
            // Access underlying Symfony message to build multipart
            $message->getSymfonyMessage()->setBody(
                $this->buildMultipartBody($textBody, $icsContent, $method)
            );
        });
    }

    /**
     * Build multipart/alternative body with text/calendar inline
     */
    private function buildMultipartBody(string $textBody, string $icsContent, string $method)
    {
        // Build text/plain part
        $textPart = new \Symfony\Component\Mime\Part\TextPart($textBody, 'utf-8');
        
        // Build text/calendar part - must use DataPart to set custom content-type
        $calendarPart = new \Symfony\Component\Mime\Part\DataPart($icsContent, 'invite.ics', 'text/calendar');
        
        // Override headers for calendar part
        $headers = $calendarPart->getPreparedHeaders();
        $headers->remove('Content-Type');
        $headers->addParameterizedHeader(
            'Content-Type',
            'text/calendar',
            [
                'method' => $method,
                'name' => 'invite.ics',
                'charset' => 'UTF-8'
            ]
        );
        $headers->remove('Content-Disposition');
        $headers->addTextHeader('Content-Disposition', 'inline');
        
        // Build text/html part
        $htmlBody = $this->buildHtmlFromText($textBody);
        $htmlPart = new \Symfony\Component\Mime\Part\TextPart($htmlBody, 'utf-8', 'html');
        
        // Create multipart/alternative message
        return new \Symfony\Component\Mime\Part\Multipart\AlternativePart($textPart, $calendarPart, $htmlPart);
    }

    /**
     * Build HTML from plain text
     */
    private function buildHtmlFromText(string $text): string
    {
        $escaped = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $html = nl2br($escaped);
        return '<!DOCTYPE html><html><body style="font-family: Arial, sans-serif; line-height:1.6; color:#333;">'
            . '<div style="background-color:#f8f9fa; padding:16px; border-radius:8px; border-left:4px solid #4F46E5;">'
            . $html
            . '</div>'
            . '<p style="color:#6b7280; font-size:12px; margin-top:16px;">Sent by <strong>SyncMyDay</strong></p>'
            . '</body></html>';
    }
}

