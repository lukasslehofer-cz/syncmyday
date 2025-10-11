<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Calendar Invitation Mail
 * 
 * Sends iCalendar invitations that are automatically processed by Outlook/Gmail
 * by attaching ICS data with proper text/calendar headers.
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
            ->attachData(
                $this->icsContent,
                'invite.ics',
                [
                    'mime' => "text/calendar; method={$this->method}; charset=UTF-8",
                ]
            );
    }

}

