<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use App\Notifications\ResidentVerificationNotification;
use App\Services\AuditService;
use Illuminate\Http\Request;

class ResidentController extends Controller
{
    public function index(Request $request)
    {
        $query = Resident::with('user');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('full_name', 'like', "%{$s}%")
                    ->orWhere('first_name', 'like', "%{$s}%")
                    ->orWhere('middle_name', 'like', "%{$s}%")
                    ->orWhere('last_name', 'like', "%{$s}%")
                    ->orWhere('resident_number', 'like', "%{$s}%");
            });
        }

        $residents = $query->orderBy('full_name')->paginate(25)->withQueryString();

        return view('staff.residents.index', compact('residents'));
    }

    public function show(Resident $resident)
    {
        $resident->load(['documentRequests.documentType', 'appointments']);

        return view('staff.residents.show', compact('resident'));
    }

    public function edit(Resident $resident)
    {
        return view('staff.residents.edit', compact('resident'));
    }

    public function update(Request $request, Resident $resident)
    {
        $data = $request->validate([
            'first_name' => 'required_without:full_name|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'last_name' => 'required_without:full_name|string|max:100',
            'suffix' => 'nullable|string|max:30',
            'full_name' => 'nullable|string|max:255',
            'gender' => 'required|in:male,female,other',
            'birthdate' => 'required|date',
            'civil_status' => 'required|in:single,married,widowed,separated,divorced',
            'house_number' => 'nullable|string|max:100',
            'street' => 'nullable|string|max:150',
            'purok' => 'nullable|string|max:100',
            'barangay' => 'nullable|string|max:150',
            'city' => 'nullable|string|max:150',
            'province' => 'nullable|string|max:150',
            'postal_code' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'contact_number' => 'nullable|string|max:50',
            'email' => 'nullable|email',
            'occupation' => 'nullable|string|max:255',
            'valid_id_type' => 'nullable|string|max:100',
            'valid_id_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $data['full_name'] = $this->composeFullName($data);
        $data['address'] = $data['address'] ?: $this->composeAddress($data);
        $data['verification_status'] = 'pending';
        $data['verified_by'] = null;
        $data['verified_at'] = null;

        $resident->update($data);
        AuditService::log('Residents', 'staff_update', null, $data, $resident->full_name);

        return redirect()->route('staff.residents.show', $resident)->with('success', 'Resident updated.');
    }

    public function approveVerification(Resident $resident)
    {
        $resident->update([
            'verification_status' => 'verified',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        AuditService::log('Residents', 'verify', null, ['verification_status' => 'verified'], $resident->full_name);
        $resident->user?->notify(new ResidentVerificationNotification($resident, 'verified'));

        return back()->with('success', 'Resident verification approved.');
    }

    public function rejectVerification(Request $request, Resident $resident)
    {
        $data = $request->validate([
            'verification_notes' => 'nullable|string|max:1000',
        ]);

        $resident->update([
            'verification_status' => 'rejected',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'notes' => $data['verification_notes'] ?? $resident->notes,
        ]);

        AuditService::log('Residents', 'reject_verification', null, $data, $resident->full_name);
        $resident->user?->notify(new ResidentVerificationNotification($resident, 'rejected', $data['verification_notes'] ?? null));

        return back()->with('success', 'Resident verification rejected.');
    }

    private function composeFullName(array $data): string
    {
        return implode(' ', array_filter([
            $data['first_name'] ?? null,
            $data['middle_name'] ?? null,
            $data['last_name'] ?? null,
            $data['suffix'] ?? null,
        ]));
    }

    private function composeAddress(array $data): string
    {
        return implode(', ', array_filter([
            $data['house_number'] ?? null,
            $data['street'] ?? null,
            $data['purok'] ?? null,
            $data['barangay'] ?? null,
            $data['city'] ?? null,
            $data['province'] ?? null,
            $data['postal_code'] ?? null,
        ]));
    }
}
