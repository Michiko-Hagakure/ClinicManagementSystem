<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use App\Services\EmrApiService;
use Illuminate\Support\Facades\Log;

class PatientController extends Controller
{
    protected $emrApiService;

    public function __construct(EmrApiService $emrApiService)
    {
        $this->emrApiService = $emrApiService;
    }

    /**
     * Display patient lookup interface.
     */
    public function lookup(): View
    {
        return view('patients.lookup');
    }

    /**
     * Search patients from EMR service.
     */
    public function search(Request $request): JsonResponse
    {
        Log::info('POS Patient search hit.');
        $query = $request->get('q', '');

        if (empty($query)) {
            Log::info('POS Patient search: Empty query.');
            return response()->json([]);
        }

        Log::info('POS Patient search: Searching for query.', ['query' => $query]);
        $patients = $this->emrApiService->searchPatients($query);
        Log::info('POS Patient search: Found patients.', ['count' => count($patients)]);

        // The EmrApiService already maps the data, so we can return it directly
        return response()->json($patients);
    }

    /**
     * API search endpoint for inter-service communication.
     */
    public function apiSearch(string $query): JsonResponse
    {
        return $this->search(request()->merge(['q' => $query]));
    }
}
