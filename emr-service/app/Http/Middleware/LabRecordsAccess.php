<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class LabRecordsAccess
{
    /**
     * Handle an incoming request for lab records access control.
     * Based on clinic workflow from interview and documentation.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission  'view', 'input', 'review', 'edit'
     */
    public function handle(Request $request, Closure $next, string $permission = 'view'): Response
    {
        $user = Auth::user();
        
        // Check if user is authenticated and active
        if (!$user || !$user->is_active) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        // Permission-based access control
        $hasPermission = match($permission) {
            'view' => $user->canViewLabRecords(),
            'input' => $user->canInputLabResults(),
            'review' => $user->canReviewLabResults(), 
            'edit' => $user->canEditLabRecords(),
            default => false
        };

        if (!$hasPermission) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Insufficient permissions for this action',
                    'required_permission' => $permission,
                    'user_role' => $user->role
                ], 403);
            }
            
            return redirect()->back()->with('error', 
                "Access denied. Your role ({$user->getRoleDisplayName()}) does not have permission to {$permission} lab records."
            );
        }

        return $next($request);
    }
}
