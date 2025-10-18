<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Inventory API Service - Handles communication with Inventory service for medicine data
 */
class InventoryApiService
{
    private string $inventoryBaseUrl;
    private int $timeout;

    public function __construct()
    {
        $this->inventoryBaseUrl = config('services.inventory.base_url', 'http://127.0.0.1:8003');
        $this->timeout = config('services.inventory.timeout', 10);
    }

    /**
     * Search medicines in inventory system
     */
    public function searchMedicines(string $query = '', int $limit = 10): array
    {
        try {
            Log::info('InventoryApiService: Sending search request to Inventory.', [
                'query' => $query, 
                'url' => "{$this->inventoryBaseUrl}/api/v1/medicines/search"
            ]);

            $response = Http::timeout($this->timeout)
                ->get("{$this->inventoryBaseUrl}/api/v1/public/medicines/search", [
                    'q' => $query,
                    'limit' => $limit
                ]);

            Log::info('InventoryApiService: Received response from Inventory.', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $medicines = $response->json();
                
                // Map inventory format to POS format
                return collect($medicines)->map(function ($medicine) {
                    return $this->mapInventoryMedicineToPos($medicine);
                })->toArray();
            }

            Log::warning('Inventory API search failed', [
                'query' => $query,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [];
        } catch (Exception $e) {
            Log::error('Inventory API search error', [
                'query' => $query,
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Get medicine details by ID from inventory system
     */
    public function getMedicine(int $medicineId): ?array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->inventoryBaseUrl}/api/v1/public/medicines/{$medicineId}");

            if ($response->successful()) {
                $result = $response->json();
                
                if ($result['success'] ?? false) {
                    return $this->mapInventoryMedicineToPos($result['medicine']);
                }
            }

            Log::warning('Inventory API get medicine failed', [
                'medicine_id' => $medicineId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return null;
        } catch (Exception $e) {
            Log::error('Inventory API get medicine error', [
                'medicine_id' => $medicineId,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Get all medicines from inventory system (for POS display)
     */
    public function getAllMedicines(int $limit = 100): array
    {
        try {
            // Use search with empty query to get all medicines
            $response = Http::timeout($this->timeout)
                ->get("{$this->inventoryBaseUrl}/api/v1/public/medicines/search", [
                    'q' => '',
                    'limit' => $limit
                ]);

            if ($response->successful()) {
                $medicines = $response->json();
                
                return collect($medicines)->map(function ($medicine) {
                    return $this->mapInventoryMedicineToPos($medicine);
                })->toArray();
            }

            Log::warning('Inventory API get all medicines failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [];
        } catch (Exception $e) {
            Log::error('Inventory API get all medicines error', [
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Reduce medicine stock after sale
     */
    public function reduceStock(int $medicineId, int $quantity, ?int $patientId = null): bool
    {
        try {
            Log::info('InventoryApiService: Reducing medicine stock', [
                'medicine_id' => $medicineId,
                'quantity' => $quantity,
                'patient_id' => $patientId,
                'url' => "{$this->inventoryBaseUrl}/api/v1/medicines/{$medicineId}/reduce-stock"
            ]);

            $payload = ['quantity' => $quantity];
            if ($patientId !== null) {
                $payload['patient_id'] = $patientId;
            }

            $response = Http::timeout($this->timeout)
                ->post("{$this->inventoryBaseUrl}/api/v1/public/medicines/{$medicineId}/reduce-stock", $payload);

            Log::info('InventoryApiService: Received reduce stock response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $result = $response->json();
                return $result['success'] ?? false;
            }

            Log::warning('Inventory API reduce stock failed', [
                'medicine_id' => $medicineId,
                'quantity' => $quantity,
                'patient_id' => $patientId,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return false;
        } catch (Exception $e) {
            Log::error('Inventory API reduce stock error', [
                'medicine_id' => $medicineId,
                'quantity' => $quantity,
                'patient_id' => $patientId,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Check if inventory service is available
     */
    public function isInventoryServiceAvailable(): bool
    {
        try {
            $response = Http::timeout(5)->get("{$this->inventoryBaseUrl}/");
            return $response->successful();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Map inventory medicine format to POS format
     */
    private function mapInventoryMedicineToPos(array $inventoryMedicine): array
    {
        return [
            'id' => $inventoryMedicine['medicine_id'] ?? null,
            'medicine_id' => $inventoryMedicine['medicine_id'] ?? null,
            'name' => $inventoryMedicine['name'] ?? '',
            'dosage' => $inventoryMedicine['dosage'] ?? '',
            'price' => (float) ($inventoryMedicine['price'] ?? 0),
            'stock' => (int) ($inventoryMedicine['stock_quantity'] ?? 0),
            'stock_quantity' => (int) ($inventoryMedicine['stock_quantity'] ?? 0),
            'category' => $inventoryMedicine['category'] ?? 'General',
            'created_at' => $inventoryMedicine['created_at'] ?? now(),
            'updated_at' => $inventoryMedicine['updated_at'] ?? now()
        ];
    }

    /**
     * Determine medicine category based on name (simple heuristic)
     * This is a fallback - ideally inventory service would provide category
     */
    private function determineMedicineCategory(string $medicineName): string
    {
        $medicineName = strtolower($medicineName);
        
        if (str_contains($medicineName, 'acetyl') || str_contains($medicineName, 'paracetamol') || 
            str_contains($medicineName, 'ibuprofen') || str_contains($medicineName, 'aspirin')) {
            return 'Pain Relief';
        }
        
        if (str_contains($medicineName, 'amoxicillin') || str_contains($medicineName, 'azithromycin') ||
            str_contains($medicineName, 'ciprofloxacin')) {
            return 'Antibiotic';
        }
        
        if (str_contains($medicineName, 'cetirizine') || str_contains($medicineName, 'loratadine')) {
            return 'Antihistamine';
        }
        
        if (str_contains($medicineName, 'omeprazole') || str_contains($medicineName, 'simethicone')) {
            return 'Gastrointestinal';
        }
        
        if (str_contains($medicineName, 'vitamin') || str_contains($medicineName, 'calcium')) {
            return 'Vitamin/Supplement';
        }
        
        return 'General';
    }
}

