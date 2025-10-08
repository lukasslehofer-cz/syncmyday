<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'source_connection_id',
        'source_email_connection_id',
        'source_calendar_id',
        'direction',
        'filters',
        'blocker_title',
        'is_active',
        'last_triggered_at',
    ];

    protected $casts = [
        'filters' => 'array',
        'is_active' => 'boolean',
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

        // Work hours filter (if set)
        if ($workHours = $this->getFilter('work_hours')) {
            // Parse work hours (e.g., "09:00-17:00")
            [$start, $end] = explode('-', $workHours);
            $eventStart = \Carbon\Carbon::parse($event['start']);
            $eventHour = $eventStart->format('H:i');
            
            if ($eventHour < $start || $eventHour > $end) {
                return false;
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

