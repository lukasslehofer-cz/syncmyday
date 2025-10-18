<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parent_rule_id',
        'name',
        'source_connection_id',
        'source_email_connection_id',
        'source_calendar_id',
        'direction',
        'filters',
        'blocker_title',
        'is_active',
        'initial_sync_completed',
        'last_triggered_at',
        'time_filter_enabled',
        'time_filter_type',
        'time_filter_start',
        'time_filter_end',
        'time_filter_days',
    ];

    protected $casts = [
        'filters' => 'array',
        'time_filter_days' => 'array',
        'is_active' => 'boolean',
        'initial_sync_completed' => 'boolean',
        'time_filter_enabled' => 'boolean',
        'last_triggered_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sourceConnection()
    {
        return $this->belongsTo(CalendarConnection::class, 'source_connection_id');
    }
    
    public function sourceEmailConnection()
    {
        return $this->belongsTo(EmailCalendarConnection::class, 'source_email_connection_id');
    }
    
    /**
     * Get the source (either CalendarConnection or EmailCalendarConnection)
     */
    public function getSource()
    {
        return $this->sourceConnection ?? $this->sourceEmailConnection;
    }
    
    /**
     * Check if source is an email calendar
     */
    public function isEmailSource(): bool
    {
        return !is_null($this->source_email_connection_id);
    }

    public function targets()
    {
        return $this->hasMany(SyncRuleTarget::class);
    }

    public function syncLogs()
    {
        return $this->hasMany(SyncLog::class);
    }

    /**
     * Get filter value
     */
    public function getFilter(string $key, $default = null)
    {
        return data_get($this->filters, $key, $default);
    }

    /**
     * Check if event should be synced based on filters
     */
    public function shouldSyncEvent(array $event): bool
    {
        // Only sync "busy" events if filter is enabled
        if ($this->getFilter('busy_only', true)) {
            $showAs = $event['showAs'] ?? $event['busyStatus'] ?? 'busy';
            if ($showAs !== 'busy') {
                return false;
            }
        }

        // Ignore all-day events if filter is enabled
        if ($this->getFilter('ignore_all_day', false)) {
            if ($event['isAllDay'] ?? false) {
                return false;
            }
        }

        // Time filter (new advanced filter)
        if ($this->time_filter_enabled) {
            $eventStart = \Carbon\Carbon::parse($event['start']);
            
            // Check day of week (1=Monday, 7=Sunday)
            if (!empty($this->time_filter_days)) {
                $dayOfWeek = $eventStart->dayOfWeekIso; // 1-7
                if (!in_array($dayOfWeek, $this->time_filter_days)) {
                    return false;
                }
            }
            
            // Check time range
            if ($this->time_filter_start && $this->time_filter_end) {
                $eventTime = $eventStart->format('H:i:s');
                
                if ($eventTime < $this->time_filter_start || $eventTime >= $this->time_filter_end) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Scope: active rules only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

