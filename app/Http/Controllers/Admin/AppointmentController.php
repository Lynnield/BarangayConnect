<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Appointment, AppointmentSlot, Resident};
use App\Services\AuditService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        return redirect()->route('admin.appointments.slots');
    }

    public function create()
    {
        $residents = Resident::orderBy('full_name')->get(['id', 'full_name']);

        return view('admin.appointments.create', compact('residents'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['managed_by'] = $request->user()->id;
        $appt = Appointment::create($data);
        AuditService::log('Appointments', 'create', null, $appt->toArray(), $appt->appointment_number);

        return redirect()->route('admin.appointments.index')->with('success', 'Appointment created.');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['resident', 'documentRequest.documentType', 'managedBy']);

        return view('admin.appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $residents = Resident::orderBy('full_name')->get(['id', 'full_name']);

        return view('admin.appointments.edit', compact('appointment', 'residents'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $old = $appointment->toArray();
        $appointment->update($this->validated($request));
        AuditService::log('Appointments', 'update', $old, $appointment->fresh()->toArray(), $appointment->appointment_number);

        return redirect()->route('admin.appointments.index')->with('success', 'Appointment updated.');
    }

    public function destroy(Appointment $appointment)
    {
        AuditService::log('Appointments', 'delete', $appointment->toArray(), null, $appointment->appointment_number);
        $appointment->delete();

        return redirect()->route('admin.appointments.index')->with('success', 'Appointment deleted.');
    }

    public function calendar(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $start = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $items = Appointment::with('resident')
            ->whereBetween('appointment_date', [$start->toDateString(), $end->toDateString()])
            ->get();

        return view('admin.appointments.calendar', compact('items', 'month', 'start', 'end'));
    }

    public function slots()
    {
        $slots = AppointmentSlot::orderBy('slot_date')->orderBy('slot_time')->paginate(50);

        return view('admin.appointments.slots', compact('slots'));
    }

    public function storeSlot(Request $request)
    {
        $request->validate([
            'slot_date' => 'required|date',
            'slot_time' => 'required|date_format:H:i',
            'max_appointments' => 'required|integer|min:1|max:50',
        ]);

        AppointmentSlot::updateOrCreate(
            [
                'slot_date' => $request->slot_date,
                'slot_time' => $request->slot_time,
            ],
            [
                'max_appointments' => $request->max_appointments,
                'is_available' => true,
            ]
        );

        return back()->with('success', 'Slot saved.');
    }

    public function editSlot(AppointmentSlot $slot)
    {
        return view('admin.appointments.slots-edit', compact('slot'));
    }

    public function updateSlot(Request $request, AppointmentSlot $slot)
    {
        $request->validate([
            'slot_date' => 'required|date',
            'slot_time' => 'required|date_format:H:i',
            'max_appointments' => 'required|integer|min:1|max:50',
            'is_available' => 'required|boolean',
        ]);

        $slot->update([
            'slot_date' => $request->slot_date,
            'slot_time' => $request->slot_time,
            'max_appointments' => $request->max_appointments,
            'is_available' => $request->is_available,
        ]);

        return redirect()->route('admin.appointments.slots')->with('success', 'Schedule updated.');
    }

    public function destroySlot(AppointmentSlot $slot)
    {
        $slot->delete();

        return back()->with('success', 'Schedule removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'document_request_id' => 'nullable|exists:document_requests,id',
            'resident_id' => 'required|exists:residents,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i',
            'status' => 'required|in:scheduled,confirmed,rescheduled,completed,cancelled,no_show',
            'notes' => 'nullable|string|max:1000',
        ]);
    }
}
