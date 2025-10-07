<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')
            ->with(['permissions' => function($query) {
                $query->select('permissions.id', 'permission_name', 'module', 'action');
            }])
            ->get()
            ->map(function($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->role_name,
                    'description' => $role->description,
                    'userCount' => $role->users_count,
                    'permissions' => $role->permissions->map(function($permission) {
                        return [
                            'id' => $permission->id,
                            'module' => $permission->module,
                            'action' => $permission->action,
                            'permission_name' => $permission->permission_name
                        ];
                    })
                ];
            });

        $allPermissions = Permission::select('id', 'permission_name', 'module', 'action', 'description')
            ->orderBy('module')
            ->orderBy('action')
            ->get()
            ->groupBy('module');

        return Inertia::render('RolePermission', [
            'roles' => $roles,
            'allPermissions' => $allPermissions
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,role_name',
            'description' => 'nullable|string|max:1000'
        ]);

        $role = Role::create([
            'role_name' => $validated['name'],
            'description' => $validated['description']
        ]);

        return redirect()->back()->with('success', 'Role berhasil ditambahkan!');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        
        // Cek apakah role masih digunakan oleh user
        if ($role->users()->count() > 0) {
            return redirect()->back()->with('error', 'Role tidak dapat dihapus karena masih digunakan oleh user!');
        }

        $role->delete();

        return redirect()->back()->with('success', 'Role berhasil dihapus!');
    }

    public function getPermissions($roleId)
    {
        $role = Role::with('permissions')->findOrFail($roleId);
        
        return response()->json([
            'permissions' => $role->permissions->map(function($permission) {
                return [
                    'id' => $permission->id,
                    'module' => $permission->module,
                    'action' => $permission->action,
                    'permission_name' => $permission->permission_name
                ];
            })
        ]);
    }

    public function updatePermissions(Request $request, $roleId)
    {
        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*.module' => 'required|string',
            'permissions.*.action' => 'required|string',
            'permissions.*.allowed' => 'required|boolean'
        ]);

        $role = Role::findOrFail($roleId);

        // Ambil semua permission yang allowed = true
        $allowedPermissions = collect($validated['permissions'])
            ->filter(fn($p) => $p['allowed'])
            ->map(function($p) {
                return "{$p['module']}.{$p['action']}";
            })
            ->toArray();

        // Ambil ID permissions dari database
        $permissionIds = Permission::whereIn('permission_name', $allowedPermissions)
            ->pluck('id')
            ->toArray();

        // Sync permissions ke role
        $role->permissions()->sync($permissionIds);

        return redirect()->back()->with('success', 'Permission berhasil disimpan!');
    }
}