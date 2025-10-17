<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\ConnectionsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OAuth\OAuthController;
use App\Http\Controllers\CalDavController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\SyncRulesController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\EmailWebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home & Public pages
Route::get('/', function () {
    return view('welcome', [
        'formattedPrice' => \App\Helpers\PricingHelper::formatPrice(),
    ]);
})->name('home');

// Public legal pages
Route::view('/privacy', 'legal.privacy')->name('privacy');
Route::view('/terms', 'legal.terms')->name('terms');

// Contact
Route::get('/contact', [App\Http\Controllers\ContactController::class, 'show'])->name('contact');
Route::post('/contact', [App\Http\Controllers\ContactController::class, 'send'])->name('contact.send');

// Feedback (for deleted accounts)
Route::get('/feedback', [App\Http\Controllers\FeedbackController::class, 'show'])->name('feedback');
Route::post('/feedback', [App\Http\Controllers\FeedbackController::class, 'send'])->name('feedback.send');

// Help Center
Route::prefix('help')->name('help.')->group(function () {
    Route::get('/', function () {
        $locale = app()->getLocale();
        return view("help.index.{$locale}", [], ['locale' => $locale])
            ->render();
    })->name('index')->fallback(function () {
        return view('help.index.en');
    });
    
    Route::get('/faq', function () {
        $locale = app()->getLocale();
        try {
            return view("help.faq.{$locale}");
        } catch (\Exception $e) {
            return view('help.faq.en');
        }
    })->name('faq');
    
    Route::get('/connect-google', function () {
        $locale = app()->getLocale();
        try {
            return view("help.connect-google.{$locale}");
        } catch (\Exception $e) {
            return view('help.connect-google.en');
        }
    })->name('connect-google');
    
    Route::get('/connect-microsoft', function () {
        $locale = app()->getLocale();
        try {
            return view("help.connect-microsoft.{$locale}");
        } catch (\Exception $e) {
            return view('help.connect-microsoft.en');
        }
    })->name('connect-microsoft');
    
    Route::get('/connect-apple', function () {
        $locale = app()->getLocale();
        try {
            return view("help.connect-apple.{$locale}");
        } catch (\Exception $e) {
            return view('help.connect-apple.en');
        }
    })->name('connect-apple');
    
    Route::get('/connect-caldav', function () {
        $locale = app()->getLocale();
        try {
            return view("help.connect-caldav.{$locale}");
        } catch (\Exception $e) {
            return view('help.connect-caldav.en');
        }
    })->name('connect-caldav');
    
    Route::get('/connect-email', function () {
        $locale = app()->getLocale();
        try {
            return view("help.connect-email.{$locale}");
        } catch (\Exception $e) {
            return view('help.connect-email.en');
        }
    })->name('connect-email');
    
    Route::get('/sync-rules', function () {
        $locale = app()->getLocale();
        try {
            return view("help.sync-rules.{$locale}");
        } catch (\Exception $e) {
            return view('help.sync-rules.en');
        }
    })->name('sync-rules');
});

// Blog
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', [App\Http\Controllers\BlogController::class, 'index'])->name('index');
    Route::get('/category/{slug}', [App\Http\Controllers\BlogController::class, 'category'])->name('category');
    Route::get('/{slug}', [App\Http\Controllers\BlogController::class, 'show'])->name('show');
});

// Debug routes for session testing (REMOVE IN PRODUCTION!)
Route::get('/debug-session', function () {
    return response()->json([
        'session_id' => session()->getId(),
        'session_driver' => config('session.driver'),
        'session_secure' => config('session.secure'),
        'session_domain' => config('session.domain'),
        'session_same_site' => config('session.same_site'),
        'app_url' => config('app.url'),
        'app_env' => config('app.env'),
        'https' => request()->secure(),
        'test_value' => session('test_key', 'not_set'),
        'storage_writable' => is_writable(storage_path('framework/sessions')),
    ]);
});

Route::get('/debug-session-set', function () {
    session(['test_key' => 'session_works_' . now()]);
    return 'Session value set. Visit /debug-session to verify.';
});

// Authentication
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    // OAuth Login/Registration (Google & Microsoft)
    Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
    Route::get('/auth/microsoft', [SocialAuthController::class, 'redirectToMicrosoft'])->name('auth.microsoft');
    Route::get('/auth/microsoft/callback', [SocialAuthController::class, 'handleMicrosoftCallback'])->name('auth.microsoft.callback');
    
    // Password Reset
    Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Email Verification
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::get('/email/verified', [EmailVerificationController::class, 'success'])->name('verification.success');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.resend');
});

