<?php

namespace App\Http\Middleware;

use App\Services\AuthService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $level = 'basic'): Response
    {
        Log::info('AuthMiddleware: Checking authentication', [
            'path' => $request->path(),
            'level' => $level,
            'session_id' => session()->getId()
        ]);

        // For API routes, don't redirect to login page
        $isApiRoute = $request->is('api/*');

        // Check if user is authenticated, forward the browser's Cookie header
        $user = $this->authService->checkAuth($request->header('Cookie'));
        
        if (!$user) {
            Log::warning('AuthMiddleware: User not authenticated', [
                'path' => $request->path(),
                'session_id' => session()->getId()
            ]);

            if ($isApiRoute) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Redirect to auth service login page
            return redirect()->away($this->authService->getLoginUrl());
        }

        // Store user data in session for this service
        $this->authService->storeUserSession($user);

        // Check access level permissions
        switch ($level) {
            case 'pharmacy':
                if (!$this->authService->hasInventoryAccess($user)) {
                    Log::warning('AuthMiddleware: User lacks pharmacy access', [
                        'user_id' => $user['id'],
                        'role' => $user['role'],
                        'path' => $request->path()
                    ]);

                    if ($isApiRoute) {
                        return response()->json(['error' => 'Access denied - pharmacy staff access required'], 403);
                    }

                    return redirect()->away($this->authService->getLoginUrl())
                        ->with('error', 'Access denied. Pharmacy staff access required.');
                }
                break;

            case 'admin':
                if (!$this->authService->hasAdminAccess($user)) {
                    Log::warning('AuthMiddleware: User lacks admin access', [
                        'user_id' => $user['id'],
                        'role' => $user['role'],
                        'path' => $request->path()
                    ]);

                    if ($isApiRoute) {
                        return response()->json(['error' => 'Access denied - admin access required'], 403);
                    }

                    return redirect()->route('pharmacy.dashboard')
                        ->with('error', 'Access denied. Admin privileges required.');
                }
                break;

            case 'basic':
            default:
                // Basic access - just need to be authenticated pharmacy staff
                if (!$this->authService->hasInventoryAccess($user)) {
                    Log::warning('AuthMiddleware: User lacks basic inventory access', [
                        'user_id' => $user['id'],
                        'role' => $user['role'],
                        'path' => $request->path()
                    ]);

                    if ($isApiRoute) {
                        return response()->json(['error' => 'Access denied - inventory access required'], 403);
                    }

                    return redirect()->away($this->authService->getLoginUrl())
                        ->with('error', 'Access denied. You do not have permission to access the inventory system.');
                }
                break;
        }

        Log::info('AuthMiddleware: Access granted', [
            'user_id' => $user['id'],
            'role' => $user['role'],
            'level' => $level,
            'path' => $request->path()
        ]);

        // Add user data to request for use in controllers
        $request->attributes->add(['authenticated_user' => $user]);

        return $next($request);
    }
}
