<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Calendar Invitation Mail
 * 
 * Sends iCalendar invitations that are automatically processed by Outlook/Gmail
 * by composing multipart/alternative with inline text/calendar part.
 */
class CalendarInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $icsContent;
    public string $method;
    public string $summary;
    public string $textBody;

    /**
     * Create a new message instance.
     */
    public function __construct(
        string $icsContent,
        string $method,
        string $summary,
        string $textBody = ''
    ) {
        $this->icsContent = $icsContent;
        $this->method = $method;
        $this->summary = $summary;
        $this->textBody = $textBody ?: "This is a calendar invitation from SyncMyDay.\n\nEvent: {$summary}";
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject("Calendar: {$this->summary}")
            ->withSymfonyMessage(function ($message) {
                // Build text/plain part
                $textPart = new \Symfony\Component\Mime\Part\TextPart($this->textBody, 'utf-8');

                // Build text/calendar inline part
                $calendarPart = new \Symfony\Component\Mime\Part\TextPart($this->icsContent, 'utf-8');
                $calendarPart->getHeaders()->addParameterizedHeader(
                    'Content-Type',
                    'text/calendar',
                    [
                        'method' => $this->method,
                        'name' => 'invite.ics',
                        'charset' => 'UTF-8'
                    ]
                );
                $calendarPart->getHeaders()->addTextHeader('Content-Disposition', 'inline');

                // Build text/html part without Blade views
                $html = $this->buildHtmlFromText($this->textBody);
                $htmlPart = new \Symfony\Component\Mime\Part\TextPart($html, 'utf-8', 'html');

                // Compose multipart/alternative with 3 parts
                $alternative = new \Symfony\Component\Mime\Part\Multipart\AlternativePart($textPart, $calendarPart, $htmlPart);
                $message->setBody($alternative);
            });
    }

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

