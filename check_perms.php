<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$perms = \App\Models\Permission::where('module', 'activity_log')->get();
echo json_encode($perms->toArray(), JSON_PRETTY_PRINT);
