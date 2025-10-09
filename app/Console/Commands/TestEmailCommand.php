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
    protected $signature = 'email:test {email?} {--type=all : Type of email to send (all, welcome, trial-7, trial-1, verify, payment)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email sending functionality';

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

        // Find or create a test user
        $testUser = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Test User',
                'password' => bcrypt('test-password'),
                'locale' => 'cs',
                'subscription_tier' => 'pro',
                'subscription_ends_at' => now()->addDays(30),
            ]
        );

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
        $this->info('📤 Sending ALL email types to: ' . $email);
        $this->newLine();
        
        $emails = [
            ['name' => '1️⃣  Welcome Email', 'mail' => new WelcomeMail($user)],
            ['name' => '2️⃣  Verification Email', 'mail' => null, 'custom' => true],
            ['name' => '3️⃣  Trial Ending (7 days)', 'mail' => new TrialEndingInSevenDaysMail($user)],
            ['name' => '4️⃣  Trial Ending (1 day)', 'mail' => new TrialEndingTomorrowMail($user)],
            ['name' => '5️⃣  Payment Success', 'mail' => new PaymentSuccessMail($user, 29.00, now()->addYear()->format('d.m.Y'))],
        ];
        
        foreach ($emails as $emailData) {
            $this->line($emailData['name']);
            
            if (isset($emailData['custom']) && $emailData['custom']) {
                $this->sendVerificationEmail($email, $user);
            } else {
                Mail::to($email)->send($emailData['mail']);
                $this->call('queue:work', ['--once' => true, '--tries' => 1, '--quiet' => true]);
            }
            
            $this->line('  ✅ Sent');
            
            // Add delay to avoid rate limiting
            sleep(2);
            $this->newLine();
        }
        
        $this->info('🎉 All emails sent successfully!');
        $this->line('🔗 Check Mailtrap: https://mailtrap.io/inboxes');
    }

    /**
     * Send specific email type
     */
    protected function sendSpecificEmail($email, $user, $type)
    {
        $this->info('📤 Sending email to: ' . $email);
        $this->line('  Type: ' . $type);
        
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
            case 'payment':
                Mail::to($email)->send(new PaymentSuccessMail($user, 29.00, now()->addYear()->format('d.m.Y')));
                break;
            case 'verify':
                $this->sendVerificationEmail($email, $user);
                return;
            default:
                $this->error('Unknown email type: ' . $type);
                return;
        }
        
        $this->newLine();
        $this->info('✅ Email queued successfully!');
        $this->line('⏳ Processing queue job...');
        
        $this->call('queue:work', ['--once' => true, '--tries' => 1]);
        
        $this->newLine();
        $this->info('✅ Email sent successfully!');
        $this->line('🔗 Mailtrap: https://mailtrap.io/inboxes');
    }

    /**
     * Send verification email
     */
    protected function sendVerificationEmail($email, $user)
    {
        // Generate verification URL
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );
        
        // Send email directly (not queued)
        Mail::send('emails.verify-email', [
            'user' => $user,
            'verificationUrl' => $verificationUrl,
        ], function ($message) use ($email) {
            $message->to($email)
                    ->subject(__('emails.verify_email_subject'));
        });
    }
}
