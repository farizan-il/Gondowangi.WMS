<?php
use App\Http\Controllers\MasterDataController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/master-data/all', [MasterDataController::class, 'getAllData']);


Route::middleware('auth:sanctum')->group(function () {
    // SKU/Material
    Route::post('/master-data/sku', [MasterDataController::class, 'storeSku']);
    Route::put('/master-data/sku/{id}', [MasterDataController::class, 'updateSku']);
    Route::delete('/master-data/sku/{id}', [MasterDataController::class, 'deleteSku']);
    
    // Supplier
    Route::post('/master-data/supplier', [MasterDataController::class, 'storeSupplier']);
    Route::put('/master-data/supplier/{id}', [MasterDataController::class, 'updateSupplier']);
    Route::delete('/master-data/supplier/{id}', [MasterDataController::class, 'deleteSupplier']);
    
    // Bin Location
    Route::post('/master-data/bin', [MasterDataController::class, 'storeBin']);
    Route::put('/master-data/bin/{id}', [MasterDataController::class, 'updateBin']);
    Route::delete('/master-data/bin/{id}', [MasterDataController::class, 'deleteBin']);
    
    // User
    Route::post('/master-data/user', [MasterDataController::class, 'storeUser']);
    Route::put('/master-data/user/{id}', [MasterDataController::class, 'updateUser']);
    Route::delete('/master-data/user/{id}', [MasterDataController::class, 'deleteUser']);
});