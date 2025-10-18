@extends('layouts.doctor')

@section('title', 'Lab Results Review - Doctor Portal')

@section('content')
<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0 text-gray-800">Lab Results & Uploads</h1>
        <p class="text-muted mb-0">Review lab results and upload new test results/X-rays for patients</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" onclick="location.reload()">
            <i class="bi bi-arrow-clockwise me-1"></i>Refresh
        </button>
        <a href="{{ route('doctor.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
        </a>
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Tab Navigation -->
<ul class="nav nav-tabs mb-4" id="labTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="review-tab" data-bs-toggle="tab" data-bs-target="#review" type="button" role="tab">
            <i class="bi bi-clipboard-pulse me-2"></i>Review Results ({{ $pendingResults->total() }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab">
            <i class="bi bi-check-circle me-2"></i>Completed Reviews ({{ $completedResults->total() }})
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload" type="button" role="tab">
            <i class="bi bi-cloud-upload me-2"></i>Upload New Results
        </button>
    </li>
</ul>

<div class="tab-content" id="labTabsContent">
    <!-- Review Tab -->
    <div class="tab-pane fade show active" id="review" role="tabpanel" aria-labelledby="review-tab">
        <!-- Statistics -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-left-warning shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Pending Reviews</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $pendingResults->total() }}</div>
                    </div>
                    <div class="ms-2">
                        <i class="bi bi-clipboard-pulse text-warning" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-left-info shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Today's Results</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $pendingResults->filter(function($result) { return $result->test_date->isToday(); })->count() }}
                        </div>
                    </div>
                    <div class="ms-2">
                        <i class="bi bi-calendar-check text-info" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-left-danger shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Urgent Reviews</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $pendingResults->filter(function($result) { return $result->test_date->diffInDays(now()) > 2; })->count() }}
                        </div>
                    </div>
                    <div class="ms-2">
                        <i class="bi bi-exclamation-triangle text-danger" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Lab Results List -->
