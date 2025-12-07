<?php

use App\Http\Controllers\PengajuanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WorkflowController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Approval\ApproverController;
use App\Http\Controllers\Karyawan\KategoriPengajuanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('Approval-app.Authentifikasi.index');
// });

// Route::get('/beranda', function () {
//     return view('Approval-app.Requester.Beranda.index');
// });


// Route::get('/Pengajuan', function () {
//     return view('Approval-app.Requester.DataPengajuan.Pengajuan.index');
// });
// Route::get('/BuatPengajuan', function () {
//     return view('Approval-app.Requester.DataPengajuan.Pengajuan.create');
// });
// Route::get('/PengajuanSelesai', function () {
//     return view('Approval-app.Requester.DataPengajuan.Selesai.index');
// });
// Route::get('/realisasi', function () {
//     return view('Approval-app.Requester.DataPengajuan.Pengajuan.Settlement.index');
// });


// ROUTE UNTUK APPROVER
// Route::get('/dashboardadmin', function () {
//     return view('Approval-app.Approver.Beranda.index');
// });

// Route::get('/adminRealisasi', function () {
//     return view('Approval-app.Approver.DataPengajuan.Pengajuan.Settlement.index');
// });

// Route::get('/adminLaporanPengajuan', function () {
//     return view('Approval-app.Approver.DataPengajuan.Pengajuan.Laporan.index');
// });

// Route untuk approval pengajuan
// Route::resource('/adminLaporanPengajuan', ApproverController::class);

// // Route tambahan untuk update status
// Route::patch('/adminLaporanPengajuan/{id}/status', [ApproverController::class, 'updateStatus'])->name('adminLaporanPengajuan.updateStatus');


// Route::get('/adminPengajuan', function () {
//     return view('Approval-app.Approver.DataPengajuan.Pengajuan.index');
// });

// Route::get('/adminBuatPengajuan', function () {
//     return view('Approval-app.Approver.DataPengajuan.Pengajuan.create');
// });
// Route::get('/adminPengajuanSelesai', function () {
//     return view('Approval-app.Approver.DataPengajuan.Selesai.index');
// });


// // ROUTE UNTUK ADMIN
// // Route::get('/Management', function () {
// //     return view('Approval-app.HelpDesk.KelolaOtoritasi.index');
// // });
// // Route::get('/ManagementOtoritasi', function () {
// //     return view('Approval-app.HelpDesk.KelolaOtoritasi.create');
// // });


// Route::get('/ManagePengajuan', function () {
//     return view('Approval-app.HelpDesk.KelolaFormPengajuan.index');
// });
// Route::get('/ManageKategoriPengajuan', function () {
//     return view('Approval-app.HelpDesk.KategoriFormPengajuan.index');
// });
// Route::get('/ManageFormPengajuan', function () {
//     return view('Approval-app.HelpDesk.KelolaFormPengajuan.create');
// });

// // Route::group(['prefix' => 'admin', 'middleware' => ['auth', 'admin'], 'as' => 'admin.'], function () {
    
// //     // Kelola Otoritasi/Flow Approval Routes
// //     Route::group(['prefix' => 'kelola-otoritasi', 'as' => 'kelola-otoritasi.'], function () {
        
// //         // Main CRUD routes
// //         Route::get('/', [App\Http\Controllers\Admin\KelolaOtoritasiController::class, 'index'])
// //             ->name('index');
        
// //         Route::get('/create', [App\Http\Controllers\Admin\KelolaOtoritasiController::class, 'create'])
// //             ->name('create');
        
// //         Route::post('/store', [App\Http\Controllers\Admin\KelolaOtoritasiController::class, 'store'])
// //             ->name('store');
        
// //         Route::get('/{kategoriId}/{departmentId}/edit', [App\Http\Controllers\Admin\KelolaOtoritasiController::class, 'edit'])
// //             ->name('edit')
// //             ->where(['kategoriId' => '[0-9]+', 'departmentId' => '[0-9]+']);
        
