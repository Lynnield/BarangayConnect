<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\{Appointment, Resident};
use App\Services\AuditService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['resident', 'documentRequest']);

        if ($request->filled('date')) {
            $query->whereDate('appointment_date', $request->date);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $appointments = $query->orderBy('appointment_date')->orderBy('appointment_time')->paginate(25)->withQueryString();

        return view('staff.appointments.index', compact('appointments'));
    }

    public function create()
    {
        $residents = Resident::orderBy('full_name')->get(['id', 'full_name']);

        return view('staff.appointments.create', compact('residents'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['managed_by'] = $request->user()->id;
        Appointment::create($data);
        AuditService::log('Appointments', 'create', null, $data, 'Staff created appointment');

        return redirect()->route('staff.appointments.index')->with('success', 'Appointment created.');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['resident', 'documentRequest.documentType']);

        return view('staff.appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $residents = Resident::orderBy('full_name')->get(['id', 'full_name']);

        return view('staff.appointments.edit', compact('appointment', 'residents'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $appointment->update($this->validated($request));
        AuditService::log('Appointments', 'update', null, null, $appointment->appointment_number);

        return redirect()->route('staff.appointments.index')->with('success', 'Appointment updated.');
    }

    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        return redirect()->route('staff.appointments.index')->with('success', 'Deleted.');
    }

    public function calendar(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        $start = \Carbon\Carbon::parse($month . '-01')->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $items = Appointment::with('resident')
            ->whereBetween('appointment_date', [$start->toDateString(), $end->toDateString()])
            ->get();

        return view('staff.appointments.calendar', compact('items', 'month', 'start', 'end'));
    }

    public function confirm(Appointment $appointment)
    {
        $appointment->update(['status' => 'confirmed', 'managed_by' => auth()->id()]);

        return back()->with('success', 'Appointment confirmed.');
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
