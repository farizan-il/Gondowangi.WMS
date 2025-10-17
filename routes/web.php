<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\Transaction\GoodsReceiptController;
use App\Http\Controllers\Transaction\QualityControlController;
use App\Http\Controllers\Transaction\PutawayTransferController;
use App\Http\Controllers\Transaction\ReservationController;
use App\Http\Controllers\Transaction\PickingListController;
use App\Http\Controllers\Transaction\ReturnController;
use App\Http\Controllers\Transaction\BintobinController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:incoming.view')
        ->name('dashboard');

    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log');

    // Master Data - requires central_data permissions
    Route::get('/master-data', [MasterDataController::class, 'index'])
        ->middleware('permission:central_data.sku_management_view')
        ->name('master-data');
    
    // SKU Routes
    Route::post('/master-data/sku', [MasterDataController::class, 'storeSku'])
        ->middleware('permission:central_data.sku_management_view')
        ->name('master-data.sku.store');
    Route::put('/master-data/sku/{id}', [MasterDataController::class, 'updateSku'])
        ->middleware('permission:central_data.sku_management_view')
        ->name('master-data.sku.update');
    Route::delete('/master-data/sku/{id}', [MasterDataController::class, 'deleteSku'])
        ->middleware('permission:central_data.sku_management_view')
        ->name('master-data.sku.delete');
    
    // Supplier Routes
    Route::post('/master-data/supplier', [MasterDataController::class, 'storeSupplier'])
        ->middleware('permission:central_data.sku_management_view')
        ->name('master-data.supplier.store');
    Route::put('/master-data/supplier/{id}', [MasterDataController::class, 'updateSupplier'])
        ->middleware('permission:central_data.sku_management_view')
        ->name('master-data.supplier.update');
    Route::delete('/master-data/supplier/{id}', [MasterDataController::class, 'deleteSupplier'])
        ->middleware('permission:central_data.sku_management_view')
        ->name('master-data.supplier.delete');
    
    // Bin Routes
    Route::post('/master-data/bin', [MasterDataController::class, 'storeBin'])
        ->middleware('permission:central_data.sku_management_view')
        ->name('master-data.bin.store');
    Route::put('/master-data/bin/{id}', [MasterDataController::class, 'updateBin'])
        ->middleware('permission:central_data.sku_management_view')
        ->name('master-data.bin.update');
    Route::delete('/master-data/bin/{id}', [MasterDataController::class, 'deleteBin'])
        ->middleware('permission:central_data.sku_management_view')
        ->name('master-data.bin.delete');
    Route::get('/master-data/bin/{id}/qrcode', [MasterDataController::class, 'generateBinQRCode'])
        ->middleware('permission:central_data.sku_management_view')
        ->name('master-data.bin.qrcode');
    
    // User Routes
    Route::post('/master-data/user', [MasterDataController::class, 'storeUser'])
        ->middleware('permission:central_data.sku_management_view')
        ->name('master-data.user.store');
    Route::put('/master-data/user/{id}', [MasterDataController::class, 'updateUser'])
        ->middleware('permission:central_data.sku_management_view')
        ->name('master-data.user.update');
    Route::delete('/master-data/user/{id}', [MasterDataController::class, 'deleteUser'])
        ->middleware('permission:central_data.sku_management_view')
        ->name('master-data.user.delete');
    
    // Role Permission routes - only for admins
    Route::middleware('permission:central_data.role_management_view')->group(function () {
        Route::get('/role-permission', [RolePermissionController::class, 'index'])->name('role-permission');
        Route::post('/role-permission', [RolePermissionController::class, 'store'])->name('role-permission.store');
        Route::delete('/role-permission/{id}', [RolePermissionController::class, 'destroy'])->name('role-permission.destroy');
        Route::get('/role-permission/{id}/permissions', [RolePermissionController::class, 'getPermissions'])->name('role-permission.permissions');
        Route::put('/role-permission/{id}/permissions', [RolePermissionController::class, 'updatePermissions'])->name('role-permission.update-permissions');
    });

    // Transaction Routes with Permission Protection
    Route::prefix('transaction')->name('transaction.')->group(function () {
        // Awal Route Goods Receipt
        Route::get('/goods-receipt', [GoodsReceiptController::class, 'index'])
            ->middleware('permission:incoming.view')
            ->name('goods-receipt');
        Route::post('/goods-receipt', [GoodsReceiptController::class, 'store'])
            ->middleware('permission:incoming.create')
            ->name('goods-receipt.store');
        // Akhir Route Goods Receipt

        // Awal Route Quality Control
        Route::get('/quality-control', [QualityControlController::class, 'index'])
            ->middleware('permission:qc.view')
            ->name('quality-control');

        Route::post('/quality-control/scan', [QualityControlController::class, 'scanQR'])
            ->middleware('permission:qc.view')
            ->name('quality-control.scan');

        Route::post('/quality-control', [QualityControlController::class, 'store'])
            ->middleware('permission:qc.input_qc_result')
            ->name('quality-control.store');
        // Akhir Route Quality Control

        // AWAL PutAway & Transfer Order
       Route::get('/putaway-transfer', [PutawayTransferController::class, 'index'])
            ->middleware('permission:putaway.view')
            ->name('putaway.transfer.index');
            
        // API endpoints untuk PutAway
        Route::get('/putaway-transfer/qc-released', [PutawayTransferController::class, 'getQcReleasedMaterials'])
            ->name('putaway-transfer.qc-released');
            
        Route::get('/putaway-transfer/available-bins', [PutawayTransferController::class, 'getAvailableBins'])
            ->name('putaway-transfer.available-bins');

        Route::get('/putaway-transfer/bin-details', [PutawayTransferController::class, 'getBinDetails'])
            ->name('putaway-transfer.bin-details');
            
        Route::post('/putaway-transfer/generate', [PutawayTransferController::class, 'generateAutoPutaway'])
            ->name('putaway-transfer.generate');
        // AKHIR PutAway & Transfer Order

        // Bin to bin
        Route::get('/bin-to-bin', [BintobinController::class, 'index'])
            ->middleware('permission:bin-to-bin.view')
            ->name('bin-to-bin');

        // Reservation
        Route::get('/reservation', [ReservationController::class, 'index'])
            ->middleware('permission:reservation.view')
            ->name('reservation');

        // Picking List
        Route::get('/picking-list', [PickingListController::class, 'index'])
            ->middleware('permission:picking.view')
            ->name('picking-list');

        // Return
        Route::get('/return', [ReturnController::class, 'index'])
            ->middleware('permission:return.view')
            ->name('return');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});