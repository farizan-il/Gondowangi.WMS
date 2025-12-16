<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ReturnModel;
use App\Models\ReturnItem;

echo "Checking ReturnModels (Last 5)...\n";
$returns = ReturnModel::with('items')->orderBy('id', 'desc')->take(5)->get();

foreach ($returns as $ret) {
    echo "ID: {$ret->id} | No: {$ret->return_number} | Type: {$ret->return_type} | Dept: {$ret->department} | Status: {$ret->status}\n";
    foreach ($ret->items as $item) {
        echo "   - Item: {$item->material_id} | Qty: {$item->qty_return} | Reason: {$item->return_reason}\n";
    }
}
