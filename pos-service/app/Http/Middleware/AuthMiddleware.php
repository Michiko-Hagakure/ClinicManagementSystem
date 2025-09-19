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
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is logged in via session
        if (!session('logged_in')) {
            // For AJAX requests, return JSON response
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Authentication required',
                    'redirect' => 'http://127.0.0.1:8002/login'
                ], 401);
            }
            
            // For regular requests, redirect to login
            return redirect('http://127.0.0.1:8002/login')
                ->with('error', 'Please log in to access the POS system.');
        }
        
        // Check if user has cashier role
        if (session('user_role') !== 'cashier') {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Insufficient permissions',
                    'redirect' => 'http://127.0.0.1:8002/login'
                ], 403);
            }
            
            return redirect('http://127.0.0.1:8002/login')
                ->with('error', 'Only cashiers can access the POS system.');
        }
        
        return $next($request);
    }
}
