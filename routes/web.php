<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/activity-log', function () {
        return Inertia::render('RiwayatAktivitas');
    })->name('activity-log');

    // Master Data Routes (menggunakan satu file MasterData.vue dengan props)
    Route::get('/master-data', function () {
        return Inertia::render('MasterData');
    })->name('master-data');

    // Role Permission
    Route::get('/role-permission', function () {
        return Inertia::render('RolePermission');
    })->name('role-permission');

    // Transaction Routes
    Route::prefix('transaction')->name('transaction.')->group(function () {
        // Penerimaan Barang
        Route::get('/goods-receipt', function () {
            return Inertia::render('PenerimaanBarang');
        })->name('goods-receipt');

        // Quality Control
        Route::get('/quality-control', function () {
            return Inertia::render('QualityControl');
        })->name('quality-control');

        // PutAway & Transfer Order
        Route::get('/putaway-transfer', function () {
            return Inertia::render('PutAwasTO');
        })->name('putaway-transfer');

        // Reservation
        Route::get('/reservation', function () {
            return Inertia::render('Reservation');
        })->name('reservation');

        // Picking List
        Route::get('/picking-list', function () {
            return Inertia::render('PickingList');
        })->name('picking-list');

        // Return (nama file: Retum.vue)
        Route::get('/return', function () {
            return Inertia::render('Return');
        })->name('return');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

