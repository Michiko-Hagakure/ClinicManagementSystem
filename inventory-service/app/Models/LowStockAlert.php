<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LowStockAlert extends Model
{
    use HasFactory;

    protected $table = 'low_stock_alerts';
    protected $primaryKey = 'alert_id';

    protected $fillable = [
        'medicine_id',
        'alert_date',
        'threshold'
    ];

    protected $casts = [
        'alert_date' => 'datetime',
        'threshold' => 'integer'
    ];

    /**
     * Relationship with Medicine
     */
    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'medicine_id', 'medicine_id');
    }

    /**
     * Create alert for low stock medicine
     */
    public static function createAlert($medicineId, $threshold = 10)
    {
        // Check if alert already exists for this medicine
        $existingAlert = self::where('medicine_id', $medicineId)->first();
        
        if (!$existingAlert) {
            return self::create([
                'medicine_id' => $medicineId,
                'alert_date' => now(),
                'threshold' => $threshold
            ]);
        }
        
        return $existingAlert;
    }

    /**
     * Remove alert when stock is replenished
     */
    public static function removeAlert($medicineId)
    {
        return self::where('medicine_id', $medicineId)->delete();
    }

    /**
     * Get all active alerts
     */
    public function scopeActive($query)
    {
        return $query->join('medicine', 'low_stock_alerts.medicine_id', '=', 'medicine.medicine_id')
                    ->where('medicine.stock_quantity', '<=', 'low_stock_alerts.threshold');
    }

    /**
     * Scope for recent alerts
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('alert_date', '>=', now()->subDays($days));
    }

    /**
     * Get formatted alert date
     */
    public function getFormattedAlertDateAttribute()
    {
        return $this->alert_date->format('M d, Y g:i A');
    }
}
