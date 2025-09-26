@extends('Gondowangi.Admin.Layout.main')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .timeline-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }
    .btn-action {
        margin: 0 2px;
    }
    .modal-backdrop {
        z-index: 1040;
    }
    .modal {
        z-index: 1050;
    }
</style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="container-fluid border rounded shadow">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">{{ $title }}</h4>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                            <i class="fas fa-plus"></i> Tambah Perjalanan
                        </button>
                    </div>
                    <div class="alert alert-info alert-dismissible fade show" role="alert">
                        Klik gambar untuk melihat versi besarnya dalam tampilan detail.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive"> 
                            <table class="table table-striped table-bordered" id="perjalananTable">
                                <thead class="">
                                    <tr>
                                        <th>No</th>
                                        <th>Tahun</th>
                                        <th>Judul</th>
                                        <th>Deskripsi</th>
                                        <th>Tipe</th>
                                        <th>Gambar</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($perjalanan as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $item->year }}</td>
                                        <td>{{ $item->title }}</td>
                                        <td>{{ Str::limit($item->description, 100) }}</td>
                                        <td>{{ $item->timeline_type }}</td>
                                        <td>
                                            @if($item->image_url)
                                                <img src="{{ asset($item->image_url) }}" 
                                                     class="timeline-image rounded" 
                                                     alt="Timeline Image"
                                                     style="width: 60px; height: 40px; object-fit: cover; cursor: pointer;"
                                                     data-bs-toggle="modal" 
                                                     data-bs-target="#timelineImageModal"
                                                     onclick="showTimelineImageModal('{{ asset($item->image_url) }}')">
                                            @else
                                                <span class="text-muted">Tidak ada gambar</span>
                                            @endif
                                        </td>

                                        <td>
                                            <button type="button" class="btn btn-sm btn-info btn-action" onclick="viewData({{ $item->id }})">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-warning btn-action" onclick="editData({{ $item->id }})">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger btn-action" onclick="deleteData({{ $item->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Tidak ada data perjalanan</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Gambar Timeline -->
<div class="modal fade" id="timelineImageModal" tabindex="-1" aria-labelledby="timelineImageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="timelineImageModalLabel">Gambar Timeline</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
      </div>
      <div class="modal-body text-center">
        <img id="timelineImageModalContent" src="" alt="Gambar Timeline" class="img-fluid rounded">
      </div>
    </div>
  </div>
</div>


<!-- Modal Tambah -->
<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">Tambah Perjalanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="add_year" class="form-label">Tahun <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="add_year" name="year" min="1900" max="{{ date('Y') + 10 }}" required>
                            </div>
                        </div>
                        <!--<div class="col-md-6">-->
                        <!--    <div class="mb-3">-->
                        <!--        <label for="add_timeline_type" class="form-label">Tipe Timeline <span class="text-danger">*</span></label>-->
                        <!--        <input type="text" class="form-control" id="add_timeline_type" name="timeline_type" required>-->
                        <!--    </div>-->
                        <!--</div>-->
                    </div>
                    <div class="mb-3">
                        <label for="add_title" class="form-label">Judul <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="add_title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="add_description" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="add_description" name="description" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="add_image" class="form-label">Gambar</label>
                        <input type="file" class="form-control" id="add_image" name="image" accept="image/*">
                        <div class="form-text">Format yang didukung: JPEG, PNG, JPG, GIF. Maksimal 2MB.</div>
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


