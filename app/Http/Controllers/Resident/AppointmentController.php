<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\{Appointment, AppointmentSlot, DocumentRequest, Resident};
use App\Services\AuditService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $resident = $this->resident();
        if (! $resident) {
            return redirect()->route('resident.profile.edit')
                ->with('warning', 'Please complete your resident profile first.');
        }
        $appointments = $resident->appointments()->with('documentRequest.documentType')->latest('appointment_date')->paginate(15);

        return view('resident.appointments.index', compact('appointments'));
    }

    public function create(?DocumentRequest $documentRequest = null)
    {
        $resident = $this->resident();
        if (! $resident) {
            return redirect()->route('resident.profile.edit')
                ->with('warning', 'Please complete your resident profile first.');
        }

        if ($documentRequest && $documentRequest->resident_id !== $resident->id) {
            abort(403);
        }

        return view('resident.appointments.create', compact('documentRequest'));
    }

    public function store(Request $request)
    {
        $resident = $this->resident();
        if (! $resident) {
            return redirect()->route('resident.profile.edit')
                ->with('warning', 'Please complete your resident profile first.');
        }

        $request->validate([
            'document_request_id' => 'nullable|exists:document_requests,id',
            'slot_date' => 'required|date|after_or_equal:today',
            'slot_time' => 'required|date_format:H:i',
        ]);

        if ($request->filled('document_request_id')) {
            $dr = DocumentRequest::findOrFail($request->document_request_id);
            abort_if($dr->resident_id !== $resident->id, 403);
        }

        $slot = AppointmentSlot::where('slot_date', $request->slot_date)
            ->where('slot_time', $request->slot_time)
            ->where('is_available', true)
            ->first();

        if (! $slot) {
            return back()->withErrors(['slot_time' => 'Selected slot is not available.']);
        }

        $booked = Appointment::whereDate('appointment_date', $request->slot_date)
            ->whereTime('appointment_time', $request->slot_time)
            ->whereIn('status', ['scheduled', 'confirmed', 'rescheduled'])
            ->count();

        if ($booked >= $slot->max_appointments) {
            return back()->withErrors(['slot_time' => 'This slot is fully booked.']);
        }

        $appt = Appointment::create([
            'document_request_id' => $request->document_request_id,
            'resident_id' => $resident->id,
            'appointment_date' => $request->slot_date,
            'appointment_time' => $request->slot_time,
            'status' => 'scheduled',
        ]);

        AuditService::log('Appointments', 'resident_book', null, $appt->toArray(), $appt->appointment_number);

        return redirect()->route('resident.appointments.show', $appt)->with('success', 'Appointment scheduled.');
    }

    public function show(Appointment $appointment)
    {
        $resident = $this->resident();
        abort_if(! $resident || $appointment->resident_id !== $resident->id, 403);
        $appointment->load(['documentRequest.documentType']);

        return view('resident.appointments.show', compact('appointment'));
    }

    public function reschedule(Request $request, Appointment $appointment)
    {
        $resident = $this->resident();
        abort_if(! $resident || $appointment->resident_id !== $resident->id, 403);

        $request->validate([
            'slot_date' => 'required|date|after_or_equal:today',
            'slot_time' => 'required|date_format:H:i',
        ]);

        $appointment->update([
            'appointment_date' => $request->slot_date,
            'appointment_time' => $request->slot_time,
            'status' => 'rescheduled',
        ]);

        return back()->with('success', 'Appointment rescheduled.');
    }

    public function cancel(Appointment $appointment)
    {
        $resident = $this->resident();
        abort_if(! $resident || $appointment->resident_id !== $resident->id, 403);
        $appointment->update(['status' => 'cancelled']);

        return back()->with('success', 'Appointment cancelled.');
    }

    public function getAvailableSlots(Request $request)
    {
        $request->validate(['date' => 'required|date']);

        $slots = AppointmentSlot::whereDate('slot_date', $request->date)
            ->where('is_available', true)
            ->orderBy('slot_time')
            ->get()
            ->map(function ($slot) {
                $booked = Appointment::whereDate('appointment_date', $slot->slot_date)
                    ->whereTime('appointment_time', $slot->slot_time)
                    ->whereIn('status', ['scheduled', 'confirmed', 'rescheduled'])
                    ->count();

                return [
                    'time' => Carbon::parse($slot->slot_time)->format('H:i'),
                    'available' => $booked < $slot->max_appointments,
                ];
            });

        return response()->json($slots);
    }

    private function resident(): ?Resident
    {
        return auth()->user()->resident;
    }
}
