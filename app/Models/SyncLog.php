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

