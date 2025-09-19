<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

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
     * Handle login request.
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        Log::info('Login attempt', ['email' => $request->email]);

        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            Log::info('Login successful, session regenerated', [
                'user_id' => Auth::id(),
                'session_id' => session()->getId(),
                'session_data' => session()->all(),
            ]);

            $user = Auth::user();

            $token = $user->createToken('auth-token')->plainTextToken;
            session(['api_token' => $token]);
            
            session(['authenticated' => true]);
            session(['user_id' => $user->id]);
            session(['user_name' => $user->name]);
            session(['user_email' => $user->email]);
            session(['user_role' => $user->role]);
            
            session()->save();

            // Role-based redirection
            \Illuminate\Support\Facades\Log::info('User authenticated, redirecting...', ['user' => $user->email, 'role' => $user->role]);
            return $this->redirectUserByRole($user->role);
        }

        Log::warning('Login failed', ['email' => $request->email]);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Redirect user based on their role.
     */
    private function redirectUserByRole(string $role): RedirectResponse
    {
        switch ($role) {
            case 'cashier':
                // Redirect to POS Service dashboard (port 8002)
                return redirect()->away('http://127.0.0.1:8002/dashboard');

            case 'doctor':
                // Redirect to Doctor Portal in EMR (port 8001)
                return redirect()->away('http://127.0.0.1:8001/doctor');

            case 'clinic_staff':
            case 'medical_staff':
            case 'owner':
            default:
                // Redirect to EMR Dashboard (port 8001)
                return redirect()->away('http://127.0.0.1:8001/dashboard');
        }
    }

    /**
     * API endpoint to check if user is authenticated (for other services).
     */
    public function checkAuth(Request $request)
    {
        if (Auth::check()) {
            return response()->json([
                'authenticated' => true,
                'user' => [
                    'id' => Auth::id(),
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                    'role' => Auth::user()->role,
                ]
            ]);
        }
    
        return response()->json(['authenticated' => false]);
    }

    public function getToken(Request $request)
    {
        return response()->json(['api_token' => $request->session()->get('api_token')]);
    }
}