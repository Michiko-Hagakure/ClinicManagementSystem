<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $table = 'medicine';
    protected $primaryKey = 'medicine_id';

    protected $fillable = [
        'name',
        'dosage',
        'category',
        'stock_quantity',
        'price'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer'
    ];

    /**
     * Relationship with DispenseMedicine
     */
    public function dispensedMedicines()
    {
        return $this->hasMany(DispenseMedicine::class, 'medicine_id', 'medicine_id');
    }

    /**
     * Relationship with LowStockAlert
     */
    public function lowStockAlerts()
    {
        return $this->hasMany(LowStockAlert::class, 'medicine_id', 'medicine_id');
    }

    /**
     * Check if medicine is low in stock (below 10 units)
     */
    public function isLowStock($threshold = 10)
    {
        return $this->stock_quantity <= $threshold;
    }

    /**
     * Reduce stock quantity when dispensing medicine
     */
    public function reduceStock($quantity)
    {
        if ($this->stock_quantity >= $quantity) {
            $this->stock_quantity -= $quantity;
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Add stock quantity when restocking
     */
    public function addStock($quantity)
    {
        $this->stock_quantity += $quantity;
        $this->save();
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return '₱' . number_format($this->price, 2);
    }

    /**
     * Scope for searching medicines
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('dosage', 'like', "%{$search}%");
    }

    /**
     * Scope for low stock medicines
     */
    public function scopeLowStock($query, $threshold = 10)
    {
        return $query->where('stock_quantity', '<=', $threshold);
    }
}
