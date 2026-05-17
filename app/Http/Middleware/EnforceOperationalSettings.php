<?php

namespace App\Http\Middleware;

use App\Models\SystemSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnforceOperationalSettings
{
    public function handle(Request $request, Closure $next)
    {
        if ($this->maintenanceBlocks($request)) {
            return response('Barangay Connect is currently in maintenance mode. Please try again later.', 503);
        }

        if (Auth::check()) {
            $timeout = SystemSetting::int('session_timeout', (int) config('session.lifetime', 120));
            $lastActivity = (int) $request->session()->get('auth.last_activity', time());

            if ($timeout > 0 && (time() - $lastActivity) > ($timeout * 60)) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors(['email' => 'Your session expired due to inactivity.']);
            }

            $request->session()->put('auth.last_activity', time());
        }

        return $next($request);
    }

    private function maintenanceBlocks(Request $request): bool
    {
        if (! SystemSetting::bool('maintenance_mode', false)) {
            return false;
        }

        if ($request->is('login', 'logout') || $request->is('admin/settings*')) {
            return false;
        }

        return ! optional($request->user())->isAdmin();
    }
}
