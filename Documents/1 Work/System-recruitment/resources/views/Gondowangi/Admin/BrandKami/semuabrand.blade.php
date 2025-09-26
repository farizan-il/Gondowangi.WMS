@extends('Gondowangi.Admin.Layout.main')

@section('head')
<link rel="stylesheet" href="{{ asset('admin/assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('admin/assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

<style>
    .preview-image {
        max-width: 100px;
        max-height: 60px;
        object-fit: cover;
        border-radius: 4px;
    }
    .badge {
        font-size: 0.75em;
    }
    .cursor-pointer {
        cursor: pointer;
    }
    
    .brand-logo:hover,
    .brand-image:hover {
        transform: scale(1.1);
        transition: transform 0.2s ease-in-out;
    }
    
    .table-shadow {
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border shadow">
                <div class="card-header d-flex justify-content-between">
                    <div class="alert  alert-dismissible fade show" role="alert">
                        <strong>Table Kelola Carousel Banner</strong>
                    </div>
                    <div class="card-tools">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addBannerModal">
                            <i class="fas fa-plus"></i> Tambah Banner
                        </button>
                    </div>
                </div>
                
                <div class="alert alert-info alert-dismissible fade show " role="alert">
                    Klik gambar untuk melihat versi besarnya dalam tampilan detail.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
                
                <div class="card-body table-shadow">
                    <div class="table-responsive">
                        <table id="bannerTable" class="table table-shadow">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Gambar</th>
                                    <th>Judul</th>
                                    <th>Subtitle</th>
                                    <th>Button</th>
                                    <th>Urutan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($banners as $key => $banner)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        <img src="{{ asset($banner->image_path) }}" 
                                             alt="{{ $banner->image_path }}" 
                                             class="preview-image rounded" 
                                             style="cursor:pointer; max-width: 100px;"
                                             data-bs-toggle="modal" 
                                             data-bs-target="#imageModal" 
                                             onclick="showImageInModal('{{ asset($banner->image_path) }}')">
                                    </td>
                                    <td>{{ $banner->title ?? '-' }}</td>
                                    <td>{{ $banner->subtitle ?? '-' }}</td>
                                    <td>
                                        @if($banner->button_text)
                                            <span class="badge badge-info">{{ $banner->button_text }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <span class="text-info"><strong>{{ $banner->sort_order }}</strong></span>
                                    </td>
                                    <td>
                                        @if($banner->is_active)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="">
                                            <button type="button" class="btn btn-sm btn-outline-warning" 
                                                    onclick="editBanner({{ $banner }})" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editBannerModal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <form action="{{ route('admin.semuabrandadmin.toggle-status', $banner) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="btn btn-sm {{ $banner->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}"
                                                        title="{{ $banner->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                    <i class="fas {{ $banner->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                                </button>
                                            </form>
                                            
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="deleteBanner({{ $banner->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!--table untuk carousel brand produk-->
<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <!--@if(session('success'))-->
            <!--    <div class="alert alert-success alert-dismissible fade show" role="alert">-->
            <!--        {{ session('success') }}-->
            <!--        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>-->
            <!--    </div>-->
            <!--@endif-->
            
            <!--@if($errors->any())-->
            <!--    <div class="alert alert-danger alert-dismissible fade show" role="alert">-->
            <!--        <ul class="mb-0">-->
            <!--            @foreach($errors->all() as $error)-->
            <!--                <li>{{ $error }}</li>-->
            <!--            @endforeach-->
            <!--        </ul>-->
            <!--        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>-->
            <!--    </div>-->
            <!--@endif-->
            
            <div class="card border shadow">
                <div class="card-header d-flex justify-content-between">
                    <div class="alert  alert-dismissible fade show" role="alert">
                        <strong>Table Kelola Carousel Brand</strong>
                    </div>
                    <div class="card-tools">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addBrandModal">
                            <i class="fas fa-plus"></i> Tambah Carousel Brand
                        </button>
                    </div>
                </div>
                
                <div class="card-body table-shadow">
                    <div class="table-responsive">
                        <!--table untuk foto brand -->
                        <table id="bannerTable" class="table table-shadow">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Gambar</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($brandCarousel as $key => $banner)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <img src="{{ asset($banner->image_path) }}" 
                                                 alt="{{ $banner->image_path }}" 
                                                 class="preview-image rounded" 
                                                 style="cursor:pointer; max-width: 100px;" 
                                                 data-bs-toggle="modal" 
                                                 data-bs-target="#imageModal" 
                                                 onclick="showImageInModal('{{ asset($banner->image_path) }}')">
                                        </td>
                                        <td>
                                            @if($banner->is_active)
                                                <span class="badge badge-success">Aktif</span>
                                            @else
                                                <span class="badge badge-danger">Nonaktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-warning" 
                                                        onclick="editBanner({{ $banner }})" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editBannerModal">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                
                                                <form action="{{ route('admin.semuabrandadmin.toggle-status', $banner) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" 
                                                            class="btn btn-sm {{ $banner->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}" 
                                                            title="{{ $banner->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                        <i class="fas {{ $banner->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                                    </button>
                                                </form>
                                                
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteBanner({{ $banner->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Belum ada data carousel brand</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="content-wrapper">
    <section class="content">
        <div class="container-fluid">
            <!--@if(session('success'))-->
            <!--    <div class="alert alert-success alert-dismissible fade show" role="alert">-->
            <!--        {{ session('success') }}-->
            <!--        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>-->
            <!--    </div>-->
            <!--@endif-->
            
            <!--@if($errors->any())-->
            <!--    <div class="alert alert-danger alert-dismissible fade show" role="alert">-->
            <!--        <ul class="mb-0">-->
            <!--            @foreach($errors->all() as $error)-->
            <!--                <li>{{ $error }}</li>-->
            <!--            @endforeach-->
            <!--        </ul>-->
            <!--        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>-->
            <!--    </div>-->
            <!--@endif-->
            
            <div class="card border shadow">
                <div class="alert  alert-dismissible fade show" role="alert">
                    <strong>Table Kelola Foto Brand</strong>
                </div>
                
                <div class="card-body table-shadow">
                    <div class="table-responsive">
                        <!--table untuk foto brand -->
                        <table class="table table-striped table-hover">
                            <thead class="">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Brand</th>
                                    <!--<th>Logo</th>-->
                                    <th>Brand Image</th>
                                    <th>Website</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($brands as $index => $brand)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <strong>{{ $brand->brand_name }}</strong>
                                            @if($brand->description)
                                                <br><small class="text-muted">{{ Str::limit($brand->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($brand->logo_url)
                                                <img src="{{ asset($brand->logo_url) }}" 
                                                     alt="{{ $brand->brand_name }}" 
                                                     class="img-thumbnail cursor-pointer brand-logo rounded"
                                                     style="width: 60px; height: 60px; object-fit: cover;"
                                                     data-bs-toggle="modal" 
                                                     data-bs-target="#imageModal"
                                                     data-image="{{ asset($brand->logo_url) }}"
                                                     data-title="Logo {{ $brand->brand_name }}">
                                            @else
                                                <span class="text-muted">No Logo</span>
                                            @endif
                                        </td>
                                        <!--<td>-->
                                        <!--    @if($brand->brand_img)-->
                                        <!--        <img src="{{ asset('assets/brand/' . $brand->brand_img) }}" -->
                                        <!--             alt="Brand Image {{ $brand->brand_name }}" -->
                                        <!--             class="img-thumbnail cursor-pointer brand-image rounded"-->
                                        <!--             style="width: 80px; height: 60px; object-fit: cover;"-->
                                        <!--             data-bs-toggle="modal" -->
                                        <!--             data-bs-target="#imageModal"-->
                                        <!--             data-image="{{ asset($brand->brand_img) }}"-->
                                        <!--             data-title="Brand Image {{ $brand->brand_name }}">-->
                                        <!--    @else-->
                                        <!--        <span class="text-muted">No Image</span>-->
                                        <!--    @endif-->
                                        <!--</td>-->
                                        <td>
                                            @if($brand->website_url)
                                                <a href="https://gondowangi.com/semuabrand" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-external-link-alt"></i> 
                                                </a>
                                            @else
                                                <span class="text-muted">No Website</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($brand->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" 
                                                    class="btn btn-sm btn-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editBrandImageModal"
                                                    data-brand-id="{{ $brand->id }}"
                                                    data-brand-name="{{ $brand->brand_name }}"
                                                    data-current-image="{{ $brand->brand_img ? asset('storage/' . $brand->brand_img) : '' }}">
                                                Edit Image
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">
                                            <i class="fas fa-inbox fa-2x mb-2"></i>
                                            <br>Belum ada data brand
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg"> <!-- modal-lg untuk gambar besar -->
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="imageModalLabel">Preview Gambar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body text-center">
        <img id="modalImage" src="" alt="Preview" class="img-fluid rounded">
      </div>
    </div>
  </div>
</div>

<!-- Modal untuk Tambah Carousel Brand -->
<div class="modal fade" id="addBrandModal" tabindex="-1" aria-labelledby="addBrandModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.semuabrandadmin.store-brand-image') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addBrandModalLabel">Tambah Carousel Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="image" class="form-label">Gambar Carousel Brand <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                        <div class="form-text">Format yang diizinkan: JPEG, PNG, JPG, GIF. Maksimal 2MB</div>
                    </div>
                    
                    <!-- Preview gambar -->
                    <div class="mb-3" id="imagePreview" style="display: none;">
                        <label class="form-label">Preview Gambar:</label>
                        <div>
                            <img id="previewImg" src="" alt="Preview" class="img-fluid rounded" style="max-height: 200px;">
                        </div>
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

<!-- Modal Tambah Banner -->
<div class="modal fade" id="addBannerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.semuabrandadmin.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Banner</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-2">
                                <label>Gambar Banner <span class="text-danger">*</span></label>
                                <input type="file" name="image" class="form-control" accept="image/*" required onchange="previewImage(event)">
                                <small class="text-muted">Format: JPG, PNG, GIF, WEBP. Max: 2MB</small>
                            </div>
                            <!-- Preview Gambar -->
                            <div class="mt-3 text-center">
                                <img id="imagePreview" src="#" alt="Preview Gambar" class="img-fluid d-none rounded border shadow-sm" style="max-height: 250px;">
                            </div>
                        </div>

                        <!-- Field lainnya -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Judul</label>
                                <input type="text" name="title" class="form-control" placeholder="Masukkan judul">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Subtitle</label>
                                <input type="text" name="subtitle" class="form-control" placeholder="Masukkan subtitle">
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Masukkan deskripsi"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Text Button</label>
                                <input type="text" name="button_text" class="form-control" placeholder="Contoh: Tentang Kami">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>URL Button</label>
                                <input type="url" name="button_url" class="form-control" placeholder="https://example.com">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Urutan</label>
                                <input type="number" name="sort_order" class="form-control" value="0" min="0">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="form-check ml-4">
                                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active_add" checked>
                                    <label class="form-check-label m-0 p-0" for="is_active_add">
                                        Aktif
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end .row -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for Image Preview -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Preview Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="previewImage" src="" alt="Preview" class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>

<!-- Modal for Edit Brand Image -->
<div class="modal fade" id="editBrandImageModal" tabindex="-1" aria-labelledby="editBrandImageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editBrandImageForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editBrandImageModalLabel">Edit Brand Image</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="brand_name_display" class="form-label">Nama Brand</label>
                        <input type="text" class="form-control" id="brand_name_display" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="current_image_preview" class="form-label">Gambar Saat Ini</label>
                        <div id="current_image_preview" class="text-center mb-2">
                            <img id="current_brand_image" src="" alt="Current Image" class="img-thumbnail" style="max-width: 200px; display: none;">
                            <p id="no_current_image" class="text-muted" style="display: none;">Belum ada gambar</p>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="brand_img" class="form-label">Upload Gambar Baru</label>
                        <input type="file" class="form-control" id="brand_img" name="brand_img" accept="image/*">
                        <div class="form-text">Format yang didukung: JPG, PNG, GIF. Maksimal 2MB.</div>
                    </div>
                    
                    <div class="mb-3">
                        <div id="new_image_preview" class="text-center" style="display: none;">
                            <label class="form-label">Preview Gambar Baru</label>
                            <br>
                            <img id="new_brand_image" src="" alt="New Image Preview" class="img-thumbnail" style="max-width: 200px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script Preview Gambar -->
<script>
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('imagePreview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<!-- Modal Edit Banner -->
<div class="modal fade" id="editBannerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editBannerForm" method="POST" enctype="multipart/form-data" data-base-url="{{ url('admin/semuabrand') }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h4 class="modal-title">Edit Banner</h4>
                    <button type="button" class="close" data-bs-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Gambar Banner</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <small class="text-muted">Kosongkan jika tidak ingin mengubah gambar</small>
                                <div id="currentImage" class="mt-2"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Judul</label>
                                <input type="text" name="title" id="edit_title" class="form-control">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Subtitle</label>
                                <input type="text" name="subtitle" id="edit_subtitle" class="form-control">
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Deskripsi</label>
                                <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Text Button</label>
                                <input type="text" name="button_text" id="edit_button_text" class="form-control">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>URL Button</label>
                                <input type="url" name="button_url" id="edit_button_url" class="form-control">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Urutan</label>
                                <input type="number" name="sort_order" id="edit_sort_order" class="form-control" min="0">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="form-check">
                                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active_edit">
                                    <label class="form-check-label" for="is_active_edit">
                                        Aktif
                                    </label>
                                </div>
                            </div>
                        </div>
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

<!-- Delete Form Hidden -->
<form id="deleteForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('script')
<script src="{{ asset('admin/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('admin/assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('admin/assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview modal
    const imageModal = document.getElementById('imageModal');
    imageModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const imageSrc = button.getAttribute('data-image');
        const imageTitle = button.getAttribute('data-title');
        
        const previewImage = document.getElementById('previewImage');
        const modalTitle = document.getElementById('imageModalLabel');
        
        previewImage.src = imageSrc;
        modalTitle.textContent = imageTitle;
    });
    
    // Edit brand image modal
    const editModal = document.getElementById('editBrandImageModal');
    editModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const brandId = button.getAttribute('data-brand-id');
        const brandName = button.getAttribute('data-brand-name');
        const currentImage = button.getAttribute('data-current-image');
        
        // Set form action
        const form = document.getElementById('editBrandImageForm');
        form.action = `/admin/brand/${brandId}/update-image`;
        
        // Set brand name
        document.getElementById('brand_name_display').value = brandName;
        
        // Set current image preview
        const currentImageElement = document.getElementById('current_brand_image');
        const noCurrentImageElement = document.getElementById('no_current_image');
        
        if (currentImage && currentImage !== '') {
            currentImageElement.src = currentImage;
            currentImageElement.style.display = 'block';
            noCurrentImageElement.style.display = 'none';
        } else {
            currentImageElement.style.display = 'none';
            noCurrentImageElement.style.display = 'block';
        }
        
        // Reset file input and preview
        document.getElementById('brand_img').value = '';
        document.getElementById('new_image_preview').style.display = 'none';
    });
    
    // File input change handler for preview
    document.getElementById('brand_img').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const previewContainer = document.getElementById('new_image_preview');
        const previewImage = document.getElementById('new_brand_image');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = 'none';
        }
    });
});
</script>

<script>
    function showImageInModal(src) {
        document.getElementById('modalImage').src = src;
    }
</script>

<script>
$(document).ready(function() {
    $('#bannerTable').DataTable({
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
    });
});

function editBanner(banner) {
    const form = document.getElementById('editBannerForm');

    // Ambil base url dari blade di data attribute
    const baseUrl = form.getAttribute('data-base-url');
    form.action = `${baseUrl}/${banner.id}`;

    // Isi form seperti biasa
    document.getElementById('edit_title').value = banner.title || '';
    document.getElementById('edit_subtitle').value = banner.subtitle || '';
    document.getElementById('edit_description').value = banner.description || '';
    document.getElementById('edit_button_text').value = banner.button_text || '';
    document.getElementById('edit_button_url').value = banner.button_url || '';
    document.getElementById('edit_sort_order').value = banner.sort_order || 0;
    document.getElementById('is_active_edit').checked = banner.is_active;

    // Tampilkan gambar saat ini
    const currentImageDiv = document.getElementById('currentImage');
    if (banner.image_path) {
        currentImageDiv.innerHTML = `
            <label class="text-muted">Gambar saat ini:</label><br>
            <img src="{{ asset('storage') }}/${banner.image_path}" alt="Current Banner" style="max-width: 200px; height: auto; border-radius: 4px;">
        `;
    } else {
        currentImageDiv.innerHTML = '';
    }
}


function deleteBanner(id) {
    if (confirm('Apakah Anda yakin ingin menghapus banner ini?')) {
        const form = document.getElementById('deleteForm');
        form.action = `{{ url('admin/banner') }}/${id}`;
        form.submit();
    }
}
</script>
@endsection