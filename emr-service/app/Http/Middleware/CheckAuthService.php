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
                    session($userData);
                    session(['authenticated' => true, 'api_token' => $token]);
                    return $next($request);
                }
            }
        } catch (\Exception $e) {
            Log::error('Could not connect to auth service: ' . $e->getMessage());
        }

        return redirect()->away('http://127.0.0.1:8000/login');
    }
}