<div class="card shadow">
    <div class="card-header bg-info text-white">
        <h6 class="m-0 font-weight-bold">
            <i class="bi bi-list-ul me-2"></i>Pending Lab Results
        </h6>
    </div>
    <div class="card-body p-0">
        @if($pendingResults->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3">Patient</th>
                            <th class="py-3">Test Type</th>
                            <th class="py-3">Test Date</th>
                            <th class="py-3">Urgency</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingResults as $labResult)
                        <tr>
                            <td class="py-3">
                                <div>
                                    <h6 class="mb-1">{{ $labResult->patient->first_name }} {{ $labResult->patient->last_name }}</h6>
                                    <small class="text-muted">
                                        <i class="bi bi-person-badge me-1"></i>{{ $labResult->patient->patient_code }}
                                        <span class="mx-2">•</span>
                                        <i class="bi bi-gender-{{ strtolower($labResult->patient->gender ?? 'ambiguous') }} me-1"></i>{{ $labResult->patient->gender ?? 'N/A' }}
                                    </small>
                                </div>
                            </td>
                            <td class="py-3">
                                <span class="fw-bold">{{ $labResult->test_type ?? $labResult->test_name }}</span>
                                @if($labResult->test_category)
                                    <br><small class="text-muted">{{ $labResult->test_category }}</small>
                                    @if($labResult->test_category === 'Doctor Upload')
                                        <span class="badge bg-success ms-1">Your Upload</span>
                                    @endif
                                @endif
                            </td>
                            <td class="py-3">
                                <div>
                                    <span class="fw-bold">{{ $labResult->test_date->format('M d, Y') }}</span>
                                    <br><small class="text-muted">{{ $labResult->test_date->format('H:i') }}</small>
                                </div>
                            </td>
                            <td class="py-3">
                                @php
                                    $daysSince = $labResult->test_date->diffInDays(now());
                                @endphp
                                @if($daysSince > 2)
                                    <span class="badge bg-danger">
                                        <i class="bi bi-exclamation-triangle me-1"></i>Urgent
                                    </span>
                                @elseif($daysSince > 1)
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-clock me-1"></i>Priority
                                    </span>
                                @else
                                    <span class="badge bg-info">
                                        <i class="bi bi-check-circle me-1"></i>Normal
                                    </span>
                                @endif
                            </td>
                            <td class="py-3">
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-hourglass-split me-1"></i>Pending Review
                                </span>
                            </td>
                            <td class="py-3">
                                <div class="d-flex gap-2">
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#reviewModal{{ $labResult->id }}">
                                        <i class="bi bi-eye me-1"></i>Review
                                    </button>
                                    <a href="{{ route('doctor.view-patient', $labResult->patient) }}" 
                                       class="btn btn-outline-info btn-sm">
                                        <i class="bi bi-person me-1"></i>Patient
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <!-- Review Modal -->
                        <div class="modal fade" id="reviewModal{{ $labResult->id }}" tabindex="-1">
                            <div class="modal-dialog modal-xl">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary text-white">
                                        <h5 class="modal-title">
                                            <i class="bi bi-clipboard-pulse me-2"></i>Review Lab Result
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <!-- Patient Info -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <h6 class="text-primary">Patient Information</h6>
                                                <p class="mb-1"><strong>Name:</strong> {{ $labResult->patient->first_name }} {{ $labResult->patient->last_name }}</p>
                                                <p class="mb-1"><strong>ID:</strong> {{ $labResult->patient->patient_code }}</p>
                                                <p class="mb-0"><strong>Age/Gender:</strong> 
                                                    {{ $labResult->patient->age ?? 'N/A' }} years
                                                    / {{ ucfirst($labResult->patient->gender ?? 'N/A') }}
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <h6 class="text-primary">Test Information</h6>
                                                <p class="mb-1"><strong>Test Type:</strong> {{ $labResult->test_type }}</p>
                                                <p class="mb-1"><strong>Date:</strong> {{ $labResult->test_date->format('M d, Y H:i') }}</p>
                                                <p class="mb-0"><strong>Category:</strong> {{ $labResult->test_category ?? 'N/A' }}</p>
                                            </div>
                                        </div>

                                        <!-- Attached Files/Images -->
                                        @if($labResult->file_attachments && is_array($labResult->file_attachments) && count($labResult->file_attachments) > 0)
                                        <div class="mb-3">
                                            <h6 class="text-primary">Attached Files</h6>
                                            <div class="bg-light p-3 rounded text-center">
                                                @foreach($labResult->file_attachments as $file)
                                                    @php
                                                        $extension = pathinfo($file, PATHINFO_EXTENSION);
                                                        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                                                    @endphp
                                                    
                                                    @if(in_array(strtolower($extension), $imageExtensions))
                                                        <img src="{{ asset('storage/lab-results/' . $file) }}" 
                                                             alt="Lab Result Image" 
                                                             class="img-fluid rounded mb-2"
                                                             style="max-height: 500px; width: auto;">
                                                    @else
                                                        <div class="alert alert-info">
                                                            <i class="bi bi-file-earmark-text me-2"></i>
                                                            <a href="{{ asset('storage/lab-results/' . $file) }}" 
                                                               target="_blank" 
                                                               class="text-decoration-none">
                                                                {{ basename($file) }}
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif

                                        <!-- Test Results -->
                                        @php
                                            $showResults = $labResult->results && 
                                                          !str_contains($labResult->results, 'Attached Files:') &&
                                                          !str_contains($labResult->results, 'lab_');
                                        @endphp
                                        @if($showResults)
                                        <div class="mb-3">
                                            <h6 class="text-primary">Test Results</h6>
                                            <div class="bg-light p-3 rounded">
                                                <pre>{{ $labResult->results }}</pre>
                                            </div>
                                        </div>
                                        @endif

                                        <!-- Review Form -->
                                        <form action="{{ route('doctor.lab-results.review', $labResult) }}" method="POST">
                                            @csrf
                                            
                                            <div class="mb-3">
                                                <label for="doctor_notes{{ $labResult->id }}" class="form-label">
                                                    <strong>Doctor's Notes and Interpretation</strong>
                                                </label>
                                                <textarea class="form-control" id="doctor_notes{{ $labResult->id }}" name="doctor_notes" rows="4" 
                                                          placeholder="Enter your clinical interpretation, recommendations, and any follow-up needed...">{{ $labResult->doctor_notes }}</textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label for="status{{ $labResult->id }}" class="form-label">
                                                    <strong>Review Status</strong>
                                                </label>
                                                <select class="form-select" id="status{{ $labResult->id }}" name="status" required>
                                                    <option value="reviewed" {{ $labResult->status === 'reviewed' ? 'selected' : '' }}>
                                                        Reviewed - Normal
                                                    </option>
                                                    <option value="requires_followup" {{ $labResult->status === 'requires_followup' ? 'selected' : '' }}>
                                                        Reviewed - Requires Follow-up
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="d-flex justify-content-end gap-2">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    Cancel
                                                </button>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="bi bi-check-circle me-1"></i>Complete Review
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($pendingResults->hasPages())
                <div class="card-footer">
                    {{ $pendingResults->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                <h4 class="mt-3 text-muted">All Lab Results Reviewed</h4>
                <p class="text-muted">Great job! You're all caught up with lab result reviews.</p>
                <a href="{{ route('doctor.dashboard') }}" class="btn btn-primary">
                    <i class="bi bi-speedometer2 me-1"></i>Back to Dashboard
                </a>
            </div>
        @endif
        </div>
    </div>
    </div> <!-- End Review Tab -->

    <!-- Completed Results Tab -->
    <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="bi bi-check-circle me-2"></i>Completed Lab Result Reviews (Last 30 Days)
                </h6>
            </div>
            <div class="card-body p-0">
                @if($completedResults->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3">Patient</th>
                                    <th class="py-3">Test Type</th>
                                    <th class="py-3">Upload Date</th>
                                    <th class="py-3">Files</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($completedResults as $labResult)
                                <tr>
                                    <td class="py-3">
                                        <div>
                                            <h6 class="mb-1">{{ $labResult->patient->first_name }} {{ $labResult->patient->last_name }}</h6>
                                            <small class="text-muted">
                                                <i class="bi bi-person-badge me-1"></i>{{ $labResult->patient->patient_code }}
                                                @if($labResult->patient->gender)
                                                    <span class="mx-2">•</span>
                                                    <i class="bi bi-gender-{{ strtolower($labResult->patient->gender) }} me-1"></i>{{ ucfirst($labResult->patient->gender) }}
                                                @endif
                                            </small>
                                        </div>
                                    </td>
                                            <td class="py-3">
                                                <span class="fw-bold">{{ $labResult->test_type ?? $labResult->test_name }}</span>
                                                @if($labResult->test_category)
                                                    <br><small class="text-muted">{{ $labResult->test_category }}</small>
                                                    @if($labResult->test_category === 'Doctor Upload')
                                                        <span class="badge bg-info ms-1">Doctor Uploaded</span>
                                                    @endif
                                                @endif
                                            </td>
                                    <td class="py-3">
                                        <div>
                                            <span class="fw-bold">{{ $labResult->test_date ? $labResult->test_date->format('M d, Y') : 'N/A' }}</span>
                                            @if($labResult->created_at)
                                                <br><small class="text-muted">{{ $labResult->created_at->format('H:i') }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        @php
                                            $fileCount = 0;
                                            if ($labResult->file_attachments) {
                                                if (is_array($labResult->file_attachments)) {
                                                    $fileCount = count($labResult->file_attachments);
                                                } elseif (is_string($labResult->file_attachments)) {
                                                    $decoded = json_decode($labResult->file_attachments, true);
                                                    $fileCount = is_array($decoded) ? count($decoded) : 0;
                                                }
                                            }
                                        @endphp
                                        @if($fileCount > 0)
                                            <span class="badge bg-info">
                                                <i class="bi bi-file-earmark me-1"></i>{{ $fileCount }} file(s)
                                            </span>
                                        @else
                                            <span class="text-muted">No files</span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>Completed
                                        </span>
                                        @if($labResult->reviewed_by)
                                            <br><small class="text-muted">by {{ $labResult->reviewed_by }}</small>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#viewModal{{ $labResult->id }}">
                                                <i class="bi bi-eye me-1"></i>View
                                            </button>
                                            <a href="{{ route('doctor.view-patient', $labResult->patient) }}" 
                                               class="btn btn-outline-info btn-sm">
                                                <i class="bi bi-person me-1"></i>Patient
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                                <!-- View Modal -->
                                <div class="modal fade" id="viewModal{{ $labResult->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-success text-white">
                                                <h5 class="modal-title">
                                                    <i class="bi bi-file-earmark-medical me-2"></i>Lab Result Details
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <!-- Patient Info -->
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <h6 class="text-success">Patient Information</h6>
                                                        <p class="mb-1"><strong>Name:</strong> {{ $labResult->patient->first_name }} {{ $labResult->patient->last_name }}</p>
                                                        <p class="mb-1"><strong>ID:</strong> {{ $labResult->patient->patient_code }}</p>
                                                        <p class="mb-0"><strong>Age/Gender:</strong> 
                                                            {{ $labResult->patient->age }} / {{ ucfirst($labResult->patient->gender ?? 'N/A') }}
                                                        </p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6 class="text-success">Test Information</h6>
                                                        <p class="mb-1"><strong>Test Type:</strong> {{ $labResult->test_type ?? $labResult->test_name }}</p>
                                                        <p class="mb-1"><strong>Date:</strong> {{ $labResult->test_date ? $labResult->test_date->format('M d, Y H:i') : 'N/A' }}</p>
                                                        <p class="mb-0"><strong>Category:</strong> {{ $labResult->test_category ?? 'N/A' }}</p>
                                                    </div>
                                                </div>

                                                <!-- Test Results -->
                                                @if($labResult->results || $labResult->result)
                                                    <div class="mb-3">
                                                        <h6 class="text-success">Test Results</h6>
                                                        <div class="bg-light p-3 rounded">
                                                            <pre>{{ $labResult->results ?? $labResult->result }}</pre>
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- File Attachments -->
                                                @php
                                                    $modalAttachments = [];
                                                    if ($labResult->file_attachments) {
                                                        if (is_array($labResult->file_attachments)) {
                                                            $modalAttachments = $labResult->file_attachments;
                                                        } elseif (is_string($labResult->file_attachments)) {
                                                            $decoded = json_decode($labResult->file_attachments, true);
                                                            $modalAttachments = is_array($decoded) ? $decoded : [];
                                                        }
                                                    }
                                                @endphp
                                                @if(count($modalAttachments) > 0)
                                                    <div class="mb-3">
                                                        <h6 class="text-success">File Attachments</h6>
                                                        <div class="row g-2">
                                                            @foreach($modalAttachments as $fileName)
                                                                <div class="col-auto">
                                                                    <div class="card" style="width: 120px;">
                                                                        <div class="card-body p-2 text-center">
                                                                            @php
                                                                                $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                                                                            @endphp
                                                                            @if(in_array($extension, ['jpg', 'jpeg', 'png']))
                                                                                <i class="bi bi-image text-success" style="font-size: 2rem;"></i>
                                                                            @elseif($extension === 'pdf')
                                                                                <i class="bi bi-file-pdf text-danger" style="font-size: 2rem;"></i>
                                                                            @else
                                                                                <i class="bi bi-file-text text-primary" style="font-size: 2rem;"></i>
                                                                            @endif
                                                                            <div class="small text-muted mt-1">
                                                                                {{ strlen($fileName) > 15 ? substr($fileName, 0, 12) . '...' : $fileName }}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Doctor Notes -->
                                                @if($labResult->doctor_notes)
                                                    <div class="mb-3">
                                                        <h6 class="text-success">Doctor's Notes</h6>
                                                        <div class="bg-light p-3 rounded">
                                                            {{ $labResult->doctor_notes }}
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Review Info -->
                                                <div class="mb-3">
                                                    <h6 class="text-success">Review Information</h6>
                                                    <p class="mb-1"><strong>Status:</strong> <span class="badge bg-success">Completed</span></p>
                                                    @if($labResult->reviewed_by)
                                                        <p class="mb-1"><strong>Reviewed By:</strong> {{ $labResult->reviewed_by }}</p>
                                                    @endif
                                                    @if($labResult->reviewed_at)
                                                        <p class="mb-0"><strong>Reviewed At:</strong> {{ $labResult->reviewed_at->format('M d, Y H:i') }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($completedResults->hasPages())
                        <div class="card-footer">
                            {{ $completedResults->appends(['pending_page' => $pendingResults->currentPage()])->links() }}
                        </div>
                    @endif
                @else
                    <!-- Empty State -->
                    <div class="text-center py-5">
                        <i class="bi bi-file-earmark-check text-muted" style="font-size: 4rem;"></i>
                        <h4 class="mt-3 text-muted">No Completed Reviews</h4>
                        <p class="text-muted">You haven't completed any lab result reviews in the last 30 days.</p>
                        <button class="btn btn-primary" onclick="document.getElementById('review-tab').click()">
                            <i class="bi bi-clipboard-pulse me-1"></i>Review Lab Results
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div> <!-- End Completed Results Tab -->

    <!-- Upload Tab -->
    <div class="tab-pane fade" id="upload" role="tabpanel" aria-labelledby="upload-tab">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="bi bi-cloud-upload me-2"></i>Upload Lab Results / X-rays
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('doctor.lab-results.upload') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    <div class="row">
                        <!-- Patient Selection -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="patient_id" class="form-label">
                                    <i class="bi bi-person me-1"></i>Select Patient *
                                </label>
                                <select class="form-select" id="patient_id" name="patient_id" required>
                                    <option value="">Choose a patient...</option>
                                    @foreach($patients as $patient)
                                        <option value="{{ $patient->id }}">
                                            {{ $patient->first_name }} {{ $patient->last_name }} 
                                            @if($patient->patient_code) - ID: {{ $patient->patient_code }}@endif
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Select which patient these results belong to</small>
                            </div>
                        </div>

                        <!-- Test Date -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="test_date" class="form-label">
                                    <i class="bi bi-calendar me-1"></i>Test Date *
                                </label>
                                <input type="date" class="form-control" id="test_date" name="test_date" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Test Type -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="test_type" class="form-label">
                                    <i class="bi bi-clipboard-pulse me-1"></i>Test Type *
                                </label>
                                <select class="form-select" id="test_type" name="test_type" required>
                                    <option value="">Select test type...</option>
                                    <option value="Blood Test">Blood Test</option>
                                    <option value="Urinalysis">Urinalysis</option>
                                    <option value="X-ray">X-ray</option>
                                    <option value="CT Scan">CT Scan</option>
                                    <option value="MRI">MRI</option>
                                    <option value="Ultrasound">Ultrasound</option>
                                    <option value="ECG">ECG</option>
                                    <option value="Echo">Echocardiogram</option>
                                    <option value="Biopsy">Biopsy</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>

                        <!-- Test Category -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="test_category" class="form-label">
                                    <i class="bi bi-tags me-1"></i>Category (Optional)
                                </label>
                                <input type="text" class="form-control" id="test_category" name="test_category" 
                                       placeholder="e.g., Cardiology, Radiology, Hematology">
                            </div>
                        </div>
                    </div>

                    <!-- File Upload -->
                    <div class="mb-3">
                        <label for="files" class="form-label">
                            <i class="bi bi-file-earmark-medical me-1"></i>Upload Files * 
                            <small class="text-muted">(Images, PDFs, Documents)</small>
                        </label>
                        <input type="file" class="form-control" id="files" name="files[]" multiple 
                               accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" required>
                        <small class="form-text text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Supported formats: JPG, PNG, PDF, DOC, DOCX. Max 10MB per file. You can select multiple files.
                        </small>
                        <div id="filePreview" class="mt-2"></div>
                    </div>

                    <!-- Results Text -->
                    <div class="mb-3">
                        <label for="results" class="form-label">
                            <i class="bi bi-file-text me-1"></i>Test Results (Optional)
                        </label>
                        <textarea class="form-control" id="results" name="results" rows="4" 
                                  placeholder="Enter any text results, values, or findings here..."></textarea>
                        <small class="form-text text-muted">You can enter textual results in addition to uploaded files</small>
                    </div>

                    <!-- Doctor Notes -->
                    <div class="mb-3">
                        <label for="notes" class="form-label">
                            <i class="bi bi-chat-square-text me-1"></i>Doctor's Notes & Interpretation
                        </label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="Your clinical interpretation, recommendations, follow-up instructions..."></textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" id="resetBtn">
                            <i class="bi bi-arrow-clockwise me-1"></i>Reset
                        </button>
                        <button type="submit" id="uploadSubmitBtn" class="btn btn-success">
                            <i class="bi bi-cloud-upload me-1"></i>Upload Lab Results
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Instructions -->
        <div class="card shadow mt-4">
            <div class="card-header bg-info text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="bi bi-lightbulb me-2"></i>Upload Instructions
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-primary"><i class="bi bi-1-circle me-1"></i>Select Patient</h6>
                        <p class="small text-muted">Choose the patient whose results you're uploading from the dropdown list.</p>
                        
                        <h6 class="text-primary"><i class="bi bi-2-circle me-1"></i>Choose Test Type</h6>
                        <p class="small text-muted">Select the appropriate test type (Blood Test, X-ray, etc.) from the dropdown.</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-primary"><i class="bi bi-3-circle me-1"></i>Upload Files</h6>
                        <p class="small text-muted">Upload image scans, PDF reports, or documents. Multiple files are supported.</p>
                        
                        <h6 class="text-primary"><i class="bi bi-4-circle me-1"></i>Add Interpretation</h6>
                        <p class="small text-muted">Include your clinical notes and recommendations for the patient.</p>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End Upload Tab -->
</div> <!-- End Tab Content -->

<script>
// File preview functionality with image thumbnails
document.getElementById('files').addEventListener('change', function(e) {
    const filePreview = document.getElementById('filePreview');
    filePreview.innerHTML = '';
    
    if (e.target.files.length > 0) {
        const previewDiv = document.createElement('div');
        previewDiv.className = 'row g-3 mt-2';
        
        Array.from(e.target.files).forEach(file => {
            const colDiv = document.createElement('div');
            colDiv.className = 'col-md-3 col-sm-4 col-6';
            
            const fileCard = document.createElement('div');
            fileCard.className = 'card h-100';
            
            const fileBody = document.createElement('div');
            fileBody.className = 'card-body p-2 text-center';
            
            // Show actual image preview for image files
            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.className = 'img-fluid rounded mb-2';
                img.style.maxHeight = '150px';
                img.style.width = '100%';
                img.style.objectFit = 'cover';
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
                
                fileBody.appendChild(img);
            } else {
                // Show icon for non-image files
                const fileIcon = document.createElement('i');
                if (file.type === 'application/pdf') {
                    fileIcon.className = 'bi bi-file-pdf text-danger';
                } else {
                    fileIcon.className = 'bi bi-file-text text-primary';
                }
                fileIcon.style.fontSize = '3rem';
                fileBody.appendChild(fileIcon);
            }
            
            const fileName = document.createElement('div');
            fileName.className = 'small text-muted mt-2';
            fileName.style.fontWeight = '500';
            fileName.textContent = file.name.length > 20 ? file.name.substring(0, 17) + '...' : file.name;
            
            const fileSize = document.createElement('div');
            fileSize.className = 'small text-muted';
            fileSize.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
            
            fileBody.appendChild(fileName);
            fileBody.appendChild(fileSize);
            fileCard.appendChild(fileBody);
            colDiv.appendChild(fileCard);
            previewDiv.appendChild(colDiv);
        });
        
        filePreview.appendChild(previewDiv);
    }
});

// Prevent double submission of upload form
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('uploadSubmitBtn');
    
    // Check if form is already being submitted
    if (submitBtn.disabled) {
        e.preventDefault();
        return false;
    }
    
    // Disable button and show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Uploading...';
    
    // Allow form to submit normally
    return true;
});

// Reset button functionality
document.getElementById('resetBtn').addEventListener('click', function() {
    // Reset the form
    document.getElementById('uploadForm').reset();
    
    // Clear file preview
    document.getElementById('filePreview').innerHTML = '';
    
    // Re-enable submit button
    const submitBtn = document.getElementById('uploadSubmitBtn');
    submitBtn.disabled = false;
    submitBtn.innerHTML = '<i class="bi bi-cloud-upload me-1"></i>Upload Lab Results';
});
</script>

@endsection
