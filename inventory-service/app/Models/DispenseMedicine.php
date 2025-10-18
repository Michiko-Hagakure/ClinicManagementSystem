<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DispenseMedicine extends Model
{
    use HasFactory;

    protected $table = 'dispense_medicine';
    protected $primaryKey = 'dispense_id';

    protected $fillable = [
        'patient_id',
        'medicine_id',
        'quantity',
        'date'
    ];

    protected $casts = [
        'date' => 'datetime',
        'quantity' => 'integer',
        'patient_id' => 'integer'
    ];

    /**
     * Relationship with Medicine
     */
    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'medicine_id', 'medicine_id');
    }

    /**
     * Get the total cost for this dispensed medicine
     */
    public function getTotalCostAttribute()
    {
        return $this->quantity * ($this->medicine->price ?? 0);
    }

    /**
     * Get formatted total cost
     */
    public function getFormattedTotalCostAttribute()
    {
        return '₱' . number_format($this->total_cost, 2);
    }

    /**
     * Scope for filtering by patient
     */
    public function scopeForPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('date', [$from, $to]);
    }

    /**
     * Scope for recent dispensations
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('date', '>=', now()->subDays($days));
    }
}
