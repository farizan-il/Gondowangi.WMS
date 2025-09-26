
// Route untuk admin di halaman/fitur beranda
Route::resource('/dashboard', DashboardController::class);
Route::resource('/produkkami', ProdukKamiController::class);
Route::resource('/awards', AwardController::class);
Route::patch('/awards/{award}/toggle-status', [AwardController::class, 'toggleStatus'])->name('awards.toggle-status');
// Route::resource('/beritaartikel', BeritadanArtikelController::class);
Route::resource('/perjalanan', PerjalananController::class);
Route::resource('/caturpilar', CaturPilarController::class);
Route::resource('/newproduk', ProdukController::class);
Route::resource('/lowongan', LowonganController::class);
Route::resource('/allberita', SemuaBeritaController::class);

Route::resource('/postingan', PostController::class);
Route::post('/postingan/update-quote', [PostController::class, 'updateQuote'])->name('postingan.update-quote');

Route::prefix('produkkami')->name('produkkami.')->group(function () {
    Route::get('/produkkami', [ProdukKamiController::class, 'index'])->name('index');
    Route::post('/brands', [ProdukKamiController::class, 'store'])->name('store');
    Route::get('/brands/{brand}', [ProdukKamiController::class, 'show'])->name('show');
    Route::put('/brands/{brand}', [ProdukKamiController::class, 'update'])->name('update');
    Route::delete('/brands/{brand}', [ProdukKamiController::class, 'destroy'])->name('destroy');
    Route::get('/search', [ProdukKamiController::class, 'search'])->name('search');
});

Route::prefix('admin')->group(function () {
    Route::prefix('tentang-kami')->group(function () {
        Route::resource('perjalanan', App\Http\Controllers\Gondowangi\AdminController\TentangKami\PerjalananController::class);
    });
});

Route::resource('/beritaartikeladd', BeritadanArtikelController::class);
Route::prefix('admin')->name('admin.')->group(function () {
    // News Management Routes - Gunakan satu set route saja
    Route::get('berita-artikel', [BeritadanArtikelController::class, 'index'])
        ->name('news.index');
    Route::post('berita-artikel', [BeritadanArtikelController::class, 'store'])
        ->name('news.store');
    Route::get('berita-artikel/{id}', [BeritadanArtikelController::class, 'show'])
        ->name('news.show');
    Route::post('berita-artikel/{id}', [BeritadanArtikelController::class, 'update'])
        ->name('news.update');
    Route::delete('berita-artikel/{id}', [BeritadanArtikelController::class, 'destroy'])
        ->name('news.destroy');
    
    // Toggle featured route
    Route::post('berita-artikel/{id}/toggle-featured', [BeritadanArtikelController::class, 'toggleFeatured'])
        ->name('news.toggle-featured');
});

Route::prefix('admin')->group(function () {
    Route::get('/lowongan', [LowonganController::class, 'index'])->name('admin.lowongan.index');
    Route::post('/lowongan', [LowonganController::class, 'store'])->name('admin.lowongan.store');
    Route::get('/lowongan/{id}', [LowonganController::class, 'show'])->name('admin.lowongan.show');
    Route::put('/lowongan/{id}', [LowonganController::class, 'update'])->name('admin.lowongan.update');
    Route::delete('/lowongan/{id}', [LowonganController::class, 'destroy'])->name('admin.lowongan.destroy');
    Route::post('/lowongan/{id}/toggle-status', [LowonganController::class, 'toggleStatus'])->name('admin.lowongan.toggle-status');
});
    
Route::get('/admin/berita', [SemuaBeritaController::class, 'index'])->name('admin.berita.index');

// Routes untuk API modal operations
Route::get('/admin/berita/{id}', [SemuaBeritaController::class, 'show'])->name('admin.berita.show');
Route::put('/admin/berita/{id}', [SemuaBeritaController::class, 'update'])->name('admin.berita.update');
Route::delete('/admin/berita/{id}', [SemuaBeritaController::class, 'destroy'])->name('admin.berita.destroy');

    
  
    
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('catur-pilar', CaturPilarController::class);
});

// Contact Management Routes (tanpa auth untuk testing)
Route::prefix('admin')->name('admin.')->group(function () {
    // Route untuk index (menampilkan daftar kontak)
    Route::get('/contacts', [LokasiController::class, 'index'])->name('contacts.index');
    
    // Route untuk show (melihat detail kontak) - READ
    Route::get('/contacts/{id}', [LokasiController::class, 'show'])->name('contacts.show');
    
    // Route untuk delete (menghapus kontak) - DELETE
    Route::delete('/contacts/{id}', [LokasiController::class, 'destroy'])->name('contacts.destroy');
    
    // Route untuk mark as read (sudah ada sebelumnya)
    Route::post('contacts/{id}/mark-read', [LokasiController::class, 'markAsRead'])->name('contacts.mark-read');
});

// Optional: Frontend contact form route (jika diperlukan)
Route::post('/contact-form', [LokasiController::class, 'store'])->name('contact.submit');
    
    
Route::prefix('admin')->name('admin.')->group(function () {
    // Routes untuk Banner
    Route::get('banner', [BannerController::class, 'index'])->name('banner.index');
    Route::post('banner', [BannerController::class, 'store'])->name('banner.store');
    Route::put('banner/{banner}', [BannerController::class, 'update'])->name('banner.update');
    Route::delete('banner/{banner}', [BannerController::class, 'destroy'])->name('banner.destroy');
    Route::patch('banner/{banner}/toggle-status', [BannerController::class, 'toggleStatus'])->name('banner.toggle-status');
});

