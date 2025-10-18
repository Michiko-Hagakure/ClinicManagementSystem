<?php

namespace App\Helpers;

use App\Services\EmrApiService;
use App\Services\InventoryApiService;
use Illuminate\Support\Facades\Cache;

class ServiceHealthHelper
{
    /**
     * Check EMR service health
     */
    public static function isEmrHealthy(): bool
    {
        return Cache::remember('service_health_emr', 60, function () {
            try {
                $service = app(EmrApiService::class);
                return $service->isEmrServiceAvailable();
            } catch (\Exception $e) {
                return false;
            }
        });
    }

    /**
     * Check Inventory service health
     */
    public static function isInventoryHealthy(): bool
    {
        return Cache::remember('service_health_inventory', 60, function () {
            try {
                $service = app(InventoryApiService::class);
                return $service->isInventoryServiceAvailable();
            } catch (\Exception $e) {
                return false;
            }
        });
    }

    /**
     * Get all services health status
     */
    public static function getAllServicesHealth(): array
    {
        return [
            'emr' => [
                'name' => 'EMR Service',
                'status' => self::isEmrHealthy(),
                'url' => config('services.emr.base_url', 'http://127.0.0.1:8001'),
                'description' => 'Patient records and consultations'
            ],
            'inventory' => [
                'name' => 'Inventory Service',
                'status' => self::isInventoryHealthy(),
                'url' => config('services.inventory.base_url', 'http://127.0.0.1:8003'),
                'description' => 'Medicine and stock management'
            ],
        ];
    }

    /**
     * Get user-friendly error message for service failure
     */
    public static function getServiceErrorMessage(string $serviceName): string
    {
        $messages = [
            'emr' => 'The Patient Records system is currently unavailable. Transactions can still be processed, but patient appointments may not be created automatically. Please contact IT support if this persists.',
            'inventory' => 'The Inventory system is currently unavailable. Medicine dispensing may not update stock levels automatically. Please manually verify stock after the system is restored.',
        ];

        return $messages[$serviceName] ?? 'An external service is currently unavailable. Please contact IT support.';
    }

    /**
     * Clear service health cache
     */
    public static function clearHealthCache(): void
    {
        Cache::forget('service_health_emr');
        Cache::forget('service_health_inventory');
    }
}

