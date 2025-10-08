<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\Encryption\TokenEncryptionService;

class CalendarConnection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'provider',
        'provider_account_id',
        'provider_email',
        'access_token_encrypted',
        'refresh_token_encrypted',
        'token_expires_at',
        'available_calendars',
        'status',
        'last_error',
        'last_sync_at',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'last_sync_at' => 'datetime',
        'available_calendars' => 'array',
    ];

    protected $hidden = [
        'access_token_encrypted',
        'refresh_token_encrypted',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sourceSyncRules()
    {
        return $this->hasMany(SyncRule::class, 'source_connection_id');
    }

    public function webhookSubscriptions()
    {
        return $this->hasMany(WebhookSubscription::class);
    }

    /**
     * Get decrypted access token
     */
    public function getAccessToken(): string
    {
        $encryptionService = app(TokenEncryptionService::class);
        return $encryptionService->decrypt($this->access_token_encrypted);
    }

    /**
     * Get decrypted refresh token
     */
    public function getRefreshToken(): ?string
    {
        if (!$this->refresh_token_encrypted) {
            return null;
        }
        
        $encryptionService = app(TokenEncryptionService::class);
        return $encryptionService->decrypt($this->refresh_token_encrypted);
    }

    /**
     * Set encrypted access token
     */
    public function setAccessToken(string $token): void
    {
        $encryptionService = app(TokenEncryptionService::class);
        $this->access_token_encrypted = $encryptionService->encrypt($token);
    }

    /**
     * Set encrypted refresh token
     */
    public function setRefreshToken(?string $token): void
    {
        if (!$token) {
            $this->refresh_token_encrypted = null;
            return;
        }
        
        $encryptionService = app(TokenEncryptionService::class);
        $this->refresh_token_encrypted = $encryptionService->encrypt($token);
    }

    /**
     * Check if token is expired or about to expire (within 5 minutes)
     */
    public function isTokenExpired(): bool
    {
        if (!$this->token_expires_at) {
            return false;
        }
        
        return $this->token_expires_at->subMinutes(5)->isPast();
    }

    /**
     * Check if connection is healthy
     */
    public function isHealthy(): bool
    {
        return $this->status === 'active' && !$this->isTokenExpired();
    }
}

