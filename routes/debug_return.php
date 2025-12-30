<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DebugController;

// DEBUG ROUTE - Remove after debugging
Route::get('/debug/return/rejected-inventory', [DebugController::class, 'checkRejectedInventory']);
