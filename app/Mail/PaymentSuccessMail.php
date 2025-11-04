<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentSuccessMail extends Mailable
{
    use Queueable, SerializesModels;
    
    // Note: Queue disabled for shared hosting compatibility

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public ?float $amount = null,
        public ?string $nextBillingDate = null
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
            subject: __('emails.payment_success_subject'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-success',
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
