@extends('layouts.admin')

@section('title', 'User Management')
@section('page-title', 'User Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">
        <i class="bi bi-people me-2"></i>All Users
        <span class="badge bg-primary ms-2">{{ $users->total() }} total</span>
    </h5>
    <a href="{{ route('admin.users.create') }}" class="btn btn-success">
        <i class="bi bi-plus-circle me-1"></i>Create New User
    </a>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Search by name, employee ID, department..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select">
                    <option value="">All Roles</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>System Administrator</option>
                    <option value="owner" {{ request('role') == 'owner' ? 'selected' : '' }}>Owner</option>
                    <option value="doctor" {{ request('role') == 'doctor' ? 'selected' : '' }}>Doctor</option>
                    <option value="medical_staff" {{ request('role') == 'medical_staff' ? 'selected' : '' }}>Medical Staff</option>
                    <option value="cashier" {{ request('role') == 'cashier' ? 'selected' : '' }}>Cashier</option>
                    <option value="pharmacy_staff" {{ request('role') == 'pharmacy_staff' ? 'selected' : '' }}>Pharmacy Staff</option>
                    <option value="pharmacist" {{ request('role') == 'pharmacist' ? 'selected' : '' }}>Pharmacist</option>
                    <option value="clinic_staff" {{ request('role') == 'clinic_staff' ? 'selected' : '' }}>Clinic Staff</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary me-2"><i class="bi bi-funnel"></i> Filter</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i> Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card">
    <div class="card-body p-0">
        @if($users->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>User</th>
                            <th>Employee ID</th>
                            <th>Role</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th class="text-end" style="width: 180px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $user->profile_picture_url }}" alt="{{ $user->name }}" 
                                         class="rounded-circle me-2" 
                                         style="width: 40px; height: 40px; object-fit: cover; border: 2px solid #00A689;">
                                    <div>
                                        <strong>{{ $user->name }}</strong>
                                        @if($user->id === auth()->id())
                                            <span class="badge bg-info ms-1">You</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <code class="text-primary fw-bold">{{ $user->employee_id ?? 'N/A' }}</code>
                            </td>
                            <td>
                                <span class="badge 
                                    @if($user->role === 'admin') bg-danger
                                    @elseif($user->role === 'owner') bg-warning
                                    @elseif($user->role === 'doctor') bg-primary
                                    @else bg-secondary
                                    @endif">
                                    {{ $user->getRoleDisplayName() }}
                                </span>
                            </td>
                            <td>{{ $user->department ?? '-' }}</td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Active</span>
                                @else
                                    <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> Inactive</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $user->created_at->format('M d, Y') }}</small>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-primary" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-{{ $user->is_active ? 'warning' : 'success' }}" title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="bi bi-{{ $user->is_active ? 'pause-circle' : 'play-circle' }}"></i>
                                        </button>
                                    </form>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">
                                Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                            </small>
                        </div>
                        <div>
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="bi bi-people display-1 text-muted"></i>
                <h5 class="mt-3 text-muted">No users found</h5>
                @if(request()->hasAny(['search', 'role', 'status']))
                    <p class="text-muted">Try adjusting your filters.</p>
                @else
                    <a href="{{ route('admin.users.create') }}" class="btn btn-success mt-2">
                        <i class="bi bi-plus-circle me-1"></i>Create First User
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

