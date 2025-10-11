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
                // Build parts using Symfony Mime API
                $textPart = new \Symfony\Component\Mime\Part\TextPart($this->textBody, 'utf-8');
                $htmlPart = new \Symfony\Component\Mime\Part\TextPart(
                    view('emails.calendar-html', [
                        'textBody' => $this->textBody,
                        'summary' => $this->summary,
                    ])->render(),
                    'utf-8',
                    'html'
                );

                // Create inline calendar part
                $calendarPart = new \Symfony\Component\Mime\Part\TextPart(
                    $this->icsContent,
                    'utf-8'
                );
                // Override headers for calendar part
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

                // Compose multipart/alternative with 3 parts
                $alternative = new \Symfony\Component\Mime\Part\Multipart\AlternativePart($textPart, $calendarPart, $htmlPart);

                $message->setBody($alternative);
            });
    }
}

