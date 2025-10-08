<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\ConnectionsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OAuth\OAuthController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\SyncRulesController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home & Public pages
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// OAuth
Route::middleware('auth')->prefix('oauth')->group(function () {
    Route::get('/google', [OAuthController::class, 'redirectToGoogle'])->name('oauth.google');
    Route::get('/google/callback', [OAuthController::class, 'handleGoogleCallback'])->name('oauth.google.callback');
    Route::get('/microsoft', [OAuthController::class, 'redirectToMicrosoft'])->name('oauth.microsoft');
    Route::get('/microsoft/callback', [OAuthController::class, 'handleMicrosoftCallback'])->name('oauth.microsoft.callback');
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
        Route::delete('/{emailCalendar}', [\App\Http\Controllers\EmailCalendarController::class, 'destroy'])->name('destroy');
        Route::get('/{emailCalendar}/test', [\App\Http\Controllers\EmailCalendarController::class, 'test'])->name('test');
        Route::post('/{emailCalendar}/test', [\App\Http\Controllers\EmailCalendarController::class, 'processTest'])->name('test.process');
    });

    // Onboarding
    Route::prefix('onboarding')->name('onboarding.')->group(function () {
        Route::get('/start', [OnboardingController::class, 'start'])->name('start');
        Route::get('/connect-calendars', [OnboardingController::class, 'connectCalendars'])->name('connect-calendars');
        Route::get('/create-rule', [OnboardingController::class, 'createRule'])->name('create-rule');
        Route::post('/complete', [OnboardingController::class, 'complete'])->name('complete');
    });

    // Calendar connections
    Route::prefix('connections')->name('connections.')->group(function () {
        Route::get('/', [ConnectionsController::class, 'index'])->name('index');
        Route::delete('/{connection}', [ConnectionsController::class, 'destroy'])->name('destroy');
        Route::post('/{connection}/refresh', [ConnectionsController::class, 'refresh'])->name('refresh');
    });

    // Sync rules
    Route::prefix('sync-rules')->name('sync-rules.')->group(function () {
        Route::get('/', [SyncRulesController::class, 'index'])->name('index');
        Route::get('/create', [SyncRulesController::class, 'create'])->name('create');
        Route::post('/', [SyncRulesController::class, 'store'])->name('store');
        Route::post('/{rule}/toggle', [SyncRulesController::class, 'toggle'])->name('toggle');
        Route::delete('/{rule}', [SyncRulesController::class, 'destroy'])->name('destroy');
    });

    // Billing
    Route::prefix('billing')->name('billing.')->group(function () {
        Route::get('/', [BillingController::class, 'index'])->name('index');
        Route::post('/checkout', [BillingController::class, 'createCheckoutSession'])->name('checkout');
        Route::get('/success', [BillingController::class, 'success'])->name('success');
        Route::get('/portal', [BillingController::class, 'portal'])->name('portal');
    });
    Route::get('/billing', [BillingController::class, 'index'])->name('billing'); // Shorthand

    // Admin routes
    Route::middleware('can:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/users/{user}', [AdminController::class, 'userDetails'])->name('user-details');
        Route::get('/connections', [AdminController::class, 'connections'])->name('connections');
        Route::get('/webhooks', [AdminController::class, 'webhooks'])->name('webhooks');
        Route::get('/logs', [AdminController::class, 'logs'])->name('logs');
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

// Health check
Route::get('/health', [AdminController::class, 'health'])->name('health');

