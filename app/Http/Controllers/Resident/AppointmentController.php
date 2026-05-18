<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\{Appointment, AppointmentSlot, DocumentRequest, Resident};
use App\Services\AuditService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AppointmentController extends Controller
{
    private const ACTIVE_STATUSES = ['scheduled', 'confirmed', 'rescheduled'];

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

        $availableSchedules = AppointmentSlot::where('is_available', true)
            ->whereDate('slot_date', '>=', today()->toDateString())
            ->orderBy('slot_date')
            ->orderBy('slot_time')
            ->get();

        $availableDates = $availableSchedules->pluck('slot_date')->unique()->map(function ($date) {
            return $this->formatSlotDate($date);
        })->values();

        $defaultDate = $availableDates->first() ?? today()->format('Y-m-d');

        $availableTimes = $this->mapAvailableSlots(
            $this->slotsForDate($availableSchedules, $defaultDate)
        );

        return view('resident.appointments.create', compact('documentRequest', 'defaultDate', 'availableDates', 'availableTimes'));
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
            'schedule_id' => 'required|exists:appointment_slots,id',
        ]);

        if ($request->filled('document_request_id')) {
            $dr = DocumentRequest::findOrFail($request->document_request_id);
            abort_if($dr->resident_id !== $resident->id, 403);
        }

        $slot = $this->resolveSlot((int) $request->schedule_id, $request->slot_date);

        if (! $slot) {
            return back()->withErrors(['slot_date' => 'Selected schedule is not available.'])->withInput();
        }

        if ($this->countBookingsForSlot($slot) >= $slot->max_appointments) {
            return back()->withErrors(['slot_date' => 'This slot is fully booked.'])->withInput();
        }

        $appt = Appointment::create([
            'document_request_id' => $request->document_request_id,
            'resident_id' => $resident->id,
            'schedule_id' => $slot->id,
            'appointment_date' => $slot->slot_date,
            'appointment_time' => $this->normalizeTime($slot->slot_time),
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

        $canManage = in_array($appointment->status, self::ACTIVE_STATUSES, true);
        $availableDates = collect();
        $defaultDate = today()->format('Y-m-d');
        $availableTimes = collect();

        if ($canManage) {
            $availableSchedules = AppointmentSlot::where('is_available', true)
                ->whereDate('slot_date', '>=', today()->toDateString())
                ->orderBy('slot_date')
                ->orderBy('slot_time')
                ->get();

            $availableDates = $availableSchedules->pluck('slot_date')->unique()->map(function ($date) {
                return $this->formatSlotDate($date);
            })->values();

            $defaultDate = $availableDates->first() ?? $defaultDate;

            $availableTimes = $this->mapAvailableSlots(
                $this->slotsForDate($availableSchedules, $defaultDate),
                $appointment->id
            );
        }

        return view('resident.appointments.show', compact(
            'appointment',
            'canManage',
            'availableDates',
            'defaultDate',
            'availableTimes'
        ));
    }

    public function reschedule(Request $request, Appointment $appointment)
    {
        $resident = $this->resident();
        abort_if(! $resident || $appointment->resident_id !== $resident->id, 403);
        abort_if(! in_array($appointment->status, self::ACTIVE_STATUSES, true), 403);

        $request->validate([
            'slot_date' => 'required|date|after_or_equal:today',
            'schedule_id' => 'required|exists:appointment_slots,id',
        ]);

        $slot = $this->resolveSlot((int) $request->schedule_id, $request->slot_date);

        if (! $slot) {
            return back()->withErrors(['slot_date' => 'Selected schedule is not available.'])->withInput();
        }

        if ($this->countBookingsForSlot($slot, $appointment->id) >= $slot->max_appointments) {
            return back()->withErrors(['slot_date' => 'This slot is fully booked.'])->withInput();
        }

        $appointment->update([
            'schedule_id' => $slot->id,
            'appointment_date' => $slot->slot_date,
            'appointment_time' => $this->normalizeTime($slot->slot_time),
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
        $request->validate([
            'date' => 'required|date',
            'except' => 'nullable|integer|exists:appointments,id',
        ]);

        $slots = AppointmentSlot::whereDate('slot_date', $request->date)
            ->where('is_available', true)
            ->orderBy('slot_time')
            ->get();

        $exceptId = $request->filled('except') ? (int) $request->except : null;

        return response()->json($this->mapAvailableSlots($slots, $exceptId));
    }

    private function resident(): ?Resident
    {
        return auth()->user()->resident;
    }

    private function resolveSlot(int $scheduleId, string $slotDate): ?AppointmentSlot
    {
        return AppointmentSlot::query()
            ->whereKey($scheduleId)
            ->whereDate('slot_date', $slotDate)
            ->where('is_available', true)
            ->first();
    }

    private function countBookingsForSlot(AppointmentSlot $slot, ?int $exceptAppointmentId = null): int
    {
        $query = Appointment::query()
            ->whereDate('appointment_date', $slot->slot_date)
            ->whereTime('appointment_time', $this->normalizeTime($slot->slot_time))
            ->whereIn('status', self::ACTIVE_STATUSES);

        if ($exceptAppointmentId) {
            $query->whereKeyNot($exceptAppointmentId);
        }

        return $query->count();
    }

    private function mapAvailableSlots(Collection $slots, ?int $exceptAppointmentId = null): Collection
    {
        return $slots->map(function (AppointmentSlot $slot) use ($exceptAppointmentId) {
            $booked = $this->countBookingsForSlot($slot, $exceptAppointmentId);

            return [
                'id' => $slot->id,
                'time' => Carbon::parse($slot->slot_time)->format('g:i A'),
                'time_value' => Carbon::parse($slot->slot_time)->format('H:i'),
                'available' => $booked < $slot->max_appointments,
            ];
        })->filter(fn (array $slot) => $slot['available'])->values();
    }

    private function slotsForDate(Collection $schedules, string $date): Collection
    {
        return $schedules->filter(
            fn (AppointmentSlot $slot) => $this->formatSlotDate($slot->slot_date) === $this->formatSlotDate($date)
        )->values();
    }

    private function formatSlotDate(mixed $date): string
    {
        return Carbon::parse($date)->toDateString();
    }

    private function normalizeTime(mixed $time): string
    {
        return Carbon::parse($time)->format('H:i:s');
    }
}
