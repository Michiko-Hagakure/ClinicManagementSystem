<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminController extends Controller
{
    /**
     * Display admin dashboard with user list
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show create user form
     */
    public function create()
    {
        $roles = $this->getRolesList();
        $departments = $this->getDepartmentsList();

        return view('admin.users.create', compact('roles', 'departments'));
    }

    /**
     * Store new user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'string', 'in:admin,doctor,cashier,pharmacist,medical_staff,owner'],
            'department' => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
            'cropped_image' => ['nullable', 'string'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = true; // Always active by default

        // Handle profile picture upload
        if ($request->filled('cropped_image')) {
            $image = $request->cropped_image;
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = 'profile_' . time() . '_' . uniqid() . '.png';
            $path = public_path('uploads/profile-pictures/' . $imageName);
            
            file_put_contents($path, base64_decode($image));
            $validated['profile_picture'] = 'uploads/profile-pictures/' . $imageName;
        }

        unset($validated['cropped_image']);
        
        $user = User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', "User account created successfully for {$user->name}!");
    }

    /**
     * Show edit user form
     */
    public function edit(User $user)
    {
        $roles = $this->getRolesList();
        $departments = $this->getDepartmentsList();

        return view('admin.users.edit', compact('user', 'roles', 'departments'));
    }

    /**
     * Update user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => ['required', 'string', 'in:admin,doctor,cashier,pharmacist,medical_staff,owner'],
            'department' => ['nullable', 'string', 'max:100'],
            'is_active' => ['boolean'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        // Keep existing is_active status when updating (don't change it)
        unset($validated['is_active']);

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', "User account updated successfully for {$user->name}!");
    }

    /**
     * Delete user
     */
    public function destroy(User $user)
    {
        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account!');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User account for {$userName} deleted successfully.");
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();

        $status = $user->is_active ? 'activated' : 'deactivated';

        return back()->with('success', "User {$user->name} has been {$status}.");
    }

    /**
     * Get available roles list
     */
    private function getRolesList(): array
    {
        return [
            'admin' => 'System Administrator',
            'owner' => 'Owner',
            'doctor' => 'Doctor',
            'medical_staff' => 'Medical Staff',
            'cashier' => 'Cashier',
            'pharmacist' => 'Pharmacist',
        ];
    }

    /**
     * Get available departments list
     */
    private function getDepartmentsList(): array
    {
        return [
            'Administration',
            'Medical',
            'Pharmacy',
            'Billing',
            'Laboratory',
            'Radiology',
            'General',
        ];
    }
}

