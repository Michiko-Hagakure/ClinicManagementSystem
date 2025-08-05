@extends('layouts.app')

@section('title', 'Patient Record')

@section('content')
    {{-- Page Header with a "Back" button --}}
    <div class="header p-3 mb-4 d-flex justify-content-between align-items-center">
        {{-- This name will be dynamic later --}}
        <h3 class="mb-0">Patient Record: Juan Dela Cruz</h3> 
        <a href="{{ route('emr.index') }}" class="btn btn-secondary btn-sm">← Back to Patient List</a>
    </div>

    {{-- Main Content Card with Tabbed Interface --}}
    <div class="card">
        <div class="card-body">
            
            {{-- Tab Navigation --}}
            <ul class="nav nav-tabs" id="patientTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="consultation-tab" data-bs-toggle="tab" data-bs-target="#consultation" type="button" role="tab" aria-controls="consultation" aria-selected="true">Consultation History</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="lab-results-tab" data-bs-toggle="tab" data-bs-target="#lab-results" type="button" role="tab" aria-controls="lab-results" aria-selected="false">Lab & Imaging Results</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="demographics-tab" data-bs-toggle="tab" data-bs-target="#demographics" type="button" role="tab" aria-controls="demographics" aria-selected="false">Patient Demographics</button>
                </li>
            </ul>

            {{-- Tab Content --}}
            <div class="tab-content pt-4" id="patientTabContent">
                
                {{-- Consultation History Tab Pane --}}
                <div class="tab-pane fade show active" id="consultation" role="tabpanel" aria-labelledby="consultation-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>Consultations</h4>
                        <a href="#" class="btn btn-primary btn-sm">+ Add Consultation Note</a>
                    </div>
                    <p>A list of past consultations will go here.</p>
                </div>
                
                {{-- Lab Results Tab Pane --}}
                <div class="tab-pane fade" id="lab-results" role="tabpanel" aria-labelledby="lab-results-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4>Lab & Imaging</h4>
                        <a href="#" class="btn btn-primary btn-sm">+ Upload Lab/X-ray Result</a>
                    </div>
                    <p>A table of uploaded lab results will go here.</p>
                </div>
                
                {{-- Demographics Tab Pane --}}
                <div class="tab-pane fade" id="demographics" role="tabpanel" aria-labelledby="demographics-tab">
                    <h4>Demographic Information</h4>
                    <p>The patient's detailed info (address, contact, etc.) will be displayed here.</p>
                </div>

            </div>

        </div>
    </div>
@endsection