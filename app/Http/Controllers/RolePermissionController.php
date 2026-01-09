<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class RolePermissionController extends Controller
{
    public function index()
    {
        // Ambil semua role dengan jumlah user dan permission yang dimilikinya
        $roles = Role::withCount('users')
            ->with(['permissions' => function($query) {
                // Pilih kolom yang diperlukan
                $query->select('permissions.id', 'permission_name', 'module', 'action', 'description');
            }])
            ->get()
            ->map(function($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->role_name,
                    'description' => $role->description,
                    'userCount' => $role->users_count,
                    // Kita akan kirim semua permission yang dimiliki role
                    'permissions' => $role->permissions->map(function($permission) {
                        return [
                            'id' => $permission->id,
                            'module' => $permission->module,
                            'action' => $permission->action,
                            'permission_name' => $permission->permission_name
                        ];
                    })->pluck('permission_name')->toArray(), // Hanya kirim array permission_name
                ];
            });

        // Ambil SEMUA permission, kelompokkan berdasarkan modul, dan format agar mudah dibaca di frontend
        $allPermissions = Permission::select('id', 'permission_name', 'module', 'action', 'description')
            ->orderBy('module')
            ->orderBy('action')
            ->get()
            ->groupBy('module')
            ->map(function($permissions, $module) {
                // Konversi nama modul menjadi lebih mudah dibaca
                $moduleName = Str::title(str_replace(['_', '-'], ' ', $module));
                
                // Tambahkan ikon/emoji jika perlu (sesuaikan di frontend)
                $emoji = match ($module) {
                    'incoming' => '📥',
                    'qc' => '🔍',
                    'qc_status' => '🏷️',
                    'putaway' => '📦',
                    'bin-to-bin' => '🔄',
                    'reservation' => '📋',
                    'picking' => '🛒',
                    'return' => '↩️',
                    'central_data' => '⚙️',
                    default => '📄',
                };
                
                return [
                    'module_key' => $module,
                    'module_name' => "{$emoji} {$moduleName}",
                    'permissions' => $permissions->map(function($permission) {
                        return [
                            'id' => $permission->id,
                            'name' => $permission->permission_name,
                            // Buat action name yang lebih mudah dibaca
                            'display_name' => Str::title(str_replace(['_', '-'], ' ', $permission->action)),
                            'description' => $permission->description ?? '',
                        ];
                    })
                ];
            })->values(); // Pastikan array menjadi list (bukan associative array)

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
            // PERBAIKAN: Ganti 'permissions.*.permission_name' menjadi 'permissions.*.name'
            'permissions.*.name' => 'required|string', 
            'permissions.*.allowed' => 'required|boolean'
        ]);

        $role = Role::findOrFail($roleId);

        // Ambil semua permission_name yang allowed = true
        $allowedPermissionNames = collect($validated['permissions'])
            ->filter(fn($p) => $p['allowed'])
            // PERBAIKAN: Pluck menggunakan kunci 'name'
            ->pluck('name') 
            ->toArray(); // Array berisi ['incoming.view', 'putaway.create', dst.]

        // Ambil ID permissions dari database
        // Gunakan 'permission_name' untuk mencari di tabel permissions
        $permissionIds = Permission::whereIn('permission_name', $allowedPermissionNames)
            ->pluck('id')
            ->toArray();

        // Sync permissions ke role
        $role->permissions()->sync($permissionIds);

        return redirect()->back()->with('success', 'Permission berhasil disimpan!');
    }

    /**
     * Store a new permission
     */
    public function storePermission(Request $request)
    {
        $validated = $request->validate([
            'module' => 'required|string|max:100',
            'action' => 'required|string|max:100',
            'description' => 'nullable|string|max:500'
        ]);

        // Generate permission_name from module.action
        $permissionName = Str::slug($validated['module'], '_') . '.' . Str::slug($validated['action'], '_');

        // Check if permission already exists
        if (Permission::where('permission_name', $permissionName)->exists()) {
            return redirect()->back()->with('error', "Permission '{$permissionName}' sudah ada!");
        }

        Permission::create([
            'permission_name' => $permissionName,
            'module' => Str::slug($validated['module'], '_'),
            'action' => Str::slug($validated['action'], '_'),
            'description' => $validated['description'] ?? ''
        ]);

        return redirect()->back()->with('success', 'Permission berhasil ditambahkan!');
    }

    /**
     * Update an existing permission
     */
    public function updatePermission(Request $request, $id)
    {
        $validated = $request->validate([
            'module' => 'required|string|max:100',
            'action' => 'required|string|max:100',
            'description' => 'nullable|string|max:500'
        ]);

        $permission = Permission::findOrFail($id);

        // Generate new permission_name
        $newPermissionName = Str::slug($validated['module'], '_') . '.' . Str::slug($validated['action'], '_');

        // Check if new permission_name conflicts with another permission
        if (Permission::where('permission_name', $newPermissionName)
            ->where('id', '!=', $id)
            ->exists()) {
            return redirect()->back()->with('error', "Permission '{$newPermissionName}' sudah ada!");
        }

        $permission->update([
            'permission_name' => $newPermissionName,
            'module' => Str::slug($validated['module'], '_'),
            'action' => Str::slug($validated['action'], '_'),
            'description' => $validated['description'] ?? ''
        ]);

        return redirect()->back()->with('success', 'Permission berhasil diupdate!');
    }

    /**
     * Delete a permission
     */
    public function destroyPermission($id)
    {
        $permission = Permission::findOrFail($id);

        // Detach from all roles first
        $permission->roles()->detach();

        // Delete the permission
        $permission->delete();

        return redirect()->back()->with('success', 'Permission berhasil dihapus!');
    }
}