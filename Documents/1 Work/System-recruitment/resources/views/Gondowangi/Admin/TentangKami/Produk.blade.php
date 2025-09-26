@extends('Gondowangi.Admin.Layout.main')

@section('head')
<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- Dropzone CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css">
<style>
    .product-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }
    .badge-status {
        font-size: 12px;
        padding: 6px 12px;
    }
    .btn-action {
        margin: 2px;
        padding: 6px 12px;
        font-size: 12px;
    }
    .modal-lg {
        max-width: 900px;
    }
    .product-gallery img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        margin: 5px;
        border-radius: 8px;
        cursor: pointer;
    }
    .feature-item {
        background: #f8f9fa;
        padding: 10px;
        margin: 5px 0;
        border-radius: 5px;
        border-left: 3px solid #28a745;
    }
    .price-display {
        font-size: 1.2em;
        font-weight: bold;
        color: #28a745;
    }
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="row">
        <div class="col-md-12 grid-margin">
            <div class="row">
                <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                    <h3 class="font-weight-bold">Manajemen Produk Berbahan Alami</h3>
                    <h6 class="font-weight-normal mb-0">Kelola produk kecantikan dan kesehatan alami Anda</h6>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title mb-0">Total Produk</h4>
                            <h2 class="text-primary font-weight-bold">12</h2>
                        </div>
                        <div class="bg-primary-light">
                            <i class="mdi mdi-package-variant icon-lg text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title mb-0">Produk Aktif</h4>
                            <h2 class="text-success font-weight-bold">10</h2>
                        </div>
                        <div class="bg-success-light">
                            <i class="mdi mdi-check-circle icon-lg text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title mb-0">Kategori</h4>
                            <h2 class="text-info font-weight-bold">5</h2>
                        </div>
                        <div class="bg-info-light">
                            <i class="mdi mdi-tag-multiple icon-lg text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title mb-0">Stok Rendah</h4>
                            <h2 class="text-warning font-weight-bold">3</h2>
                        </div>
                        <div class="bg-warning-light">
                            <i class="mdi mdi-alert icon-lg text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">Daftar Produk</h4>
                        <button type="button" class="btn btn-success btn-rounded" data-toggle="modal" data-target="#addProductModal">
                            <i class="mdi mdi-plus"></i> Tambah Produk
                        </button>
                    </div>

                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-control" id="categoryFilter">
                                <option value="">Semua Kategori</option>
                                <option value="makeup">Makeup Dekoratif</option>
                                <option value="skincare">Perawatan Kulit</option>
                                <option value="bodycare">Perawatan Tubuh</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="statusFilter">
                                <option value="">Semua Status</option>
                                <option value="active">Aktif</option>
                                <option value="inactive">Tidak Aktif</option>
                                <option value="draft">Draft</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Cari produk..." id="searchProduct">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products Table -->
                    <div class="table-responsive">
                        <table class="table table-striped" id="productTable">
                            <thead>
                                <tr>
                                    <th>Gambar</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Status</th>
                                    <th>Tanggal Dibuat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Perfect Wear -->
                                <tr>
                                    <td>
                                        <img src="https://via.placeholder.com/60x60/28a745/ffffff?text=PW" alt="Perfect Wear" class="product-image">
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-0">Perfect Wear</h6>
                                            <small class="text-muted">Koleksi makeup dekoratif tahan lama</small>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-info">Makeup Dekoratif</span></td>
                                    <td class="price-display">Rp 50.000</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-success mr-2">25</span>
                                            <small class="text-muted">unit</small>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-success badge-status">Aktif</span></td>
                                    <td>15 Jun 2025</td>
                                    <td>
                                        <button class="btn btn-outline-primary btn-action" onclick="showProduct(1)">
                                            <i class="mdi mdi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-warning btn-action" onclick="editProduct(1)">
                                            <i class="mdi mdi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-action" onclick="deleteProduct(1)">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Beautiful Youth -->
                                <tr>
                                    <td>
                                        <img src="https://via.placeholder.com/60x60/e91e63/ffffff?text=BY" alt="Beautiful Youth" class="product-image">
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-0">Beautiful Youth</h6>
                                            <small class="text-muted">It's your true color</small>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-warning">Perawatan Kulit</span></td>
                                    <td class="price-display">Rp 75.000</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-warning mr-2">8</span>
                                            <small class="text-muted">unit</small>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-success badge-status">Aktif</span></td>
                                    <td>14 Jun 2025</td>
                                    <td>
                                        <button class="btn btn-outline-primary btn-action" onclick="showProduct(2)">
                                            <i class="mdi mdi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-warning btn-action" onclick="editProduct(2)">
                                            <i class="mdi mdi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-action" onclick="deleteProduct(2)">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Natur Beauty -->
                                <tr>
                                    <td>
                                        <img src="https://via.placeholder.com/60x60/2196f3/ffffff?text=NB" alt="Natur Beauty" class="product-image">
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-0">Natur Beauty</h6>
                                            <small class="text-muted">Chief Executive Operations</small>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-secondary">Perawatan Tubuh</span></td>
                                    <td class="price-display">Rp 95.000</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="badge badge-success mr-2">15</span>
                                            <small class="text-muted">unit</small>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-warning badge-status">Draft</span></td>
                                    <td>13 Jun 2025</td>
                                    <td>
                                        <button class="btn btn-outline-primary btn-action" onclick="showProduct(3)">
                                            <i class="mdi mdi-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-warning btn-action" onclick="editProduct(3)">
                                            <i class="mdi mdi-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-action" onclick="deleteProduct(3)">
                                            <i class="mdi mdi-delete"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Tambah Produk Baru</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="productForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Basic Information -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Informasi Dasar</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Nama Produk *</label>
                                        <input type="text" class="form-control" name="name" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Tagline</label>
                                        <input type="text" class="form-control" name="tagline" placeholder="Contoh: It's your true color">
                                    </div>
                                    <div class="form-group">
                                        <label>Deskripsi</label>
                                        <textarea class="form-control" name="description" rows="4" placeholder="Deskripsi lengkap produk..."></textarea>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Kategori *</label>
                                                <select class="form-control" name="category" required>
                                                    <option value="">Pilih Kategori</option>
                                                    <option value="makeup">Makeup Dekoratif</option>
                                                    <option value="skincare">Perawatan Kulit</option>
                                                    <option value="bodycare">Perawatan Tubuh</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Status *</label>
                                                <select class="form-control" name="status" required>
                                                    <option value="active">Aktif</option>
                                                    <option value="inactive">Tidak Aktif</option>
                                                    <option value="draft">Draft</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Features -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Fitur Produk</h6>
                                </div>
                                <div class="card-body">
                                    <div id="featuresContainer">
                                        <div class="feature-input-group mb-2">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="features[]" placeholder="Contoh: Foundation tahan lama dengan perlindungan kulit">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-success" type="button" onclick="addFeature()">
                                                        <i class="mdi mdi-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Ingredients -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Bahan Alami</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Bahan Utama</label>
                                        <input type="text" class="form-control" name="main_ingredients" placeholder="Contoh: Ekstrak lidah buaya, minyak kelapa, vitamin E">
                                    </div>
                                    <div class="form-group">
                                        <label>Manfaat Bahan</label>
                                        <textarea class="form-control" name="ingredient_benefits" rows="3" placeholder="Jelaskan manfaat dari bahan-bahan alami yang digunakan..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Pricing & Stock -->
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0">Harga & Stok</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Harga (Rp) *</label>
                                        <input type="number" class="form-control" name="price" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Harga Diskon (Rp)</label>
                                        <input type="number" class="form-control" name="discount_price">
                                    </div>
                                    <div class="form-group">
                                        <label>Stok *</label>
                                        <input type="number" class="form-control" name="stock" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Minimum Stok Alert</label>
                                        <input type="number" class="form-control" name="min_stock" value="5">
                                    </div>
                                </div>
                            </div>

                            <!-- Images -->
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0">Gambar Produk</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Gambar Utama *</label>
                                        <input type="file" class="form-control-file" name="main_image" accept="image/*">
                                    </div>
                                    <div class="form-group">
                                        <label>Gambar Tambahan</label>
                                        <input type="file" class="form-control-file" name="gallery_images[]" accept="image/*" multiple>
                                        <small class="text-muted">Pilih beberapa gambar untuk galeri</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batalkan</button>
                    <button type="submit" class="btn btn-success">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Product Modal -->
