<?php

namespace App\Console\Commands;

use App\Mail\WelcomeMail;
use App\Mail\TrialEndingInSevenDaysMail;
use App\Mail\TrialEndingTomorrowMail;
use App\Mail\PaymentSuccessMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email?} {--locale=cs : Language for emails (cs, en, de, pl, sk)} {--type=all : Type of email to send (all, welcome, verify, verify-calendar, password-reset, trial-7, trial-1, payment-success, payment-failed, contact, trial-expired, account-deleted, subscription-suspended)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email sending functionality - sends all 11 email types in selected language';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Testing Email System...');
        $this->newLine();

        // Get email address
        $email = $this->argument('email') ?? $this->ask('Enter email address to send test to');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address!');
            return Command::FAILURE;
        }

        // Get and validate locale
        $locale = $this->option('locale');
        $supportedLocales = ['cs', 'en', 'de', 'pl', 'sk'];
        
        if (!in_array($locale, $supportedLocales)) {
            $this->error('Invalid locale! Supported: ' . implode(', ', $supportedLocales));
            return Command::FAILURE;
        }

        // Set application locale
        app()->setLocale($locale);
        $this->line('  🌍 Language: ' . strtoupper($locale));
        $this->newLine();

        // Find or create a test user with selected locale
        $testUser = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Test User',
                'password' => bcrypt('test-password'),
                'locale' => $locale,
                'subscription_tier' => 'pro',
                'subscription_ends_at' => now()->addDays(30),
            ]
        );

        // Update locale if user exists but has different locale
        if (!$testUser->wasRecentlyCreated && $testUser->locale !== $locale) {
            $testUser->update(['locale' => $locale]);
            $this->line('  Updated user locale to: ' . $locale);
        }

        if ($testUser->wasRecentlyCreated) {
            $this->line('  Created test user in database');
        } else {
            $this->line('  Using existing user from database');
        }

        // Display SMTP configuration
        $this->info('📧 SMTP Configuration:');
        $this->line('  Host: ' . config('mail.mailers.smtp.host'));
        $this->line('  Port: ' . config('mail.mailers.smtp.port'));
        $this->line('  From: ' . config('mail.from.address'));
        $this->newLine();

        $type = $this->option('type');
        
        try {
            if ($type === 'all') {
                $this->sendAllEmails($email, $testUser);
            } else {
                $this->sendSpecificEmail($email, $testUser, $type);
            }
            
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->newLine();
            $this->error('❌ Failed to send email!');
            $this->newLine();
            $this->error('Error: ' . $e->getMessage());
            $this->newLine();
            
            if ($this->option('verbose')) {
                $this->line('Stack trace:');
                $this->line($e->getTraceAsString());
            }
            
            return Command::FAILURE;
        }
    }

    /**
     * Send all email types
     */
    protected function sendAllEmails($email, $user)
    {
        $this->info('📤 Sending ALL 11 email types to: ' . $email);
        $this->info('🌍 Language: ' . strtoupper(app()->getLocale()));
        $this->newLine();
        
        $emails = [
            ['name' => '1️⃣   Welcome Email', 'type' => 'mailable', 'mail' => new WelcomeMail($user)],
            ['name' => '2️⃣   Verify Email (User Registration)', 'type' => 'verify-email'],
            ['name' => '3️⃣   Verify Email Calendar', 'type' => 'verify-email-calendar'],
            ['name' => '4️⃣   Password Reset', 'type' => 'password-reset'],
            ['name' => '5️⃣   Trial Ending (7 days)', 'type' => 'mailable', 'mail' => new TrialEndingInSevenDaysMail($user)],
            ['name' => '6️⃣   Trial Ending (1 day)', 'type' => 'mailable', 'mail' => new TrialEndingTomorrowMail($user)],
            ['name' => '7️⃣   Trial Expired', 'type' => 'mailable', 'mail' => new \App\Mail\TrialExpiredMail($user)],
            ['name' => '8️⃣   Payment Success', 'type' => 'mailable', 'mail' => new PaymentSuccessMail($user, 29.00, now()->addYear()->format('d.m.Y'))],
            ['name' => '9️⃣   Payment Failed', 'type' => 'payment-failed'],
            ['name' => '🔟  Subscription Suspended', 'type' => 'mailable', 'mail' => new \App\Mail\SubscriptionSuspendedMail($user)],
            ['name' => '1️⃣1️⃣ Account Deleted', 'type' => 'mailable', 'mail' => new \App\Mail\AccountDeletedMail($user)],
        ];
        
        foreach ($emails as $emailData) {
            $this->line($emailData['name']);
            
            if ($emailData['type'] === 'mailable') {
                Mail::to($email)->send($emailData['mail']);
                $this->call('queue:work', ['--once' => true, '--tries' => 1, '--quiet' => true]);
            } else {
                $this->sendCustomEmail($email, $user, $emailData['type']);
            }
            
            $this->line('  ✅ Sent');
            
            // Add delay to avoid rate limiting
            sleep(1);
            $this->newLine();
        }
        
        $this->info('🎉 All 11 emails sent successfully!');
        $this->line('🔗 Check your inbox: ' . $email);
    }

    /**
     * Send specific email type
     */
    protected function sendSpecificEmail($email, $user, $type)
    {
        $this->info('📤 Sending email to: ' . $email);
        $this->line('  Type: ' . $type);
        $this->line('  Language: ' . strtoupper(app()->getLocale()));
        $this->newLine();
        
        switch ($type) {
            case 'welcome':
                Mail::to($email)->send(new WelcomeMail($user));
                break;
            case 'trial-7':
                Mail::to($email)->send(new TrialEndingInSevenDaysMail($user));
                break;
            case 'trial-1':
                Mail::to($email)->send(new TrialEndingTomorrowMail($user));
                break;
            case 'trial-expired':
                Mail::to($email)->send(new \App\Mail\TrialExpiredMail($user));
                break;
            case 'payment-success':
                Mail::to($email)->send(new PaymentSuccessMail($user, 29.00, now()->addYear()->format('d.m.Y')));
                break;
            case 'subscription-suspended':
                Mail::to($email)->send(new \App\Mail\SubscriptionSuspendedMail($user));
                break;
            case 'account-deleted':
                Mail::to($email)->send(new \App\Mail\AccountDeletedMail($user));
                break;
            case 'verify':
            case 'verify-email':
            case 'verify-calendar':
            case 'password-reset':
            case 'payment-failed':
            case 'contact':
                $this->sendCustomEmail($email, $user, $type);
                return;
            default:
                $this->error('Unknown email type: ' . $type);
                $this->line('Available types: all, welcome, verify, verify-calendar, password-reset, trial-7, trial-1, trial-expired, payment-success, payment-failed, subscription-suspended, account-deleted, contact');
                return;
        }
        
        $this->info('⏳ Processing queue job...');
        $this->call('queue:work', ['--once' => true, '--tries' => 1, '--quiet' => true]);
        
        $this->newLine();
        $this->info('✅ Email sent successfully!');
        $this->line('🔗 Check your inbox: ' . $email);
    }

    /**
     * Send custom email types (using Mail::send)
     */
    protected function sendCustomEmail($email, $user, $type)
    {
        switch ($type) {
            case 'verify':
            case 'verify-email':
                $verificationUrl = URL::temporarySignedRoute(
                    'verification.verify',
                    now()->addMinutes(60),
                    ['id' => $user->id, 'hash' => sha1($user->email)]
                );
                
                Mail::send('emails.verify-email', [
                    'user' => $user,
                    'verificationUrl' => $verificationUrl,
                ], function ($message) use ($email) {
                    $message->to($email)
                            ->subject(__('emails.verify_email_subject'));
                });
                break;
                
            case 'verify-calendar':
            case 'verify-email-calendar':
                // Create or use existing email calendar connection for testing
                $emailCalendar = \App\Models\EmailCalendarConnection::where('user_id', $user->id)
                    ->where('target_email', $email)
                    ->first();
                
                if (!$emailCalendar) {
                    // Generate unique email address and token
                    $emailData = \App\Models\EmailCalendarConnection::generateUniqueEmailAddress();
                    
                    $emailCalendar = \App\Models\EmailCalendarConnection::create([
                        'user_id' => $user->id,
                        'email_address' => $emailData['email_address'],
                        'email_token' => $emailData['email_token'],
                        'name' => 'Test Email Calendar',
                        'target_email' => $email,
                        'target_email_verified_at' => null,
                        'status' => 'active',
                    ]);
                }
                
                $verificationUrl = URL::temporarySignedRoute(
                    'email-calendars.verify',
                    now()->addMinutes(60),
                    [
                        'id' => $emailCalendar->id,
                        'hash' => sha1($emailCalendar->target_email),
                    ]
                );
                
                Mail::send('emails.verify-email-calendar', [
                    'emailCalendar' => $emailCalendar,
                    'verificationUrl' => $verificationUrl,
                ], function ($message) use ($email) {
                    $message->to($email)
                            ->subject(__('emails.verify_email_calendar_subject'));
                });
                break;
                
            case 'password-reset':
                $token = 'test-reset-token-' . time();
                
                Mail::send('emails.password-reset', [
                    'user' => $user,
                    'token' => $token,
                ], function ($message) use ($email) {
                    $message->to($email)
                            ->subject(__('emails.password_reset_subject'));
                });
                break;
                
            case 'payment-failed':
                $invoiceUrl = 'https://syncmyday.cz/billing';
                
                Mail::send('emails.payment-failed', [
                    'user' => $user,
                    'amount' => '29.00',
                    'currency' => 'EUR',
                    'invoiceUrl' => $invoiceUrl,
                ], function ($message) use ($email) {
                    $message->to($email)
                            ->subject(__('emails.payment_failed_subject'));
                });
                break;
                
            case 'contact':
                $supportEmail = app()->getLocale() === 'cs' ? 'support@syncmyday.cz' : 'support@syncmyday.eu';
                
                Mail::send('emails.contact', [
                    'contactName' => 'Test User',
                    'contactEmail' => $email,
                    'contactSubject' => 'Test Contact Message',
                    'contactMessage' => 'This is a test contact form submission to verify the email template.',
                ], function ($message) use ($supportEmail, $email) {
                    $message->to($supportEmail)
                            ->replyTo($email, 'Test User')
                            ->subject('Contact Form: Test Contact Message');
                });
                
                $this->line('  📧 Sent to support: ' . $supportEmail);
                break;
        }
    }
}
