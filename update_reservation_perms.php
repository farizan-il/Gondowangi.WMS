<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

echo "Starting Reservation Permission Migration...\n";

// 1. Define New Permissions
$newPerms = [
    [
        'name' => 'reservation.create.foh',
        'module' => 'reservation',
        'action' => 'create_foh',
        'description' => 'Buat Reservasi FOH & RS',
        'roles' => ['Logistik SPV', 'IT']
    ],
    [
        'name' => 'reservation.create.packaging',
        'module' => 'reservation',
        'action' => 'create_packaging',
        'description' => 'Buat Reservasi Packaging Material',
        'roles' => ['Produksi', 'IT']
    ],
    [
        'name' => 'reservation.create.raw_material',
        'module' => 'reservation',
        'action' => 'create_raw_material',
        'description' => 'Buat Reservasi Raw Material',
        'roles' => ['Produksi', 'IT']
    ],
    [
        'name' => 'reservation.create.add',
        'module' => 'reservation',
        'action' => 'create_add',
        'description' => 'Buat Reservasi Tambahan (ADD)',
        'roles' => ['Produksi', 'IT']
    ]
];

foreach ($newPerms as $permData) {
    // Create Permission
    $p = Permission::firstOrCreate(
        ['permission_name' => $permData['name']],
        [
            'module' => $permData['module'],
            'action' => $permData['action'],
            'description' => $permData['description']
        ]
    );
    
    echo "Processing {$permData['name']}...\n";

    // Assign to Roles
    foreach ($permData['roles'] as $roleName) {
        $role = Role::where('role_name', $roleName)->first();
        if ($role) {
            if (!$role->permissions->contains($p->id)) {
                $role->permissions()->attach($p->id);
                echo "  -> Attached to {$roleName}\n";
            } else {
                echo "  -> Already assigned to {$roleName}\n";
            }
        } else {
            echo "  -> Role {$roleName} NOT FOUND\n";
        }
    }
}

echo "Migration Complete.\n";
