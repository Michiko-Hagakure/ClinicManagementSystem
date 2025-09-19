<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * EMR API Service - Handles communication with EMR service for patient data
 */
class EmrApiService
{
    private string $emrBaseUrl;
    private int $timeout;

    public function __construct()
    {
        $this->emrBaseUrl = config('services.emr.base_url', 'http://127.0.0.1:8001');
        $this->timeout = config('services.emr.timeout', 10);
    }

    /**
     * Search patients in EMR system
     */
    public function searchPatients(string $query): array
    {
        try {
            Log::info('EmrApiService: Sending search request to EMR.', ['query' => $query, 'url' => "{$this->emrBaseUrl}/api/v1/patients"]);
            $response = Http::timeout($this->timeout)
                ->get("{$this->emrBaseUrl}/api/v1/patients", [
                    'q' => $query
                ]);

            Log::info('EmrApiService: Received response from EMR.', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $patients = $response->json();
                
                // EMR now returns data in the correct format, no mapping needed
                return $patients;
            }

            Log::warning('EMR API search failed', [
                'query' => $query,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [];
        } catch (Exception $e) {
            Log::error('EMR API search error', [
                'query' => $query,
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Get patient details by ID from EMR system
     */
    public function getPatient(int $patientId): ?array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->emrBaseUrl}/api/v1/patients/{$patientId}");

            if ($response->successful()) {
                $patient = $response->json();
                return $this->mapEmrPatientToPos($patient);
            }

            Log::warning('EMR API get patient failed', [
                'patient_id' => $patientId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (Exception $e) {
            Log::error('EMR API get patient error', [
                'patient_id' => $patientId,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Get all patients from EMR system (for dropdown/selection)
     */
    public function getAllPatients(int $limit = 100): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->emrBaseUrl}/api/v1/patients", [
                    'limit' => $limit
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $patients = $data['data'] ?? $data; // Handle paginated or direct array response
                
                return collect($patients)->map(function ($patient) {
                    return $this->mapEmrPatientToPos($patient);
                })->toArray();
            }

            Log::warning('EMR API get all patients failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [];
        } catch (Exception $e) {
            Log::error('EMR API get all patients error', [
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Create new patient in EMR system
     */
    public function createPatient(array $patientData): ?array
    {
        try {
            // Map POS format to EMR format
            $emrPatientData = $this->mapPosPatientToEmr($patientData);

            $response = Http::timeout($this->timeout)
                ->post("{$this->emrBaseUrl}/api/v1/patients", $emrPatientData);

            if ($response->successful()) {
                $patient = $response->json();
                return $this->mapEmrPatientToPos($patient);
            }

            Log::warning('EMR API create patient failed', [
                'data' => $emrPatientData,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (Exception $e) {
            Log::error('EMR API create patient error', [
                'data' => $patientData,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Check if EMR service is available
     */
    public function isEmrServiceAvailable(): bool
    {
        try {
            $response = Http::timeout(5)->get("{$this->emrBaseUrl}/");
            return $response->successful();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Map EMR patient format to POS patient format
     */
    private function mapEmrPatientToPos(array $emrPatient): array
    {
        return [
            'id' => $emrPatient['id'] ?? null,
            'patient_code' => 'P' . str_pad($emrPatient['id'] ?? 0, 4, '0', STR_PAD_LEFT),
            'first_name' => $emrPatient['first_name'] ?? '',
            'last_name' => $emrPatient['last_name'] ?? '',
            'middle_name' => $emrPatient['middle_name'] ?? '', // This column does not exist in EMR
            'full_name' => $emrPatient['full_name'] ?? $this->buildFullName($emrPatient),
            'date_of_birth' => $emrPatient['date_of_birth'] ?? null,
            'gender' => $this->mapGender($emrPatient['gender'] ?? ''),
            'phone' => $emrPatient['phone_number'] ?? '',
            'email' => $emrPatient['email'] ?? null,
            'address' => $emrPatient['address'] ?? '',
            'insurance_provider' => null, // EMR doesn't have insurance field
            'age' => $emrPatient['age'] ?? null,
            'civil_status' => $emrPatient['civil_status'] ?? null,
            'created_at' => $emrPatient['created_at'] ?? now(),
            'updated_at' => $emrPatient['updated_at'] ?? now()
        ];
    }

    /**
     * Map POS patient format to EMR patient format
     */
    private function mapPosPatientToEmr(array $posPatient): array
    {
        return [
            'first_name' => $posPatient['first_name'] ?? '',
            'last_name' => $posPatient['last_name'] ?? '',
            'date_of_birth' => $posPatient['date_of_birth'] ?? null,
            'gender' => $this->mapGenderToEmr($posPatient['gender'] ?? ''),
            'phone_number' => $posPatient['phone'] ?? '',
            'address' => $posPatient['address'] ?? '',
            'civil_status' => $posPatient['civil_status'] ?? 'Single',
        ];
    }

    /**
     * Build full name from EMR patient data
     */
    private function buildFullName(array $emrPatient): string
    {
        $parts = array_filter([
            $emrPatient['first_name'] ?? '',
            // 'middle_name' is not available in the EMR patient data
            $emrPatient['last_name'] ?? ''
        ]);

        return implode(' ', $parts);
    }

    /**
     * Map EMR gender to POS gender
     */
    private function mapGender(string $emrGender): string
    {
        return match (strtolower($emrGender)) {
            'male', 'm' => 'Male',
            'female', 'f' => 'Female',
            default => $emrGender
        };
    }

    /**
     * Map POS gender to EMR gender
     */
    private function mapGenderToEmr(string $posGender): string
    {
        return match (strtolower($posGender)) {
            'male', 'm' => 'Male',
            'female', 'f' => 'Female',
            default => $posGender
        };
    }

    /**
     * Calculate age from birth date
     */
    private function calculateAge(?string $birthDate): ?int
    {
        if (!$birthDate) {
            return null;
        }

        try {
            return now()->diffInYears($birthDate);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Create consultation in EMR system
     */
    public function createConsultation(array $consultationData): ?array
    {
        try {
            Log::info('EmrApiService: Creating consultation in EMR', [
                'data' => $consultationData,
                'url' => "{$this->emrBaseUrl}/api/v1/consultations"
            ]);

            $response = Http::timeout($this->timeout)
                ->post("{$this->emrBaseUrl}/api/v1/consultations", $consultationData);

            Log::info('EmrApiService: Received consultation creation response from EMR', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $consultation = $response->json();
                return $consultation;
            }

            Log::warning('EMR API consultation creation failed', [
                'data' => $consultationData,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (Exception $e) {
            Log::error('EMR API consultation creation error', [
                'data' => $consultationData,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }
}
