<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalBill extends Model
{
    protected $fillable = [
        'bill_number',
        'patient_id',
        'subtotal',
        'discount',
        'tax',
        'total_amount',
        'status',
        'payment_method',
        'notes',
        'cashier_name',
        'bill_date',
        'due_date',
        'paid_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'bill_date' => 'datetime',
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
    ];

    /**
     * Generate next bill number
     */
    public static function generateBillNumber(): string
    {
        $year = now()->year;
        $month = str_pad(now()->month, 2, '0', STR_PAD_LEFT);
        $lastBill = self::whereYear('created_at', $year)
                       ->whereMonth('created_at', now()->month)
                       ->orderBy('id', 'desc')
                       ->first();
        
        $sequence = $lastBill ? intval(substr($lastBill->bill_number, -4)) + 1 : 1;
        
        return 'BILL-' . $year . $month . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Relationships
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BillItem::class);
    }

    public function billItems(): HasMany
    {
        return $this->hasMany(BillItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('bill_date', today());
    }

    /**
     * Accessors
     */
    public function getFormattedTotalAttribute(): string
    {
        return '₱' . number_format($this->total_amount, 2);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'paid' => '<span class="badge bg-success">Paid</span>',
            'pending' => '<span class="badge bg-warning">Pending</span>',
            'partially_paid' => '<span class="badge bg-info">Partially Paid</span>',
            'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }
}
