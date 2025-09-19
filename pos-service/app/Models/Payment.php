<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'medical_bill_id',
        'payment_reference',
        'amount',
        'payment_method',
        'status',
        'transaction_id',
        'notes',
        'processed_by',
        'payment_date',
        'change_amount',
        'payment_details',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'change_amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'payment_details' => 'array',
    ];

    /**
     * Generate next payment reference
     */
    public static function generatePaymentReference(): string
    {
        $year = now()->year;
        $month = str_pad(now()->month, 2, '0', STR_PAD_LEFT);
        $lastPayment = self::whereYear('created_at', $year)
                          ->whereMonth('created_at', now()->month)
                          ->orderBy('id', 'desc')
                          ->first();
        
        $sequence = $lastPayment ? intval(substr($lastPayment->payment_reference, -4)) + 1 : 1;
        
        return 'PAY-' . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Relationships
     */
    public function medicalBill(): BelongsTo
    {
        return $this->belongsTo(MedicalBill::class);
    }

    /**
     * Scopes
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('payment_date', today());
    }

    public function scopeByMethod($query, string $method)
    {
        return $query->where('payment_method', $method);
    }

    /**
     * Accessors
     */
    public function getFormattedAmountAttribute(): string
    {
        return '₱' . number_format($this->amount, 2);
    }

    public function getFormattedChangeAmountAttribute(): string
    {
        return '₱' . number_format($this->change_amount, 2);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'completed' => '<span class="badge bg-success">Completed</span>',
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'failed' => '<span class="badge bg-danger">Failed</span>',
            'refunded' => '<span class="badge bg-info">Refunded</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    public function getPaymentMethodBadgeAttribute(): string
    {
        return match($this->payment_method) {
            'cash' => '<span class="badge bg-success"><i class="bi bi-cash me-1"></i>Cash</span>',
            'credit_card' => '<span class="badge bg-info"><i class="bi bi-credit-card me-1"></i>Card</span>',
            'gcash' => '<span class="badge bg-warning"><i class="bi bi-phone me-1"></i>GCash</span>',
            'paymaya' => '<span class="badge bg-primary"><i class="bi bi-phone me-1"></i>PayMaya</span>',
            'insurance' => '<span class="badge bg-secondary"><i class="bi bi-shield-check me-1"></i>Insurance</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }
}
