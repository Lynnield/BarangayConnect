<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\{Appointment, DocumentRequest};

class DashboardController extends Controller
{
    public function index()
    {
        $resident = auth()->user()->resident;

        $stats = [
            'active_requests' => $resident
                ? $resident->documentRequests()->whereNotIn('status', ['released', 'cancelled', 'rejected'])->count()
                : 0,
            'released' => $resident
                ? $resident->documentRequests()->where('status', 'released')->count()
                : 0,
            'upcoming_appointments' => $resident
                ? $resident->appointments()->where('appointment_date', '>=', today())->whereIn('status', ['scheduled', 'confirmed'])->count()
                : 0,
        ];

        $recentRequests = $resident
            ? $resident->documentRequests()->with('documentType')->latest()->limit(5)->get()
            : collect();

        $upcoming = $resident
            ? $resident->appointments()->with('documentRequest')->where('appointment_date', '>=', today())->orderBy('appointment_date')->limit(5)->get()
            : collect();

        return view('resident.dashboard', compact('resident', 'stats', 'recentRequests', 'upcoming'));
    }
}
