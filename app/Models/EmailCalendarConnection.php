<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmailCalendarConnection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email_address',
        'email_token',
        'name',
        'target_email',
        'description',
        'sender_whitelist',
        'emails_received',
        'events_processed',
        'last_email_at',
        'status',
        'last_error',
    ];

    protected $casts = [
        'sender_whitelist' => 'array',
        'last_email_at' => 'datetime',
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
     */
    public static function generateUniqueEmailAddress(): array
    {
        $maxAttempts = 10;
        
        for ($i = 0; $i < $maxAttempts; $i++) {
            $token = Str::random(8); // Short, easy to type
            $emailAddress = $token . '@' . config('app.email_domain', 'syncmyday.com');
            
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
     * Find connection by email token
     */
    public static function findByToken(string $token): ?self
    {
        return self::where('email_token', $token)
            ->where('status', 'active')
            ->first();
    }

    /**
     * Check if sender is whitelisted
     */
    public function isSenderAllowed(string $senderEmail): bool
    {
        // If no whitelist, allow all
        if (empty($this->sender_whitelist)) {
            return true;
        }

        $senderEmail = strtolower(trim($senderEmail));
        
        foreach ($this->sender_whitelist as $allowed) {
            $allowed = strtolower(trim($allowed));
            
            // Exact match
            if ($senderEmail === $allowed) {
                return true;
            }
            
            // Wildcard domain match (e.g., *@company.com)
            if (str_starts_with($allowed, '*@')) {
                $domain = substr($allowed, 2);
                if (str_ends_with($senderEmail, '@' . $domain)) {
                    return true;
                }
            }
        }
        
        return false;
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
}

