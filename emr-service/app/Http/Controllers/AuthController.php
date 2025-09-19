<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }
    
    /**
     * Handle login attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6'
        ]);
        
        // Find user by email
        $user = User::where('email', $credentials['email'])->first();
        
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->withInput($request->only('email'));
        }
        
        // Store user session data
        session([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'user_role' => $user->role,
            'logged_in' => true
        ]);
        
        // Role-based redirection
        return $this->redirectUserByRole($user->role);
    }
    
    /**
     * Redirect user based on their role.
     */
    private function redirectUserByRole(string $role): RedirectResponse
    {
        switch ($role) {
            case 'cashier':
                // Redirect to POS Service dashboard
                return redirect()->away('http://127.0.0.1:8001/dashboard');
                
            case 'doctor':
                // Redirect to Doctor Portal
                return redirect()->route('doctor.dashboard');
                
            case 'clinic_staff':
            case 'medical_staff':
            case 'owner':
            default:
                // Redirect to EMR Dashboard
                return redirect()->route('dashboard');
        }
    }
    
    /**
     * Handle logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        session()->flush();
        
        return redirect()->route('login')
            ->with('success', 'You have been logged out successfully.');
    }
    
    /**
     * Check if user is authenticated (for API calls).
     */
    public function checkAuth()
    {
        return response()->json([
            'authenticated' => session('logged_in', false),
            'user' => [
                'id' => session('user_id'),
                'name' => session('user_name'),
                'email' => session('user_email'),
                'role' => session('user_role')
            ]
        ]);
    }
}
