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
            'phone' => 'nullable|string|max:30',
            'address' => 'nullable|string|max:500',
            'full_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female,other',
            'birthdate' => 'required|date',
            'civil_status' => 'required|in:single,married,widowed,separated,divorced',
            'res_address' => 'required|string|max:500',
            'contact_number' => 'nullable|string|max:50',
            'occupation' => 'nullable|string|max:255',
            'valid_id_type' => 'nullable|string|max:100',
            'valid_id_number' => 'nullable|string|max:100',
        ]);

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        $payload = [
            'full_name' => $request->full_name,
            ...$this->splitName($request->full_name),
            'gender' => $request->gender,
            'birthdate' => $request->birthdate,
            'civil_status' => $request->civil_status,
            'address' => $request->res_address,
            'contact_number' => $request->contact_number ?? $user->phone,
            'email' => $user->email,
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
