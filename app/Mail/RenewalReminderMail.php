<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RenewalReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public ?float $amount = null,
        public ?string $currency = null,
        public ?string $renewalDate = null
    ) {
        $this->locale($user->locale);
        
        // Set mailer to MXroute for system emails
        $emailConfig = \App\Helpers\EmailHelper::getEmailConfig($user, 'info');
        $this->mailer = $emailConfig['mailer'];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $emailConfig = \App\Helpers\EmailHelper::getEmailConfig($this->user, 'info');
        
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address($emailConfig['address'], $emailConfig['name']),
            subject: __('emails.renewal_reminder_subject'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.renewal-reminder',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

