<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillItem extends Model
{
    protected $fillable = [
        'medical_bill_id',
        'medical_service_id',
        'service_name',
        'service_category',
        'quantity',
        'unit_price',
        'total_price',
        'notes',
        'performed_by',
        'service_date',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'service_date' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function medicalBill(): BelongsTo
    {
        return $this->belongsTo(MedicalBill::class);
    }

    public function medicalService(): BelongsTo
    {
        return $this->belongsTo(MedicalService::class);
    }

    /**
     * Accessors
     */
    public function getFormattedUnitPriceAttribute(): string
    {
        return '₱' . number_format($this->unit_price, 2);
    }

    public function getFormattedTotalPriceAttribute(): string
    {
        return '₱' . number_format($this->total_price, 2);
    }

    public function getCategoryBadgeAttribute(): string
    {
        return match($this->service_category) {
            'consultation' => '<span class="badge bg-primary">Consultation</span>',
            'diagnostic' => '<span class="badge bg-info">Diagnostic</span>',
            'medication' => '<span class="badge bg-success">Medication</span>',
            'procedure' => '<span class="badge bg-warning">Procedure</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    /**
     * Mutators
     */
    public function setQuantityAttribute($value)
    {
        $this->attributes['quantity'] = max(1, intval($value));
        $this->calculateTotal();
    }

    public function setUnitPriceAttribute($value)
    {
        $this->attributes['unit_price'] = $value;
        $this->calculateTotal();
    }

    /**
     * Calculate total price based on quantity and unit price
     */
    protected function calculateTotal()
    {
        if (isset($this->attributes['quantity']) && isset($this->attributes['unit_price'])) {
            $this->attributes['total_price'] = $this->attributes['quantity'] * $this->attributes['unit_price'];
        }
    }
}
