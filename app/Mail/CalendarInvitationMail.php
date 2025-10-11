<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Calendar Invitation Mail
 * 
 * Sends iCalendar invitations that are automatically processed by Outlook/Gmail
 * by embedding ICS data directly in email body with proper text/calendar headers.
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
            ->text('emails.calendar-plain')
            ->html('emails.calendar-html')
            ->with([
                'textBody' => $this->textBody,
                'summary' => $this->summary,
            ])
            ->withSymfonyMessage(function ($message) {
                // Add the text/calendar part directly to Symfony message
                $calendarPart = new \Symfony\Component\Mime\Part\TextPart(
                    $this->icsContent,
                    'utf-8',
                    'calendar',
                    '7bit'
                );
                
                // Set proper headers for calendar invitation
                $calendarPart->getHeaders()
                    ->addTextHeader('Content-Type', "text/calendar; method={$this->method}; name=\"invite.ics\"; charset=UTF-8")
                    ->addTextHeader('Content-Disposition', 'inline')
                    ->addTextHeader('Content-Transfer-Encoding', '7bit');
                
                // Get the message body and add calendar part
                $body = $message->getBody();
                if ($body instanceof \Symfony\Component\Mime\Part\Multipart\AlternativeMultipart) {
                    // Add calendar part to existing alternatives
                    $parts = $body->getParts();
                    $parts[] = $calendarPart;
                    $message->setBody(new \Symfony\Component\Mime\Part\Multipart\AlternativeMultipart(...$parts));
                }
            });
    }

}

