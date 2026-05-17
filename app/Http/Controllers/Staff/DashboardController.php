<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\{Appointment, DocumentRequest, Resident, ActivityFeed};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'pending_requests' => DocumentRequest::where('status', 'pending')->count(),
            'under_review' => DocumentRequest::where('status', 'under_review')->count(),
            'ready_pickup' => DocumentRequest::where('status', 'ready_for_pickup')->count(),
            'today_appointments' => Appointment::whereDate('appointment_date', today())->count(),
            'residents' => Resident::count(),
        ];

        $recentActivities = ActivityFeed::with('user')->latest()->limit(8)->get();
        $recentRequests = DocumentRequest::with(['resident', 'documentType'])->latest()->limit(6)->get();

        $statusBreakdown = DocumentRequest::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')->get()->pluck('count', 'status');

        return view('staff.dashboard', compact('stats', 'recentActivities', 'recentRequests', 'statusBreakdown'));
    }
}