<!-- Modal Edit -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Perjalanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_id" name="id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_year" class="form-label">Tahun <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="edit_year" name="year" min="1900" max="{{ date('Y') + 10 }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_timeline_type" class="form-label">Tipe Timeline <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_timeline_type" name="timeline_type" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_title" class="form-label">Judul <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_title" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Deskripsi <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="edit_description" name="description" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_image" class="form-label">Gambar</label>
                        <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                        <div class="form-text">Format yang didukung: JPEG, PNG, JPG, GIF. Maksimal 2MB. Kosongkan jika tidak ingin mengubah gambar.</div>
                        <div id="current_image" class="mt-2"></div>
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

<!-- Modal View -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">Detail Perjalanan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tahun:</label>
                            <p id="view_year"></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipe Timeline:</label>
                            <p id="view_timeline_type"></p>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Judul:</label>
                    <p id="view_title"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Deskripsi:</label>
                    <p id="view_description"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Gambar:</label>
                    <div id="view_image"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Delete Confirmation -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data perjalanan ini?</p>
                <p class="text-danger"><strong>Tindakan ini tidak dapat dibatalkan!</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Sweet Alert 2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function showTimelineImageModal(imageUrl) {
        document.getElementById('timelineImageModalContent').src = imageUrl;
    }
</script>


<script>
    $(document).ready(function() {
        // Setup CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    
        // Handle form submission for adding data
        $('#addForm').on('submit', function(e) {
            e.preventDefault();
            
            var formData = new FormData(this);
            
             $.ajax({
        url: '{{ url("admin/tentang-kami/perjalanan") }}',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                $('#addModal').modal('hide');
                $('#addForm')[0].reset();

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: response.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            }
        },
        error: function(xhr, status, error) {
            let errorMessage = '';

            // Coba ambil error dari response JSON
            if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = xhr.responseJSON.errors;
                Object.keys(errors).forEach(function(key) {
                    errorMessage += errors[key][0] + '\n';
                });
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                // Jika ada pesan error umum
                errorMessage = xhr.responseJSON.message;
            } else {
                // Fallback untuk menampilkan error dari XHR
                errorMessage = 'Status: ' + status + '\n';
                errorMessage += 'Error: ' + error + '\n';
                errorMessage += 'Response: ' + xhr.responseText;
            }

            // Tampilkan SweetAlert dengan detail error
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan!',
                text: errorMessage,
                customClass: {
                    popup: 'text-start'
                }
            });

            // Optional: log ke console untuk debugging developer
            console.error('AJAX Error:', {
                status: status,
                error: error,
                response: xhr.responseText
            });
        }
    });
        });
    
        // Handle form submission for updating data
        $('#editForm').on('submit', function(e) {
            e.preventDefault();
            
            var formData = new FormData(this);
            formData.append('_method', 'PUT');
            var id = $('#edit_id').val();
            
            $.ajax({
                url: '{{ url("admin/tentang-kami/perjalanan") }}/' + id,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#editModal').modal('hide');
                        $('#editForm')[0].reset();
                        
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    let errors = xhr.responseJSON.errors;
                    let errorMessage = '';
                    
                    if (errors) {
                        Object.keys(errors).forEach(function(key) {
                            errorMessage += errors[key][0] + '\n';
                        });
                    } else {
                        errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage
                    });
                }
            });
        });
    });
    
    // Function to view data
    function viewData(id) {
        $.ajax({
            url: '{{ url("admin/tentang-kami/perjalanan") }}/' + id,
            type: 'GET',
            success: function(data) {
                $('#view_year').text(data.year);
                $('#view_timeline_type').text(data.timeline_type);
                $('#view_title').text(data.title);
                $('#view_description').text(data.description);
                
                if (data.image_url) {
                    $('#view_image').html('<img src="' + data.image_url + '" class="img-fluid" style="max-height: 300px;" alt="Timeline Image">');
                } else {
                    $('#view_image').html('<p class="text-muted">Tidak ada gambar</p>');
                }
                
                $('#viewModal').modal('show');
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Gagal memuat data. Silakan coba lagi.'
                });
            }
        });
    }
    
    // Function to edit data
    function editData(id) {
        $.ajax({
            url: '{{ url("admin/tentang-kami/perjalanan") }}/' + id,
            type: 'GET',
            success: function(data) {
                $('#edit_id').val(data.id);
                $('#edit_year').val(data.year);
                $('#edit_timeline_type').val(data.timeline_type);
                $('#edit_title').val(data.title);
                $('#edit_description').val(data.description);
                
                if (data.image_url) {
                    $('#current_image').html('<div class="mt-2"><label class="form-label fw-bold">Gambar Saat Ini:</label><br><img src="' + data.image_url + '" class="img-thumbnail" style="max-height: 100px;" alt="Current Image"></div>');
                } else {
                    $('#current_image').html('');
                }
                
                $('#editModal').modal('show');
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Gagal memuat data. Silakan coba lagi.'
                });
            }
        });
    }
    
    // Function to delete data
    function deleteData(id) {
        $('#deleteModal').modal('show');
        
        $('#confirmDelete').off('click').on('click', function() {
            $.ajax({
                url: '{{ url("admin/tentang-kami/perjalanan") }}/' + id,
                type: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#deleteModal').modal('hide');
                        
                        // Show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    }
                },
                error: function(xhr) {
                    console.log('Error:', xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Gagal menghapus data. Silakan coba lagi.'
                    });
                }
            });
        });
    }
</script>

<!-- Sweet Alert 2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection