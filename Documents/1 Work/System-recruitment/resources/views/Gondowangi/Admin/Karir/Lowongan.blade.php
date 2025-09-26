@extends('Gondowangi.Admin.Layout.main')

@section('head')
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap4.min.css">
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .career-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .career-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }
        .status-badge {
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
        }
        .department-badge {
            font-size: 0.75rem;
            padding: 0.3rem 0.6rem;
        }
        .btn-action {
            padding: 0.25rem 0.5rem;
            margin: 0 0.1rem;
        }
        .modal-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        .page-header {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #28a745;
        }
        .image-preview {
            max-width: 150px;
            max-height: 150px;
            border-radius: 8px;
            border: 2px solid #ddd;
        }
    </style>
@endsection

@section('content')
<div class="content-wrapper">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0">Manajemen Lowongan Karir</h1>
                    <p class="mb-0 mt-2">Kelola semua lowongan karir perusahaan Gondowangi dengan mudah</p>
                </div>
                <div class="col-md-4 text-right">
                    <button class="btn btn-light btn-lg" onclick="openAddModal()">
                        <i class="mdi mdi-plus"></i> Tambah Lowongan
                    </button>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number">{{ $stats['total_lowongan'] }}</div>
                    <div class="text-muted">Total Lowongan</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number">{{ $stats['aktif'] }}</div>
                    <div class="text-muted">Aktif</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number">{{ $stats['nonaktif'] }}</div>
                    <div class="text-muted">Nonaktif</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-number">{{ $stats['total_pelamar'] }}</div>
                    <div class="text-muted">Total Pelamar</div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <select class="form-control" id="filterDepartment">
                            <option value="">Semua Departemen</option>
                            @foreach($departments as $department)
                                <option value="{{ $department }}">{{ $department }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" id="filterStatus">
                            <option value="">Semua Status</option>
                            <option value="active">Aktif</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" id="searchJob" placeholder="Cari lowongan...">
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary" onclick="filterJobs()">
                            <i class="mdi mdi-filter"></i> Filter
                        </button>
                        <button class="btn btn-secondary" onclick="resetFilter()">
                            <i class="mdi mdi-refresh"></i> Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Jobs Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="jobsTable">
                        <thead class="thead-dark">
                            <tr>
                                <th>No</th>
                                <th>Gambar</th>
                                <th>Posisi</th>
                                <th>Departemen</th>
                                <th>Tipe</th>
                                <th>Lokasi</th>
                                <th>Status</th>
                                <th>Deadline</th>
                                <th>Pelamar</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($lowongan as $index => $job)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    @if($job->image_url)
                                        <img src="{{ asset($job->image_url) }}" 
                                             class="rounded" 
                                             alt="Job Image" 
                                             style="width: 50px; height: 50px; object-fit: cover; cursor: pointer;"
                                             data-bs-toggle="modal" 
                                             data-bs-target="#jobImageModal"
                                             onclick="showJobImageModal('{{ asset($job->image_url) }}')">
                                    @else
                                        <img src="https://via.placeholder.com/50x50" class="rounded" alt="Job Image">
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $job->position_title }}</strong><br>
                                    <small class="text-muted">{{ $job->department }} Team</small>
                                </td>
                                <td>
                                    <span class="badge 
                                        @if($job->department == 'Marketing') badge-dark
                                        @elseif($job->department == 'HCD') badge-dark
                                        @elseif($job->department == 'Factory') badge-dark
                                        @else badge-dark
                                        @endif department-badge">
                                        {{ $job->department }}
                                    </span>
                                </td>
                                <td>{{ $job->job_type }}</td>
                                <td>{{ $job->location }}</td>
                                <td>
                                    <span class="badge {{ $job->status == 'open' ? 'badge-success' : 'badge-danger' }} status-badge">
                                        {{ $job->status == 'open' ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($job->deadline)->format('Y-m-d') }}</td>
                                <td>
                                    <span class="badge bg-info rounded">{{ $job->karyawan_count }} Pelamar</span>
                                </td>

                                <td>
                                    <button class="btn btn-info btn-sm btn-action" onclick="viewJob({{ $job->id }})" title="Lihat">
                                        <i class="mdi mdi-eye"></i>
                                    </button>
                                    <button class="btn btn-warning btn-sm btn-action" onclick="editJob({{ $job->id }})" title="Edit">
                                        <i class="mdi mdi-pencil"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm btn-action" onclick="deleteJob({{ $job->id }})" title="Hapus">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                    <button class="btn btn-secondary btn-sm btn-action" onclick="toggleStatus({{ $job->id }})" title="Toggle Status">
                                        <i class="mdi mdi-toggle-switch"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center">Belum ada data lowongan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Job Image -->
