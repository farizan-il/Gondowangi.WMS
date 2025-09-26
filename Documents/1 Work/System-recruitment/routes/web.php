<?php

use App\Http\Controllers\Gondowangi\Beranda\BerandaController;
use App\Http\Controllers\Gondowangi\Berita\BeritaController;
use App\Http\Controllers\Gondowangi\Brands\Azalea\AzaleaController;
use App\Http\Controllers\Gondowangi\Brands\Natur\NaturController;
use App\Http\Controllers\Gondowangi\Brands\Mizzu\MizzuController;
use App\Http\Controllers\Gondowangi\Brands\HGForMan\HgForManController;
use App\Http\Controllers\Gondowangi\Brands\SemuaBrandController;
use App\Http\Controllers\Gondowangi\Karir\KarirController;
use App\Http\Controllers\Gondowangi\KontakKami\KontakKamiController;
use App\Http\Controllers\Gondowangi\TentangKami\TentangKamiController;
use App\Http\Controllers\Gondowangi\AdminController\Dashboard\DashboardController;
use App\Http\Controllers\Gondowangi\AdminController\Beranda\ProdukKamiController;
use App\Http\Controllers\Gondowangi\AdminController\Beranda\AwardController;
use App\Http\Controllers\Gondowangi\AdminController\Beranda\BeritadanArtikelController;
use App\Http\Controllers\Gondowangi\AdminController\TentangKami\PerjalananController;
use App\Http\Controllers\Gondowangi\AdminController\TentangKami\CaturPilarController;
use App\Http\Controllers\Gondowangi\AdminController\TentangKami\ProdukController;
use App\Http\Controllers\Gondowangi\AdminController\Karir\LowonganController;
use App\Http\Controllers\Gondowangi\AdminController\Berita\SemuaBeritaController;
use App\Http\Controllers\Gondowangi\AdminController\Berita\PostController;
use App\Http\Controllers\Gondowangi\AdminController\KontakKami\LokasiController;
use App\Http\Controllers\Gondowangi\AdminController\Beranda\BannerController;
use App\Http\Controllers\Gondowangi\AdminController\Beranda\AcaraMendatangController;
use App\Http\Controllers\Gondowangi\Kandidat\AdminKandidat\AdminKandidatController;
use App\Http\Controllers\Gondowangi\AdminController\Footer\FooterController;
use App\Http\Controllers\Gondowangi\Authentifikasi\AuthentifikasiController;
use App\Http\Controllers\Gondowangi\AdminController\BrandKami\SemuaBrandAdminController;
use App\Http\Controllers\Gondowangi\AdminController\BrandKami\NaturAdminController;
use App\Http\Controllers\Gondowangi\Kandidat\KaryawanController;
use App\Http\Controllers\Gondowangi\AdminController\TentangKami\TentangKamiBannerController;
use App\Http\Controllers\Gondowangi\Kandidat\AdminKandidat\LolosController;

use Illuminate\Support\Facades\Route;

Route::resource('/', BerandaController::class);
Route::resource('/beranda', BerandaController::class);
Route::resource('/tentangkami', TentangKamiController::class);
Route::resource('/karir', KarirController::class);
Route::resource('/beritaclient', BeritaController::class);
Route::resource('/kontakkami', KontakKamiController::class);
Route::resource('/semuabrand', SemuaBrandController::class);
Route::resource('/azalea', AzaleaController::class);
Route::resource('/natur', NaturController::class);
Route::resource('/mizzu', MizzuController::class);
Route::resource('/hgforman', HgForManController::class);

Route::prefix('berita')->name('berita.')->group(function () {
    Route::get('/', [BeritaController::class, 'index'])->name('index');
    Route::get('/load-more', [BeritaController::class, 'loadMore'])->name('load-more');
    Route::get('/search', [BeritaController::class, 'search'])->name('search');
    Route::get('/{slug}', [BeritaController::class, 'show'])->name('show');
});

