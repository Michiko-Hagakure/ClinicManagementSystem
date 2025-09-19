<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Role helper methods based on clinic workflow from interview
     */
    
    // Lab Records Permissions - based on Mary Angels Clinic workflow
    public function canViewLabRecords(): bool
    {
        // Everyone can view for their specific purposes
        return $this->is_active;
    }

    public function canInputLabResults(): bool
    {
        // ONLY medical technicians can input results (Lab tech, Rad tech, etc.)
        return $this->is_active && $this->role === 'medical_staff';
    }

    public function canReviewLabResults(): bool
    {
        // ONLY doctors can review and interpret results
        return $this->is_active && in_array($this->role, ['doctor', 'owner']);
    }

    public function canEditLabRecords(): bool
    {
        // Medical staff can edit their inputs, doctors can add interpretations
        return $this->is_active && in_array($this->role, ['medical_staff', 'doctor', 'owner']);
    }

    // Administrative permissions for clinic staff
    public function canManagePatients(): bool
    {
        // Clinic staff can manage patient records administratively
        return $this->is_active && in_array($this->role, ['clinic_staff', 'owner']);
    }

    public function canSearchRecords(): bool
    {
        // Everyone needs search for their workflows
        return $this->is_active;
    }

    // General role checks
    public function isClinicStaff(): bool
    {
        return $this->role === 'clinic_staff' && $this->is_active;
    }

    public function isMedicalStaff(): bool
    {
        return $this->role === 'medical_staff' && $this->is_active;
    }

    public function isDoctor(): bool
    {
        return $this->role === 'doctor' && $this->is_active;
    }

    public function isCashier(): bool
    {
        return $this->role === 'cashier' && $this->is_active;
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner' && $this->is_active;
    }

    // Department helpers for medical staff
    public function isLaboratoryTech(): bool
    {
        return $this->isMedicalStaff() && $this->department === 'laboratory';
    }

    public function isRadiologyTech(): bool
    {
        return $this->isMedicalStaff() && $this->department === 'radiology';
    }

    public function isUltrasoundTech(): bool
    {
        return $this->isMedicalStaff() && $this->department === 'ultrasound';
    }

    // Get role display name
    public function getRoleDisplayName(): string
    {
        return match($this->role) {
            'clinic_staff' => 'Clinic Staff',
            'medical_staff' => 'Medical Staff',
            'doctor' => 'Doctor',
            'cashier' => 'Cashier',
            'owner' => 'Owner',
            default => 'Unknown'
        };
    }
}
