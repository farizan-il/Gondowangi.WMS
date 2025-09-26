@extends('Gondowangi.Admin.Layout.main')

@section('head')
<!-- Font Awesome CDN -->
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-...your-integrity..." crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<style>
    /* Tambahkan CSS ini ke file CSS Anda */
    .ck-editor__editable_inline {
        min-height: 300px;
    }
    /* Stats Cards */
    .stats-cards {
        margin-bottom: 2rem;
    }
    
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 1.5rem;
        color: white;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    
    .stat-card.primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .stat-card.success {
        background-image: linear-gradient( 75.1deg,  rgba(34,126,34,1) 6%, rgba(99,226,17,1) 84.3% );
        
    }
    
    .stat-card.warning {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        
    }
    
    .stat-card.danger {
       background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }
    
    .stat-card .card-icon {
        position: absolute;
        top: 1rem;
        right: 1rem;
        font-size: 2rem;
        opacity: 0.3;
    }
    
    .stat-card h3 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .stat-card p {
        font-size: 0.9rem;
        margin-bottom: 0;
        opacity: 0.9;
    }
    
    /* Pulse animation for warning card */
    .pulse {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    /* Main Content */
    .main-content {
        background: white;
        border-radius: 10px;
        padding: 1.5rem;
    }
    
    .content-header {
        border-bottom: 1px solid #eee;
        padding-bottom: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .content-header h4 {
        color: #333;
        font-weight: 600;
    }
    
    /* Search Section */
    .search-section {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
    }
    
    .search-section .form-control,
    .search-section .form-select {
        border: 1px solid #ddd;
        border-radius: 6px;
    }
    
    .search-section .btn-outline-primary {
        border-color: #007bff;
        color: #007bff;
    }
    
    .search-section .btn-outline-primary:hover {
        background-color: #007bff;
        color: white;
    }
    
    /* News Table */
    .news-table {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .news-table thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .news-table thead th {
        border: none;
        padding: 1rem 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    
    .news-table tbody tr {
        transition: background-color 0.2s ease;
    }
    
    .news-table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .news-table tbody td {
        padding: 1rem 0.75rem;
        vertical-align: middle;
        border-bottom: 1px solid #eee;
    }
    
    .news-image {
        width: 60px;
        height: 40px;
        object-fit: cover;
        border-radius: 4px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .news-preview {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    /* Badges */
    .badge {
        font-size: 0.75rem;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-weight: 500;
    }
    
    .badge.bg-info {
        background-color: #17a2b8 !important;
    }
    
    .badge.bg-success {
        background-color: #28a745 !important;
    }
    
    .badge.bg-warning {
        background-color: #ffc107 !important;
        color: #212529 !important;
    }
    
    .badge.published {
        background-color: #28a745;
        color: white;
    }
    
    .badge.draft {
        background-color: #ffc107;
        color: #212529;
    }
    
    .badge.featured {
        background-color: #ff6b6b;
        color: white;
    }
    
    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.25rem;
        justify-content: center;
    }
    
    .action-buttons .btn {
        padding: 0.375rem 0.5rem;
        font-size: 0.875rem;
        border-radius: 4px;
        transition: all 0.2s ease;
    }
    
    .action-buttons .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .action-buttons .btn-info {
        background-color: #17a2b8;
        border-color: #17a2b8;
    }
    
    .action-buttons .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #212529;
    }
    
    .action-buttons .btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
    }
    
    /* Modal Styles */
    .modal-content {
        border-radius: 10px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    
    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px 10px 0 0;
        border-bottom: none;
    }
    
    .modal-header .modal-title {
        font-weight: 600;
    }
    
    .modal-header .btn-close {
        filter: brightness(0) invert(1);
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    .modal-footer {
        border-top: 1px solid #eee;
        padding: 1rem 1.5rem;
    }
    
    /* Form Styles */
    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
    }
    
    .form-control,
    .form-select {
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 0.75rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }
    
    .form-control:focus,
    .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .form-check-input:checked {
        background-color: #667eea;
        border-color: #667eea;
    }
    
    /* Pagination */
    .pagination {
        justify-content: center;
        margin-top: 1.5rem;
    }
    
    .pagination .page-link {
        color: #667eea;
        border-color: #dee2e6;
        padding: 0.5rem 0.75rem;
        margin: 0 0.25rem;
        border-radius: 6px;
        transition: all 0.2s ease;
    }
    
    .pagination .page-link:hover {
        background-color: #667eea;
        border-color: #667eea;
        color: white;
        transform: translateY(-1px);
    }
    
    .pagination .page-item.active .page-link {
        background-color: #667eea;
        border-color: #667eea;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .stats-cards .col-md-6 {
            margin-bottom: 1rem;
        }
        
        .search-section .row > div {
            margin-bottom: 0.5rem;
        }
        
        .action-buttons {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .action-buttons .btn {
            width: 100%;
        }
        
        .news-table {
            font-size: 0.875rem;
        }
        
        .modal-dialog {
            margin: 1rem;
        }
    }
    
    /* Loading Animation */
    .loading {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255,255,255,.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    /* Success/Error Messages */
    .alert {
        border-radius: 8px;
        border: none;
        padding: 1rem 1.5rem;
        margin-bottom: 1rem;
    }
    
    .alert-success {
        background-color: #d4edda;
        color: #155724;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
    }
    
    .alert-info {
        background-color: #d1ecf1;
        color: #0c5460;
    }
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <!-- Page Header -->
    <div class="page-header">
        <h4>Manajemen Berita & Artikel</h4>
        <p class="mb-0">Kelola semua berita dan artikel perusahaan</p>
    </div>

    <!-- Enhanced Statistics Cards -->
    <div class="row stats-cards">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card primary" data-aos="fade-up" data-aos-delay="100">
                <div class="card-icon">
                    <i class="fas fa-newspaper"></i>
                </div>
                <h3 class="mb-1" data-count="{{ $stats['total'] }}">{{ $stats['total'] }}</h3>
                <p class="text-white mb-0">Total Berita</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card success" data-aos="fade-up" data-aos-delay="200">
                <div class="card-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 class="mb-1" data-count="{{ $stats['published'] }}">{{ $stats['published'] }}</h3>
                <p class="text-white mb-0">Dipublikasi</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card warning pulse" data-aos="fade-up" data-aos-delay="300">
                <div class="card-icon">
                    <i class="fas fa-star"></i>
                </div>
                <h3 class="mb-1" data-count="{{ $stats['featured'] }}">{{ $stats['featured'] }}</h3>
                <p class="text-white mb-0">Berita Utama</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card danger" data-aos="fade-up" data-aos-delay="400">
                <div class="card-icon">
                    <i class="fas fa-edit"></i>
                </div>
                <h3 class="mb-1" data-count="{{ $stats['draft'] }}">{{ $stats['draft'] }}</h3>
                <p class="text-white mb-0">Draft</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content shadow p-3 rounded border">
        <!-- Content Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Manajemen Berita dan Artikel</h4>
            <button type="button" class="btn btn-primary btn-icon-text" data-bs-toggle="modal" data-bs-target="#addNewsModaladmin">
                <i class="mdi mdi-plus btn-icon-prepend"></i> Tambah Berita
            </button>
        </div>

        <!-- Search Section -->
        <div class="search-section mt-2">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" class="form-control" placeholder="Cari berita..." id="searchInput">
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="statusFilter">
                        <option value="">Semua Status</option>
                        <option value="published">Dipublikasi</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="categoryFilter">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-primary w-100" onclick="searchNews()">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                Klik gambar untuk melihat versi besarnya dalam tampilan detail.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>

            <table class="table news-table">
                <thead>
                    <tr>
                        <th width="80">Gambar</th>
                        <th>Judul</th>
                        <th width="120">Kategori</th>
                        <th width="100">Tanggal</th>
                        <th width="100">Status</th>
                        <th width="100">Utama</th>
                        <th width="200">Aksi</th>
                    </tr>
                </thead>
                <tbody id="newsTableBody">
                    @foreach($news as $item)
                    <tr id="news-row-{{ $item->id }}">
                        <td>
                            <img src="{{ asset($item->featured_image) }}" 
                                 alt="{{ $item->title }}" 
                                 class="news-image" 
                                 style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px; cursor: pointer;"
                                 data-bs-toggle="modal" 
                                 data-bs-target="#newsImageModal"
                                 onclick="showNewsImageModal('{{ asset($item->featured_image) }}', '{{ $item->title }}')">
                        </td>

                        <td>
                            <strong>{{ $item->title }}</strong>
                            <br>
                            <small class="text-muted">{{ Str::limit($item->excerpt, 60) }}</small>
                        </td>
                        <td>
                            <span class="badge bg-info">{{ $item->category->category_name }}</span>
                        </td>
                        <td>
                            <small>{{ \Carbon\Carbon::parse($item->published_at)->translatedFormat('j F Y') }}</small>
                        </td>
                        <td>
                            <span class="badge {{ $item->status === 'published' ? 'bg-success' : 'bg-warning' }}">
                                {{ $item->status === 'published' ? 'Dipublikasi' : 'Draft' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($item->is_featured)
                                    <span class="badge bg-warning me-2">
                                        <i class="fas fa-star"></i> Utama
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark me-2">
                                        <i class="far fa-star"></i> Biasa
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="action-buttons d-flex">
                                <!-- Toggle Featured Button -->
                                <button class="btn btn-sm me-1 {{ $item->is_featured ? 'btn-warning' : 'btn-outline-warning' }}" 
                                        onclick="toggleFeatured({{ $item->id }}, '{{ $item->title }}', {{ $item->is_featured ? 'true' : 'false' }})" 
                                        title="{{ $item->is_featured ? 'Hapus dari Utama' : 'Jadikan Utama' }}"
                                        id="toggle-btn-{{ $item->id }}">
                                    <i class="fas fa-star"></i>
                                </button>
                                
                                <!-- View Button -->
                                <button class="btn btn-info btn-sm me-1" onclick="viewNews({{ $item->id }})" title="Lihat">
                                    <i class="fas fa-eye"></i>
                                </button>
                                
                                <!-- Edit Button -->
                                <button class="btn btn-success btn-sm me-1" onclick="editNews({{ $item->id }})" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                <!-- Delete Button -->
                                <button class="btn btn-danger btn-sm" onclick="deleteNews({{ $item->id }})" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            <!-- Alert Modal for Confirmation -->
            <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmModalLabel">Konfirmasi</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="confirmModalBody">
                            <!-- Dynamic content will be inserted here -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn btn-primary" id="confirmButton">Ya, Lanjutkan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Gambar News -->
<div class="modal fade" id="newsImageModal" tabindex="-1" aria-labelledby="newsImageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="newsImageModalLabel">Gambar Berita</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body text-center">
        <img id="newsImageModalContent" src="" alt="Gambar" class="img-fluid rounded mb-2">
        <p id="newsImageTitle" class="fw-semibold"></p>
      </div>
    </div>
  </div>
</div>

<!-- Modal Tambah/Edit Berita -->
<div class="modal fade" id="addNewsModaladmin" tabindex="-1" aria-labelledby="addNewsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="newsForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="newsId" name="id">
                <div class="modal-header">
                    <h5 class="modal-title" id="addNewsModalLabel">Tambah Berita Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group mb-3">
                                <label for="newsTitle" class="form-label">Judul Berita *</label>
                                <input type="text" class="form-control" id="newsTitle" name="title" required>
                                <div class="form-text">Masukkan judul berita yang menarik dan deskriptif</div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="newsSlug" class="form-label">Slug URL (dibuat otomatis)</label>
                                <input type="text" class="form-control" id="newsSlug" name="slug" readonly>
                                <div class="form-text">URL slug akan otomatis dibuat dari judul</div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="newsExcerpt" class="form-label">Ringkasan/Excerpt</label>
                                <textarea class="form-control" id="newsExcerpt" name="excerpt" rows="3" maxlength="500"></textarea>
                                <div class="form-text">Ringkasan singkat untuk preview (maksimal 500 karakter)</div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="newsContentEditor" class="form-label">Konten Berita *</label>
                                <div id="newsContentEditor"></div>
                                <textarea id="newsContent" name="content" style="display: none;" ></textarea>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label for="newsCategory" class="form-label">Kategori *</label>
                                <select class="form-select" id="newsCategory" name="category_id" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="newsStatus" class="form-label">Status *</label>
                                <select class="form-select" id="newsStatus" name="status" required>
                                    <option value="draft">Draft</option>
                                    <option value="published">Published</option>
                                    <option value="archived">Archived</option>
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="publishDate" class="form-label">Tanggal Publikasi</label>
                                <input type="datetime-local" class="form-control" id="publishDate" name="published_at">
                            </div>

                            <div class="form-group mb-3">
                                <label for="newsThumbnail" class="form-label">Thumbnail</label>
                                <input type="file" class="form-control" id="newsThumbnail" name="featured_image" accept="image/*">
                                <div class="form-text">Format: JPG, PNG, GIF (Max: 2MB)</div>
                                <img id="thumbnailPreview" class="image-preview mt-2" style="display: none; max-width: 100%; height: auto;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="saveAsDraft()">Simpan sebagai Draft</button>
                    <button type="submit" class="btn btn-success">Publikasikan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add News Modal - error -->
<div class="modal fade" id="addNewsModal" tabindex="-1" aria-labelledby="addNewsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addNewsModalLabel">Tambah Berita Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addNewsForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Judul Berita</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Kategori</label>
                        <select class="form-select" id="category_id" name="category_id" required>
                            <option value="">Pilih Kategori</option>
                            <option value="1">Berita</option>
                            <option value="2">Artikel</option>
                            
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="excerpt" class="form-label">Ringkasan</label>
                        <textarea class="form-control" id="excerpt" name="excerpt" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="content" class="form-label">Konten</label>
                        <textarea class="form-control" id="contentadd" name="content" rows="8" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="featured_image" class="form-label">Gambar Utama</label>
                        <input type="file" class="form-control" id="featured_image" name="featured_image" accept="image/*">
                    </div>
                    
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="draft">Draft</option>
                            <option value="published">Dipublikasi</option>
                        </select>
                    </div>
                    <!--<div class="mb-3">-->
                    <!--    <label for="meta_description" class="form-label">Meta Description</label>-->
                    <!--    <textarea class="form-control" id="meta_description" name="meta_description" rows="2"></textarea>-->
                    <!--</div>-->
                    <!--<div class="mb-3">-->
                    <!--    <label for="tags" class="form-label">Tags (pisahkan dengan koma)</label>-->
                    <!--    <input type="text" class="form-control" id="tags" name="tags">-->
                    <!--</div>-->
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured">
                        <label class="form-check-label" for="is_featured">
                            Jadikan Berita Utama
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit News Modal -->
<div class="modal fade" id="editNewsModal" tabindex="-1" aria-labelledby="editNewsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editNewsModalLabel">Edit Berita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editNewsForm" enctype="multipart/form-data">
                <input type="hidden" id="edit_news_id" name="news_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_title" class="form-label">Judul Berita</label>
                        <input type="text" class="form-control" id="edit_title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_category_id" class="form-label">Kategori</label>
                        <select class="form-select" id="edit_category_id" name="category_id" required>
                            <option value="">Pilih Kategori</option>
                            <option value="3">Artikel</option>
                            <option value="2">Penghargaan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_excerpt" class="form-label">Ringkasan</label>
                        <textarea class="form-control" id="edit_excerpt" name="excerpt" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_content" class="form-label">Konten</label>
                        <textarea class="form-control" id="edit_content" name="content" rows="8" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_featured_image" class="form-label">Gambar Utama</label>
                        <input type="file" class="form-control" id="edit_featured_image" name="featured_image" accept="image/*">
                        <div id="current_image" class="mt-2"></div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status" class="form-label">Status</label>
                        <select class="form-select" id="edit_status" name="status" required>
                            <option value="draft">Draft</option>
                            <option value="published">Dipublikasi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_meta_description" class="form-label">Meta Description</label>
                        <textarea class="form-control" id="edit_meta_description" name="meta_description" rows="2" readonly></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_tags" class="form-label">Tags (pisahkan dengan koma)</label>
                        <input type="text" class="form-control" id="edit_tags" name="tags" readonly>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="edit_is_featured" name="is_featured">
                        <label class="form-check-label" for="edit_is_featured">
                            Jadikan Berita Utama
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View News Modal -->
<div class="modal fade" id="viewNewsModal" tabindex="-1" aria-labelledby="viewNewsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewNewsModalLabel">Detail Berita</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewNewsContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


<script>
    let editor;
    document.addEventListener("DOMContentLoaded", function () {
        ClassicEditor
            .create(document.querySelector('#newsContentEditor'))
            .then(newEditor => {
                editor = newEditor;
            })
            .catch(error => {
                console.error(error);
            });

        // Sebelum submit, salin isi editor ke textarea
        document.querySelector('#newsForm').addEventListener('submit', function (e) {
            document.querySelector('#newsContent').value = editor.getData();
        });
    });
</script>

<script>
    let editor;
    document.addEventListener("DOMContentLoaded", function () {
        ClassicEditor
            .create(document.querySelector('#newsContentEditor'))
            .then(newEditor => {
                editor = newEditor;
            })
            .catch(error => {
                console.error(error);
            });

        document.querySelector('#newsForm').addEventListener('submit', function (e) {
            e.preventDefault();
            document.querySelector('#newsContent').value = editor.getData();

            const formData = new FormData(this);

            fetch('/beritaartikeladd', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // ✅ Tutup modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addNewsModal'));
                    modal.hide();

                    // ✅ Reset form
                    document.getElementById('newsForm').reset();
                    editor.setData('');

                    // ✅ Tampilkan alert
                    alert(data.message);
                } else {
                    alert('Gagal menyimpan: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error(error);
                alert('Terjadi kesalahan saat menyimpan berita.');
            });
        });
    });
</script>

<script>
    function showNewsImageModal(imageUrl, title) {
        document.getElementById('newsImageModalContent').src = imageUrl;
        document.getElementById('newsImageTitle').textContent = title;
    }
</script>


<script>
    // CKEditor instances
    let addContentEditor = null;
    let editContentEditor = null;

    // Initialize CKEditor when Add Modal is shown
    document.getElementById('addNewsModal').addEventListener('shown.bs.modal', function() {
        if (!addContentEditor) {
            ClassicEditor
                .create(document.querySelector('#contentadd'), {
                    toolbar: [
                        'heading', '|',
                        'bold', 'italic', 'underline', 'strikethrough', '|',
                        'fontSize', 'fontColor', 'fontBackgroundColor', '|',
                        'alignment', '|',
                        'numberedList', 'bulletedList', 'todoList', '|',
                        'outdent', 'indent', '|',
                        'link', 'blockQuote', 'insertTable', '|',
                        'undo', 'redo', '|',
                        'sourceEditing'
                    ],
                    fontSize: {
                        options: [
                            9, 10, 11, 12, 13, 14, 15, 16, 18, 20, 22, 24, 26, 28, 30, 32, 34, 36
                        ]
                    },
                    heading: {
                        options: [
                            { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                            { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                            { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                            { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                            { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
                        ]
                    },
                    table: {
                        contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
                    }
                })
                .then(editor => {
                    addContentEditor = editor;
                })
                .catch(error => {
                    console.error('Error initializing CKEditor:', error);
                });
        }
    });

    // Initialize CKEditor when Edit Modal is shown
    document.getElementById('editNewsModal').addEventListener('shown.bs.modal', function() {
        if (!editContentEditor) {
            ClassicEditor
                .create(document.querySelector('#edit_content'), {
                    toolbar: [
                        'heading', '|',
                        'bold', 'italic', 'underline', 'strikethrough', '|',
                        'fontSize', 'fontColor', 'fontBackgroundColor', '|',
                        'alignment', '|',
                        'numberedList', 'bulletedList', 'todoList', '|',
                        'outdent', 'indent', '|',
                        'link', 'blockQuote', 'insertTable', '|',
                        'undo', 'redo', '|',
                        'sourceEditing'
                    ],
                    fontSize: {
                        options: [
                            9, 10, 11, 12, 13, 14, 15, 16, 18, 20, 22, 24, 26, 28, 30, 32, 34, 36
                        ]
                    },
                    heading: {
                        options: [
                            { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                            { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                            { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                            { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                            { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
                        ]
                    },
                    table: {
                        contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
                    }
                })
                .then(editor => {
                    editContentEditor = editor;
                })
                .catch(error => {
                    console.error('Error initializing CKEditor:', error);
                });
        }
    });

    // Cleanup CKEditor when Add Modal is hidden
    document.getElementById('addNewsModal').addEventListener('hidden.bs.modal', function() {
        if (addContentEditor) {
            addContentEditor.destroy();
            addContentEditor = null;
        }
        // Reset form
        document.getElementById('addNewsForm').reset();
    });

    // Cleanup CKEditor when Edit Modal is hidden
    document.getElementById('editNewsModal').addEventListener('hidden.bs.modal', function() {
        if (editContentEditor) {
            editContentEditor.destroy();
            editContentEditor = null;
        }
        // Reset form
        document.getElementById('editNewsForm').reset();
        document.getElementById('current_image').innerHTML = '';
    });
    
    // Get CSRF token
    function getCSRFToken() {
        const token = document.querySelector('meta[name="csrf-token"]') || 
                      document.querySelector('input[name="_token"]');
        return token ? token.getAttribute('content') || token.value : '';
    }
    
    // Show Alert/Notification
    function showAlert(message, type = 'info') {
        // Create alert element
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alertDiv);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentElement) {
                alertDiv.remove();
            }
        }, 5000);
    }
    
    // Toggle Featured News
    function toggleFeatured(newsId, newsTitle, currentStatus) {
        const action = currentStatus ? 'menghapus dari berita utama' : 'menjadikan berita utama';
        const actionText = currentStatus ? 'Berita ini akan dihapus dari berita utama.' : 'Berita ini akan dijadikan berita utama dan berita utama sebelumnya akan otomatis dihapus.';
        
        // Show confirmation modal
        document.getElementById('confirmModalLabel').textContent = 'Konfirmasi Perubahan Berita Utama';
        document.getElementById('confirmModalBody').innerHTML = `
            <div class="alert alert-info">
                <h6><i class="fas fa-info-circle"></i> Perhatian!</h6>
                <p class="mb-1"><strong>Berita:</strong> ${newsTitle}</p>
                <p class="mb-1"><strong>Aksi:</strong> ${action.charAt(0).toUpperCase() + action.slice(1)}</p>
                <p class="mb-0">${actionText}</p>
            </div>
            <p>Apakah Anda yakin ingin melanjutkan?</p>
        `;
        
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
        confirmModal.show();
        
        // Handle confirmation
        document.getElementById('confirmButton').onclick = function() {
            confirmModal.hide();
            performToggleFeatured(newsId, newsTitle, currentStatus);
        };
    }
    
    // Perform Toggle Featured
    function performToggleFeatured(newsId, newsTitle, currentStatus) {
        const toggleBtn = document.getElementById(`toggle-btn-${newsId}`);
        const originalHTML = toggleBtn.innerHTML;
        
        // Show loading state
        toggleBtn.disabled = true;
        toggleBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        
        // Gunakan route yang benar
        fetch(`/admin/berita-artikel/${newsId}/toggle-featured`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCSRFToken(),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                
                // Update button appearance
                updateFeaturedButton(newsId, data.is_featured);
                
                // Update badge in the table
                updateFeaturedBadge(newsId, data.is_featured);
                
                // If this news became featured, update other news buttons
                if (data.is_featured) {
                    updateOtherFeaturedButtons(newsId);
                }
                
            } else {
                showAlert('Terjadi kesalahan: ' + (data.message || 'Unknown error'), 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan: ' + error.message, 'danger');
        })
        .finally(() => {
            // Restore button state
            toggleBtn.disabled = false;
            toggleBtn.innerHTML = originalHTML;
        });
    }
    
    // Update Featured Button
    function updateFeaturedButton(newsId, isFeatured) {
        const button = document.getElementById(`toggle-btn-${newsId}`);
        if (isFeatured) {
            button.className = 'btn btn-sm me-1 btn-warning';
            button.title = 'Hapus dari Utama';
        } else {
            button.className = 'btn btn-sm me-1 btn-outline-warning';
            button.title = 'Jadikan Utama';
        }
        
        // Update onclick attribute
        const newsTitle = button.closest('tr').querySelector('strong').textContent;
        button.setAttribute('onclick', `toggleFeatured(${newsId}, '${newsTitle}', ${isFeatured})`);
    }
    
    // Update Featured Badge
    function updateFeaturedBadge(newsId, isFeatured) {
        const row = document.getElementById(`news-row-${newsId}`);
        const badgeCell = row.querySelector('td:nth-child(6) .d-flex');
        
        if (isFeatured) {
            badgeCell.innerHTML = `
                <span class="badge bg-warning me-2">
                    <i class="fas fa-star"></i> Utama
                </span>
            `;
        } else {
            badgeCell.innerHTML = `
                <span class="badge bg-light text-dark me-2">
                    <i class="far fa-star"></i> Biasa
                </span>
            `;
        }
    }
    
    // Update Other Featured Buttons
    function updateOtherFeaturedButtons(currentNewsId) {
        document.querySelectorAll('[id^="toggle-btn-"]').forEach(button => {
            const buttonNewsId = button.id.replace('toggle-btn-', '');
            if (buttonNewsId !== currentNewsId.toString()) {
                updateFeaturedButton(buttonNewsId, false);
                updateFeaturedBadge(buttonNewsId, false);
            }
        });
    }
    
    // Add News
    document.getElementById('addNewsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitButton = this.querySelector('button[type="submit"]');
        
        // Check if is_featured is checked
        const isFeatured = document.getElementById('is_featured').checked;
        
        if (isFeatured) {
            // Show confirmation for featured news
            const existingFeatured = document.querySelector('.badge.bg-warning');
            if (existingFeatured) {
                if (!confirm('Berita ini akan dijadikan berita utama dan berita utama sebelumnya akan otomatis dihapus. Lanjutkan?')) {
                    return;
                }
            }
        }
        
        // Disable submit button
        submitButton.disabled = true;
        submitButton.textContent = 'Menyimpan...';
        
        // Gunakan route yang benar
        fetch('/admin/berita-artikel', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': getCSRFToken(),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('addNewsModal'));
                if (modal) {
                    modal.hide();
                }
                
                // Reset form
                document.getElementById('addNewsForm').reset();
                
                // Reload page after short delay
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showAlert('Terjadi kesalahan: ' + (data.message || 'Unknown error'), 'danger');
                console.error('Server error:', data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan: ' + error.message, 'danger');
        })
        .finally(() => {
            // Re-enable submit button
            submitButton.disabled = false;
            submitButton.textContent = 'Simpan';
        });
    });
    
    // View News
    function viewNews(id) {
        fetch(`/admin/berita-artikel/${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const news = data.data;
                const content = `
                    <div class="row">
                        <div class="col-md-4">
                            <img src="${news.featured_image || '/placeholder.jpg'}" alt="${news.title}" class="img-fluid rounded">
                        </div>
                        <div class="col-md-8">
                            <h5>${news.title}</h5>
                            <p><span class="badge bg-info">${news.category.category_name}</span></p>
                            <p><strong>Status:</strong> <span class="badge ${news.status === 'published' ? 'bg-success' : 'bg-warning'}">${news.status === 'published' ? 'Dipublikasi' : 'Draft'}</span></p>
                            <p><strong>Berita Utama:</strong> <span class="badge ${news.is_featured ? 'bg-warning' : 'bg-light text-dark'}">${news.is_featured ? 'Ya' : 'Tidak'}</span></p>
                            <p><strong>Penulis:</strong> ${news.author.name}</p>
                            <p><strong>Tanggal:</strong> ${new Date(news.created_at).toLocaleDateString('id-ID')}</p>
                            <p><strong>Ringkasan:</strong> ${news.excerpt}</p>
                            <div><strong>Konten:</strong></div>
                            <div class="mt-2">${news.content}</div>
                        </div>
                    </div>
                `;
                document.getElementById('viewNewsContent').innerHTML = content;
                new bootstrap.Modal(document.getElementById('viewNewsModal')).show();
            } else {
                showAlert('Terjadi kesalahan: ' + (data.message || 'Unknown error'), 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan: ' + error.message, 'danger');
        });
    }
    
    // Edit News
    function editNews(id) {
        fetch(`/admin/berita-artikel/${id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const news = data.data;
                document.getElementById('edit_news_id').value = news.id;
                document.getElementById('edit_title').value = news.title;
                document.getElementById('edit_category_id').value = news.category_id;
                document.getElementById('edit_excerpt').value = news.excerpt;
                document.getElementById('edit_content').value = news.content;
                document.getElementById('edit_status').value = news.status;
                document.getElementById('edit_meta_description').value = news.meta_description || '';
                document.getElementById('edit_tags').value = news.tags || '';
                document.getElementById('edit_is_featured').checked = news.is_featured;
                
                // Show current image
                if (news.featured_image) {
                    document.getElementById('current_image').innerHTML = `
                        <small class="text-muted">Gambar saat ini:</small><br>
                        <img src="${news.featured_image}" alt="Current Image" style="width: 100px; height: 60px; object-fit: cover;" class="rounded">
                    `;
                } else {
                    document.getElementById('current_image').innerHTML = `
                        <small class="text-muted">Tidak ada gambar</small>
                    `;
                }
                
                new bootstrap.Modal(document.getElementById('editNewsModal')).show();
            } else {
                showAlert('Terjadi kesalahan: ' + (data.message || 'Unknown error'), 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan: ' + error.message, 'danger');
        });
    }
    
    // Update News
    document.getElementById('editNewsForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const newsId = document.getElementById('edit_news_id').value;
        const isFeatured = document.getElementById('edit_is_featured').checked;
        const submitButton = this.querySelector('button[type="submit"]');
        
        if (isFeatured) {
            // Show confirmation for featured news
            const existingFeatured = document.querySelector('.badge.bg-warning');
            if (existingFeatured && !existingFeatured.closest('tr').id.includes(newsId)) {
                if (!confirm('Berita ini akan dijadikan berita utama dan berita utama sebelumnya akan otomatis dihapus. Lanjutkan?')) {
                    return;
                }
            }
        }
        
        // Disable submit button
        submitButton.disabled = true;
        submitButton.textContent = 'Memperbarui...';
        
        // Gunakan route yang benar
        fetch(`/admin/berita-artikel/${newsId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': getCSRFToken(),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('editNewsModal'));
                if (modal) {
                    modal.hide();
                }
                
                // Reload page after short delay
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showAlert('Terjadi kesalahan: ' + (data.message || 'Unknown error'), 'danger');
                console.error('Server error:', data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Terjadi kesalahan: ' + error.message, 'danger');
        })
        .finally(() => {
            // Re-enable submit button
            submitButton.disabled = false;
            submitButton.textContent = 'Update';
        });
    });
    
    // Delete News
    function deleteNews(id) {
        if (confirm('Apakah Anda yakin ingin menghapus berita ini?')) {
            fetch(`/admin/berita-artikel/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': getCSRFToken(),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    document.getElementById(`news-row-${id}`).remove();
                } else {
                    showAlert('Terjadi kesalahan: ' + (data.message || 'Unknown error'), 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Terjadi kesalahan: ' + error.message, 'danger');
            });
        }
    }
</script>
@endsection