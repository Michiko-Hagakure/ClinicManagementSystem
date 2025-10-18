<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportsApiController extends Controller
{
    /**
     * Get patient statistics for owner dashboard
     */
    public function getPatientReport(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth());
        $endDate = $request->input('end_date', now()->endOfDay());

        // Convert to Carbon instances
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // Total patients (all time)
        $totalPatients = Patient::count();

        // New patients registered in period
        $newPatients = Patient::whereBetween('created_at', [$start, $end])->count();

        // Total consultations in period
        $totalConsultations = Consultation::whereBetween('consultation_date', [$start, $end])->count();

        // Services rendered - group by chief_complaint
        $servicesRendered = Consultation::whereBetween('consultation_date', [$start, $end])
            ->whereNotNull('chief_complaint')
            ->select('chief_complaint as service_name', DB::raw('COUNT(*) as count'))
            ->groupBy('chief_complaint')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'service_name' => $item->service_name,
                    'count' => $item->count
                ];
            })
            ->toArray();

        // Daily patient visits
        $dailyVisits = Consultation::whereBetween('consultation_date', [$start, $end])
            ->select(
                DB::raw('DATE(consultation_date) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy(DB::raw('DATE(consultation_date)'))
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('M d'),
                    'count' => $item->count
                ];
            })
            ->toArray();

        // Consultation trends by date
        $consultationTrends = Consultation::whereBetween('consultation_date', [$start, $end])
            ->select(DB::raw('DATE(consultation_date) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy(DB::raw('DATE(consultation_date)'))
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        return response()->json([
            'total_patients' => $totalPatients,
            'new_patients' => $newPatients,
            'total_consultations' => $totalConsultations,
            'services_rendered' => $servicesRendered,
            'daily_visits' => $dailyVisits,
            'consultation_trends' => $consultationTrends,
        ]);
    }
}