// //         Route::put('/{kategoriId}/{departmentId}', [App\Http\Controllers\Admin\KelolaOtoritasiController::class, 'update'])
// //             ->name('update')
// //             ->where(['kategoriId' => '[0-9]+', 'departmentId' => '[0-9]+']);
        
// //         Route::delete('/{kategoriId}/{departmentId}', [App\Http\Controllers\Admin\KelolaOtoritasiController::class, 'destroy'])
// //             ->name('destroy')
// //             ->where(['kategoriId' => '[0-9]+', 'departmentId' => '[0-9]+']);
        
// //         // AJAX routes
// //         Route::get('/get-existing-flow', [App\Http\Controllers\Admin\KelolaOtoritasiController::class, 'getExistingFlow'])
// //             ->name('get-existing-flow');
        
// //         Route::get('/workflow-stats', [App\Http\Controllers\Admin\KelolaOtoritasiController::class, 'getWorkflowStats'])
// //             ->name('workflow-stats');
        
// //         // Status management
// //         Route::patch('/{kategoriId}/{departmentId}/toggle-status', [App\Http\Controllers\Admin\KelolaOtoritasiController::class, 'toggleStatus'])
// //             ->name('toggle-status')
// //             ->where(['kategoriId' => '[0-9]+', 'departmentId' => '[0-9]+']);
        
// //         // Workflow duplication
// //         Route::post('/duplicate', [App\Http\Controllers\Admin\KelolaOtoritasiController::class, 'duplicate'])
// //             ->name('duplicate');
// //     });
    
// // });


// // ROUTE UNTUK REQUESTER/KARYAWAN
// Route::get('/pengajuan', [KategoriPengajuanController::class, 'index'])->name('pengajuan.index');
// Route::get('/pengajuan/detail/{id}', [KategoriPengajuanController::class, 'getDetailPengajuan'])->name('pengajuan.detail');
// Route::get('/pengajuan/timeline/{id}', [KategoriPengajuanController::class, 'getTimelinePengajuan'])->name('pengajuan.timeline');
// Route::get('/settlement/detail/{id}', [KategoriPengajuanController::class, 'getDetailSettlement'])->name('settlement.detail');
// // ROUTE UNTUK REQUESTER/KARYAWAN

// // ROUTE UNTUK APPROVAR
// Route::resource('approvarLaporanPengajuan2', ApproverController::class)->names([
//     'index' => 'approver.index',
//     'show' => 'approver.show',
//     'update' => 'approver.update'
// ]);
// // ROUTE UNTUK APPROVAR



// // Routes untuk pengajuan
// Route::prefix('pengajuan')->group(function () {
//     Route::post('/save-draft', [KategoriPengajuanController::class, 'saveDraft'])->name('pengajuan.save-draft');
//     Route::post('/submit', [KategoriPengajuanController::class, 'submit'])->name('pengajuan.submit');
//     Route::post('/store', [KategoriPengajuanController::class, 'store'])->name('pengajuan.store');
// });


// Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    
//     Route::group(['prefix' => 'kelola-otoritasi', 'as' => 'kelola-otoritasi.'], function () {
        
//         Route::get('/', [App\Http\Controllers\Admin\KelolaOtoritasiController::class, 'index'])->name('index');
//         Route::get('/create', [App\Http\Controllers\Admin\KelolaOtoritasiController::class, 'create'])->name('create');
//         Route::post('/store', [App\Http\Controllers\Admin\KelolaOtoritasiController::class, 'store'])->name('store');

//         Route::get('/{kategoriId}/{departmentId}/edit', [App\Http\Controllers\Admin\KelolaOtoritasiController::class, 'edit'])
//             ->name('edit')
//             ->where(['kategoriId' => '[0-9]+', 'departmentId' => '[0-9]+']);
        
//         Route::put('/{kategoriId}/{departmentId}', [App\Http\Controllers\Admin\KelolaOtoritasiController::class, 'update'])
//             ->name('update')
//             ->where(['kategoriId' => '[0-9]+', 'departmentId' => '[0-9]+']);
        
