<?php

use App\Models\StockMovement;
use Illuminate\Database\Eloquent\Relations\Relation;

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Attempt to eager load 'user' on StockMovement
    // Use limit 1 to avoid loading too much data if there are many rows
    $sm = StockMovement::with('user')->first();
    
    if (!$sm) {
        echo "No StockMovement records found. Cannot verify fully, but code executed without eager loading error.\n";
    } else {
        echo "Success: StockMovement loaded with 'user' relationship.\n";
        echo "Movement ID: " . $sm->id . "\n";
        echo "User (executedBy): " . ($sm->user ? $sm->user->name : 'None') . "\n";
    }
} catch (\Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
    exit(1);
}