// Tambahkan middleware tracking untuk guest routes di card
Route::middleware(['visitor.tracking'])->group(function () {
    Route::resource('/', BerandaController::class);
    Route::resource('/beranda', BerandaController::class);
    Route::resource('/tentangkami', TentangKamiController::class);
    Route::resource('/karir', KarirController::class);
    Route::resource('/beritaclient', BeritaController::class);
    Route::resource('/kontakkami', KontakKamiController::class);
    Route::resource('/semuabrand', SemuaBrandController::class);
    Route::resource('/natur', NaturController::class);
    Route::resource('/mizzu', MizzuController::class);
    Route::resource('/azalea', AzaleaController::class);
    Route::resource('/hgforman', HgForManController::class);
});

// ===== ROUTE UNTUK GUEST (tambahkan middleware tracking) =====
Route::middleware(['web', 'track.visitor'])->group(function () {
    Route::resource('/', BerandaController::class);
    Route::resource('/beranda', BerandaController::class);
    Route::resource('/tentangkami', TentangKamiController::class);
    Route::resource('/karir', KarirController::class);
    Route::resource('/beritaclient', BeritaController::class);
    Route::resource('/kontakkami', KontakKamiController::class);
    Route::resource('/semuabrand', SemuaBrandController::class);
    Route::resource('/natur', NaturController::class);
    Route::resource('/mizzu', MizzuController::class);
    Route::resource('/azalea', AzaleaController::class);
    Route::resource('/hgforman', HgForManController::class);
});


// Route untuk debugging email
Route::get('/debug-email-config', [AuthentifikasiController::class, 'debugEmailConfig'])->name('debug.email.config');
Route::get('/test-basic-email', [AuthentifikasiController::class, 'testBasicEmail'])->name('debug.email.basic');

