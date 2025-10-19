<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FakturoidInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'fakturoid_id',
        'fakturoid_number',
        'stripe_invoice_id',
        'amount',
        'currency',
        'language',
        'description',
        'issued_at',
        'pdf_url',
        'status',
        'error_message',
        'retry_count',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'issued_at' => 'datetime',
        'retry_count' => 'integer',
    ];

    /**
     * Get the user that owns the invoice
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if invoice creation failed
     */
    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if invoice was successfully created
     */
    public function isCreated(): bool
    {
        return $this->status === 'created' && $this->fakturoid_id !== null;
    }

    /**
     * Check if invoice is pending creation
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if invoice can be retried
     */
    public function canRetry(): bool
    {
        return ($this->status === 'failed' || $this->status === 'pending') 
               && $this->retry_count < 5;
    }

    /**
     * Format amount with currency
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . ' ' . strtoupper($this->currency);
    }

    /**
     * Scope for pending invoices that need retry
     */
    public function scopeNeedsRetry($query)
    {
        return $query->whereIn('status', ['pending', 'failed'])
            ->where('retry_count', '<', 5)
            ->where('created_at', '>', now()->subDays(7)); // Only retry for 7 days
    }
}