//         Route::delete('/{kategoriId}/{departmentId}', [App\Http\Controllers\Admin\KelolaOtoritasiController::class, 'destroy'])
//             ->name('destroy')
//             ->where(['kategoriId' => '[0-9]+', 'departmentId' => '[0-9]+']);
        
//         Route::get('/get-existing-flow', [App\Http\Controllers\Admin\KelolaOtoritasiController::class, 'getExistingFlow'])->name('get-existing-flow');
//         Route::get('/workflow-stats', [App\Http\Controllers\Admin\KelolaOtoritasiController::class, 'getWorkflowStats'])->name('workflow-stats');
        
//         Route::patch('/{kategoriId}/{departmentId}/toggle-status', [App\Http\Controllers\Admin\KelolaOtoritasiController::class, 'toggleStatus'])
//             ->name('toggle-status')
//             ->where(['kategoriId' => '[0-9]+', 'departmentId' => '[0-9]+']);
        
//         Route::post('/duplicate', [App\Http\Controllers\Admin\KelolaOtoritasiController::class, 'duplicate'])->name('duplicate');
//     });

// });

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Route::middleware(['auth'])->group(function () {
    
//     // Route untuk karyawan tanpa bawahan
//     Route::get('/beranda', function () {
//         return view('Approval-app.Requester.Beranda.index');
//     })->name('beranda');
    
//     // Route untuk karyawan dengan bawahan (approver) 
//     Route::get('/approvarLaporanPengajuan', [ApproverController::class, 'index'])->name('approval.laporan-pengajuan');
    
//     // Route untuk redirect setelah login (optional)
//     Route::get('/', function () {
//         $user = auth()->user();
        
//         if ($user->bawahan()->exists()) {
//             return redirect('/approvarLaporanPengajuan');
//         } else {
//             return redirect('/beranda');
//         }
//     });
    
// });

Route::middleware(['auth'])->group(function () {
    // Routes untuk kategori pengajuan
    Route::get('/kategori-pengajuan/{id}/form-fields', [KategoriPengajuanController::class, 'getFormFields'])->name('kategori-pengajuan.form-fields');
    Route::prefix('kategori-pengajuan')->group(function () {
        Route::get('/', [KategoriPengajuanController::class, 'index'])->name('kategori-pengajuan.index');
        Route::get('/create', [KategoriPengajuanController::class, 'create'])->name('kategori-pengajuan.create');
        Route::get('/{id}/form-fields', [KategoriPengajuanController::class, 'getFormFields'])->name('kategori-pengajuan.form-fields');
    });

    // Route untuk karyawan tanpa bawahan
    Route::get('/beranda', function () {
        return view('Approval-app.Requester.Beranda.index');
    })->name('beranda');

    Route::get('/adminPengajuan', [KategoriPengajuanController::class, 'index'])->name('index');

    // Route untuk karyawan dengan bawahan (approver) 
    Route::get('/approvarLaporanPengajuan', [ApproverController::class, 'index'])->name('approval.laporan-pengajuan');

    // CRUD Routes untuk Approver
    Route::get('/approvarLaporanPengajuan/{id}', [ApproverController::class, 'show'])->name('approval.pengajuan.show');
    Route::get('/approvarLaporanPengajuan/{id}/edit', [ApproverController::class, 'edit'])->name('approval.pengajuan.edit');
    Route::put('/approvarLaporanPengajuan/{id}', [ApproverController::class, 'update'])->name('approval.pengajuan.update');
    Route::patch('/approvarLaporanPengajuan/{id}/status', [ApproverController::class, 'updateStatus'])->name('approval.pengajuan.status');
    Route::get('/approvarLaporanPengajuan/{id}/history', [ApproverController::class, 'getHistory'])->name('approval.pengajuan.history');

    // Route untuk redirect setelah login (optional)
    Route::get('/', function () {
        $user = auth()->user();

        if ($user->bawahan()->exists()) {
            return redirect('/approvarLaporanPengajuan');
        } else {
            return redirect('/beranda');
        }
    });
});










