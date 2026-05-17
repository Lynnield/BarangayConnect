<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Permission, Role};
use App\Services\AuditService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->with('permissions')->paginate(15);

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('module')->orderBy('name')->get()->groupBy('module');

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:100|unique:roles,slug',
            'description' => 'nullable|string',
        ]);

        $role = Role::create($request->only(['name', 'slug', 'description']));
        AuditService::log('Roles', 'create', null, $role->toArray(), "Created role: {$role->name}");

        return redirect()->route('admin.roles.index')->with('success', 'Role created.');
    }

    public function show(Role $role)
    {
        $role->load('permissions', 'users');

        return view('admin.roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('module')->orderBy('name')->get()->groupBy('module');
        $role->load('permissions');

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:100|unique:roles,slug,' . $role->id,
            'description' => 'nullable|string',
        ]);

        $old = $role->toArray();
        $role->update($request->only(['name', 'slug', 'description']));
        AuditService::log('Roles', 'update', $old, $role->fresh()->toArray(), "Updated role: {$role->name}");

        return redirect()->route('admin.roles.index')->with('success', 'Role updated.');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete a role that is assigned to users.']);
        }

        $name = $role->name;
        $role->permissions()->detach();
        $role->delete();
        AuditService::log('Roles', 'delete', ['name' => $name], null, "Deleted role: {$name}");

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted.');
    }

    public function updatePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role->permissions()->sync($request->input('permission_ids', []));
        AuditService::log('Roles', 'permissions', null, null, "Updated permissions for role: {$role->name}");

        return back()->with('success', 'Permissions updated.');
    }
}
