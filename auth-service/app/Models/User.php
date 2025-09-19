<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
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
     * @var array<int, string>
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
     * Role helper methods
     */
    public function isClinicStaff(): bool
    {
        return $this->role === 'clinic_staff';
    }

    public function isMedicalStaff(): bool
    {
        return $this->role === 'medical_staff';
    }

    public function isDoctor(): bool
    {
        return $this->role === 'doctor';
    }

    public function isCashier(): bool
    {
        return $this->role === 'cashier';
    }

    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    public function getRoleDisplayName(): string
    {
        return match($this->role) {
            'clinic_staff' => 'Clinic Staff',
            'medical_staff' => 'Medical Staff',
            'doctor' => 'Doctor',
            'cashier' => 'Cashier',
            'owner' => 'Owner',
            default => 'User'
        };
    }
}