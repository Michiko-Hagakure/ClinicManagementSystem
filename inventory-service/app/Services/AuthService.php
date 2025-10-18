<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AuthService
{
    private string $authBaseUrl;
    private int $timeout;

    public function __construct()
    {
        $this->authBaseUrl = env('AUTH_SERVICE_URL', 'http://127.0.0.1:8000');
        $this->timeout = 10; // 10 seconds timeout
    }

    /**
     * Check if user is authenticated via shared session
     * With matching APP_KEY across services, Laravel can now properly decrypt the session cookie
     */
    public function checkAuth(?string $cookieHeader = null): ?array
    {
        try {
            // First, check if user data is already stored in the local session
            $localUser = $this->getCurrentUser();
            if ($localUser) {
                Log::info('AuthService: User authenticated from local session', [
                    'user_id' => $localUser['id']
                ]);
                return $localUser;
            }

            // Check if there's a user_id in the shared session
            // Laravel automatically handles the shared session database because we configured it
            $userId = Session::get('user_id');
            
            if ($userId) {
                Log::info('AuthService: Found user_id in shared session', [
                    'user_id' => $userId,
                    'session_id' => Session::getId()
                ]);

                // Fetch full user details from auth service via API
                $response = Http::timeout($this->timeout)
                    ->get("{$this->authBaseUrl}/api/users/{$userId}");

                if ($response->successful()) {
                    $user = $response->json();
                    
                    Log::info('AuthService: User authenticated successfully', [
                        'user_id' => $user['id'],
                        'role' => $user['role']
                    ]);
                    
                    // Store in local session for faster subsequent requests
                    $this->storeUserSession($user);
                    
                    return $user;
                }
            }

            Log::warning('AuthService: No valid authentication found', [
                'session_id' => Session::getId(),
                'has_user_id' => !empty($userId)
            ]);
            return null;

        } catch (\Exception $e) {
            Log::error('AuthService: Authentication check failed', [
                'error' => $e->getMessage(),
                'session_id' => Session::getId()
            ]);
            return null;
        }
    }

    /**
     * Check if user has required role for inventory access
     */
    public function hasInventoryAccess(?array $user): bool
    {
        if (!$user || !isset($user['role'])) {
            return false;
        }

        $allowedRoles = ['pharmacy_staff', 'pharmacist', 'owner'];
        
        return in_array($user['role'], $allowedRoles);
    }

    /**
     * Check if user has admin access (for reports, exports, etc.)
     */
    public function hasAdminAccess(?array $user): bool
    {
        if (!$user || !isset($user['role'])) {
            return false;
        }

        return $user['role'] === 'owner';
    }

    /**
     * Get login URL for auth service
     */
    public function getLoginUrl(): string
    {
        return "{$this->authBaseUrl}/login";
    }

    /**
     * Get logout URL for auth service  
     */
    public function getLogoutUrl(): string
    {
        return "{$this->authBaseUrl}/logout";
    }

    /**
     * Store user data in session (when authenticated)
     */
    public function storeUserSession(array $user): void
    {
        Session::put([
            'authenticated' => true,
            'user_id' => $user['id'],
            'user_name' => $user['name'],
            'user_email' => $user['email'],
            'user_role' => $user['role'],
            'user_profile_picture' => $user['profile_picture_url'] ?? null,
            'user_department' => $user['department'] ?? null
        ]);
        
        Session::save();
    }

    /**
     * Clear user session
     */
    public function clearUserSession(): void
    {
        Session::forget(['authenticated', 'user_id', 'user_name', 'user_email', 'user_role', 'user_profile_picture', 'user_department']);
        Session::save();
    }

    /**
     * Get current authenticated user from session
     */
    public function getCurrentUser(): ?array
    {
        if (Session::get('authenticated', false)) {
            return [
                'id' => Session::get('user_id'),
                'name' => Session::get('user_name'),
                'email' => Session::get('user_email'),
                'role' => Session::get('user_role'),
                'profile_picture_url' => Session::get('user_profile_picture'),
                'department' => Session::get('user_department')
            ];
        }

        return null;
    }

    /**
     * Check if current user is pharmacy staff
     */
    public function isPharmacyStaff(): bool
    {
        $user = $this->getCurrentUser();
        return $user && in_array($user['role'], ['pharmacy_staff', 'pharmacist']);
    }

    /**
     * Check if current user is owner
     */
    public function isOwner(): bool
    {
        $user = $this->getCurrentUser();
        return $user && $user['role'] === 'owner';
    }
}
