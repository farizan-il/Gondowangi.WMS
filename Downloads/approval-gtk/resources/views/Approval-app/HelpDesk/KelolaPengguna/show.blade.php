@extends('Approval-app.Layout.main-admin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-header mb-0">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                         <!--ini samping kiri-->
                            <div>
                                <h4 class="mb-1">Detail Karyawan</h4>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item">
                                            <a href="{{ route('kelola-pengguna.index') }}">Kelola Pengguna</a>
                                        </li>
                                        <li class="breadcrumb-item active">{{ $karyawan->nama }}</li>
                                    </ol>
                                </nav>
                            </div>
                            
                            <!--ini samping kanan-->
                            <div class="d-flex gap-2">
                                <a href="{{ route('kelola-pengguna.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="button" class="btn btn-warning" onclick="editKaryawan({{ $karyawan->id }})">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button type="button" class="btn btn-info" onclick="resetPassword({{ $karyawan->id }})">
                                    <i class="fas fa-key"></i> Reset Password
                                </button>
                            </div>
                    </div>
                </div>
            </div>
                    
                    
            

            <div class="row">
                <!-- Profile Information -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="avatar-lg mx-auto mb-3">
                                <!--<div class="avatar-title rounded-circle bg-primary text-white fs-2">-->
                                <!--    {{ substr($karyawan->nama, 0, 2) }}-->
                                <!--</div>-->
                                <img src="https://png.pngtree.com/png-clipart/20220909/original/pngtree-cartoon-man-avatar-vector-ilustration-png-image_8515463.png" class="rounded-circle" style="width: 90px;">
                            </div>
                            <h5 class="mb-1">{{ $karyawan->nama }}</h5>
                            <p class="text-muted mb-3">{{ $karyawan->email }}</p>
                            
                            @if($karyawan->status == 'aktif')
                                <span class="badge bg-success fs-6">Active</span>
                            @else
                                <span class="badge bg-secondary fs-6">Inactive</span>
                            @endif
                        </div>
                    </div>

                    <!-- Basic Information -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Informasi Dasar</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="ps-0 fw-medium">Department:</td>
                                            <td class="pe-0">
                                                @if($karyawan->department)
                                                    <span class="badge bg-info">{{ $karyawan->department->nama }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 fw-medium">Role Level:</td>
                                            <td class="pe-0">{{ $karyawan->roleLevel->nama ?? '-' }}</td>
                                        </tr>
                                        <!--<tr>-->
                                        <!--    <td class="ps-0 fw-medium">Atasan:</td>-->
                                        <!--    <td class="pe-0">{{ $karyawan->atasan->nama ?? '-' }}</td>-->
                                        <!--</tr>-->
                                        <!--<tr>-->
                                        <!--    <td class="ps-0 fw-medium">No. Telepon:</td>-->
                                        <!--    <td class="pe-0">{{ $karyawan->phone ?? '-' }}</td>-->
                                        <!--</tr>-->
                                        <tr>
                                            <td class="ps-0 fw-medium">Bergabung:</td>
                                            <td class="pe-0">{{ $karyawan->created_at->format('d M Y') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            @if($karyawan->alamat)
                            <div class="mt-3">
                                <h6 class="fw-medium">Alamat:</h6>
                                <p class="text-muted mb-0">{{ $karyawan->alamat }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Account Information -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Informasi Akun</h6>
                        </div>
                        <div class="card-body">
                            @if($karyawan)
                            <div class="table-responsive">
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                        <tr>
                                            <td class="ps-0 fw-medium">User ID:</td>
                                            <td class="pe-0">#{{ $karyawan->id }}</td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 fw-medium">Status Akun:</td>
                                            <td class="pe-0">
                                                @if($karyawan->deleted_at)
                                                    <span class="badge bg-danger">Nonaktif</span>
                                                @else
                                                    <span class="badge bg-success">Aktif</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="ps-0 fw-medium">Terakhir Login:</td>
                                            <td class="pe-0">
                                                {{ $karyawan->last_login_at ? $karyawan->last_login_at->format('d M Y H:i') : 'Belum pernah login' }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center text-muted">
                                <i class="fas fa-user-slash fa-2x mb-2"></i>
                                <p>Akun user belum dibuat</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="col-lg-8">
                    <!-- Statistik -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="mb-0 text-white">{{ $karyawan->pengajuan()->count() }}</h5>
                                            <small>Total Pengajuan</small>
                                        </div>
                                        <i class="fas fa-file-alt fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="mb-0">{{ $karyawan->pengajuan()->where('status_pengajuan', 'completed')->count() }}</h5>
                                            <small>Disetujui</small>
                                        </div>
                                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="mb-0">
                                                {{ $karyawan->pengajuan()->whereIn('status_pengajuan', ['proses', 'settlement_created', 'proses_settlement'])->count() }}
                                            </h5>
                                            <small>Pending</small>
                                        </div>
                                        <i class="fas fa-clock fa-2x opacity-50"></i>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h5 class="mb-0">{{ $karyawan->pengajuan()->where('status_pengajuan', 'rejected')->count() }}</h5>
                                            <small>Ditolak</small>
                                        </div>
                                        <i class="fas fa-times-circle fa-2x opacity-50"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Otoritas Pengajuan -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Otoritas Pengajuan</h6>
                        </div>
                        <div class="card-body">
                            @if($otoritasPengajuan->count() > 0)
                                <div class="row">
                                    @foreach($otoritasPengajuan as $kategoriId => $flows)
                                        @php $kategori = $flows->first()->kategoriPengajuan; @endphp
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-success">
                                                <div class="card-header bg-success text-white py-2">
                                                    <h6 class="mb-0">{{ $kategori->nama }}</h6>
                                                </div>
                                                <div class="card-body py-2">
                                                    <small class="text-muted">
                                                        <i class="fas fa-paper-plane"></i>
                                                        Dapat mengajukan: {{ $kategori->nama }}
                                                    </small>
                                                    <div class="mt-2" style="display: flex; align-items: center; flex-wrap: wrap; gap: 8px;">
                                                        @foreach($flows as $index => $flow)
                                                            <span class="badge bg-light text-dark" style="padding: 6px 10px; border-radius: 20px; font-size: 12px; white-space: nowrap;">
                                                                {{ $flow->urutan }}. {{ $flow->approver->nama}}
                                                            </span>
                                                            @if(!$loop->last)
                                                                <i class="fas fa-arrow-right text-success" style="font-size: 14px; margin: 0 2px;"></i>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-file-alt fa-3x mb-3"></i>
                                    <h6>Tidak ada otoritas pengajuan</h6>
                                    <p class="mb-0">Karyawan ini belum memiliki otoritas untuk membuat pengajuan apapun</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Otoritas Approval -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">Otoritas Approval</h6>
                        </div>
                        <div class="card-body">
                            @if($otoritasApproval->count() > 0)
                            <div class="row">
                                @foreach($otoritasApproval as $kategoriId => $flows)
                                    @php $kategori = $flows->first()->kategoriPengajuan; @endphp
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-warning">
                                            <div class="card-header bg-warning text-dark py-2">
                                                <h6 class="mb-0">{{ $kategori->nama }}</h6>
                                            </div>
                                            <div class="card-body py-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-check-circle"></i> 
                                                    Berperan sebagai approver
                                                </small>
                                                <div class="mt-2">
                                                    @foreach($flows as $flow)
                                                    <span class="badge bg-light text-dark me-1">
                                                        Step {{ $flow->urutan }}: {{ $flow->approver->nama }}
                                                    </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-user-check fa-3x mb-3"></i>
                                <h6>Tidak ada otoritas approval</h6>
                                <p class="mb-0">Karyawan ini belum memiliki otoritas untuk menyetujui pengajuan apapun</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Riwayat Pengajuan -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0">Riwayat Pengajuan (10 Terakhir)</h6>
                            @if($karyawan->pengajuan->count() > 10)
                            <small class="text-muted">
                                <a href="#" class="text-decoration-none">Lihat Semua ({{ $karyawan->pengajuan()->count() }})</a>
                            </small>
                            @endif
                        </div>
                        <div class="card-body">
                            @if($karyawan->pengajuan->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Nomor</th>
                                            <th>Kategori</th>
                                            <th>Tanggal</th>
                                            <th>Status</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($karyawan->pengajuan as $pengajuan)
                                        <tr>
                                            <td>
                                                <a href="#" class="text-decoration-none">
                                                    {{ $pengajuan->nomor_pengajuan }}
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $pengajuan->kategoriPengajuan->nama ?? '-' }}</span>
                                            </td>
                                            <td>{{ $pengajuan->created_at->format('d M Y') }}</td>
                                            <td>
                                                @switch($pengajuan->status_pengajuan)
                                                    @case('draft')
                                                        <span class="badge bg-secondary">Draft</span>
                                                        @break
                                                    @case('proses')
                                                        <span class="badge bg-warning">Proses</span>
                                                        @break
                                                    @case('proses_settlement')
                                                        <span class="badge bg-warning">Proses Settlement</span>
                                                        @break
                                                    @case('completed')
                                                        <span class="badge bg-success">Pengajuan Selesai</span>
                                                        @break
                                                    @case('rejected')
                                                        <span class="badge bg-danger">Rejected</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-light text-dark">{{ $pengajuan->status }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                @if($pengajuan->nominal_pengajuan)
                                                    <storng class="text-primary">Rp {{ number_format($pengajuan->nominal_pengajuan, 0, ',', '.') }}</storng>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <h6>Belum ada pengajuan</h6>
                                <p class="mb-0">Karyawan ini belum pernah membuat pengajuan</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Karyawan (sama seperti di index.blade.php) -->
<div class="modal fade" id="editKaryawanModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editKaryawanForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_karyawan_id" name="karyawan_id" value="{{ $karyawan->id }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_nama" class="form-label">Nama Lengkap *</label>
                                <input type="text" class="form-control" id="edit_nama" name="nama" value="{{ $karyawan->nama }}" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="edit_email" name="email" value="{{ $karyawan->email }}" required>
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
                                    @foreach(App\Models\Department::orderBy('nama')->get() as $dept)
                                        <option value="{{ $dept->id }}" {{ $karyawan->department_id == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->nama }}
                                        </option>
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
                                    @foreach(App\Models\RoleLevel::orderBy('nama')->get() as $role)
                                        <option value="{{ $role->id }}" {{ $karyawan->role_level_id == $role->id ? 'selected' : '' }}>
                                            {{ $role->nama }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <!--<div class="row">-->
                    <!--    <div class="col-md-6">-->
                    <!--        <div class="mb-3">-->
                    <!--            <label for="edit_atasan_id" class="form-label">Atasan</label>-->
                    <!--            <select class="form-select" id="edit_atasan_id" name="atasan_id">-->
                    <!--                <option value="">Pilih Atasan</option>-->
                    <!--                @foreach(App\Models\Karyawan::where('status', 'active')->where('id', '!=', $karyawan->id)->orderBy('nama')->get() as $atasan)-->
                    <!--                    <option value="{{ $atasan->id }}" {{ $karyawan->atasan_id == $atasan->id ? 'selected' : '' }}>-->
                    <!--                        {{ $atasan->nama }}-->
                    <!--                    </option>-->
                    <!--                @endforeach-->
                    <!--            </select>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--    <div class="col-md-6">-->
                    <!--        <div class="mb-3">-->
                    <!--            <label for="edit_status" class="form-label">Status *</label>-->
                    <!--            <select class="form-select" id="edit_status" name="status" required>-->
                    <!--                <option value="active" {{ $karyawan->status == 'aktif' ? 'selected' : '' }}>Active</option>-->
                    <!--            </select>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->

                    <!--<div class="row">-->
                    <!--    <div class="col-md-6">-->
                    <!--        <div class="mb-3">-->
                    <!--            <label for="edit_phone" class="form-label">No. Telepon</label>-->
                    <!--            <input type="text" class="form-control" id="edit_phone" name="phone" value="{{ $karyawan->phone }}">-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->

                    <!--<div class="mb-3">-->
                    <!--    <label for="edit_alamat" class="form-label">Alamat</label>-->
                    <!--    <textarea class="form-control" id="edit_alamat" name="alamat" rows="2">{{ $karyawan->alamat }}</textarea>-->
                    <!--</div>-->
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
        // Edit Karyawan Form
        document.getElementById('editKaryawanForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const karyawanId = document.getElementById('edit_karyawan_id').value;
            const formData = new FormData(this);
            
            fetch(`{{ route("kelola-pengguna.index") }}/${karyawanId}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('editKaryawanModal')).hide();
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
        });
    });
    
    function editKaryawan(id) {
        new bootstrap.Modal(document.getElementById('editKaryawanModal')).show();
    }
    
    function resetPassword(id) {
        if (confirm('Apakah Anda yakin ingin mereset password ke default?')) {
            fetch(`{{ route("kelola-pengguna.index") }}/${id}/reset-password`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
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
    
    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);
        
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
</script>

@endsection