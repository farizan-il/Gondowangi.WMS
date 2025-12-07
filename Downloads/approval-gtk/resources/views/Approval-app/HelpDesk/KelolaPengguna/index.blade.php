@extends('Approval-app.Layout.main-admin')
@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="row">
    <div class="col-12 mb-4">
        <div class="page-header mb-0">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Kelola Pengguna</h4>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addKaryawanModal">
                        <i class="fas fa-plus"></i> Tambah Karyawan
                    </button>
                </div>
                
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-select" id="departmentFilter" name="department_id">
                                <option value="">Semua Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                        {{ $dept->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter" name="status">
                                <option value="">Semua Status</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="searchInput" placeholder="Cari nama atau email..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-secondary" id="clearFilters">Reset</button>
                        </div>
                    </div>
    
                    <!-- Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th>Jabatan</th>
                                    <!--<th>Atasan Langsung</th>-->
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($karyawans as $index => $karyawan)
                                <tr>
                                    <td>{{ $karyawans->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                <!--<div class="avatar-title rounded-circle bg-primary text-white">-->
                                                <!--    {{ substr($karyawan->nama, 0, 2) }}-->
                                                <!--</div>-->
                                                <img src="https://png.pngtree.com/png-clipart/20220909/original/pngtree-cartoon-man-avatar-vector-ilustration-png-image_8515463.png" class="rounded-circle" style="width: 25px;">
                                            </div>
                                            <div>
                                                <strong>{{ $karyawan->nama }}</strong>
                                                @if($karyawan->phone)
                                                    <br><small class="text-muted">{{ $karyawan->phone }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $karyawan->email }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $karyawan->department->nama ?? '-' }}</span>
                                    </td>
                                    <td>{{ $karyawan->roleLevel->nama ?? '-' }}</td>
                                    <!--<td>{{ $karyawan->atasan->nama ?? '-' }}</td>-->
                                    <td>
                                        @if($karyawan->status == 'aktif')
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('kelola-pengguna.show', $karyawan->id) }}" 
                                               class="btn btn-sm btn-outline-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Tidak ada data karyawan</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Menampilkan {{ $karyawans->firstItem() ?? 0 }} sampai {{ $karyawans->lastItem() ?? 0 }} 
                            dari {{ $karyawans->total() }} data
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Karyawan -->
<div class="modal fade" id="addKaryawanModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Karyawan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addKaryawanForm" method="POST" action="{{ route('kelola-pengguna-baru.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Lengkap *</label>
                                <input type="text" class="form-control" id="nama" name="nama" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                                <div class="invalid-feedback"></div>
                                <div class="form-text">Email akan digunakan sebagai username login</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nik" class="form-label">NIK *</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nik" 
                                       name="nik" 
                                       required 
                                       minlength="7" 
                                       maxlength="7"
                                       title="NIK harus terdiri dari tepat 7 huruf">
                                <div class="invalid-feedback"></div>
                                <div class="form-text">Nomor Induk Karyawan (tepat 7 huruf)</div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="department_id" class="form-label">Department *</label>
                                <select class="form-select" id="department_id" name="department_id" required>
                                    <option value="">Pilih Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->nama }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role_level_id" class="form-label">Jabatan *</label>
                                <select class="form-select" id="role_level_id" name="role_level_id" required>
                                    <option value="">Pilih Jabatan</option>
                                    @foreach($roleLevels as $role)
                                        <option value="{{ $role->id }}">{{ $role->nama }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="golongan_id" class="form-label">Golongan *</label>
                                <select class="form-select" id="golongan_id" name="golongan_id" required>
                                    <option value="">Pilih Golongan</option>
                                    @foreach($golongan as $gol)
                                        <option value="{{ $gol->id }}">{{ $gol->nama_golongan }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Informasi:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Password default: <strong>Gondowangi-123</strong></li>
                            <li>Karyawan akan diminta mengubah password saat login pertama</li>
                            <li>Status karyawan akan diset sebagai <strong>Aktif</strong></li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span class="spinner-border spinner-border-sm d-none" id="loadingSpinner"></span>
                        <span id="submitText">Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Karyawan -->
<div class="modal fade" id="editKaryawanModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editKaryawanForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_karyawan_id" name="karyawan_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_nama" class="form-label">Nama Lengkap *</label>
                                <input type="text" class="form-control" id="edit_nama" name="nama" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="edit_email" name="email" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_department_id" class="form-label">Department *</label>
                                <select class="form-select" id="edit_department_id" name="department_id" required>
                                    <option value="">Pilih Department</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->nama }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_role_level_id" class="form-label">Role Level *</label>
                                <select class="form-select" id="edit_role_level_id" name="role_level_id" required>
                                    <option value="">Pilih Role Level</option>
                                    @foreach($roleLevels as $role)
                                        <option value="{{ $role->id }}">{{ $role->nama }}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_atasan_id" class="form-label">Atasan</label>
                                <select class="form-select" id="edit_atasan_id" name="atasan_id">
                                    <option value="">Pilih Atasan</option>
                                    @foreach($atasanOptions as $atasan)
                                        <option value="{{ $atasan->id }}">{{ $atasan->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_status" class="form-label">Status *</label>
                                <select class="form-select" id="edit_status" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_phone" class="form-label">No. Telepon</label>
                                <input type="text" class="form-control" id="edit_phone" name="phone">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="edit_alamat" name="alamat" rows="2"></textarea>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const departmentFilter = document.getElementById('departmentFilter');
    const statusFilter = document.getElementById('statusFilter');
    const searchInput = document.getElementById('searchInput');
    const clearFilters = document.getElementById('clearFilters');

    function applyFilters() {
        const url = new URL(window.location.href);
        url.searchParams.set('department_id', departmentFilter.value);
        url.searchParams.set('status', statusFilter.value);
        url.searchParams.set('search', searchInput.value);
        
        if (!departmentFilter.value) url.searchParams.delete('department_id');
        if (!statusFilter.value) url.searchParams.delete('status');
        if (!searchInput.value) url.searchParams.delete('search');
        
        window.location.href = url.toString();
    }

    if (departmentFilter) departmentFilter.addEventListener('change', applyFilters);
    if (statusFilter) statusFilter.addEventListener('change', applyFilters);
    
    // Search with debounce
    let searchTimeout;
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyFilters, 500);
        });
    }

    if (clearFilters) {
        clearFilters.addEventListener('click', function() {
            window.location.href = window.location.pathname;
        });
    }

    // Add Karyawan Form
    const addKaryawanForm = document.getElementById('addKaryawanForm');
    const submitBtn = document.getElementById('submitBtn');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const submitText = document.getElementById('submitText');
    
    if (addKaryawanForm) {
        addKaryawanForm.addEventListener('submit', function(e) {
            e.preventDefault();
    
            // Reset previous validation states
            clearValidationErrors();
            
            // Show loading state
            setLoadingState(true);
    
            const formData = new FormData(this);
            
            // Get CSRF token from meta tag
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                setLoadingState(false);
                showAlert('danger', 'CSRF token tidak ditemukan. Silakan refresh halaman.');
                return;
            }

            fetch("{{ route('kelola-pengguna-baru.store') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                // Always try to parse JSON, even for error responses
                return response.json().then(data => {
                    if (!response.ok) {
                        // Log the full error response for debugging
                        console.log('Server response:', data);
                        console.log('Status:', response.status);
                        
                        // If it's a validation error (422), handle it properly
                        if (response.status === 422) {
                            return data; // Return the data to handle validation errors
                        }
                        
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return data;
                });
            })
            .then(data => {
                setLoadingState(false);
                
                if (data.success) {
                    showAlert('success', data.message);
                    
                    // Reset form
                    addKaryawanForm.reset();
                    
                    // Close modal
                    const modalElement = document.getElementById('addKaryawanModal');
                    if (modalElement) {
                        const modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) {
                            modal.hide();
                        }
                    }
                    
                    // Refresh halaman setelah 2 detik
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                    
                } else {
                    // Handle validation errors
                    if (data.errors) {
                        displayValidationErrors(data.errors);
                    }
                    showAlert('danger', data.message || 'Terjadi kesalahan saat menyimpan data');
                }
            })
            .catch(error => {
                setLoadingState(false);
                console.error('Error:', error);
                
                // More specific error handling
                if (error.message.includes('Failed to fetch')) {
                    showAlert('danger', 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.');
                } else if (error.message.includes('HTTP error')) {
                    showAlert('danger', 'Terjadi kesalahan server. Silakan coba lagi.');
                } else {
                    showAlert('danger', 'Terjadi kesalahan sistem. Silakan coba lagi.');
                }
            });
        });
    }
    
    // Helper functions
    function setLoadingState(loading) {
        if (submitBtn && loadingSpinner && submitText) {
            if (loading) {
                submitBtn.disabled = true;
                loadingSpinner.classList.remove('d-none');
                submitText.textContent = 'Menyimpan...';
            } else {
                submitBtn.disabled = false;
                loadingSpinner.classList.add('d-none');
                submitText.textContent = 'Simpan';
            }
        }
    }
    
    function clearValidationErrors() {
        // Remove all validation error classes and messages
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        document.querySelectorAll('.invalid-feedback').forEach(el => {
            el.textContent = '';
        });
    }
    
    function displayValidationErrors(errors) {
        Object.keys(errors).forEach(field => {
            const input = document.getElementById(field);
            if (input) {
                const feedback = input.parentNode.querySelector('.invalid-feedback');
                
                if (feedback) {
                    input.classList.add('is-invalid');
                    feedback.textContent = errors[field][0];
                } else {
                    // If no invalid-feedback div found, create one
                    const newFeedback = document.createElement('div');
                    newFeedback.className = 'invalid-feedback';
                    newFeedback.textContent = errors[field][0];
                    input.classList.add('is-invalid');
                    input.parentNode.appendChild(newFeedback);
                }
            }
        });
    }
    
    // Reset validation when modal is opened
    const addKaryawanModal = document.getElementById('addKaryawanModal');
    if (addKaryawanModal) {
        addKaryawanModal.addEventListener('shown.bs.modal', function() {
            clearValidationErrors();
            if (addKaryawanForm) {
                addKaryawanForm.reset();
            }
        });
    }
});

