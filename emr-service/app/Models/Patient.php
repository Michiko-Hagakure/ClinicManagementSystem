<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Patient extends Model
{
    use HasFactory;

    protected $table = 'patients';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'civil_status',
        'phone_number',
        'email',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'medical_history',
        'allergies'
    ];

    protected $casts = [
        'date_of_birth' => 'date'
    ];

    // Relationships
    public function consultations()
    {
        return $this->hasMany(Consultation::class, 'patient_id');
    }

    public function labResults()
    {
        return $this->hasMany(LabResult::class, 'patient_id');
    }

    // Accessor for full name
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    // Accessor for age
    public function getAgeAttribute()
    {
        if (!$this->date_of_birth) {
            return 'N/A';
        }
        return $this->date_of_birth->age;
    }

    // Accessor for patient code
    public function getPatientCodeAttribute()
    {
        return 'P' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }
}
