<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Concerns\SortsQueries;
use App\Models\{User, Role, LoginHistory};
use App\Support\ListSorts;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    use SortsQueries;

    public function index(Request $request)
    {
        $query = User::with('role');

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->role) {
            $query->whereHas('role', fn($q) => $q->where('slug', $request->role));
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $this->applyListSort($query, $request, ListSorts::users(), 'name', 'asc');
        $users = $query->paginate(15)->withQueryString();
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive,suspended',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'status' => $request->status,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        AuditService::log('Users', 'create', null, $user->toArray(), "Created user: {$user->email}");

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load('role', 'resident');
        $loginHistories = LoginHistory::where('user_id', $user->id)->latest()->limit(20)->get();
        return view('admin.users.show', compact('user', 'loginHistories'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,inactive,suspended',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $oldData = $user->toArray();

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role_id' => $request->role_id,
            'status' => $request->status,
            'phone' => $request->phone,
            'address' => $request->address,
        ];

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            ]);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        AuditService::log('Users', 'update', $oldData, $user->fresh()->toArray(), "Updated user: {$user->email}");

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        AuditService::log('Users', 'delete', $user->toArray(), null, "Deleted user: {$user->email}");
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function forceLogout(User $user)
    {
        // Invalidate all sessions for user
        \DB::table('sessions')->where('user_id', $user->id)->delete();
        AuditService::log('Users', 'force_logout', null, null, "Force logged out user: {$user->email}");

        return back()->with('success', 'User has been logged out from all devices.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'new_password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
        ]);

        $user->update(['password' => Hash::make($request->new_password)]);
        AuditService::log('Users', 'reset_password', null, null, "Admin reset password for: {$user->email}");

        return back()->with('success', 'Password reset successfully.');
    }

    public function loginHistory(User $user)
    {
        $histories = LoginHistory::where('user_id', $user->id)->latest()->paginate(20);
        return view('admin.users.login-history', compact('user', 'histories'));
    }
}