// Email Calendar Verification (public route - user clicks link from their email)
Route::get('/email-calendars/verify/{id}/{hash}', [\App\Http\Controllers\EmailCalendarVerificationController::class, 'verify'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('email-calendars.verify');

// OAuth
Route::middleware('auth')->prefix('oauth')->group(function () {
    Route::get('/google', [OAuthController::class, 'redirectToGoogle'])->name('oauth.google');
    Route::get('/google/callback', [OAuthController::class, 'handleGoogleCallback'])->name('oauth.google.callback');
    Route::get('/microsoft', [OAuthController::class, 'redirectToMicrosoft'])->name('oauth.microsoft');
    Route::get('/microsoft/callback', [OAuthController::class, 'handleMicrosoftCallback'])->name('oauth.microsoft.callback');
    Route::get('/complete', [OAuthController::class, 'showCompleteForm'])->name('oauth.complete');
    Route::post('/complete', [OAuthController::class, 'completeSetup'])->name('oauth.complete.submit');
});

// CalDAV
Route::middleware('auth')->prefix('caldav')->name('caldav.')->group(function () {
    Route::get('/setup', [CalDavController::class, 'showSetup'])->name('setup');
    Route::post('/test', [CalDavController::class, 'testConnection'])->name('test');
    Route::get('/select-calendars', [CalDavController::class, 'showSelectCalendars'])->name('select-calendars');
    Route::post('/complete', [CalDavController::class, 'complete'])->name('complete');
    Route::delete('/{connection}/disconnect', [CalDavController::class, 'disconnect'])->name('disconnect');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Email Calendars
    Route::prefix('email-calendars')->name('email-calendars.')->group(function () {
        Route::get('/', [\App\Http\Controllers\EmailCalendarController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\EmailCalendarController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\EmailCalendarController::class, 'store'])->name('store');
        Route::get('/{emailCalendar}', [\App\Http\Controllers\EmailCalendarController::class, 'show'])->name('show');
        Route::get('/{emailCalendar}/edit', [\App\Http\Controllers\EmailCalendarController::class, 'edit'])->name('edit');
        Route::put('/{emailCalendar}', [\App\Http\Controllers\EmailCalendarController::class, 'update'])->name('update');
        Route::delete('/{emailCalendar}', [\App\Http\Controllers\EmailCalendarController::class, 'destroy'])->name('destroy');
        
        // Email Calendar Verification
        Route::get('/{emailCalendar}/verify-notice', [\App\Http\Controllers\EmailCalendarVerificationController::class, 'notice'])->name('verification.notice');
        Route::get('/{emailCalendar}/verified', [\App\Http\Controllers\EmailCalendarVerificationController::class, 'success'])->name('verification.success');
        Route::post('/{emailCalendar}/resend-verification', [\App\Http\Controllers\EmailCalendarVerificationController::class, 'resend'])
            ->middleware('throttle:6,1')
            ->name('verification.resend');
        Route::get('/{emailCalendar}/test', [\App\Http\Controllers\EmailCalendarController::class, 'test'])->name('test');
        Route::post('/{emailCalendar}/test', [\App\Http\Controllers\EmailCalendarController::class, 'processTest'])->name('test.process');
    });

    // Onboarding
    Route::prefix('onboarding')->name('onboarding.')->group(function () {
        Route::get('/start', [OnboardingController::class, 'start'])->name('start');
        Route::get('/connect-calendars', [OnboardingController::class, 'connectCalendars'])->name('connect-calendars');
        Route::get('/create-rule', [OnboardingController::class, 'createRule'])->name('create-rule');
        Route::post('/complete', [OnboardingController::class, 'complete'])->name('complete');
        Route::post('/dismiss', [OnboardingController::class, 'dismissProgress'])->name('dismiss');
    });

    // Calendar connections
    Route::prefix('connections')->name('connections.')->group(function () {
        Route::get('/', [ConnectionsController::class, 'index'])->name('index');
        Route::get('/complete-oauth', [OAuthController::class, 'showCompleteForm'])->name('complete-oauth');
        Route::post('/complete-oauth', [OAuthController::class, 'completeSetup'])->name('complete-oauth.submit');
        Route::get('/{connection}', [ConnectionsController::class, 'show'])->name('show');
        Route::get('/{connection}/edit', [ConnectionsController::class, 'edit'])->name('edit');
        Route::put('/{connection}', [ConnectionsController::class, 'update'])->name('update');
        Route::delete('/{connection}', [ConnectionsController::class, 'destroy'])->name('destroy');
        Route::post('/{connection}/refresh', [ConnectionsController::class, 'refresh'])->name('refresh');
    });

    // Sync rules
    Route::prefix('sync-rules')->name('sync-rules.')->group(function () {
        Route::get('/', [SyncRulesController::class, 'index'])->name('index');
        Route::get('/create', [SyncRulesController::class, 'create'])->name('create');
        Route::post('/', [SyncRulesController::class, 'store'])->name('store');
        Route::get('/{rule}/edit', [SyncRulesController::class, 'edit'])->name('edit');
        Route::put('/{rule}', [SyncRulesController::class, 'update'])->name('update');
        Route::post('/{rule}/toggle', [SyncRulesController::class, 'toggle'])->name('toggle');
        Route::delete('/{rule}', [SyncRulesController::class, 'destroy'])->name('destroy');
    });

    // Billing
    Route::prefix('billing')->name('billing.')->group(function () {
        Route::get('/', [BillingController::class, 'index'])->name('index');
        Route::post('/checkout', [BillingController::class, 'createCheckoutSession'])->name('checkout');
        Route::get('/success', [BillingController::class, 'success'])->name('success');
        Route::get('/manage', [BillingController::class, 'manage'])->name('manage');
        Route::post('/update-payment-method', [BillingController::class, 'updatePaymentMethod'])->name('update-payment-method');
        Route::post('/cancel', [BillingController::class, 'cancelSubscription'])->name('cancel');
        Route::post('/reactivate', [BillingController::class, 'reactivateSubscription'])->name('reactivate');
        Route::get('/reactivate-with-payment', [BillingController::class, 'reactivateWithPayment'])->name('reactivate-with-payment');
    });
    Route::get('/billing', [BillingController::class, 'index'])->name('billing'); // Shorthand

    // Account Settings
    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/', [AccountController::class, 'index'])->name('index');
        Route::put('/info', [AccountController::class, 'updateInfo'])->name('update-info');
        Route::put('/password', [AccountController::class, 'updatePassword'])->name('update-password');
        Route::post('/add-password', [AccountController::class, 'addPassword'])->name('add-password');
        Route::post('/disconnect-oauth', [AccountController::class, 'disconnectOAuth'])->name('disconnect-oauth');
        Route::delete('/delete', [AccountController::class, 'destroy'])->name('destroy');
        
        // OAuth connection from account settings (for adding backup login methods)
        Route::get('/connect/google', [OAuthController::class, 'redirectToGoogle'])->name('connect.google');
        Route::get('/connect/microsoft', [OAuthController::class, 'redirectToMicrosoft'])->name('connect.microsoft');
    });

    // Admin routes
    Route::middleware('can:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/users/{user}', [AdminController::class, 'userDetails'])->name('user-details');
        Route::get('/connections', [AdminController::class, 'connections'])->name('connections');
        Route::get('/webhooks', [AdminController::class, 'webhooks'])->name('webhooks');
        Route::get('/logs', [AdminController::class, 'logs'])->name('logs');
        
        // Blog admin
        Route::prefix('blog')->name('blog.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\BlogAdminController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\BlogAdminController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Admin\BlogAdminController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [\App\Http\Controllers\Admin\BlogAdminController::class, 'edit'])->name('edit');
            Route::put('/{id}', [\App\Http\Controllers\Admin\BlogAdminController::class, 'update'])->name('update');
            Route::delete('/{id}', [\App\Http\Controllers\Admin\BlogAdminController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/toggle-publish', [\App\Http\Controllers\Admin\BlogAdminController::class, 'togglePublish'])->name('toggle-publish');
        });
    });
});

// Webhooks (no auth middleware - validated internally)
Route::match(['get', 'post'], '/webhooks/google/{connectionId}', [WebhookController::class, 'google'])
    ->name('webhooks.google');
Route::post('/webhooks/microsoft/{connectionId}', [WebhookController::class, 'microsoft'])
    ->name('webhooks.microsoft');

// Stripe webhooks
Route::post('/webhooks/stripe', [BillingController::class, 'webhook'])
    ->name('webhooks.stripe');

// Email webhooks (for inbound calendar emails)
Route::post('/webhooks/email/mailgun', [EmailWebhookController::class, 'mailgun'])
    ->name('webhooks.email.mailgun');
Route::post('/webhooks/email/sendgrid', [EmailWebhookController::class, 'sendgrid'])
    ->name('webhooks.email.sendgrid');
Route::post('/webhooks/email/postmark', [EmailWebhookController::class, 'postmark'])
    ->name('webhooks.email.postmark');

// Health check
Route::get('/health', [AdminController::class, 'health'])->name('health');

// Cron endpoint (for shared hosting)
Route::get('/cron/run', [\App\Http\Controllers\CronController::class, 'run'])->name('cron.run');

