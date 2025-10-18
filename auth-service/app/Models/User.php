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
        'profile_picture',
        'employee_id',
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

    public function isPharmacist(): bool
    {
        return $this->role === 'pharmacist';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function getRoleDisplayName(): string
    {
        return match($this->role) {
            'admin' => 'System Administrator',
            'medical_staff' => 'Medical Staff',
            'doctor' => 'Doctor',
            'cashier' => 'Cashier',
            'pharmacist' => 'Pharmacist',
            'owner' => 'Owner',
            default => 'User'
        };
    }

    /**
     * Get full name with title
     */
    public function getFullNameAttribute(): string
    {
        return $this->name;
    }

    /**
     * Get profile picture URL
     */
    public function getProfilePictureUrlAttribute(): string
    {
        if ($this->profile_picture && file_exists(public_path($this->profile_picture))) {
            return asset($this->profile_picture);
        }
        
        // Return default avatar
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=FFFFFF&background=00A689&size=200&bold=true';
    }

    /**
     * Boot function to auto-generate employee_id
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($user) {
            if (empty($user->employee_id)) {
                $user->employee_id = self::generateEmployeeId();
            }
        });
    }

    /**
     * Generate a unique 6-digit employee ID
     */
    public static function generateEmployeeId(): string
    {
        do {
            // Generate a random 6-digit number (100000 - 999999)
            $employeeId = (string) rand(100000, 999999);
        } while (self::where('employee_id', $employeeId)->exists());
        
        return $employeeId;
    }
}