<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
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
     * Check if user can create more sync rules
     */
    public function canCreateSyncRule(): bool
    {
        if ($this->subscription_tier === 'pro') {
            return true;
        }
        
        // Free tier: max 1 rule
        return $this->syncRules()->count() < 1;
    }
}

