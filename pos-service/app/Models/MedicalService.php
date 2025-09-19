<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicalService extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'category',
        'subcategory',
        'price',
        'estimated_duration',
        'is_active',
        'department',
        'preparation_notes',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function billItems(): HasMany
    {
        return $this->hasMany(BillItem::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeConsultations($query)
    {
        return $query->where('category', 'consultation');
    }

    public function scopeDiagnostics($query)
    {
        return $query->where('category', 'diagnostic');
    }

    public function scopeMedications($query)
    {
        return $query->where('category', 'medication');
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Accessors
     */
    public function getFormattedPriceAttribute(): string
    {
        return '₱' . number_format($this->price, 2);
    }

    public function getCategoryBadgeAttribute(): string
    {
        return match($this->category) {
            'consultation' => '<span class="badge bg-primary">Consultation</span>',
            'diagnostic' => '<span class="badge bg-info">Diagnostic</span>',
            'medication' => '<span class="badge bg-success">Medication</span>',
            'procedure' => '<span class="badge bg-warning">Procedure</span>',
            default => '<span class="badge bg-secondary">Unknown</span>',
        };
    }

    public function getFormattedDurationAttribute(): string
    {
        if (!$this->estimated_duration) {
            return 'N/A';
        }

        $hours = intval($this->estimated_duration / 60);
        $minutes = $this->estimated_duration % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }

        return $minutes . ' minutes';
    }
}
