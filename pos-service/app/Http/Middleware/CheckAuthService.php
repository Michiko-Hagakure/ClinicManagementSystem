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
        // Temporary bypass to restore access while cross-service auth is stabilized
        if (env('POS_BYPASS_AUTH', true)) {
            Log::warning('CheckAuthService: Bypassing external auth (POS_BYPASS_AUTH=true).');
            return $next($request);
        }

        // Get the auth service URL from config, with a default
        $authServiceUrl = config('services.auth.base_url', 'http://127.0.0.1:8000');

        Log::info('CheckAuthService: Running auth check.', ['url' => "{$authServiceUrl}/api/user"]);

        try {
            // Forward the session cookie to the auth service to check for an active session
            $response = Http::withHeaders([
                'Cookie' => $request->header('Cookie'),
                'Referer' => $request->header('Referer'),
                'Accept' => 'application/json',
            ])->get("{$authServiceUrl}/api/user");

            if ($response->successful()) {
                // If the auth service returns a user, the session is valid.
                // We can store a local flag to minimize API calls.
                session(['authenticated_pos' => true, 'user' => $response->json()]);
                Log::info('CheckAuthService: Auth check successful.', ['user_id' => $response->json('id')]);
                return $next($request);
            }

            // Log the failed auth attempt
            Log::warning('CheckAuthService: Auth check failed.', [
                'status' => $response->status(),
                'body' => $response->body(),
                'url' => "{$authServiceUrl}/api/user",
            ]);

        } catch (\Exception $e) {
            Log::error('CheckAuthService: Could not connect to auth service.', [
                'error' => $e->getMessage(),
                'url' => "{$authServiceUrl}/api/user",
            ]);
        }

        // If the check fails for any reason, redirect to the main login page
        return redirect()->away($authServiceUrl . '/login');
    }
}
