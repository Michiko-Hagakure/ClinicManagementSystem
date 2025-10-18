@extends('layouts.admin')

@section('title', 'Audit Logs')
@section('page-title', 'Audit Logs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">
        <i class="bi bi-shield-check me-2"></i>Security Audit Trail
        <span class="badge bg-primary ms-2">{{ $logs->total() }} records</span>
    </h5>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label small">Action Type</label>
                <select name="action" class="form-select">
                    <option value="">All Actions</option>
                    <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Create</option>
                    <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Update</option>
                    <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Delete</option>
                    <option value="toggle_status" {{ request('action') == 'toggle_status' ? 'selected' : '' }}>Status Change</option>
                    <option value="password_change" {{ request('action') == 'password_change' ? 'selected' : '' }}>Password Change</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small">Performed By</label>
                <select name="user_id" class="form-select">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">From Date</label>
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small">To Date</label>
                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2"><i class="bi bi-funnel"></i> Filter</button>
                <a href="{{ route('admin.audit-logs') }}" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i></a>
            </div>
        </form>
    </div>
</div>

<!-- Audit Logs Table -->
<div class="card">
    <div class="card-body p-0">
        @if($logs->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 180px;">Date & Time</th>
                            <th style="width: 120px;">Action</th>
                            <th style="width: 150px;">User</th>
                            <th>Description</th>
                            <th style="width: 100px;" class="text-center">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($logs as $log)
                        <tr>
                            <td>
                                <div class="small">
                                    <i class="bi bi-calendar me-1"></i>{{ $log->created_at->format('M d, Y') }}<br>
                                    <i class="bi bi-clock me-1"></i>{{ $log->created_at->format('h:i A') }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $log->actionColor }}">
                                    <i class="bi {{ $log->actionIcon }} me-1"></i>{{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                </span>
                            </td>
                            <td>
                                @if($log->user)
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $log->user->profile_picture_url }}" alt="{{ $log->user->name }}" 
                                             class="rounded-circle me-2" 
                                             style="width: 30px; height: 30px; object-fit: cover; border: 2px solid #00A689;">
                                        <div class="small">
                                            <strong>{{ $log->user->name }}</strong>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted small">System</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $log->description }}</div>
                                @if($log->ip_address)
                                    <small class="text-muted">
                                        <i class="bi bi-geo-alt"></i> IP: {{ $log->ip_address }}
                                    </small>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($log->old_values || $log->new_values)
                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#detailsModal{{ $log->id }}">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    
                                    <!-- Details Modal -->
                                    <div class="modal fade" id="detailsModal{{ $log->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Audit Log Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <h6 class="text-muted">Description:</h6>
                                                    <p>{{ $log->description }}</p>
                                                    
                                                    @if($log->old_values)
                                                        <h6 class="text-danger mt-3">Previous Values:</h6>
                                                        <pre class="bg-light p-3 rounded">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                                    @endif
                                                    
                                                    @if($log->new_values)
                                                        <h6 class="text-success mt-3">New Values:</h6>
                                                        <pre class="bg-light p-3 rounded">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                                    @endif
                                                    
                                                    <div class="mt-3">
                                                        <strong>Timestamp:</strong> {{ $log->created_at->format('M d, Y h:i:s A') }}<br>
                                                        <strong>IP Address:</strong> {{ $log->ip_address ?? 'N/A' }}<br>
                                                        <strong>User Agent:</strong> <small>{{ $log->user_agent ?? 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-3">
                {{ $logs->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-shield-check text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3">No audit logs found</p>
            </div>
        @endif
    </div>
</div>

<!-- Legend -->
<div class="card mt-3">
    <div class="card-body">
        <h6 class="mb-3"><i class="bi bi-info-circle me-2"></i>Action Types Legend</h6>
        <div class="row">
            <div class="col-md-2">
                <span class="badge bg-success"><i class="bi bi-plus-circle"></i> Create</span>
                <p class="small text-muted mb-0">New record created</p>
            </div>
            <div class="col-md-2">
                <span class="badge bg-info"><i class="bi bi-pencil"></i> Update</span>
                <p class="small text-muted mb-0">Record modified</p>
            </div>
            <div class="col-md-2">
                <span class="badge bg-danger"><i class="bi bi-trash"></i> Delete</span>
                <p class="small text-muted mb-0">Record removed</p>
            </div>
            <div class="col-md-3">
                <span class="badge bg-warning"><i class="bi bi-toggle-on"></i> Status Change</span>
                <p class="small text-muted mb-0">Active/inactive toggle</p>
            </div>
            <div class="col-md-3">
                <span class="badge bg-primary"><i class="bi bi-key"></i> Password Change</span>
                <p class="small text-muted mb-0">Password updated</p>
            </div>
        </div>
    </div>
</div>
@endsection