<div class="modal fade" id="jobImageModal" tabindex="-1" aria-labelledby="jobImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jobImageModalLabel">Gambar Pekerjaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body text-center">
                <img id="jobImageModalContent" src="" alt="Gambar Pekerjaan" class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit dengan Preview Gambar yang Diperbaiki -->
<div class="modal fade" id="jobModal" tabindex="-1" role="dialog" aria-labelledby="jobModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="jobModalLabel">
                    <i class="mdi mdi-briefcase-plus"></i> Tambah Lowongan Karir
                </h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="jobForm" enctype="multipart/form-data" method="POST">
                @csrf
                <input type="hidden" id="jobId" name="job_id">
                <input type="hidden" id="formMethod" value="POST">
                <div class="modal-body">
                    <!-- Input fields lainnya tetap sama -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="position_title">Nama Posisi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="position_title" name="position_title" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="department">Departemen <span class="text-danger">*</span></label>
                                <select class="form-control" id="department" name="department" required>
                                    <option value="">Pilih Departemen</option>
                                    <option value="Sales">Sales</option>
                                    <option value="Digital Marketing">Digital Marketing</option>
                                    <option value="Finance Accounting">Finance Accounting</option>
                                    <option value="Business Development">Business Development</option>
                                    <option value="Produksi">Produksi</option>
                                    <option value="Maintenance">Maintenance</option>
                                    <option value="Purchasing">Purchasing</option>
                                    <option value="Logistik">Logistik</option>
                                    <option value="QAC">QAC</option>
                                    <option value="PPIC">PPIC</option>
                                    <option value="HCD">HCD</option>
                                    <option value="Marketing">Marketing</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="job_type">Tipe Pekerjaan <span class="text-danger">*</span></label>
                                <select class="form-control" id="job_type" name="job_type" required>
                                    <option value="">Pilih Tipe</option>
                                    <option value="full-time">Full Time</option>
                                    <option value="part-time">Part Time</option>
                                    <option value="contract">Contract</option>
                                    <option value="internship">Internship</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="location">Lokasi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="location" name="location" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="deadline">Deadline <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="deadline" name="deadline" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Deskripsi Pekerjaan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="requirements">Persyaratan <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="requirements" name="requirements" rows="4" required></textarea>
                    </div>

                    <!-- Bagian Image yang Diperbaiki -->
                    <div class="form-group">
                        <label for="image">Gambar Lowongan</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="image" name="image" accept="image/*">
                            <label class="custom-file-label" for="image">Pilih file...</label>
                        </div>
                        <small class="form-text text-muted">Upload gambar untuk lowongan (format: jpeg, png, jpg, gif | max: 2MB)</small>
                        
                        <!-- Preview Container -->
                        <div id="imagePreviewContainer" class="mt-3">
                            <div id="currentImagePreview" style="display: none;">
                                <h6>Gambar Saat Ini:</h6>
                                <div class="position-relative d-inline-block">
                                    <img id="currentImage" src="" alt="Current Image" style="max-width: 200px; max-height: 200px; object-fit: cover;" class="rounded border">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute" style="top: 5px; right: 5px;" onclick="removeCurrentImage()" title="Hapus gambar saat ini">
                                        <i class="mdi mdi-close"></i>
                                    </button>
                                </div>
                            </div>
                            <div id="newImagePreview" style="display: none;">
                                <h6>Gambar Baru:</h6>
                                <div class="position-relative d-inline-block">
                                    <img id="newImage" src="" alt="New Image" style="max-width: 200px; max-height: 200px; object-fit: cover;" class="rounded border">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute" style="top: 5px; right: 5px;" onclick="removeNewImage()" title="Hapus gambar baru">
                                        <i class="mdi mdi-close"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Hidden input untuk menandai penghapusan gambar -->
                        <input type="hidden" id="removeImage" name="remove_image" value="0">
                    </div>
            
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control" id="status" name="status">
                            <option value="open">Aktif</option>
                            <option value="draf">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="mdi mdi-content-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalLabel">
                    <i class="mdi mdi-eye"></i> Detail Lowongan
                </h5>
                <button type="button" class="close text-white" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-bs-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewModalBody">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="https://gondowangi.com/karir" target="_blank" class="btn btn-primary">
                    <i class="mdi mdi-open-in-new"></i> Lihat di Website
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                showConfirmButton: false,
                timer: 2000
            });
        </script>
    @endif
    
    <script>
        function showJobImageModal(imageUrl) {
            document.getElementById('jobImageModalContent').src = imageUrl;
        }
    </script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('#jobsTable').DataTable({
                "pageLength": 10,
                "responsive": true,
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json"
                }
            });
            
            // Initialize Select2
            $('.select2').select2();
            
            // Image preview untuk file baru
            $('#image').change(function() {
                const file = this.files[0];
                if (file) {
                    // Update label dengan nama file
                    $(this).next('.custom-file-label').text(file.name);
                    
                    // Preview gambar baru
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#newImage').attr('src', e.target.result);
                        $('#newImagePreview').show();
                    }
                    reader.readAsDataURL(file);
                } else {
                    $('#newImagePreview').hide();
                    $(this).next('.custom-file-label').text('Pilih file...');
                }
            });
            
            // Form submission
            $('#jobForm').submit(function(e) {
                e.preventDefault();
                saveJob();
            });
        });
        
        function openAddModal() {
            $('#jobModalLabel').html('<i class="mdi mdi-briefcase-plus"></i> Tambah Lowongan Karir');
            resetForm();
            $('#jobModal').modal('show');
        }
        
        function resetForm() {
            $('#jobForm')[0].reset();
            $('#currentImagePreview').hide();
            $('#newImagePreview').hide();
            $('#jobId').val('');
            $('#formMethod').val('POST');
            $('#removeImage').val('0');
            $('.custom-file-label').text('Pilih file...');
            
            // Clear any validation errors
            $('.form-control').removeClass('is-invalid');
            $('.invalid-feedback').remove();
        }
        
        function editJob(id) {
            $('#jobModalLabel').html('<i class="mdi mdi-pencil"></i> Edit Lowongan Karir');
            resetForm(); // Reset form terlebih dahulu
            
            $('#formMethod').val('PUT');
            $('#jobId').val(id);
            
            // Show loading state
            const originalContent = $('#jobModal .modal-body').html();
            $('#jobModal .modal-body').html('<div class="text-center"><i class="mdi mdi-loading mdi-spin"></i> Memuat data...</div>');
            
            // Fetch job data
            $.ajax({
                url: `{{ url('/admin/lowongan') }}/${id}`,
                type: 'GET',
                success: function(response) {
                    // Restore original content
                    $('#jobModal .modal-body').html(originalContent);
                    
                    if (response.success) {
                        const job = response.data;
                        
                        // Populate form fields
                        $('#position_title').val(job.position_title);
                        $('#department').val(job.department);
                        $('#job_type').val(job.job_type);
                        $('#location').val(job.location);
                        $('#deadline').val(job.deadline);
                        $('#salary_range').val(job.salary_range || '');
                        $('#description').val(job.description);
                        $('#requirements').val(job.requirements);
                        $('#status').val(job.status);
                        
                        // Show current image if exists
                        if (job.image_url) {
                            $('#currentImage').attr('src', `{{ asset('') }}${job.image_url}`);
                            $('#currentImagePreview').show();
                        }
                        
                        // Re-initialize image change handler
                        $('#image').off('change').on('change', function() {
                            const file = this.files[0];
                            if (file) {
                                $(this).next('.custom-file-label').text(file.name);
                                
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    $('#newImage').attr('src', e.target.result);
                                    $('#newImagePreview').show();
                                }
                                reader.readAsDataURL(file);
                            } else {
                                $('#newImagePreview').hide();
                                $(this).next('.custom-file-label').text('Pilih file...');
                            }
                        });
                        
                        $('#jobModal').modal('show');
                    } else {
                        Swal.fire('Error!', 'Data tidak ditemukan.', 'error');
                    }
                },
                error: function(xhr) {
                    // Restore original content
                    $('#jobModal .modal-body').html(originalContent);
                    
                    let errorMessage = 'Terjadi kesalahan saat memuat data.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire('Error!', errorMessage, 'error');
                }
            });
        }
        
        function removeCurrentImage() {
            $('#currentImagePreview').hide();
            $('#removeImage').val('1');
        }
        
        function removeNewImage() {
            $('#newImagePreview').hide();
            $('#image').val('');
            $('.custom-file-label').text('Pilih file...');
        }
        
        function viewJob(id) {
            // Show loading state
            $('#viewModalBody').html('<div class="text-center"><i class="mdi mdi-loading mdi-spin"></i> Memuat data...</div>');
            $('#viewModal').modal('show');
            
            // Fetch job data
            $.ajax({
                url: `{{ url('/admin/lowongan') }}/${id}`,
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        const job = response.data;
                        const badgeClass = job.status === 'open' ? 'badge-success' : 'badge-danger';
                        const statusText = job.status === 'open' ? 'Aktif' : 'Nonaktif';
                        const imageSrc = job.image_url ? `{{ asset('') }}${job.image_url}` : 'https://via.placeholder.com/200x200';
                        
                        const content = `
                            <div class="row">
                                <div class="col-md-4">
                                    <img src="${imageSrc}" class="img-fluid rounded" alt="Job Image">
                                </div>
                                <div class="col-md-8">
                                    <h4>${job.position_title}</h4>
                                    <p><strong>Departemen:</strong> <span class="badge badge-info">${job.department}</span></p>
                                    <p><strong>Tipe:</strong> ${job.job_type}</p>
                                    <p><strong>Lokasi:</strong> ${job.location}</p>
                                    <p><strong>Deadline:</strong> ${new Date(job.deadline).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</p>
                                    <p><strong>Gaji:</strong> ${job.salary_range || 'Negotiable'}</p>
                                    <p><strong>Status:</strong> <span class="badge ${badgeClass}">${statusText}</span></p>
                                    <p><strong>Total Pelamar:</strong> ${job.applications_count || 0} orang</p>
                                </div>
                            </div>
                            <hr>
                            <h5>Deskripsi Pekerjaan:</h5>
                            <p>${job.description.replace(/\n/g, '<br>')}</p>
                            
                            <h5>Persyaratan:</h5>
                            <p>${job.requirements.replace(/\n/g, '<br>')}</p>
                        `;
                        
                        $('#viewModalBody').html(content);
                    } else {
                        $('#viewModalBody').html('<div class="alert alert-danger">Data tidak ditemukan.</div>');
                    }
                },
                error: function() {
                    $('#viewModalBody').html('<div class="alert alert-danger">Terjadi kesalahan saat memuat data.</div>');
                }
            });
        }
        
        function deleteJob(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Lowongan yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('/admin/lowongan') }}/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Terhapus!',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            location.reload();
                        }
                    });
                }
            });
        }
        
        function toggleStatus(id) {
            Swal.fire({
                title: 'Ubah Status',
                text: "Apakah Anda ingin mengubah status lowongan ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Ubah!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `{{ url('/admin/lowongan') }}/${id}/toggle-status`,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire(
                                    'Berhasil!',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            let errorMessage = 'Terjadi kesalahan saat mengubah status.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                            Swal.fire('Error!', errorMessage, 'error');
                        }
                    });
                }
            });
        }
        
        function saveJob() {
            const formData = new FormData($('#jobForm')[0]);
            const method = $('#formMethod').val();
            const jobId = $('#jobId').val();
            
            // Disable submit button to prevent double submission
            const submitBtn = $('#jobForm button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="mdi mdi-loading mdi-spin"></i> Menyimpan...');
            
            let url = '{{ url("/admin/lowongan") }}';
            let ajaxMethod = 'POST';
            
            if (method === 'PUT' && jobId) {
                url += `/${jobId}`;
                formData.append('_method', 'PUT');
            }
            
            $.ajax({
                url: url,
                type: ajaxMethod,
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        Swal.fire(
                            'Berhasil!',
                            response.message,
                            'success'
                        ).then(() => {
                            $('#jobModal').modal('hide');
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error!', response.message || 'Terjadi kesalahan saat menyimpan data.', 'error');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan saat menyimpan data.';
                    
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            errorMessage = Object.values(errors).flat().join('<br>');
                        }
                    }
                    
                    Swal.fire('Error!', errorMessage, 'error');
                },
                complete: function() {
                    // Re-enable submit button
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        }
        
        function filterJobs() {
            const department = $('#filterDepartment').val();
            const status = $('#filterStatus').val();
            const search = $('#searchJob').val();
            
            // Apply filters to DataTable
            const table = $('#jobsTable').DataTable();
            table.columns(3).search(department).draw();
            table.columns(6).search(status).draw();
            table.search(search).draw();
        }
        
        function resetFilter() {
            $('#filterDepartment').val('');
            $('#filterStatus').val('');
            $('#searchJob').val('');
            
            const table = $('#jobsTable').DataTable();
            table.search('').columns().search('').draw();
        }    
    </script>
@endsection