<div class="modal fade" id="viewProductModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Produk</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="productDetails">
                <!-- Product details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-warning" onclick="editProductFromView()">Edit Produk</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    let currentProductId = null;
    
    // Initialize DataTable
    $(document).ready(function() {
        $('#productTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/Indonesian.json'
            }
        });
    });
    
    // Add feature input
    function addFeature() {
        const container = document.getElementById('featuresContainer');
        const newFeature = document.createElement('div');
        newFeature.className = 'feature-input-group mb-2';
        newFeature.innerHTML = `
            <div class="input-group">
                <input type="text" class="form-control" name="features[]" placeholder="Fitur produk...">
                <div class="input-group-append">
                    <button class="btn btn-outline-danger" type="button" onclick="removeFeature(this)">
                        <i class="mdi mdi-minus"></i>
                    </button>
                </div>
            </div>
        `;
        container.appendChild(newFeature);
    }
    
    // Remove feature input
    function removeFeature(button) {
        button.closest('.feature-input-group').remove();
    }
    
    // Show product details
    function showProduct(id) {
        // Sample data - replace with actual AJAX call
        const sampleData = {
            1: {
                name: 'Perfect Wear',
                tagline: 'Make-up dekoratif',
                description: 'Koleksi makeup dekoratif Mizzu yang dirancang untuk memberikan tampilan sempurna yang tahan lama. Terbuat dari bahan-bahan alami yang aman untuk kulit dan ramah lingkungan.',
                price: 50000,
                features: [
                    'Perfect Wear - Foundation tahan lama dengan perlindungan kulit',
                    'Pro Liner - Eyeliner presisi tinggi dengan aplikasi mudah',
                    'Hide\'em Concealer Orange - Concealer untuk menutupi lingkaran hitam',
                    'Airblush - Perona pipi alami dengan efek matte yang segar'
                ],
                main_image: 'https://via.placeholder.com/300x300/28a745/ffffff?text=Perfect+Wear'
            }
        };
    
        const product = sampleData[id];
        if (product) {
            document.getElementById('productDetails').innerHTML = `
                <div class="row">
                    <div class="col-md-4">
                        <img src="${product.main_image}" class="img-fluid rounded" alt="${product.name}">
                    </div>
                    <div class="col-md-8">
                        <h4>${product.name}</h4>
                        <p class="text-muted">${product.tagline}</p>
                        <p>${product.description}</p>
                        <div class="price-display mb-3">Rp ${product.price.toLocaleString('id-ID')}</div>
                        <h6>Fitur Produk:</h6>
                        ${product.features.map(feature => `<div class="feature-item">${feature}</div>`).join('')}
                    </div>
                </div>
            `;
            $('#viewProductModal').modal('show');
        }
    }
    
    // Edit product
    function editProduct(id) {
        // Set current product ID for editing
        currentProductId = id;
        
        // Change modal title
        document.getElementById('modalTitle').textContent = 'Edit Produk';
        
        // Load product data (sample data - replace with actual AJAX call)
        if (id === 1) {
            document.querySelector('input[name="name"]').value = 'Perfect Wear';
            document.querySelector('input[name="tagline"]').value = 'Make-up dekoratif';
            document.querySelector('textarea[name="description"]').value = 'Koleksi makeup dekoratif Mizzu yang dirancang untuk memberikan tampilan sempurna yang tahan lama.';
            document.querySelector('select[name="category"]').value = 'makeup';
            document.querySelector('input[name="price"]').value = '50000';
            document.querySelector('input[name="stock"]').value = '25';
        }
        
        $('#addProductModal').modal('show');
    }
    
    // Edit from view modal
    function editProductFromView() {
        $('#viewProductModal').modal('hide');
        setTimeout(() => {
            editProduct(currentProductId);
        }, 300);
    }
    
    // Delete product
    function deleteProduct(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data produk yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Perform delete operation here
                Swal.fire(
                    'Terhapus!',
                    'Produk berhasil dihapus.',
                    'success'
                );
                // Reload table or remove row from DOM
            }
        });
    }
    
    // Handle form submission
    document.getElementById('productForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading
        Swal.fire({
            title: 'Menyimpan...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Simulate API call
        setTimeout(() => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: currentProductId ? 'Produk berhasil diupdate' : 'Produk berhasil ditambahkan',
                timer: 2000,
                showConfirmButton: false
            });
            
            $('#addProductModal').modal('hide');
            
            // Reset form and current product ID
            this.reset();
            currentProductId = null;
            document.getElementById('modalTitle').textContent = 'Tambah Produk Baru';
            
            // Reload table data here
        }, 1500);
    });
    
    // Reset modal when closed
    $('#addProductModal').on('hidden.bs.modal', function() {
        document.getElementById('productForm').reset();
        currentProductId = null;
        document.getElementById('modalTitle').textContent = 'Tambah Produk Baru';
        
        // Reset features container
        const container = document.getElementById('featuresContainer');
        container.innerHTML = `
            <div class="feature-input-group mb-2">
                <div class="input-group">
                    <input type="text" class="form-control" name="features[]" placeholder="Contoh: Foundation tahan lama dengan perlindungan kulit">
                    <div class="input-group-append">
                        <button class="btn btn-outline-success" type="button" onclick="addFeature()">
                            <i class="mdi mdi-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
</script>
    
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection