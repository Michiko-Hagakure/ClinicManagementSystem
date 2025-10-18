<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckAuthService
{
    public function handle(Request $request, Closure $next)
    {
        // First, check if the user is already authenticated in this service's session.
        if (session('authenticated')) {
            return $next($request);
        }

        // If not, try to authenticate against the auth service.
        try {
            $tokenResponse = Http::withHeaders([
                'Cookie' => $request->header('Cookie'),
            ])->get('http://127.0.0.1:8000/api/auth/token');

            if ($tokenResponse->successful() && $tokenResponse->json('api_token')) {
                $token = $tokenResponse->json('api_token');

                // Now, use the token to check authentication
                $authResponse = Http::withToken($token)->withHeaders([
                    'Accept' => 'application/json',
                ])->get('http://127.0.0.1:8000/api/auth/check');


                if ($authResponse->successful() && $authResponse->json('authenticated')) {
                    $userData = $authResponse->json('user');
                    
                    // Fetch full user details including profile picture
                    try {
                        $userDetailResponse = Http::get('http://127.0.0.1:8000/api/users/' . $userData['id']);
                        if ($userDetailResponse->successful()) {
                            $fullUserData = $userDetailResponse->json();
                            
                            // Store session data with correct keys
                            session([
                                'authenticated' => true,
                                'api_token' => $token,
                                'user_id' => $fullUserData['id'],
                                'user_name' => $fullUserData['name'],
                                'user_email' => $fullUserData['email'],
                                'user_role' => $fullUserData['role'],
                                'user_department' => $fullUserData['department'] ?? null,
                                'user_profile_picture' => $fullUserData['profile_picture_url'] ?? null,
                                'user_employee_id' => $fullUserData['employee_id'] ?? null,
                            ]);
                            
                            Log::info('User session synced from auth service', [
                                'user_id' => $fullUserData['id'],
                                'name' => $fullUserData['name'],
                                'role' => $fullUserData['role']
                            ]);
                            
                            return $next($request);
                        }
                    } catch (\Exception $e) {
                        Log::warning('Could not fetch full user details: ' . $e->getMessage());
                    }
                    
                    // Fallback: store basic user data
                    session([
                        'authenticated' => true,
                        'api_token' => $token,
                        'user_id' => $userData['id'],
                        'user_name' => $userData['name'],
                        'user_email' => $userData['email'],
                        'user_role' => $userData['role'],
                    ]);
                    
                    return $next($request);
                }
            }
        } catch (\Exception $e) {
            Log::error('Could not connect to auth service: ' . $e->getMessage());
        }

        return redirect()->away('http://127.0.0.1:8000/login');
    }
}
