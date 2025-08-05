@extends('layouts.app')

@section('title', 'EMR - Patient Records')

@section('content')
    {{-- Page Header --}}
    <div class="header p-3 mb-4 d-flex justify-content-between align-items-center">
        <h3 class="mb-0">EMR - Patient Records</h3>
    </div>

    {{-- Main Content Card --}}
    <div class="card">
        <div class="card-body">
            
            {{-- Action Bar with Search and Add Button --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="w-50">
                    <input type="text" class="form-control" placeholder="Search by Last Name, First Name...">
                </div>
                <a href="#" class="btn btn-primary">+ Add New Patient</a>
            </div>

            {{-- Patient List Table --}}
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">Patient ID</th>
                            <th scope="col">Patient Name</th>
                            <th scope="col">Contact Number</th>
                            <th scope="col">Date Registered</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Placeholder Data Row 1 --}}
                        <tr>
                            <td>P-001</td>
                            <td>Juan Dela Cruz</td>
                            <td>0917-123-4567</td>
                            <td>2025-08-01</td>
                            <td>
                                <a href="{{ route('emr.show', 1) }}" class="btn btn-info btn-sm">View</a>
                                <a href="#" class="btn btn-secondary btn-sm">Edit</a>
                            </td>
                        </tr>

                        {{-- Placeholder Data Row 2 --}}
                        <tr>
                            <td>P-002</td>
                            <td>Maria Clara</td>
                            <td>0928-987-6543</td>
                            <td>2025-07-25</td>
                            <td>
                                <a href="{{ route('emr.show', 2) }}" class="btn btn-info btn-sm">View</a>
                                <a href="#" class="btn btn-secondary btn-sm">Edit</a>
                            </td>
                        </tr>

                        {{-- Placeholder Data Row 3 --}}
                        <tr>
                            <td>P-003</td>
                            <td>Jose Rizal</td>
                            <td>0999-555-1212</td>
                            <td>2025-07-15</td>
                            <td>
                                <a href="{{ route('emr.show', 3) }}" class="btn btn-info btn-sm">View</a>
                                <a href="#" class="btn btn-secondary btn-sm">Edit</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
@endsection