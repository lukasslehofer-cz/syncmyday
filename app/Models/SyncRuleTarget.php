<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyncRuleTarget extends Model
{
    use HasFactory;

    protected $fillable = [
        'sync_rule_id',
        'target_connection_id',
        'target_email_connection_id',
        'target_calendar_id',
    ];

    /**
     * Relationships
     */
    public function syncRule()
    {
        return $this->belongsTo(SyncRule::class);
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
     * Get the target (either CalendarConnection or EmailCalendarConnection)
     */
    public function getTarget()
    {
        return $this->targetConnection ?? $this->targetEmailConnection;
    }
    
    /**
     * Check if target is an email calendar
     */
    public function isEmailTarget(): bool
    {
        return !is_null($this->target_email_connection_id);
    }
}

