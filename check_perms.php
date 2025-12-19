<?php

use App\Models\User;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = User::where('email', 'it@gondowangi.com')->first();

if (!$user) {
    echo "User IT not found!\n";
    exit;
}

echo "User: " . $user->name . "\n";
echo "Role: " . ($user->role ? $user->role->role_name : 'No Role') . "\n";
echo "Permissions Count: " . $user->role->permissions()->count() . "\n";

$permissions = $user->role->permissions()->pluck('permission_name')->toArray();
echo "Permissions:\n";
foreach ($permissions as $p) {
    if (str_contains($p, 'it_dashboard') || str_contains($p, 'master_data') || str_contains($p, 'role_permission')) {
        echo " - $p\n";
    }
}
