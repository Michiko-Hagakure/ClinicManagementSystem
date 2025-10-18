<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Consultation extends Model
{
    use HasFactory;

    protected $table = 'consultation';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'patient_id',
        'doctor_name',
        'consultation_date',
        'chief_complaint',
        'consultation_notes',
        'assessment',
        'treatment_plan',
        'medications_prescribed',
        'follow_up_date',
        'status',
        'referred_to',
        'patient_history_notes',
        'physical_examination',
        'patient_instructions',
        // Vital signs
        'bp',
        'temparature',
        'weight',
        'o2',
        'pr'
    ];

    protected $casts = [
        'consultation_date' => 'datetime',
        'follow_up_date' => 'date'
    ];

    // Relationships
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function labResults()
    {
        return $this->hasMany(LabResult::class, 'patient_id', 'patient_id');
    }
}
