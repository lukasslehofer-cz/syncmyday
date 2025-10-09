<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'locale',
        'timezone',
        'subscription_tier',
        'stripe_customer_id',
        'stripe_subscription_id',
        'subscription_ends_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'is_admin' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function calendarConnections()
    {
        return $this->hasMany(CalendarConnection::class);
    }

    public function emailCalendarConnections()
    {
        return $this->hasMany(EmailCalendarConnection::class);
    }

    public function syncRules()
    {
        return $this->hasMany(SyncRule::class);
    }

    public function syncLogs()
    {
        return $this->hasMany(SyncLog::class);
    }

    /**
     * Check if user has an active pro subscription
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscription_tier === 'pro' && 
               (!$this->subscription_ends_at || $this->subscription_ends_at->isFuture());
    }

    /**
     * Check if user is in trial period
     */
    public function isInTrial(): bool
    {
        return $this->subscription_tier === 'pro' && 
               $this->subscription_ends_at && 
               $this->subscription_ends_at->isFuture() && 
               !$this->stripe_subscription_id;
    }

    /**
     * Get remaining trial days
     */
    public function getRemainingTrialDays(): int
    {
        if (!$this->isInTrial()) {
            return 0;
        }

        return max(0, now()->diffInDays($this->subscription_ends_at, false));
    }

    /**
     * Check if trial is expiring soon (within 3 days)
     */
    public function isTrialExpiringSoon(): bool
    {
        if (!$this->isInTrial()) {
            return false;
        }

        $remainingDays = $this->getRemainingTrialDays();
        return $remainingDays <= 3 && $remainingDays > 0;
    }

    /**
     * Check if user can create more sync rules
     */
    public function canCreateSyncRule(): bool
    {
        // Pro users (including trial) have unlimited rules
        return $this->hasActiveSubscription();
    }

    /**
     * Expire the trial and downgrade to blocked state
     */
    public function expireTrial(): void
    {
        $this->update([
            'subscription_tier' => 'free',
            'subscription_ends_at' => now(),
        ]);
    }

}

