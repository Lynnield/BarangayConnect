<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    public function impersonate(Request $request, User $user)
    {
        $admin = Auth::user();

        // Only admins can impersonate, and they can't impersonate themselves
        if (!$admin->isAdmin() || $admin->id === $user->id) {
            abort(403);
        }

        // Store original admin ID in session
        session()->put('impersonated_by', $admin->id);

        Auth::login($user);

        AuditService::log('Admin', 'impersonation_start', null, ['target_user_id' => $user->id], "Admin started impersonating user: {$user->email}");

        return redirect($this->getDashboardRoute($user))->with('success', "You are now impersonating {$user->name}");
    }

    public function leave(Request $request)
    {
        if (!session()->has('impersonated_by')) {
            return redirect('/');
        }

        $adminId = session()->pull('impersonated_by');
        $admin = User::findOrFail($adminId);

        AuditService::log('Admin', 'impersonation_end', null, null, "Admin stopped impersonating");

        Auth::login($admin);

        return redirect()->route('admin.users.index')->with('success', 'Back to admin session.');
    }

    protected function getDashboardRoute(User $user): string
    {
        return match($user->role?->slug) {
            'admin' => route('admin.dashboard'),
            'staff' => route('staff.dashboard'),
            default => route('resident.dashboard'),
        };
    }
}
