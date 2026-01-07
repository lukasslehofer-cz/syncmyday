<?php

namespace App\Jobs;

use App\Helpers\EmailHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Send Calendar Blocker Email Job
 * 
 * Queued job for sending calendar blocker emails with rate limiting
 * to comply with MXroute's 400 emails/hour limit (set to safe 300/hour)
 */
class SendCalendarBlockerEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 5;

    /**
     * The number of seconds to wait before retrying the job.
     * If rate limit is reached, retry after 5, 10, 15, 20, 25 minutes
     */
    public $backoff = [300, 600, 900, 1200, 1500];

    /**
     * Delete the job if its models no longer exist.
     */
    public $deleteWhenMissingModels = true;

    /**
     * @var string Recipient email address
     */
    protected $toEmail;

    /**
     * @var string Email subject
     */
    protected $subject;

    /**
     * @var string Plain text body
     */
    protected $textBody;

    /**
     * @var string iCalendar (.ics) content
     */
    protected $icsContent;

    /**
     * @var string iMIP method (REQUEST, CANCEL, etc.)
     */
    protected $method;

    /**
     * @var array Email config (from, mailer, etc.)
     */
    protected $emailConfig;

    /**
     * Create a new job instance.
     */
    public function __construct(
        string $toEmail,
        string $subject,
        string $textBody,
        string $icsContent,
        string $method,
        array $emailConfig
    ) {
        $this->toEmail = $toEmail;
        $this->subject = $subject;
        $this->textBody = $textBody;
        $this->icsContent = $icsContent;
        $this->method = $method;
        $this->emailConfig = $emailConfig;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Rate limit key based on FROM address (per mailbox)
        $rateLimitKey = 'send-email:' . $this->emailConfig['address'];
        
        // Rate limit: 300 emails per hour per mailbox
        $maxAttempts = config('mail.rate_limit_per_hour', 300);
        $decaySeconds = 3600; // 1 hour

        // Attempt to send within rate limit
        $sent = RateLimiter::attempt(
            $rateLimitKey,
            $maxAttempts,
            function () {
                $this->sendEmail();
            },
            $decaySeconds
        );

        if (!$sent) {
            // Rate limit exceeded - calculate when to retry
            $availableIn = RateLimiter::availableIn($rateLimitKey);
            
            Log::warning('Rate limit exceeded for email sending', [
                'from' => $this->emailConfig['address'],
                'to' => $this->toEmail,
                'available_in_seconds' => $availableIn,
                'rate_limit' => $maxAttempts . '/hour',
            ]);

            // Release job back to queue to retry later
            // Will use backoff strategy defined above
            $this->release($availableIn > 300 ? 300 : $availableIn);
        }
    }

    /**
     * Send the email
     */
    protected function sendEmail(): void
    {
        Mail::mailer($this->emailConfig['mailer'])->send([], [], function ($message) {
            $message->to($this->toEmail)
                ->subject($this->subject)
                ->from($this->emailConfig['address'], $this->emailConfig['name']);
            
            // Access underlying Symfony message to build multipart
            $message->getSymfonyMessage()->setBody(
                $this->buildMultipartBody()
            );
        });

        Log::info('Calendar blocker email sent via queue', [
            'from' => $this->emailConfig['address'],
            'to' => $this->toEmail,
            'method' => $this->method,
            'mailer' => $this->emailConfig['mailer'],
        ]);
    }

    /**
     * Build multipart/alternative body with text/calendar inline
     */
    protected function buildMultipartBody()
    {
        // Build text/plain part
        $textPart = new \Symfony\Component\Mime\Part\TextPart($this->textBody, 'utf-8');
        
        // Build text/calendar part using custom CalendarPart class
        $calendarPart = new \App\Services\Email\CalendarPart($this->icsContent, $this->method);
        
        // Create multipart/alternative message
        return new \Symfony\Component\Mime\Part\Multipart\AlternativePart($textPart, $calendarPart);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Calendar blocker email job failed', [
            'from' => $this->emailConfig['address'],
            'to' => $this->toEmail,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }

    /**
     * Get the tags that should be assigned to the job.
     */
    public function tags(): array
    {
        return ['email', 'calendar-blocker', $this->emailConfig['address']];
    }
}

