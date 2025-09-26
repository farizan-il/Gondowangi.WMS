@extends('Gondowangi.Admin.Layout.main')

@section('head')
<style>
    .brand-card {
        border-radius: 10px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .brand-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    .brand-img {
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
    }
    .type-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 15px;
    }
    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .btn-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
    }
    .btn-gradient:hover {
        background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        color: white;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }
    .image-preview {
        max-width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 8px;
        margin-top: 10px;
    }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- CSS CDN Font Awesome -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.0/css/all.min.css">

@endsection

@section('content')
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="mb-1">Brand Detail Management</h3>
                                    <p class="text-muted mb-0">Kelola gambar dan detail untuk semua brand</p>
                                </div>
                                <button type="button" class="btn btn-gradient btn-lg" data-bs-toggle="modal" data-bs-target="#addModal">
                                    <i class="fas fa-plus mr-2"></i>Tambah Brand Detail
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <select class="form-control" id="filterType">
                                        <option value="">Semua Type</option>
                                        <option value="carousel_brand">Carousel</option>
                                        <option value="banner_brand">Banner</option>
                                        <option value="detail_brand">Detail</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-control" id="filterBrand">
                                        <option value="">Semua Brand</option>
                                        <option value="Natur">Natur</option>
                                        <option value="Mizzu">Mizzu</option>
                                        <option value="Azalea">Azalea</option>
                                        <option value="Hgforman">Hgforman</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-secondary" id="resetFilter">
                                        <i class="fas fa-undo mr-2"></i>Reset Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row" id="brandContainer">
                @forelse($brandDetails as $detail)
                <div class="col-md-4 mb-4 brand-item" 
                     data-type="{{ $detail->type }}" 
                     data-brand="{{ $detail->brand_name }}">
                    <div class="card brand-card h-100 border">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <span class="badge badge-primary type-badge">
                                        {{ ucfirst(str_replace('_', ' ', str_replace('_brand', '', $detail->type))) }}
                                    </span>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item edit-btn" href="#" data-bs-id="{{ $detail->id }}">
                                            <i class="fas fa-edit mr-2"></i>Edit
                                        </a>
                                        <a class="dropdown-item delete-btn text-danger" href="#" data-bs-id="{{ $detail->id }}">
                                            <i class="fas fa-trash mr-2"></i>Hapus
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            @if($detail->img)
                                <img src="{{ asset( $detail->img) }}" class="brand-img w-100 mb-3" alt="Brand Image">
                            @endif
                            
                            <h5 class="card-title">{{ $detail->title }}</h5>
                            
                            @if($detail->deksripsi)
                                <p class="card-text text-muted">{{ Str::limit(strip_tags($detail->deksripsi), 100) }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="empty-state">
                        <i class="fas fa-image fa-3x mb-3"></i>
                        <h4>Belum ada brand detail</h4>
                        <p>Klik tombol "Tambah Brand Detail" untuk menambahkan konten baru</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </section>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus mr-2"></i>Tambah Brand Detail
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="addForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Type *</label>
                                <select class="form-control" name="type" required>
                                    <option value="">Pilih Type</option>
                                    <option value="carousel_brand">Carousel Brand</option>
                                    <option value="banner_brand">Banner Brand</option>
                                    <option value="detail_brand">Detail Brand</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Brand Name *</label>
                                <select class="form-control" name="brand_name" required>
                                    <option value="">Pilih Brand</option>
                                    <option value="Natur">Natur</option>
                                    <option value="Mizzu">Mizzu</option>
                                    <option value="Azalea">Azalea</option>
                                    <option value="Hgforman">Hgforman</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Gambar *</label>
                        <input type="file" class="form-control-file" name="img" accept="image/*" required>
                        <small class="form-text text-muted">Format: JPG, PNG, GIF. Max: 2MB</small>
                        <div id="addImagePreview" class="mt-2" style="display: none;">
                            <img id="addPreviewImg" class="image-preview" alt="Preview">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Judul</label>
                        <input name="title" class="form-control"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <!--<textarea class="form-control" name="deksripsi" rows="4" placeholder="Masukkan deskripsi..."></textarea>-->
                        <textarea name="deksripsi" id="addDeksripsi" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-gradient">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit mr-2"></i>Edit Brand Detail
                </h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="editId" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Type *</label>
                                <select class="form-control" id="editType" name="type" required>
                                    <option value="">Pilih Type</option>
                                    <option value="carousel_brand">Carousel Brand</option>
                                    <option value="banner_brand">Banner Brand</option>
                                    <option value="detail_brand">Detail Brand</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Brand Name *</label>
                                <select class="form-control" id="editBrandName" name="brand_name" required>
                                    <option value="">Pilih Brand</option>
                                    <option value="Natur">Natur</option>
                                    <option value="Mizzu">Mizzu</option>
                                    <option value="Azalea">Azalea</option>
                                    <option value="Hgforman">Hgforman</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Gambar</label>
                        <input type="file" class="form-control-file" name="img" accept="image/*">
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah gambar</small>
                        <div id="currentImagePreview" class="mt-2">
                            <img id="currentImg" class="image-preview" alt="Current Image">
                        </div>
                        <div id="editImagePreview" class="mt-2" style="display: none;">
                            <img id="editPreviewImg" class="image-preview" alt="Preview">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Judul</label>
                        <input class="form-control"  id="editTitle" name="title" rows="4"></input>
                    </div>
                    
                    <div class="form-group">
                        <label>Deskripsi</label>
                        <textarea class="form-control" id="editDeksripsi" name="deksripsi" rows="4"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-gradient">
                        <i class="fas fa-save mr-2"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
<!-- Tambahkan di head atau sebelum closing body tag -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/ckeditor5/41.4.2/ckeditor.min.js"></script>

<script>
    // Inisialisasi CKEditor 5 untuk form add
    ClassicEditor
        .create(document.querySelector('#addDeksripsi'), {
            toolbar: ['bold', 'italic', 'underline', '|', 'numberedList', 'bulletedList', '|', 'link', 'undo', 'redo']
        })
        .then(editor => {
            window.addEditor = editor;
        })
        .catch(error => {
            console.error(error);
        });
</script>

<script>
    $(document).ready(function() {
        // Image preview for add modal
        $('input[name="img"]').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if ($(e.target).closest('#addModal').length) {
                        $('#addPreviewImg').attr('src', e.target.result);
                        $('#addImagePreview').show();
                    } else {
                        $('#editPreviewImg').attr('src', e.target.result);
                        $('#editImagePreview').show();
                        $('#currentImagePreview').hide();
                    }
                }
                reader.readAsDataURL(file);
            }
        });
    
        // Filter functionality
        $('#filterType, #filterBrand').on('change', function() {
            filterBrands();
        });
    
        $('#resetFilter').on('click', function() {
            $('#filterType, #filterBrand').val('');
            $('.brand-item').show();
        });
    
        function filterBrands() {
            const typeFilter = $('#filterType').val();
            const brandFilter = $('#filterBrand').val();
    
            $('.brand-item').each(function() {
                const itemType = $(this).data('type');
                const itemBrand = $(this).data('brand');
                
                let showItem = true;
                
                if (typeFilter && itemType !== typeFilter) {
                    showItem = false;
                }
                
                if (brandFilter && itemBrand !== brandFilter) {
                    showItem = false;
                }
                
                if (showItem) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
    
        // Add form submission
        $('#addForm').on('submit', function(e) {
            e.preventDefault();
            
            // Tampilkan loading
            Swal.fire({
                title: 'Menyimpan data...',
                text: 'Mohon tunggu sebentar',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            const formData = new FormData(this);
            
            $.ajax({
                url: '{{ route("admin.naturadmin.store") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#addModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Terjadi kesalahan';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMsg
                    });
                }
            });
        });
    
        // Edit button click - DIPERBAIKI: menggunakan data-bs-id
        $(document).on('click', '.edit-btn', function(e) {
            e.preventDefault();
            const id = $(this).data('bs-id'); // Menggunakan data-bs-id sesuai HTML
            
            $.ajax({
                url: '{{ route("admin.naturadmin.show", ":id") }}'.replace(':id', id),
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        $('#editId').val(data.id);
                        $('#editType').val(data.type);
                        $('#editBrandName').val(data.brand_name);
                        $('#editTitle').val(data.title);
                        $('#editDeksripsi').val(data.deksripsi);
                        
                        if (data.img) {
                            $('#currentImg').attr('src', '{{ asset("") }}/' + data.img);
                            
                            $('#currentImagePreview').show();
                        } else {
                            $('#currentImagePreview').hide();
                        }
                        
                        $('#editImagePreview').hide();
                        $('#editModal').modal('show');
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Gagal mengambil data'
                    });
                }
            });
        });
    
        // Edit form submission - DIPERBAIKI: menambahkan _method untuk PUT
        $('#editForm').on('submit', function(e) {
            e.preventDefault();
            
            const id = $('#editId').val();
            const formData = new FormData(this);
            formData.append('_method', 'PUT'); // Menambahkan method PUT
            
            $.ajax({
                url: '{{ route("admin.naturadmin.update", ":id") }}'.replace(':id', id),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#editModal').modal('hide');
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Terjadi kesalahan';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMsg
                    });
                }
            });
        });
    
        // Delete button click - DIPERBAIKI: menggunakan event delegation dan data-bs-id
        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            const id = $(this).data('bs-id'); // Menggunakan data-bs-id sesuai HTML
            
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data yang dihapus tidak dapat dikembalikan!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route("admin.naturadmin.destroy", ":id") }}'.replace(':id', id),
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: response.message || 'Terjadi kesalahan saat menghapus.'
                                });
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'Gagal menghapus data.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.statusText) {
                                errorMessage = xhr.status + ' - ' + xhr.statusText;
                            }
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: errorMessage
                            });
                        }
                    });
                }
            });
        });
    
        // Reset form when modal is closed
        $('#addModal').on('hidden.bs.modal', function() {
            $('#addForm')[0].reset();
            $('#addImagePreview').hide();
        });
    
        $('#editModal').on('hidden.bs.modal', function() {
            $('#editForm')[0].reset();
            $('#editImagePreview').hide();
        });
    });
</script>
@endsection