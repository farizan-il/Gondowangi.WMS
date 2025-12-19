<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\Transaction\GoodsReceiptController;
use App\Http\Controllers\Transaction\PutawayTransferController;
use App\Http\Controllers\Transaction\ReservationController;
use App\Http\Controllers\Transaction\PickingListController;
use App\Http\Controllers\Transaction\ReturnController;
use App\Http\Controllers\Transaction\BintobinController;
use App\Http\Controllers\Transaction\QualityControlController;
use App\Http\Controllers\Transaction\CycleCountController;
use App\Http\Controllers\WmsDashboardController;
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
        // ->middleware('permission:dashboard.wms')
        ->name('dashboard');

    // WMS Dashboard
    Route::get('/wms-dashboard', [WmsDashboardController::class, 'index'])
        ->name('wms-dashboard.index');
    Route::get('/wms-dashboard/data', [WmsDashboardController::class, 'getData'])->name('wms-dashboard.data');

// Material Lookup for Return
Route::get('/transaction/return/material/{code}', [\App\Http\Controllers\Transaction\ReturnController::class, 'getMaterial']);

    // Re-QC for expired materials
    Route::post('/dashboard/reqc/initiate', [DashboardController::class, 'initiateReqcForExpiredMaterials'])
        ->name('dashboard.reqc.initiate');

    Route::get('/activity-log', [ActivityLogController::class, 'index'])
        // ->middleware('permission:activity_log.view')
        ->name('activity-log');
    Route::get('/it-dashboard', [ActivityLogController::class, 'dashboard'])
        // ->middleware('permission:it_dashboard.view')
        ->name('it-dashboard');

    // Master Data Routes
    Route::get('/master-data/bin/{binId}/stocks', [MasterDataController::class, 'getBinStockDetails'])->name('bin.stocks.details');
    
    Route::prefix('master-data')->middleware(['auth'])->group(function () {
        
        Route::post('/import-stock', [MasterDataController::class, 'importStock'])->name('master-data.import-stock');
        Route::get('/', [MasterDataController::class, 'index'])->name('master-data.index');
        
        // SKU Routes
        Route::post('/sku', [MasterDataController::class, 'storeSku']);
        Route::put('/sku/{id}', [MasterDataController::class, 'updateSku']);
        Route::delete('/sku/{id}', [MasterDataController::class, 'deleteSku']);
        
        // Supplier Routes
        Route::post('/supplier', [MasterDataController::class, 'storeSupplier']);
        Route::put('/supplier/{id}', [MasterDataController::class, 'updateSupplier']);
        Route::delete('/supplier/{id}', [MasterDataController::class, 'deleteSupplier']);
        
        // Bin Location Routes
        Route::post('/bin', [MasterDataController::class, 'storeBin']);
        Route::put('/bin/{id}', [MasterDataController::class, 'updateBin']);
        Route::delete('/bin/{id}', [MasterDataController::class, 'deleteBin']);
        Route::get('/bin/{id}/qr-code/preview', [MasterDataController::class, 'previewBinQRCode'])->name('bin.qr-code.preview');
        Route::get('/bin/{id}/qr-code/download', [MasterDataController::class, 'downloadBinQRCode'])->name('bin.qr-code.download');
        
        // User Routes
        Route::post('/user', [MasterDataController::class, 'storeUser']);
        Route::put('/user/{id}', [MasterDataController::class, 'updateUser']);
        Route::delete('/user/{id}', [MasterDataController::class, 'deleteUser']);

        // API endpoint for bin locations
        Route::get('/bins', [MasterDataController::class, 'getAllBins']);
    });
    
    // Bin Routes
    Route::post('/master-data/bin', [MasterDataController::class, 'storeBin'])
        // ->middleware('permission:master_data.manage')
        ->name('master-data.bin.store');
    Route::put('/master-data/bin/{id}', [MasterDataController::class, 'updateBin'])
        // ->middleware('permission:master_data.manage')
        ->name('master-data.bin.update');
    Route::delete('/master-data/bin/{id}', [MasterDataController::class, 'deleteBin'])
        // ->middleware('permission:master_data.manage')
        ->name('master-data.bin.delete');
    Route::get('/master-data/bin/{id}/qrcode', [MasterDataController::class, 'generateBinQRCode'])
        // ->middleware('permission:master_data.view')
        ->name('master-data.bin.qrcode');
    
    // User Routes
    Route::post('/master-data/user', [MasterDataController::class, 'storeUser'])
        // ->middleware('permission:master_data.manage')
        ->name('master-data.user.store');
    Route::put('/master-data/user/{id}', [MasterDataController::class, 'updateUser'])
        // ->middleware('permission:master_data.manage')
        ->name('master-data.user.update');
    Route::delete('/master-data/user/{id}', [MasterDataController::class, 'deleteUser'])
        // ->middleware('permission:master_data.manage')
        ->name('master-data.user.delete');
    
    // Role Permission routes - now open
    // Route::middleware('permission:role_permission.view')->group(function () {
        Route::get('/role-permission', [RolePermissionController::class, 'index'])->name('role-permission');
        Route::post('/role-permission', [RolePermissionController::class, 'store'])->name('role-permission.store');
        Route::delete('/role-permission/{id}', [RolePermissionController::class, 'destroy'])->name('role-permission.destroy');
        Route::get('/role-permission/{id}/permissions', [RolePermissionController::class, 'getPermissions'])->name('role-permission.permissions');
        Route::put('/role-permission/{id}/permissions', [RolePermissionController::class, 'updatePermissions'])->name('role-permission.update-permissions');
    // });

    

    // Transaction Routes with Permission Protection
    Route::prefix('transaction')->name('transaction.')->group(function () {
        Route::post('/cycle-count/bulk-repeat', [CycleCountController::class, 'bulkRepeat']);

        Route::get('/cycle-count', [CycleCountController::class, 'index'])
            ->name('cycle-count.index');

        Route::post('/cycle-count/approve', [CycleCountController::class, 'approve'])
            ->name('cycle-count.approve');
        
        Route::get('/cycle-count/history/{materialId}', [CycleCountController::class, 'getHistory'])
            ->name('cycle-count.history');
        
        Route::post('/cycle-count/repeat', [CycleCountController::class, 'repeat'])
            ->name('cycle-count.repeat');

        // Proses Simpan (Agar tombol Simpan berfungsi)
        Route::post('/cycle-count/store', [CycleCountController::class, 'store'])
            ->name('cycle-count.store');

        // Awal Route Goods Receipt
        Route::post('/goods-receipt/parse-erp-pdf', [GoodsReceiptController::class, 'parseErpPdf'])
            // ->middleware('permission:incoming.create')
            ->name('goods-receipt.parse-erp-pdf');
        Route::get('/goods-receipt', [GoodsReceiptController::class, 'index'])
            // ->middleware('permission:incoming.view')
            ->name('goods-receipt');
        Route::post('/goods-receipt', [GoodsReceiptController::class, 'store'])
            // ->middleware('permission:incoming.create')
            ->name('goods-receipt.store');
        Route::post('/goods-receipt/store-multiple', [GoodsReceiptController::class, 'storeMultiple'])
            // ->middleware('permission:incoming.create')
            ->name('goods-receipt.store-multiple');
        Route::get('/goods-receipt/po/{id}', [GoodsReceiptController::class, 'getPoDetails'])
            // ->middleware('permission:incoming.view')
            ->name('goods-receipt.po-details');
        // Akhir Route Goods Receipt

        // Awal Route Quality Control
        Route::get('/quality-control', [QualityControlController::class, 'index'])
            // ->middleware('permission:qc.view')
            ->name('quality-control');

        Route::post('/quality-control/scan', [QualityControlController::class, 'scanQR'])
            // ->middleware('permission:qc.view')
            ->name('quality-control.scan');

        Route::post('/quality-control', [QualityControlController::class, 'store'])
            // ->middleware('permission:qc.create')
            ->name('quality-control.store');
        // Akhir Route Quality Control

        // AWAL PutAway & Transfer Order
        Route::get('/putaway-transfer', [PutawayTransferController::class, 'index'])
            // ->middleware('permission:putaway.view')
            ->name('putaway.transfer.index');

        Route::post('/putaway-transfer/complete/{id}', [PutawayTransferController::class, 'completeTO'])
            ->name('putaway.transfer.complete');

        Route::prefix('putaway-transfer')->name('putaway.transfer.')->group(function () {
            
            // Main index page
            Route::get('/', [PutawayTransferController::class, 'index'])
                // ->middleware('permission:putaway.view')
                ->name('index');
            
            // Get QC Released Materials for auto putaway
            Route::get('/qc-released', [PutawayTransferController::class, 'getQcReleasedMaterials'])
                // ->middleware('permission:putaway.view')
                ->name('qc.released');
            
            // Get available bins
            Route::get('/available-bins', [PutawayTransferController::class, 'getAvailableBins'])
                // ->middleware('permission:putaway.view')
                ->name('available.bins');
            
            // Get bin details
            Route::get('/bin-details', [PutawayTransferController::class, 'getBinDetails'])
                // ->middleware('permission:putaway.view')
                ->name('bin.details');
            
            // Generate auto putaway
            Route::post('/generate', [PutawayTransferController::class, 'generateAutoPutaway'])
                // ->middleware('permission:putaway.create')
                ->name('generate');
            
            // Complete Transfer Order
            Route::post('/complete/{id}', [PutawayTransferController::class, 'completeTO'])
                // // ->middleware('permission:putaway.execute')
                ->name('complete');

            // Scan Putaway
            Route::post('/scan', [PutawayTransferController::class, 'scanPutaway'])
                // // ->middleware('permission:putaway.execute')
                ->name('scan');

            Route::get('/reject-bins', [PutawayTransferController::class, 'getRejectBins'])
                ->name('reject-bins');
        });

        Route::post('/reservation/parse-materials', [ReservationController::class, 'parseMaterials'])
            ->name('reservation.parse-materials');
            
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
            // ->middleware('permission:bintobin.view')
            ->name('bin-to-bin');

        // Endpoint untuk memproses transfer
        Route::post('/bin-to-bin/transfer', [BintobinController::class, 'store'])
            ->name('bintobin.store');

        // Reservation
        Route::get('/reservation', [ReservationController::class, 'index'])
            // ->middleware('permission:reservation.view')
            ->name('reservation.index');
            
        // Reservation Store (POST)
        Route::post('/reservation', [ReservationController::class, 'store'])
            // ->middleware('permission:reservation.create')
            ->name('reservation.store');

        // ENDPOINT UNTUK AMBIL DATA (AJAX/Web)
        Route::get('/reservations/data', [ReservationController::class, 'getReservationsData'])
            // ->middleware('permission:reservation.view')
            ->name('reservation.data');
            
        // NEW ENDPOINT: Material Search API
        Route::get('/materials/search', [ReservationController::class, 'searchMaterials'])
            // ->middleware('permission:reservation.create') // Gunakan permission yang sesuai
            ->name('materials.search');

        // Picking List
        Route::get('/picking-list', [PickingListController::class, 'index'])
            // ->middleware('permission:picking.view')
            ->name('picking-list');
        Route::post('/picking-list', [PickingListController::class, 'store'])
            // ->middleware('permission:picking.execute')
            ->name('picking-list.store');
        Route::get('/api/picking-list', [PickingListController::class, 'getPickingList'])
            // ->middleware('permission:picking.view')
            ->name('api.picking-list');
        
        Route::post('/picking-list/update-status/{id}', [PickingListController::class, 'updateStatus'])
            // ->middleware('permission:picking.create')
            ->name('picking-list.update-status');

        // Return
        Route::get('/return', [ReturnController::class, 'index'])
            // ->middleware('permission:return.view') // Assuming permission exists or reuse existing
            ->name('return.index');
        Route::post('/return', [ReturnController::class, 'store'])
            // ->middleware('permission:return.create')
            ->name('return.store');
        Route::get('/return/material/{code}', [ReturnController::class, 'getMaterial']);
        Route::get('/return/dept-reservations', [ReturnController::class, 'getDeptReservations']);
        Route::get('/return/reservation-details', [ReturnController::class, 'getReservationDetails']);
        Route::get('/return/rejected-shipment-details', [ReturnController::class, 'getRejectedShipmentDetails']);
        Route::get('/return/supplier-shipment-details', [ReturnController::class, 'getSupplierShipmentDetails']);
        Route::post('/return/parse-pdf', [ReturnController::class, 'parsePdf']);
        Route::post('/return/approve', [ReturnController::class, 'approve'])->name('return.approve');

        Route::get('/return/reservation-details', [ReturnController::class, 'getReservationDetails'])
            // ->middleware('permission:return.view')
            ->name('return.reservation-details');
        
        // Store Return (General)
        Route::post('/return', [ReturnController::class, 'store'])
             // ->middleware('permission:return.create') // Or appropriate permission
            ->name('return.store');
        

        // Cycle Count
        // Route::prefix('cycle-count')->name('cycle-count.')->group(function () {
        //     Route::get('/', [CycleCountController::class, 'index'])->name('index');
        //     Route::get('/create', [CycleCountController::class, 'create'])->name('create');
        //     Route::post('/', [CycleCountController::class, 'store'])->name('store');
        //     Route::get('/{id}', [CycleCountController::class, 'show'])->name('show');
        //     Route::put('/{id}', [CycleCountController::class, 'update'])->name('update');
        //     Route::post('/{id}/finalize', [CycleCountController::class, 'finalize'])->name('finalize');
        // });
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});