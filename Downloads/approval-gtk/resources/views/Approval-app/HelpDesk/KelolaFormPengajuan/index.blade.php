@extends('Approval-app.Layout.main-admin')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
@endsection

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Kelola Form Pengajuan </h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Help Desk</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Kelola Form Pengajuan</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Kategori Pengajuan & Form Fields</h5>
                <a href="{{ route('kelola-form-pengajuan.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Tambah Form Field
                </a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="accordion" id="accordionKategori">
                    @foreach($kategoriPengajuan as $index => $kategori)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading{{ $kategori->id }}">
                            <button class="accordion-button {{ $index > 0 ? 'collapsed' : '' }}" type="button" 
                                    data-bs-toggle="collapse" data-bs-target="#collapse{{ $kategori->id }}" 
                                    aria-expanded="{{ $index == 0 ? 'true' : 'false' }}" 
                                    aria-controls="collapse{{ $kategori->id }}">
                                <div class="d-flex align-items-center w-100">
                                    <div class="me-3">
                                        <i class="{{ $kategori->icon ?? 'fas fa-folder' }}" 
                                           style="color: {{ $kategori->warna ?? '#6c757d' }}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <strong>{{ $kategori->nama }}</strong>
                                        <small class="text-muted d-block">{{ $kategori->deskripsi }}</small>
                                    </div>
                                    <div class="me-3">
                                        <span class="badge bg-info">{{ $kategori->formFields->count() }} Fields</span>
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse{{ $kategori->id }}" 
                             class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" 
                             aria-labelledby="heading{{ $kategori->id }}" 
                             data-bs-parent="#accordionKategori">
                            <div class="accordion-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">Form Fields untuk {{ $kategori->nama }}</h6>
                                    <div>
                                        <a href="{{ route('kelola-form-pengajuan.preview', $kategori->id) }}" 
                                           class="btn btn-outline-info btn-sm me-2">
                                            <i class="fas fa-eye me-1"></i>Preview Form
                                        </a>
                                        <a href="{{ route('kelola-form-pengajuan.create') }}?kategori={{ $kategori->id }}" 
                                           class="btn btn-primary btn-sm">
                                            <i class="fas fa-plus me-1"></i>Tambah Field
                                        </a>
                                    </div>
                                </div>

                                @if($kategori->formFields->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th width="5%">#</th>
                                                <!--<th width="15%">Nama Field</th>-->
                                                <th width="15%">Label</th>
                                                <th width="10%">Tipe</th>
                                                <!--<th width="8%">Posisi</th>-->
                                                <!--<th width="8%">Urutan</th>-->
                                                <th width="8%">Wajib</th>
                                                <th width="8%">Status</th>
                                                <th width="15%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($kategori->formFields->sortBy('urutan') as $field)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <!--<td>-->
                                                <!--    <code>{{ $field->nama_field }}</code>-->
                                                <!--</td>-->
                                                <td>{{ $field->label }}</td>
                                                <td>
                                                    <span class="badge bg-info">{{ ucfirst($field->tipe_field) }}</span>
                                                </td>
                                                <!--<td>-->
                                                <!--    <small class="text-muted">-->
                                                <!--        Row: {{ $field->posisi_row }}<br>-->
                                                <!--        Col: {{ $field->posisi_col }} ({{ $field->lebar_col }})-->
                                                <!--    </small>-->
                                                <!--</td>-->
                                                <!--<td>-->
                                                <!--    <span class="badge bg-info">{{ $field->urutan }}</span>-->
                                                <!--</td>-->
                                                <td>
                                                    @if($field->wajib)
                                                        <span class="badge bg-danger">Ya</span>
                                                    @else
                                                        <span class="badge bg-secondary">Tidak</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($field->status == 'aktif')
                                                        <span class="badge bg-success">Aktif</span>
                                                    @else
                                                        <span class="badge bg-secondary">Nonaktif</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('kelola-form-pengajuan.edit', $field->id) }}" 
                                                           class="btn btn-outline-warning btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" 
                                                                class="btn btn-outline-danger btn-sm btn-delete" 
                                                                data-id="{{ $field->id }}" 
                                                                data-name="{{ $field->label }}" 
                                                                title="Hapus">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-4">
                                    <div class="mb-3">
                                        <i class="fas fa-inbox fa-3x text-muted"></i>
                                    </div>
                                    <h6 class="text-muted">Belum ada form field untuk kategori ini</h6>
                                    <p class="text-muted small">Silakan tambah form field untuk kategori {{ $kategori->nama }}</p>
                                    <a href="{{ route('kelola-form-pengajuan.create') }}?kategori={{ $kategori->id }}" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-plus me-1"></i>Tambah Form Field Pertama
                                    </a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($kategoriPengajuan->count() == 0)
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-folder-open fa-4x text-muted"></i>
                    </div>
                    <h5 class="text-muted">Belum ada kategori pengajuan</h5>
                    <p class="text-muted">Silakan buat kategori pengajuan terlebih dahulu sebelum menambah form field</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@endsection

@section('script')
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

<script>
$(document).ready(function() {
    // Handle delete button
    $('.btn-delete').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: `Apakah Anda yakin ingin menghapus form field "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Menghapus...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Send delete request
                $.ajax({
                    url: `/kelola-form-pengajuan/${id}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: response.message,
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: response.message,
                                icon: 'error'
                            });
                        }
                    },
                    error: function(xhr) {
                        let message = 'Terjadi kesalahan saat menghapus data';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        
                        Swal.fire({
                            title: 'Error!',
                            text: message,
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });
});
</script>
@endsection