function deactivateKaryawan(id) {
    if (confirm('Apakah Anda yakin ingin menonaktifkan karyawan ini?')) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            showAlert('danger', 'CSRF token tidak ditemukan. Silakan refresh halaman.');
            return;
        }

        fetch(`{{ route("kelola-pengguna.index") }}/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
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
                showAlert('success', data.message);
                setTimeout(() => window.location.reload(), 1500);
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('danger', 'Terjadi kesalahan sistem');
        });
    }
}

function showDetailedAlert(type, message, details = '') {
    // Remove existing alerts
    document.querySelectorAll('.custom-alert').forEach(alert => alert.remove());
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed custom-alert`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 400px; max-width: 600px;';
    
    const detailsHtml = details ? `
        <hr class="my-2">
        <small class="text-muted">
            <strong>Detail:</strong><br>
            ${details}
        </small>
    ` : '';
    
    alertDiv.innerHTML = `
        <div class="d-flex align-items-start">
            <div class="flex-grow-1">
                <strong>${type === 'success' ? '✅ Berhasil!' : '❌ Error!'}</strong><br>
                ${message}
                ${detailsHtml}
            </div>
            <button type="button" class="btn-close ms-2" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 8 seconds (longer for detailed errors)
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 8000);
}

function showAlert(type, message) {
    showDetailedAlert(type, message);
}
</script>
@endsection
    