// Route untuk test SMTP connection
Route::get('/test-smtp-connection', function() {
    try {
        $transport = new \Swift_SmtpTransport(
            config('mail.mailers.smtp.host'),
            config('mail.mailers.smtp.port'),
            config('mail.mailers.smtp.encryption')
        );
        
        $transport->setUsername(config('mail.mailers.smtp.username'));
        $transport->setPassword(config('mail.mailers.smtp.password'));
        
        $mailer = new \Swift_Mailer($transport);
        
        // Test connection
        $transport->start();
        
        return response()->json([
            'success' => true,
            'message' => 'SMTP connection berhasil!',
            'config' => [
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'encryption' => config('mail.mailers.smtp.encryption'),
                'username' => config('mail.mailers.smtp.username')
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'SMTP connection gagal: ' . $e->getMessage(),
            'config' => [
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'encryption' => config('mail.mailers.smtp.encryption'),
                'username' => config('mail.mailers.smtp.username')
            ]
        ]);
    }
});

// Route asli tetap ada
Route::get('/masuk', [AuthentifikasiController::class, 'index'])->name('Auth.index');
Route::post('/login', [AuthentifikasiController::class, 'login'])->name('auth.login');
Route::post('/register', [AuthentifikasiController::class, 'register'])->name('auth.register');
Route::post('/logout', [AuthentifikasiController::class, 'logout'])->name('auth.logout');

Route::post('/forgot-password/send-otp', [AuthentifikasiController::class, 'sendOtpForgotPassword'])->name('auth.forgot.send-otp');
Route::post('/forgot-password/verify-otp', [AuthentifikasiController::class, 'verifyOtpForgotPassword'])->name('auth.forgot.verify-otp');
Route::post('/forgot-password/reset', [AuthentifikasiController::class, 'resetPassword'])->name('auth.forgot.reset-password');

// ===== ROUTE UNTUK PUBLIC (FRONTEND) =====
// Berita Frontend
Route::prefix('berita')->name('berita.')->group(function () {
    Route::get('/', [BeritaController::class, 'index'])->name('index');
    Route::get('/search', [BeritaController::class, 'search'])->name('search');
    Route::post('/load-more', [BeritaController::class, 'loadMore'])->name('load-more');
    Route::get('/kategori/{slug}', [BeritaController::class, 'category'])->name('category');
    Route::get('/arsip', [BeritaController::class, 'archive'])->name('archive');
    Route::get('/rss', [BeritaController::class, 'rss'])->name('rss');
    Route::get('/sitemap.xml', [BeritaController::class, 'sitemap'])->name('sitemap');
    Route::get('/{slug}', [BeritaController::class, 'show'])->name('show');
});

// Contact Form Public
Route::post('/contact-form', [LokasiController::class, 'store'])->name('contact.submit');

// Public Events
Route::get('/events', [AcaraMendatangController::class, 'getFeaturedEvents'])->name('public.events');

// ===== ROUTE UNTUK KANDIDAT =====
Route::middleware(['auth', 'check.role:kandidat'])->group(function () {
    Route::prefix('kandidat')->name('kandidat.')->group(function () {
        Route::get('/form-pengisian/{karyawan_id?}', [KaryawanController::class, 'index'])->name('formkaryawan.index');
        Route::get('/dashboard', [KaryawanController::class, 'dashboard'])->name('dashboard');
        Route::get('/form-pengisian', [KaryawanController::class, 'index'])->name('form.index');
        Route::post('/daftar', [KaryawanController::class, 'store'])->name('daftar.store');
        Route::put('/update-data', [KaryawanController::class, 'update'])->name('update');
        Route::get('/download/{type}', [KaryawanController::class, 'downloadDocument'])->name('download');
        Route::get('/karyawan/lamaran/{id}', [KaryawanController::class, 'viewApplication'])->name('karyawan.view-application');
    });
});

// ===== ROUTE UNTUK ADMIN KANDIDAT =====
// ===== ROUTE UNTUK ADMIN KANDIDAT - KELOLA KANDIDAT LOLOS =====
Route::middleware(['auth', 'check.role:adminkandidat'])->group(function () {
    Route::prefix('admin-kandidat')->name('admin.kandidat.')->group(function () {
        
        // Route yang sudah ada sebelumnya
        Route::get('/dashboard', [AdminKandidatController::class, 'dashboard'])->name('dashboard');
        Route::get('/employee-data', [AdminKandidatController::class, 'getEmployeeData'])->name('employee.data');
        Route::get('/employee/{id}', [AdminKandidatController::class, 'getEmployeeDetail'])->name('employee.detail');
        
        // Route baru untuk data kandidat dengan filter
        Route::get('/kandidat-data', [AdminKandidatController::class, 'getKandidatData'])->name('data');
        Route::get('/filter-options', [AdminKandidatController::class, 'getFilterOptions'])->name('filter.options');
        Route::put('/kandidat/{id}/status', [AdminKandidatController::class, 'updateKandidatStatus'])->name('update.status');
        
        // Route yang sudah ada sebelumnya
        Route::put('/employee/{id}/status', [AdminKandidatController::class, 'updateStatus'])->name('employee.status');
        Route::get('/chart-data', [AdminKandidatController::class, 'getChartData'])->name('chart.data');
        
        // Route baru untuk Ajax request detail kota
        Route::post('/city-details', [AdminKandidatController::class, 'getCityDetails'])->name('city-details');
        
        // Route baru untuk chart data
        Route::get('/applications-by-position', [AdminKandidatController::class, 'getApplicationsByPosition'])->name('applications.by.position');
        Route::get('/status-distribution', [AdminKandidatController::class, 'getStatusDistribution'])->name('status.distribution');
        Route::get('/all-applications-chart', [AdminKandidatController::class, 'getAllApplicationsChart'])->name('all.applications.chart');
        Route::get('/all-status-distribution', [AdminKandidatController::class, 'getAllStatusDistribution'])->name('all.status.distribution');

        // ===== ROUTE BARU UNTUK KELOLA KANDIDAT LOLOS =====
        
        // Main page untuk kelola kandidat lolos
        Route::get('/lolos', [LolosController::class, 'index'])->name('lolos');
        
        // API endpoints untuk kelola kandidat lolos
        Route::get('/lolos-data', [LolosController::class, 'getLolosData'])->name('lolos.data');
        Route::get('/lolos/filter-options', [LolosController::class, 'getFilterOptions'])->name('lolos.filter.options');
        Route::get('/lolos/by-position', [LolosController::class, 'getKandidatsByPosition'])->name('lolos.by.position');
        
        // Update status kandidat lolos
        Route::put('/lolos/{id}/status', [LolosController::class, 'updateStatus'])->name('lolos.update.status');
        
        // Detail kandidat lolos
        Route::get('/lolos/{id}/detail', [LolosController::class, 'getKandidatDetail'])->name('lolos.detail');
        
        // Statistik kandidat lolos
        Route::get('/lolos/statistics', [LolosController::class, 'getStatistics'])->name('lolos.statistics');
        
        // Bulk update status (opsional - untuk update status beberapa kandidat sekaligus)
        Route::put('/lolos/bulk-update-status', [LolosController::class, 'bulkUpdateStatus'])->name('lolos.bulk.update.status');
        
        // Export data kandidat lolos
        Route::get('/lolos/export', [LolosController::class, 'exportData'])->name('lolos.export');
    });
});

// ===== ROUTE UNTUK ADMIN WEB =====
Route::middleware(['auth', 'check.role:adminweb'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::put('semuabrand/{banner}', [BannerController::class, 'update'])->name('semuabrand.update');

        
        // Route untuk brand detail management
        // Route::delete('/naturadmin/{id}/destroy', [NaturAdminController::class, 'destroy'])->name('naturadmin.destroy');
        Route::resource('/naturadmin', NaturAdminController::class);
        
        // Jika ingin menambahkan route khusus untuk API calls (opsional)
        Route::get('/naturadmin/{id}/show', [NaturAdminController::class, 'show'])->name('naturadmin.show');
        
        
        

        
        Route::group(['prefix' => 'semuabrand', 'as' => 'semuabrand.'], function () {
            Route::get('/', [SemuaBrandAdminController::class, 'index'])->name('index');
            Route::put('/{banner}/update-foto', [SemuaBrandAdminController::class, 'updateFoto'])->name('updateFoto');
        });
        
        Route::prefix('brand')->group(function () {
            // Route untuk menampilkan semua brand
            Route::get('/', [SemuaBrandAdminController::class, 'index'])->name('admin.brand.index');
            // Route untuk menyimpan gambar brand carousel baru
            Route::post('/store-brand-image', [SemuaBrandAdminController::class, 'storeBrandImg'])->name('semuabrandadmin.store-brand-image');
            // Route untuk update gambar brand (hanya edit/update)
            Route::put('/{id}/update-image', [SemuaBrandAdminController::class, 'updateImage'])->name('admin.brand.update-image');
            // Route untuk menghapus gambar brand (optional - jika ingin ada fitur hapus gambar)
            Route::delete('/{id}/remove-image', [SemuaBrandAdminController::class, 'removeImage'])->name('admin.brand.remove-image');
        });
        
        
        
        // Dashboard Admin
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/realtime-data', [DashboardController::class, 'getRealTimeData'])->name('realtime-data');
        Route::get('/dashboard/realtime', [DashboardController::class, 'getRealTimeData'])->name('dashboard.realtime');
        Route::get('/dashboard/traffic-data', [DashboardController::class, 'getTrafficData'])->name('dashboard.traffic-data');
        
        // ===== MANAJEMEN KONTEN BERANDA =====
        Route::resource('produkkami', ProdukKamiController::class);
        Route::prefix('produkkami')->name('produkkami.')->group(function () {
            Route::get('/search', [ProdukKamiController::class, 'search'])->name('search');
            Route::post('/brands', [ProdukKamiController::class, 'store'])->name('brands.store');
            Route::get('/brands/{brand}', [ProdukKamiController::class, 'show'])->name('brands.show');
            Route::put('/brands/{brand}', [ProdukKamiController::class, 'update'])->name('brands.update');
            Route::delete('/brands/{brand}', [ProdukKamiController::class, 'destroy'])->name('brands.destroy');
        });
        
        // Awards Management
        Route::resource('awards', AwardController::class);
        Route::patch('awards/{award}/toggle-status', [AwardController::class, 'toggleStatus'])->name('awards.toggle-status');
        
        // Banner Beranda Management
        Route::resource('banner', BannerController::class);
        Route::patch('banner/{banner}/toggle-status', [BannerController::class, 'toggleStatus'])->name('banner.toggle-status');
        
        // Banner Tentang Kami Management
        Route::resource('bannertentangkami', TentangKamiBannerController::class);
        Route::patch('bannertentangkami/{banner}/toggle-status', [TentangKamiBannerController::class, 'toggleStatus'])->name('bannertentangkami.toggle-status');
        
        // Semua Brand Admin
        Route::resource('semuabrandadmin', SemuaBrandAdminController::class);
        Route::patch('semuabrandadmin/{banner}/toggle-status', [SemuaBrandAdminController::class, 'toggleStatus'])->name('semuabrandadmin.toggle-status');
        
        
        // ===== MANAJEMEN TENTANG KAMI =====
        Route::prefix('tentang-kami')->name('tentang-kami.')->group(function () {
            Route::resource('perjalanan', PerjalananController::class);
            Route::resource('catur-pilar', CaturPilarController::class);
        });
        
        // ===== MANAJEMEN BERITA & ARTIKEL =====
        Route::prefix('berita-artikel')->name('news.')->group(function () {
            Route::get('/', [BeritadanArtikelController::class, 'index'])->name('index');
            Route::post('/', [BeritadanArtikelController::class, 'store'])->name('store');
            Route::get('/{id}', [BeritadanArtikelController::class, 'show'])->name('show');
            Route::post('/{id}', [BeritadanArtikelController::class, 'update'])->name('update');
            Route::delete('/{id}', [BeritadanArtikelController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/toggle-featured', [BeritadanArtikelController::class, 'toggleFeatured'])->name('toggle-featured');
        });
        
        // Semua Berita Management
        Route::prefix('berita')->name('berita.')->group(function () {
            Route::get('/', [SemuaBeritaController::class, 'index'])->name('index');
            Route::get('/{id}', [SemuaBeritaController::class, 'show'])->name('show');
            Route::put('/{id}', [SemuaBeritaController::class, 'update'])->name('update');
            Route::delete('/{id}', [SemuaBeritaController::class, 'destroy'])->name('destroy');
        });
        
        // ===== MANAJEMEN LOWONGAN =====
        Route::prefix('lowongan')->name('lowongan.')->group(function () {
            Route::get('/', [LowonganController::class, 'index'])->name('index');
            Route::post('/', [LowonganController::class, 'store'])->name('store');
            Route::get('/{id}', [LowonganController::class, 'show'])->name('show');
            Route::put('/{id}', [LowonganController::class, 'update'])->name('update');
            Route::delete('/{id}', [LowonganController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/toggle-status', [LowonganController::class, 'toggleStatus'])->name('toggle-status');
        });
        
        // ===== MANAJEMEN PRODUK =====
        Route::resource('newproduk', ProdukController::class);
        
        // ===== MANAJEMEN POSTINGAN =====
        Route::resource('postingan', PostController::class);
        Route::post('postingan/update-quote', [PostController::class, 'updateQuote'])->name('postingan.update-quote');
        
        // ===== MANAJEMEN KONTAK =====
        
        Route::get('/contacts/unread-count', [LokasiController::class, 'getUnreadCount'])->name('contacts.unread-count');
        Route::prefix('contacts')->name('contacts.')->group(function () {
            Route::post('/{id}/mark-complete', [LokasiController::class, 'markAsComplete'])->name('mark-complete');
            Route::get('/', [LokasiController::class, 'index'])->name('index');
            Route::get('/{id}', [LokasiController::class, 'show'])->name('show');
            Route::delete('/{id}', [LokasiController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/mark-read', [LokasiController::class, 'markAsRead'])->name('mark-read');
        });
        
        // ===== MANAJEMEN FOOTER =====
        Route::resource('footer', FooterController::class)->only(['index', 'update']);
        Route::post('footer/toggle-status', [FooterController::class, 'toggleStatus'])->name('footer.toggle-status');
        
        // ===== MANAJEMEN ACARA MENDATANG =====
        Route::resource('acara-mendatang', AcaraMendatangController::class);
        Route::get('acara-mendatang/{id}/data', [AcaraMendatangController::class, 'show'])->name('acara-mendatang.data');
        Route::post('acara-mendatang/{id}/toggle-featured', [AcaraMendatangController::class, 'toggleFeatured'])->name('acara-mendatang.toggle-featured');
    });
});
    