<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Employments\PengajuanController;
use App\Http\Controllers\Employments\DashboardController;
use App\Http\Controllers\Employments\LaporanPengajuanController;
use App\Http\Controllers\Employments\SettlementController;
use App\Http\Controllers\Employments\TransactionRequestController;
use App\Http\Controllers\Admin\KelolaFlowApprovalController;
use App\Http\Controllers\Admin\KelolaFormPengajuanController;
use App\Http\Controllers\Admin\KategoriPengajuanController;
use App\Http\Controllers\Admin\KelolaPenggunaController;
use App\Http\Controllers\Employments\DashboardOverviewController;
use App\Http\Controllers\Employments\HistoryPengajuanController;
use App\Http\Controllers\Admin\KelolaNominalController;
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\NotificationController;



Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);

Route::middleware(['auth'])->group(function () {
    // Route POST untuk logout (lebih aman)
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    // Route GET untuk logout (jika ingin tetap menggunakan link biasa)
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout.get');

    Route::get('/pengajuan/check-status', [PengajuanController::class, 'checkPengajuanStatus'])
        ->name('pengajuan.check-status');
    
    Route::prefix('kelola-form-pengajuan')->name('kelola-form-pengajuan.')->group(function () {
        Route::get('/', [KelolaFormPengajuanController::class, 'index'])->name('index');
        Route::get('/create', [KelolaFormPengajuanController::class, 'create'])->name('create');
        Route::post('/', [KelolaFormPengajuanController::class, 'store'])->name('store');
        Route::get('/{id}', [KelolaFormPengajuanController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [KelolaFormPengajuanController::class, 'edit'])->name('edit');
        Route::put('/{id}', [KelolaFormPengajuanController::class, 'update'])->name('update');
        Route::delete('/{id}', [KelolaFormPengajuanController::class, 'destroy'])->name('destroy');
        Route::get('/preview/{kategoriId}', [KelolaFormPengajuanController::class, 'preview'])->name('preview');
    });
    
    Route::resource('kelola-pengguna', KelolaPenggunaController::class)->parameters([
        'kelola-pengguna' => 'karyawan'
    ]);
    Route::prefix('admin')->name('admin.')->group(function () {
        // Activity Logs
        Route::get('activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');

        // Route tambahan untuk reset password
        Route::post('kelola-pengguna/{karyawan}/reset-password', [KelolaPenggunaController::class, 'resetPassword'])
            ->name('kelola-pengguna.reset-password');
        
        // Route untuk mendapatkan data karyawan (AJAX)
        Route::get('kelola-pengguna/{karyawan}/json', function (App\Models\Karyawan $karyawan) {
            return response()->json($karyawan);
        })->name('kelola-pengguna.json');
    });
    
    // ROUTE UNTUK MENGELOLA FLOW APPROVAL
    Route::get('/kelola-flow-approval', [KelolaFlowApprovalController::class, 'index'])->name('kelola-flow-approval.index');
    Route::post('/kelola-flow-approval', [KelolaFlowApprovalController::class, 'store'])->name('kelola-flow-approval.store');
    Route::put('/kelola-flow-approval/{kategoriId}/{requesterId}', [KelolaFlowApprovalController::class, 'update'])->name('kelola-flow-approval.update');
    Route::delete('/kelola-flow-approval/{kategoriId}/{requesterId}', [KelolaFlowApprovalController::class, 'destroy'])->name('kelola-flow-approval.destroy');
    
    // API routes for AJAX calls
    Route::get('/kelola-flow-approval/kategori/{kategoriId}', [KelolaFlowApprovalController::class, 'getFlowsByKategori'])->name('kelola-flow-approval.flows-by-kategori');
    Route::get('/kelola-flow-approval/karyawan/department/{departmentId}', [KelolaFlowApprovalController::class, 'getKaryawanByDepartment'])->name('kelola-flow-approval.karyawan-by-department');
    Route::get('/kelola-flow-approval', [KelolaFlowApprovalController::class, 'index'])->name('kelola-flow-approval.index');
    Route::get('/get-karyawan-by-department/{departmentId}', [KelolaFlowApprovalController::class, 'getKaryawanByDepartment']);
    
    Route::get('/kelola-flow-approval', [KelolaFlowApprovalController::class, 'index'])->name('kelola-flow-approval.index');
    Route::get('/get-karyawan-by-department/{departmentId}', [KelolaFlowApprovalController::class, 'getKaryawanByDepartment']);
    Route::post('/generate-step-name', [KelolaFlowApprovalController::class, 'generateStepName']);
    // ROUTE UNTUK MENGELOLA FLOW APPROVAL


    // Route utama untuk mengakses halaman pengajuan, settlement, laporan pengajuan
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/BuatPengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
    Route::get('/PengajuanSelesai', [PengajuanController::class, 'PengajuanSelesai'])->name('pengajuanSelesai.index');
    Route::get('/PengajuanSelesa', [PengajuanController::class, 'selesai'])->name('pengajuan.selesai');
    Route::get('/LaporanPengajuan', [LaporanPengajuanController::class, 'index'])->name('approval.laporan-pengajuan');
    Route::get('/RiwayatPengajuan', [HistoryPengajuanController::class, 'index'])->name('approval.HistoryPengajuan');
    Route::get('/Settlement', [SettlementController::class, 'index'])->name('settlement.index');
    Route::get('/TransactionRequest', [TransactionRequestController::class, 'index'])->name('transactionrequest.index');
    Route::get('/overview', [DashboardOverviewController::class, 'index'])->name('dashboard');
   
    Route::get('/dashboardadmin', [DashboardAdminController::class, 'index'])->name('dashboard.index');
    Route::resource('/kelola-nominal', KelolaNominalController::class);
    
    // Route untuk mengambil form HTML (Load Modal)
    Route::get('/pengajuan/{id}/edit-form', [PengajuanController::class, 'getEditForm']);
    
    // Route untuk submit update revisi
    Route::put('/pengajuan/{id}/update-revisi', [PengajuanController::class, 'updateRevisi']);

    
    // Detail modals (AJAX only)
    Route::get('department/{id}', [AdminDashboardController::class, 'getDepartmentDetail']);
    Route::get('employee/{id}', [AdminDashboardController::class, 'getEmployeeDetail']);
    
    // Additional pages (optional - bisa dihapus jika tidak digunakan)
    Route::get('pending', [AdminDashboardController::class, 'pendingApprovals'])->name('pending');
    Route::get('financial', [AdminDashboardController::class, 'financialReport'])->name('financial');
    
    // Export (optional)
    Route::get('export/{type}/{id?}', [AdminDashboardController::class, 'export'])->name('export');
    
    // API untuk real-time updates (optional)
    Route::prefix('api')->group(function () {
        Route::get('stats', [AdminDashboardController::class, 'getOverviewStatsApi']);
        Route::get('chart', [AdminDashboardController::class, 'getChartDataApi']);
        Route::get('notifications', [AdminDashboardController::class, 'getNotifications']);
    });
    
    
    // Routes untuk membuat pengajuan dan memilih kategori pengajuannya
    Route::get('/kategori-pengajuan/{id}/form-fields', [PengajuanController::class, 'getFormFields'])->name('kategori-pengajuan.form-fields');
    Route::prefix('kategori-pengajuan')->group(function () {
        Route::get('/create', [PengajuanController::class, 'create'])->name('kategori-pengajuan.create');
        Route::get('/{id}/form-fields', [PengajuanController::class, 'getFormFields'])->name('kategori-pengajuan.form-fields');
    });
    
    // Routes untuk mengirim data pengajuan ke database
    Route::prefix('pengajuan')->group(function () {
        Route::post('/save-draft', [PengajuanController::class, 'saveDraft'])->name('pengajuan.save-draft');
        Route::post('/submit', [PengajuanController::class, 'submit'])->name('pengajuan.submit');
        Route::post('/store', [PengajuanController::class, 'store'])->name('pengajuan.store');
    });
    Route::get('/pengajuan/{id}/print-pdf', [PengajuanController::class, 'printPdf'])->name('pengajuan.print-pdf');
    
    
    // ========= AWAL ROUTE UNTUK LAPORAN PENGAJUAN
    // Route untuk detail pengajuan, timeline pengajuan
    Route::get('/pengajuan/detail/{id}', [PengajuanController::class, 'getDetailPengajuan'])->name('pengajuan.detail');
    Route::get('/pengajuan/timeline/{id}', [PengajuanController::class, 'getTimelinePengajuan'])->name('pengajuan.timeline');
    
    // Routes untuk Settlement - UPDATED dengan fitur baru
    Route::get('/laporan-pengajuan/settlement-detail/{id}', [LaporanPengajuanController::class, 'settlementDetail'])
        ->name('laporan-pengajuan.settlement-detail');
        
    // Route untuk mengirim rimender jika requester belum upload bukti transfer
    Route::post('/kirim-notifikasi-refund', [LaporanPengajuanController::class, 'kirimNotifikasiRefund'])->name('kirim-notifikasi-refund');
    
    // Route khusus untuk update status settlement
    Route::put('/laporan-pengajuan/settlement-status/{id}', [LaporanPengajuanController::class, 'updateSettlementStatus'])
        ->name('laporan-pengajuan.update-settlement-status');
        
    Route::get('/settlement/{id}/notification-data', [LaporanPengajuanController::class, 'getSettlementNotificationData'])
      ->name('settlement.notification-data');
    
    // Route untuk mengirim notifikasi pengembalian
    Route::post('/settlement/{id}/send-notification', [LaporanPengajuanController::class, 'sendRefundNotification'])
          ->name('settlement.send-notification');
    
    // Route untuk detail settlement (modal)
    Route::get('/settlement/{id}/detail', [SettlementController::class, 'showDetail'])
          ->name('settlement.detail');
        
    Route::prefix('laporan-pengajuan')->name('laporan-pengajuan.')->group(function () {
        Route::get('/', [LaporanPengajuanController::class, 'index'])->name('index');
        Route::get('/detail/{id}', [LaporanPengajuanController::class, 'detail'])->name('detail');
        // Get status pengajuan - AJAX
        Route::get('/status/{id}', [LaporanPengajuanController::class, 'getStatus'])->name('get-status');
        
        // Update status pengajuan - AJAX
        Route::put('/update-status/{id}', [LaporanPengajuanController::class, 'updateStatus'])->name('update-status');
    });
    Route::post('/laporan-pengajuan/{id}/intervene-detail', [LaporanPengajuanController::class, 'updateDetailIntervention'])
        ->name('laporan-pengajuan.intervene-detail');
    Route::post('/laporan-pengajuan/{id}/intervene-settlement-detail', [LaporanPengajuanController::class, 'updateSettlementDetailIntervention'])
    ->name('laporan-pengajuan.intervene-settlement-detail');
        
    // ============== AKHIR ROUTE UNTUK LAPORAN PENGAJUAN
    
    Route::prefix('settlement')->group(function () {
        // Route::post('/submit/{settlement}', [SettlementController::class, 'submit'])->name('settlement.submit');
        
        // Buat settlement baru dari pengajuan yang approved
        Route::get('/create/{pengajuan}', [SettlementController::class, 'create'])->name('settlement.create');
        Route::post('/store/{pengajuan}', [SettlementController::class, 'store'])->name('settlement.store');
        
        // Edit/Update settlement existing (hanya jika status masih bisa diedit)
        Route::get('/edit/{settlement}', [SettlementController::class, 'edit'])->name('settlement.edit');
        Route::put('/update/{settlement}', [SettlementController::class, 'update'])->name('settlement.update');
        
        // Detail settlement untuk modal
        Route::get('/detail/{id}', [SettlementController::class, 'getDetailSettlement'])->name('settlement.detail');
        
        // Delete settlement (jika status masih draft/pending)
        Route::delete('/delete/{settlement}', [SettlementController::class, 'destroy'])->name('settlement.destroy');
        
        // Download file bukti settlement
        Route::get('/{settlement}/detail/{detail}/download', [SettlementController::class, 'downloadFile'])->name('settlement.download-file');
        
        // Check apakah pengajuan bisa dibuat settlement
        Route::get('/check/{pengajuan}', [SettlementController::class, 'canCreateSettlement'])->name('settlement.check');
        
        // Get settlement summary untuk dashboard
        Route::get('/summary', [SettlementController::class, 'getSettlementSummary'])->name('settlement.summary');
        
        // Auto create settlement (untuk cron job atau trigger)
        Route::post('/auto-create/{pengajuan}', [SettlementController::class, 'autoCreateSettlement'])->name('settlement.auto-create');
    });
    Route::post('/settlement/{id}/upload-bukti-transfer', [SettlementController::class, 'uploadBuktiTransfer'])
          ->name('settlement.upload-bukti-transfer');

    // Routes untuk dashboard dan statistik
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [PengajuanController::class, 'getDashboardStats'])->name('dashboard.stats');
        Route::get('/recent-activities', [PengajuanController::class, 'getRecentActivities'])->name('dashboard.activities');
    });
    
    // Routes untuk export/import data
    Route::prefix('export')->group(function () {
        Route::get('/pengajuan', [PengajuanController::class, 'exportPengajuan'])->name('export.pengajuan');
        Route::get('/settlement', [SettlementController::class, 'exportSettlement'])->name('export.settlement');
        Route::get('/report/{type}', [LaporanPengajuanController::class, 'exportReport'])->name('export.report');
    });
    
    // Routes untuk notifikasi
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/all', [App\Http\Controllers\NotificationController::class, 'getAllNotifications'])->name('all');
        Route::post('/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('mark-read');
        Route::post('/clear-all', [App\Http\Controllers\NotificationController::class, 'clearAll'])->name('clear-all');
    });
    
    // Routes untuk pengaturan user
    Route::prefix('settings')->group(function () {
        Route::get('/profile', [PengajuanController::class, 'showProfile'])->name('settings.profile');
        Route::put('/profile', [PengajuanController::class, 'updateProfile'])->name('settings.profile.update');
        Route::get('/preferences', [PengajuanController::class, 'showPreferences'])->name('settings.preferences');
        Route::put('/preferences', [PengajuanController::class, 'updatePreferences'])->name('settings.preferences.update');
    });
    
    // Create settlement from pengajuan
    Route::get('/settlement/create/{pengajuanId}', [SettlementController::class, 'create'])
        ->name('settlement.create');
    
    // Store new settlement
    Route::post('/settlement/create/{pengajuanId}', [SettlementController::class, 'store'])
        ->name('settlement.store');
    
    // Show settlement detail
    Route::get('/settlement/{settlementId}', [SettlementController::class, 'show'])
        ->name('settlement.show');
    
    // Edit settlement
    Route::get('/settlement/edit/{settlementId}', [SettlementController::class, 'edit'])
        ->name('settlement.edit');
    
    // Update settlement
    Route::post('/settlement/update/{settlementId}', [SettlementController::class, 'update'])
        ->name('settlement.update');
    
    // Alternative method using PUT
    Route::put('/settlement/{settlementId}', [SettlementController::class, 'update'])
        ->name('settlement.update.put');
    
    // Delete settlement (if needed)
    Route::delete('/settlement/{settlementId}', [SettlementController::class, 'destroy'])
        ->name('settlement.destroy');
    
    // Submit settlement for approval (if you have approval workflow)
    Route::post('/settlement/{settlementId}/submit', [SettlementController::class, 'submit'])
        ->name('settlement.submit');
    
    // Download settlement report/file
    Route::get('/settlement/{settlementId}/download', [SettlementController::class, 'download'])
        ->name('settlement.download');
    
    // Settlement list/index
    Route::get('/settlements', [SettlementController::class, 'index'])
        ->name('settlement.index');
        
    Route::prefix('transaction-request')->group(function () {
        Route::get('/', [TransactionRequestController::class, 'index'])->name('transaction-request.index');
        Route::post('/update-status/{pengajuanId}', [TransactionRequestController::class, 'updateStatus'])->name('transaction-request.update-status');
    });
    
    // Download bukti transfer route
    Route::get('/pengajuan/{pengajuanId}/bukti-transfer', [TransactionRequestController::class, 'downloadBuktiTransfer'])->name('pengajuan.bukti-transfer');
    
    Route::prefix('TransactionRequest')->group(function () {
        Route::get('/detail-pengajuan-full/{id}', [TransactionRequestController::class, 'getDetailPengajuanFull'])->name('transactionrequest.detail-pengajuan-full');
        // Halaman index untuk melihat dan mengelola TR
        Route::get('/', [TransactionRequestController::class, 'index'])->name('transactionrequest.index');
        
        // Halaman show untuk melihat TR yang sudah completed
        Route::get('/show', [TransactionRequestController::class, 'show'])->name('transactionrequest.show');
        
        // Create TR baru
        Route::post('/create', [TransactionRequestController::class, 'createTR'])->name('transactionrequest.create');
        
        // Filter pengajuan berdasarkan kategori dan department
        Route::post('/filter-pengajuan', [TransactionRequestController::class, 'filterPengajuan'])->name('transactionrequest.filter-pengajuan');
        
        // Get detail TR untuk modal
        Route::get('/detail/{id}', [TransactionRequestController::class, 'getTRDetail'])->name('transactionrequest.detail');
        
        // Update status individual pengajuan dalam TR
        Route::post('/{pengajuanId}/update-status', [TransactionRequestController::class, 'updateStatus'])->name('transactionrequest.update-status');
        
        // Delete TR (hanya yang status pending)
        Route::delete('/{id}/delete', [TransactionRequestController::class, 'deleteTR'])->name('transactionrequest.delete');
        
        // Download bukti transfer untuk pengajuan individual
        Route::get('/download-bukti-pengajuan/{pengajuanId}', [TransactionRequestController::class, 'downloadBuktiPengajuan'])->name('transactionrequest.download-bukti-pengajuan');
        
        // Download bukti transfer TR (legacy - untuk backward compatibility)
        Route::get('/{id}/download-bukti', [TransactionRequestController::class, 'downloadBukti'])->name('transactionrequest.download-bukti');
        
        // Get detail pengajuan untuk modal
        Route::get('/detail-pengajuan/{id}', [TransactionRequestController::class, 'getDetailPengajuan'])->name('transactionrequest.detail-pengajuan');
        
        // Legacy routes (untuk backward compatibility)
        Route::post('/{id}/complete', [TransactionRequestController::class, 'completeTR'])->name('transactionrequest.complete');
        
        Route::get('/detail-settlement/{id}', [TransactionRequestController::class, 'getDetailSettlement'])->name('transactionrequest.detail-settlement');
        // Tambahkan di route TransactionRequest
        Route::post('/settlement/{settlementId}/update-status', [TransactionRequestController::class, 'updateSettlementStatus'])->name('transactionrequest.update-settlement-status');
        
        Route::get('/download-bukti-settlement/{settlementId}', [TransactionRequestController::class, 'downloadBuktiSettlement'])->name('transactionrequest.download-bukti-settlement');
    });
    
    Route::get('/TransactionRequest/{id}/bukti-detail', [PengajuanController::class, 'getBuktiDetail'])
    ->name('transactionrequest.bukti-detail');

    // Route untuk download bukti transfer (jika belum ada)
    Route::get('/TransactionRequest/{id}/download-bukti', [PengajuanController::class, 'downloadBukti'])
        ->name('transactionrequest.download-bukti');
        
    Route::get('/TransactionRequest/{id}/preview-bukti', [PengajuanController::class, 'previewBukti'])
        ->name('transactionrequest.preview-bukti');
    
    // Route untuk mendapatkan URL (opsional)
    Route::get('/TransactionRequest/{id}/bukti-url', [PengajuanController::class, 'getBuktiUrl'])
        ->name('transactionrequest.bukti-url');
    
    // // ROUTE UNTUK NOTIFIKASI
    Route::get('/notifications/all', [App\Http\Controllers\NotificationController::class, 'getAllNotifications'])->name('notifications.all');
    Route::post('/notifications/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/clear-all', [App\Http\Controllers\NotificationController::class, 'clearAll'])->name('notifications.clear-all');
    Route::post('/dashboard/mark-notification-read', [DashboardController::class, 'markNotificationAsRead'])->name('dashboard.mark-notification-read');
    // // AKHIR ROUTE UNTUK NOTIFIKASI
    
    // ROUTE UNTUK ADMIN
     // Route untuk Kategori Pengajuan CRUD
    Route::get('/kategori-pengajuan', [KategoriPengajuanController::class, 'index'])->name('kategori-pengajuan.index');
    Route::post('/kategori-pengajuan', [KategoriPengajuanController::class, 'store'])->name('kategori-pengajuan.store');
    Route::get('/kategori-pengajuan/{id}', [KategoriPengajuanController::class, 'show'])->name('kategori-pengajuan.show');
    Route::put('/kategori-pengajuan/{id}', [KategoriPengajuanController::class, 'update'])->name('kategori-pengajuan.update');
    Route::delete('/kategori-pengajuan/{id}', [KategoriPengajuanController::class, 'destroy'])->name('kategori-pengajuan.destroy');
    Route::get('/kategori-pengajuan-aktif', [KategoriPengajuanController::class, 'getActiveKategori'])->name('kategori-pengajuan.active');
    
    Route::prefix('admin')->group(function () {
        Route::get('/kategori-pengajuan', [App\Http\Controllers\Admin\KategoriPengajuanController::class, 'showPage'])->name('admin.kategori.index');
        Route::get('/api/kategori-pengajuan', [App\Http\Controllers\Admin\KategoriPengajuanController::class, 'index'])->name('admin.kategori.api.index');
        Route::post('/api/kategori-pengajuan', [App\Http\Controllers\Admin\KategoriPengajuanController::class, 'store'])->name('admin.kategori.api.store');
        Route::put('/api/kategori-pengajuan/{id}', [App\Http\Controllers\Admin\KategoriPengajuanController::class, 'update'])->name('admin.kategori.api.update');
        Route::delete('/api/kategori-pengajuan/{id}', [App\Http\Controllers\Admin\KategoriPengajuanController::class, 'destroy'])->name('admin.kategori.api.destroy');
        Route::get('/api/kategori-pengajuan/{id}', [App\Http\Controllers\Admin\KategoriPengajuanController::class, 'show'])->name('admin.kategori.api.show');
    });
    
    Route::post('/kelola-pengguna-baru', [KelolaPenggunaController::class, 'store'])->name('kelola-pengguna-baru.store');
});









