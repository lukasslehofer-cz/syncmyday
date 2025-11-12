<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class EmailCalendarConnection extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'user_id',
        'email_address',
        'email_token',
        'name',
        'target_email',
        'target_email_verified_at',
        'emails_received',
        'events_processed',
        'last_email_at',
        'status',
        'last_error',
    ];

    protected $casts = [
        'last_email_at' => 'datetime',
        'target_email_verified_at' => 'datetime',
        'emails_received' => 'integer',
        'events_processed' => 'integer',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function syncRulesAsSource()
    {
        return $this->hasMany(SyncRule::class, 'source_email_connection_id');
    }
    
    public function syncRuleTargets()
    {
        return $this->hasMany(SyncRuleTarget::class, 'target_email_connection_id');
    }

    public function eventMappings()
    {
        return $this->hasMany(SyncEventMapping::class, 'email_connection_id');
    }

    /**
     * Generate unique email address for this connection
     * Uses user's registration_domain to ensure email address matches their domain
     */
    public static function generateUniqueEmailAddress(User $user): array
    {
        $maxAttempts = 10;
        
        // Get domain from user's registration_domain or derive from locale
        $domain = $user->registration_domain ?? \App\Helpers\EmailHelper::getDomainFromLocale($user->locale ?? 'en');
        
        for ($i = 0; $i < $maxAttempts; $i++) {
            $token = strtolower(Str::random(8)); // Short, easy to type, always lowercase
            $emailAddress = $token . '@' . $domain;
            
            if (!self::where('email_address', $emailAddress)->exists()) {
                return [
                    'email_address' => $emailAddress,
                    'email_token' => $token,
                ];
            }
        }
        
        throw new \Exception('Failed to generate unique email address after ' . $maxAttempts . ' attempts');
    }

    /**
     * Find connection by email token (case-insensitive for backwards compatibility)
     */
    public static function findByToken(string $token): ?self
    {
        // Normalize to lowercase for search (backwards compatible with old mixed-case tokens)
        $token = strtolower($token);
        
        return self::whereRaw('LOWER(email_token) = ?', [$token])
            ->where('status', 'active')
            ->first();
    }

    /**
     * Increment stats
     */
    public function incrementEmailReceived(): void
    {
        $this->increment('emails_received');
        $this->update(['last_email_at' => now()]);
    }

    public function incrementEventProcessed(): void
    {
        $this->increment('events_processed');
    }

    /**
     * Mark as error
     */
    public function markAsError(string $error): void
    {
        $this->update([
            'status' => 'error',
            'last_error' => $error,
        ]);
    }

    /**
     * Mark as active
     */
    public function markAsActive(): void
    {
        $this->update([
            'status' => 'active',
            'last_error' => null,
        ]);
    }

    /**
     * Get all sync rules where this email calendar is involved (as source or target)
     */
    public function getAllSyncRules()
    {
        $asSource = $this->syncRulesAsSource;
        $asTarget = SyncRule::whereHas('targets', function ($query) {
            $query->where('target_email_connection_id', $this->id);
        })->get();
        
        return $asSource->merge($asTarget)->unique('id');
    }

    /**
     * Check if target email has been verified
     */
    public function hasVerifiedTargetEmail(): bool
    {
        return !is_null($this->target_email_verified_at);
    }

    /**
     * Mark target email as verified
     */
    public function markTargetEmailAsVerified(): bool
    {
        return $this->forceFill([
            'target_email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Send target email verification notification
     */
    public function sendTargetEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifyEmailCalendarNotification());
    }

    /**
     * Get the notification routing information for mail channel
     */
    public function routeNotificationForMail($notification)
    {
        return $this->target_email;
    }
}

