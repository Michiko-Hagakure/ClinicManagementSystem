<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LabResult extends Model
{
    use HasFactory;

    protected $table = 'lab_results';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'patient_id',
        'test_name',
        'test_category',
        'result',
        'reference_range',
        'status',
        'test_date',
        'technician_name',
        'notes'
    ];

    protected $casts = [
        'test_date' => 'date'
    ];

    // Relationships
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function consultation()
    {
        return $this->belongsTo(Consultation::class, 'patient_id', 'patient_id');
    }
}
