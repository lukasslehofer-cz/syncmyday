<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
    use HasFactory;

    const UPDATED_AT = null; // We only track created_at for logs

    protected $fillable = [
        'user_id',
        'sync_rule_id',
        'action',
        'direction',
        'source_event_id',
        'target_event_id',
        'event_start',
        'event_end',
        'error_message',
        'transaction_id',
    ];

    protected $casts = [
        'event_start' => 'datetime',
        'event_end' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function syncRule()
    {
        return $this->belongsTo(SyncRule::class);
    }

    /**
     * Create a log entry
     */
    public static function logSync(
        int $userId,
        ?int $syncRuleId,
        string $action,
        ?string $direction = null,
        ?string $sourceEventId = null,
        ?string $targetEventId = null,
        ?\DateTime $eventStart = null,
        ?\DateTime $eventEnd = null,
        ?string $errorMessage = null,
        ?string $transactionId = null
    ): self {
        // Handle MySQL TIMESTAMP Y2038 problem (max date: 2038-01-19)
        // For dates beyond this, store as null to avoid DB errors
        $maxTimestamp = new \DateTime('2038-01-01');
        
        if ($eventStart && $eventStart > $maxTimestamp) {
            $eventStart = null;
        }
        
        if ($eventEnd && $eventEnd > $maxTimestamp) {
            $eventEnd = null;
        }
        
        try {
            return self::create([
                'user_id' => $userId,
                'sync_rule_id' => $syncRuleId,
                'action' => $action,
                'direction' => $direction,
                'source_event_id' => $sourceEventId,
                'target_event_id' => $targetEventId,
                'event_start' => $eventStart,
                'event_end' => $eventEnd,
                'error_message' => $errorMessage,
                'transaction_id' => $transactionId,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Race condition: Sync rule was deleted during async processing
            // Try again with sync_rule_id = null (foreign key constraint)
            if ($e->getCode() === '23000' && $syncRuleId !== null) {
                \Illuminate\Support\Facades\Log::warning('Sync rule deleted during logging, retrying with sync_rule_id = null', [
                    'sync_rule_id' => $syncRuleId,
                    'user_id' => $userId,
                    'action' => $action,
                    'error' => $e->getMessage(),
                ]);
                
                return self::create([
                    'user_id' => $userId,
                    'sync_rule_id' => null, // Set to null to satisfy foreign key
                    'action' => $action,
                    'direction' => $direction,
                    'source_event_id' => $sourceEventId,
                    'target_event_id' => $targetEventId,
                    'event_start' => $eventStart,
                    'event_end' => $eventEnd,
                    'error_message' => $errorMessage ? "Sync rule deleted during processing. Original error: {$errorMessage}" : 'Sync rule deleted during processing',
                    'transaction_id' => $transactionId,
                ]);
            }
            
            // Re-throw if it's a different error
            throw $e;
        }
    }

    /**
     * Scope: recent logs (last 100)
     */
    public function scopeRecent($query, int $limit = 100)
    {
        return $query->orderBy('created_at', 'desc')->limit($limit);
    }

    /**
     * Scope: errors only
     */
    public function scopeErrors($query)
    {
        return $query->where('action', 'error');
    }
}

