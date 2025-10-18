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
        'test_type',
        'test_category',
        'result', // Original field from first migration
        'results', // New field from doctor upload migration
        'file_attachments',
        'reference_range',
        'status',
        'test_date',
        'technician_name',
        'notes',
        'doctor_notes',
        'reviewed_at',
        'reviewed_by'
    ];

    protected $casts = [
        'test_date' => 'datetime',
        'reviewed_at' => 'datetime',
        'file_attachments' => 'array'
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
