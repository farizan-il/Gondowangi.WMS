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


            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kelola Banner Tentang Kami</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBannerModal">
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
                                        <span class="badge badge-secondary">{{ $banner->sort_order }}</span>
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
                                            
                                            <form action="{{ route('admin.bannertentangkami.toggle-status', $banner) }}" 
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


<!-- Modal Tambah Banner -->
<div class="modal fade" id="addBannerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.bannertentangkami.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Banner</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Gambar Banner <span class="text-danger">*</span></label>
                                <input type="file" name="image" class="form-control" accept="image/*" required>
                                <small class="text-muted">Format: JPG, PNG, GIF, WEBP. Max: 2MB</small>
                            </div>
                        </div>
                        
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

<!-- Modal Edit Banner -->
<div class="modal fade" id="editBannerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editBannerForm" method="POST" enctype="multipart/form-data">
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
    form.action = `{{ url('admin/banner') }}/${banner.id}`;
    
    document.getElementById('edit_title').value = banner.title || '';
    document.getElementById('edit_subtitle').value = banner.subtitle || '';
    document.getElementById('edit_description').value = banner.description || '';
    document.getElementById('edit_button_text').value = banner.button_text || '';
    document.getElementById('edit_button_url').value = banner.button_url || '';
    document.getElementById('edit_sort_order').value = banner.sort_order || 0;
    document.getElementById('is_active_edit').checked = banner.is_active;
    
    // Show current image
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