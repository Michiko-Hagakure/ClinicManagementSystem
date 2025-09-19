<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicalService;

class MedicalServicesController extends Controller
{
    /**
     * Display a listing of medical services
     */
    public function index(Request $request)
    {
        $query = MedicalService::query();

        // Filter by category if provided
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by department if provided
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        // Search by name or code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $services = $query->orderBy('category')->orderBy('name')->paginate(20);

        // Get available categories and departments for filters
        $categories = MedicalService::distinct()->pluck('category');
        $departments = MedicalService::distinct()->whereNotNull('department')->pluck('department');

        return view('services.index', compact('services', 'categories', 'departments'));
    }

    /**
     * Show the form for creating a new medical service
     */
    public function create()
    {
        return view('services.create');
    }

    /**
     * Store a newly created medical service
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:medical_services,code',
            'name' => 'required|string|max:255',
            'category' => 'required|in:consultation,diagnostic,medication,procedure',
            'price' => 'required|numeric|min:0',
            'department' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        MedicalService::create([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'category' => $request->category,
            'price' => $request->price,
            'department' => $request->department,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('services.index')
            ->with('success', 'Medical service created successfully!');
    }

    /**
     * Display the specified medical service
     */
    public function show(MedicalService $service)
    {
        return view('services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified medical service
     */
    public function edit(MedicalService $service)
    {
        return view('services.edit', compact('service'));
    }

    /**
     * Update the specified medical service
     */
    public function update(Request $request, MedicalService $service)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:medical_services,code,' . $service->id,
            'name' => 'required|string|max:255',
            'category' => 'required|in:consultation,diagnostic,medication,procedure',
            'price' => 'required|numeric|min:0',
            'department' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $service->update([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'category' => $request->category,
            'price' => $request->price,
            'department' => $request->department,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('services.index')
            ->with('success', 'Medical service updated successfully!');
    }

    /**
     * Remove the specified medical service
     */
    public function destroy(MedicalService $service)
    {
        // Check if service is used in any bills
        if ($service->billItems()->count() > 0) {
            return redirect()->route('services.index')
                ->with('error', 'Cannot delete service that has been used in medical bills.');
        }

        $service->delete();

        return redirect()->route('services.index')
            ->with('success', 'Medical service deleted successfully!');
    }

    /**
     * Get services by category (API endpoint)
     */
    public function getByCategory($category)
    {
        $services = MedicalService::where('category', $category)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'price']);

        return response()->json($services);
    }

    /**
     * Get service details (API endpoint)
     */
    public function getServiceDetails($id)
    {
        $service = MedicalService::find($id);
        
        if (!$service) {
            return response()->json(['error' => 'Service not found'], 404);
        }

        return response()->json($service);
    }

    /**
     * Toggle service status
     */
    public function toggleStatus(MedicalService $service)
    {
        $service->update(['is_active' => !$service->is_active]);

        $status = $service->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('services.index')
            ->with('success', "Service {$status} successfully!");
    }
}
