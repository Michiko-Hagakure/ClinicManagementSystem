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
            'employee_id' => 'required|string|size:6',
            'password' => 'required',
        ]);

        Log::info('Login attempt', ['employee_id' => $request->employee_id]);

        // Find user by employee_id
        $user = User::where('employee_id', $request->employee_id)->first();
        
        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();

            Log::info('Login successful, session regenerated', [
                'user_id' => Auth::id(),
                'employee_id' => $user->employee_id,
                'session_id' => session()->getId(),
                'session_data' => session()->all(),
            ]);

            $token = $user->createToken('auth-token')->plainTextToken;
            session(['api_token' => $token]);
            
            session(['authenticated' => true]);
            session(['user_id' => $user->id]);
            session(['user_name' => $user->name]);
            session(['user_email' => $user->email]);
            session(['user_role' => $user->role]);
            session(['user_department' => $user->department]);
            session(['user_profile_picture' => $user->profile_picture_url]);
            session(['user_employee_id' => $user->employee_id]);
            
            session()->save();

            // Role-based redirection
            Log::info('User authenticated, redirecting...', ['employee_id' => $user->employee_id, 'role' => $user->role]);
            return $this->redirectUserByRole($user->role);
        }

        Log::warning('Login failed', ['employee_id' => $request->employee_id]);

        return back()->withErrors([
            'employee_id' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('employee_id'));
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
            case 'admin':
                // Redirect to Admin Panel for user management
                return redirect()->route('admin.users.index');

            case 'cashier':
                // Redirect to POS Service dashboard (port 8002)
                return redirect()->away('http://127.0.0.1:8002/dashboard');

            case 'pharmacist':
                // Redirect to Pharmacy/Inventory Service dashboard (port 8003)
                return redirect()->away('http://127.0.0.1:8003/pharmacy/dashboard');

            case 'doctor':
                // Redirect to Doctor Portal in EMR (port 8001)
                return redirect()->away('http://127.0.0.1:8001/doctor');

            case 'medical_staff':
                // Redirect to EMR Dashboard (port 8001)
                return redirect()->away('http://127.0.0.1:8001/');
                
            case 'owner':
                // Redirect to Owner Dashboard with consolidated reports
                return redirect()->route('owner.dashboard');

            default:
                // Redirect to EMR Dashboard (port 8001)
                return redirect()->away('http://127.0.0.1:8001/');
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