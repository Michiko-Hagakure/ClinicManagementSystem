<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $requiredRole = null): Response
    {
        // Check if user is logged in
        if (!session('logged_in')) {
            return redirect()->route('login')
                ->with('error', 'Please log in to access this page.');
        }
        
        // Check role if specified
        if ($requiredRole && session('user_role') !== $requiredRole) {
            return redirect()->route('login')
                ->with('error', 'You do not have permission to access this page.');
        }
        
        return $next($request);
    }
}
