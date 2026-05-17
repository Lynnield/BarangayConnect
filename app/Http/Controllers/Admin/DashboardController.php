<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Resident, DocumentRequest, Appointment, AuditLog, ActivityFeed};
use App\Services\SystemWarningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request, SystemWarningService $warningService)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->get('date_to', now()->format('Y-m-d'));

        // System Warnings
        $warnings = $warningService->getAllWarnings();
        $warningCounts = $warningService->getWarningCounts();

        // Stats
        $stats = [
            'total_residents' => Resident::count(),
            'total_users' => User::count(),
            'total_requests' => DocumentRequest::count(),
            'pending_requests' => DocumentRequest::where('status', 'pending')->count(),
            'approved_requests' => DocumentRequest::where('status', 'approved')->count(),
            'rejected_requests' => DocumentRequest::where('status', 'rejected')->count(),
            'ready_for_pickup' => DocumentRequest::where('status', 'ready_for_pickup')->count(),
            'released_today' => DocumentRequest::where('status', 'released')
                ->whereDate('released_at', today())->count(),
            'upcoming_appointments' => Appointment::where('appointment_date', '>=', today())
                ->whereIn('status', ['scheduled', 'confirmed'])->count(),
        ];

        $monthlyTrend = $this->monthlyRows(now()->subMonths(6));

        // Status breakdown
        $statusBreakdown = DocumentRequest::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')->get()->pluck('count', 'status');

        // Recent activities
        $recentActivities = ActivityFeed::with('user')
            ->latest()->limit(10)->get();

        // Recent requests
        $recentRequests = DocumentRequest::with(['resident', 'documentType'])
            ->latest()->limit(5)->get();

        // Upcoming appointments
        $upcomingAppointments = Appointment::with('resident')
            ->where('appointment_date', '>=', today())
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->orderBy('appointment_date')->orderBy('appointment_time')
            ->limit(5)->get();

        // Request by document type
        $requestsByType = DocumentRequest::with('documentType')
            ->select('document_type_id', DB::raw('COUNT(*) as count'))
            ->groupBy('document_type_id')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'monthlyTrend', 'statusBreakdown',
            'recentActivities', 'recentRequests', 'upcomingAppointments',
            'requestsByType', 'dateFrom', 'dateTo', 'warnings', 'warningCounts'
        ));
    }

    public function chartData(Request $request)
    {
        $type = $request->get('type', 'monthly');

        if ($type === 'monthly') {
            return response()->json($this->monthlyRows(now()->subMonths(12)));
        }

        if ($type === 'status') {
            $data = DocumentRequest::select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')->get();
            return response()->json($data);
        }

        return response()->json([]);
    }

    private function monthlyRows($from)
    {
        return DocumentRequest::query()
            ->where('created_at', '>=', $from)
            ->get(['created_at', 'status'])
            ->groupBy(fn (DocumentRequest $request) => $request->created_at->format('Y-m'))
            ->sortKeys()
            ->map(fn ($items, string $period) => [
                'period' => $period,
                'year' => (int) substr($period, 0, 4),
                'month' => (int) substr($period, 5, 2),
                'total' => $items->count(),
                'approved' => $items->where('status', 'approved')->count(),
                'rejected' => $items->where('status', 'rejected')->count(),
            ])
            ->values();
    }
}
