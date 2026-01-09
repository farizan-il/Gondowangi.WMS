<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Permission;
use App\Models\Role;

echo "Starting Permission Migration...\n";

// 1. Create New Permissions
$pViewAll = Permission::firstOrCreate(
    ['permission_name' => 'activity_log.view_all'],
    ['module' => 'activity_log', 'action' => 'view_all', 'description' => 'Lihat Semua Aktivitas']
);
echo "Created/Found: activity_log.view_all\n";

$pViewSelf = Permission::firstOrCreate(
    ['permission_name' => 'activity_log.view_self'],
    ['module' => 'activity_log', 'action' => 'view_self', 'description' => 'Lihat Aktivitas Sendiri']
);
echo "Created/Found: activity_log.view_self\n";

// 2. Assign to Roles
$roles = [
    'IT Support' => ['view_all'],
    'Logistik SPV' => ['view_all'],
    'Logistik Admin' => ['view_self'],
    'Logistik Operator' => ['view_self'],
    'QAC' => ['view_self'],
    'Produksi' => ['view_self'],
];

foreach ($roles as $roleName => $perms) {
    $role = Role::where('role_name', $roleName)->first();
    if ($role) {
        foreach ($perms as $type) {
            $p = ($type === 'view_all') ? $pViewAll : $pViewSelf;
            
            // Cek apakah sudah punya
            if (!$role->permissions->contains($p->id)) {
                $role->permissions()->attach($p->id);
                echo "Attached {$type} to {$roleName}\n";
            }
        }
    } else {
        echo "Role {$roleName} not found\n";
    }
}

// 3. Remove Old Permission
$oldPerm = Permission::where('permission_name', 'activity_log.view')->first();
if ($oldPerm) {
    // Detach from all roles first (Laravel usually needs explicit detach mostly)
    DB::table('role_permissions')->where('permission_id', $oldPerm->id)->delete();
    // Delete permission
    $oldPerm->delete();
    echo "Deleted old permission: activity_log.view\n";
} else {
    echo "Old permission not found (already deleted)\n";
}

echo "Migration Complete.\n";
