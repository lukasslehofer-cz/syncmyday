<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncEventMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'sync_rule_id',
        'source_connection_id',
        'source_type',
        'email_connection_id',
        'source_calendar_id',
        'source_event_id',
        'original_event_uid',
        'target_connection_id',
        'target_email_connection_id',
        'target_calendar_id',
        'target_event_id',
        'event_start',
        'event_end',
        'sequence',
    ];

    protected $casts = [
        'event_start' => 'datetime',
        'event_end' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function syncRule()
    {
        return $this->belongsTo(SyncRule::class);
    }

    public function sourceConnection()
    {
        return $this->belongsTo(CalendarConnection::class, 'source_connection_id');
    }
    
    public function emailConnection()
    {
        return $this->belongsTo(EmailCalendarConnection::class, 'email_connection_id');
    }

    public function targetConnection()
    {
        return $this->belongsTo(CalendarConnection::class, 'target_connection_id');
    }
    
    public function targetEmailConnection()
    {
        return $this->belongsTo(EmailCalendarConnection::class, 'target_email_connection_id');
    }

    /**
     * Find existing mapping for source event -> target calendar
     */
    public static function findMapping(
        int $syncRuleId,
        string $sourceEventId,
        int $targetConnectionId,
        string $targetCalendarId
    ): ?self {
        return self::where('sync_rule_id', $syncRuleId)
            ->where('source_event_id', $sourceEventId)
            ->where('target_connection_id', $targetConnectionId)
            ->where('target_calendar_id', $targetCalendarId)
            ->first();
    }
}

