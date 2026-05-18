<?php

namespace App\Http\Controllers\Resident;

use App\Http\Controllers\Controller;
use App\Models\Resident;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        $resident = auth()->user()->resident;

        return view('resident.profile.show', compact('resident'));
    }

    public function edit()
    {
        $user = auth()->user();
        $resident = $user->resident;

        return view('resident.profile.edit', compact('user', 'resident'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'suffix' => 'nullable|string|max:50',
            'gender' => 'required|in:male,female,other',
            'birthdate' => 'required|date',
            'civil_status' => 'required|in:single,married,widowed,separated,divorced',
            'house_number' => 'nullable|string|max:50',
            'street' => 'nullable|string|max:255',
            'purok' => 'nullable|string|max:255',
            'barangay' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'res_address' => 'required|string|max:500',
            'contact_number' => 'nullable|string|max:50',
            'occupation' => 'nullable|string|max:255',
            'valid_id_type' => 'nullable|string|max:100',
            'valid_id_number' => 'nullable|string|max:100',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        $fullName = trim(
            $request->first_name . ' ' .
            ($request->middle_name ? $request->middle_name . ' ' : '') .
            $request->last_name .
            ($request->suffix ? ' ' . $request->suffix : '')
        );

        $payload = [
            'full_name' => $fullName,
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'suffix' => $request->suffix,
            'gender' => $request->gender,
            'birthdate' => $request->birthdate,
            'civil_status' => $request->civil_status,
            'house_number' => $request->house_number,
            'street' => $request->street,
            'purok' => $request->purok,
            'barangay' => $request->barangay,
            'city' => $request->city,
            'province' => $request->province,
            'postal_code' => $request->postal_code,
            'address' => $request->res_address,
            'contact_number' => $request->contact_number ?? $user->phone,
            'email' => $request->email,
            'occupation' => $request->occupation,
            'valid_id_type' => $request->valid_id_type,
            'valid_id_number' => $request->valid_id_number,
            'verification_status' => 'pending',
            'verified_by' => null,
            'verified_at' => null,
        ];

        if ($user->resident) {
            $user->resident->update($payload);
        } else {
            $payload['user_id'] = $user->id;
            Resident::create($payload);
        }

        AuditService::log('Residents', 'profile_update', null, $payload, 'Resident profile saved');

        return redirect()->route('resident.profile.show')->with('success', 'Profile updated.');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        $path = $request->file('avatar')->store('avatars', 'public');

        if (auth()->user()->avatar) {
            Storage::disk('public')->delete(auth()->user()->avatar);
        }

        auth()->user()->update(['avatar' => $path]);

        return back()->with('success', 'Avatar updated.');
    }

    private function splitName(string $fullName): array
    {
        $parts = preg_split('/\s+/', trim($fullName)) ?: [];
        $first = array_shift($parts);
        $last = count($parts) ? array_pop($parts) : null;

        return [
            'first_name' => $first,
            'middle_name' => implode(' ', $parts) ?: null,
            'last_name' => $last,
        ];
    }
}
