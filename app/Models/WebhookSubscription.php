<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'calendar_connection_id',
        'provider_subscription_id',
        'resource_id',
        'calendar_id',
        'expires_at',
        'renewed_at',
        'sync_token',
        'status',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'renewed_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function calendarConnection()
    {
        return $this->belongsTo(CalendarConnection::class);
    }

    /**
     * Check if subscription is expired or about to expire (within 24 hours)
     */
    public function isExpiringSoon(): bool
    {
        return $this->expires_at->subHours(24)->isPast();
    }

    /**
     * Check if subscription is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Scope: active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                     ->where('expires_at', '>', now());
    }

    /**
     * Scope: expiring soon
     */
    public function scopeExpiringSoon($query)
    {
        return $query->where('status', 'active')
                     ->where('expires_at', '<=', now()->addHours(24));
    }
}