Route::prefix('admin')->name('admin.')->group(function () {
    // Routes untuk Banner - Semua Brand Admin
    Route::get('semuabrandadmin', [SemuaBrandAdminController::class, 'index'])->name('semuabrandadmin.index');
    Route::post('semuabrandadmin', [SemuaBrandAdminController::class, 'store'])->name('semuabrandadmin.store');
    Route::put('semuabrandadmin/{banner}', [SemuaBrandAdminController::class, 'update'])->name('semuabrandadmin.update');
    Route::delete('semuabrandadmin/{banner}', [SemuaBrandAdminController::class, 'destroy'])->name('semuabrandadmin.destroy');
    Route::patch('semuabrandadmin/{banner}/toggle-status', [SemuaBrandAdminController::class, 'toggleStatus'])->name('semuabrandadmin.toggle-status');
});
    
// ROUTE UNTUK ADMIN KANDIDAT
Route::prefix('admin')->group(function () {
    // Route untuk dashboard (main core)
    Route::get('/dashboard', [AdminKandidatController::class, 'dashboard'])->name('admin.dashboard');
    
    // Route untuk data karyawan
    Route::get('/employee-data', [AdminKandidatController::class, 'getEmployeeData'])->name('admin.employee.data');
    Route::get('/employee/{id}', [AdminKandidatController::class, 'getEmployeeDetail'])->name('admin.employee.detail');
    Route::put('/employee/{id}/status', [AdminKandidatController::class, 'updateStatus'])->name('admin.employee.status');
    Route::get('/chart-data', [AdminKandidatController::class, 'getChartData'])->name('admin.chart.data');
});
Route::get('/karyawan/lamaran/{id}', [AdminKandidatController::class, 'viewApplication'])
    ->name('karyawan.view-application');


// ROUTE UNTUK LOGIKA LOGIN
Route::get('/masuk', [AuthentifikasiController::class, 'index'])->name('Auth.index');
Route::post('/login', [AuthentifikasiController::class, 'login'])->name('auth.login');
Route::post('/register', [AuthentifikasiController::class, 'register'])->name('auth.register');
Route::post('/logout', [AuthentifikasiController::class, 'logout'])->name('auth.logout');

// Protected Routes dengan Middleware
Route::middleware(['auth'])->group(function () {
    // Route untuk Kandidat
    Route::middleware('role:Kandidat')->group(function () {
        // Dashboard kandidat - ini yang akan menjadi landing page setelah login
        Route::get('/dashboard-kandidat', [KaryawanController::class, 'dashboard'])
            ->name('kandidat.dashboard');
        
        // Form pengisian - hanya bisa diakses jika belum mengisi
        Route::get('/form-pengisian-karyawan', [KaryawanController::class, 'index'])
            ->name('karyawan.index');
        
        // Store data kandidat
        Route::post('/store', [KaryawanController::class, 'store'])
            ->name('karyawan.store');
        
        // Update data kandidat (jika diperlukan)
        Route::put('/update-data', [KaryawanController::class, 'update'])
            ->name('karyawan.update');
        
        // Download dokumen
        Route::get('/download/{type}', [KaryawanController::class, 'downloadDocument'])
            ->name('karyawan.download');
    });
    
    // Route untuk Admin
    Route::get('/dashboard-admin', [AdminController::class, 'dashboard'])
        ->middleware('role:Admin')
        ->name('admin.dashboard');
});


Route::resource('footer', FooterController::class);
Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    // Footer Management Routes
    Route::resource('footer', FooterController::class);
    // Additional route for toggle status
    Route::patch('footer/{footer}/toggle-status', 'App\Http\Controllers\Gondowangi\AdminController\Footer\FooterController@toggleStatus')
        ->name('footer.toggle-status');
});


Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {
    Route::resource('footer', FooterController::class)->only(['index', 'update']);
    Route::post('footer/toggle-status', [FooterController::class, 'toggleStatus'])->name('footer.toggle-status');
});


Route::prefix('berita')->name('berita.')->group(function () {
    // Index page - list all news
    Route::get('/', [BeritaController::class, 'index'])->name('index');
    
    // Search functionality
    Route::get('/search', [BeritaController::class, 'search'])->name('search');
    
    // Load more news (AJAX)
    Route::post('/load-more', [BeritaController::class, 'loadMore'])->name('load-more');

    // Category page
    Route::get('/kategori/{slug}', [BeritaController::class, 'category'])->name('category');
    
    // Archive page
    Route::get('/arsip', [BeritaController::class, 'archive'])->name('archive');
    
    // RSS Feed
    Route::get('/rss', [BeritaController::class, 'rss'])->name('rss');
    
    // Sitemap
    Route::get('/sitemap.xml', [BeritaController::class, 'sitemap'])->name('sitemap');
    
    // Individual news detail - this should be last to avoid conflicts
    Route::get('/{slug}', [BeritaController::class, 'show'])->name('show');
});
    
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('acara-mendatang', AcaraMendatangController::class);

    // Additional routes for AJAX requests if needed
    Route::get('acara-mendatang/{id}/data', [AcaraMendatangController::class, 'show'])->name('acara-mendatang.data');
});
Route::post('/admin/acara-mendatang/{id}/toggle-featured', [AcaraMendatangController::class, 'toggleFeatured'])->name('admin.acara-mendatang.toggle-featured');
// Public Routes untuk menampilkan events di frontend
Route::get('/events', [AcaraMendatangController::class, 'getFeaturedEvents'])->name('public.events');
