<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$status = $kernel->call('migrate', [
    '--database' => 'mysql_testing',
    '--force' => true,
    '-vvv' => true
]);

echo "Status: " . $status . "\n";
echo "Output: \n" . Illuminate\Support\Facades\Artisan::output();
