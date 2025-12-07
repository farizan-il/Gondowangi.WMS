@extends('Approval-app.Layout.approver-main')

@section('head')
<!-- Di bagian <head> -->
<meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .timeline-modern {
        position: relative;
        padding: 2rem 0;
        }
        
        /* Vertical line tengah */
        .timeline-modern::before {
        content: '';
        position: absolute;
        top: 0;
        bottom: 0;
        left: 50%;
        width: 2px;
        background: #e9ecef;
        }
        
        /* Item dasar */
        .timeline-modern .timeline-item {
        position: relative;
        width: 50%;
        padding: 1rem 2rem;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.6s ease-out;
        }
        
        /* Kartu */
        .timeline-modern .timeline-content {
        background: #fff;
        border: 2px solid #e0e0e0;
        border-radius: .5rem;
        border-radius: .5rrgb(201, 201, 201)
        box-shadow: 0 .25rem .5rem rgba(0,0,0,.1);
        padding: 1rem;
        }

        .timeline-modern .timeline-item.current .timeline-marker {
            background: #ffc107;
            border-color: #fff;
        }
        .timeline-modern .timeline-item.current .timeline-content {
            box-shadow: 0 .5rem 1rem rgba(0,0,0,.15);
        }
        
        /* Node bulat */
        .timeline-modern .timeline-item::after {
        content: '';
        position: absolute;
        top: 1.5rem;
        right: -9px; /* untuk kiri */
        width: 18px;
        height: 18px;
        background: #fff;
        border: 3px solid #49976d;
        border-radius: 50%;
        }
        
        /* Ganjil di kiri */
        .timeline-modern .timeline-item:nth-child(odd) {
        left: 0;
        text-align: right;
        }
        
        .timeline-modern .timeline-item:nth-child(odd) .timeline-content {
        margin-right: 2rem;
        }
        
        .timeline-modern .timeline-item:nth-child(odd)::after {
        right: -9px;
        }
        
        /* Genap di kanan */
        .timeline-modern .timeline-item:nth-child(even) {
        left: 50%;
        }
        
        .timeline-modern .timeline-item:nth-child(even) .timeline-content {
        margin-left: 2rem;
        }
        
        .timeline-modern .timeline-item:nth-child(even)::after {
        left: -9px;
        }
        
        /* State tampilan setelah muncul */
        .timeline-modern .timeline-item.show {
        opacity: 1;
        transform: translateY(0);
        }
        
        .spinner-border-sm {
    width: 1rem;
    height: 1rem;
}

.table th {
    font-weight: 600;
    background-color: #f8f9fa;
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.modal-xl {
    max-width: 1200px;
}

.form-control-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
}

.table-responsive {
    border-radius: 0.375rem;
}

.table-bordered th,
.table-bordered td {
    border: 1px solid #dee2e6;
}

.fw-bold {
    font-weight: 700 !important;
}

.text-center {
    text-align: center !important;
}

.text-start {
    text-align: left !important;
}

.text-end {
    text-align: right !important;
}

.me-2 {
    margin-right: 0.5rem !important;
}

.mb-4 {
    margin-bottom: 1.5rem !important;
}

.mt-4 {
    margin-top: 1.5rem !important;
}

.mt-2 {
    margin-top: 0.5rem !important;
}

.alert {
    margin-bottom: 1rem;
    border: 1px solid transparent;
    border-radius: 0.375rem;
    padding: 0.75rem 1.25rem;
}

.alert-success {
    color: #0f5132;
    background-color: #d1e7dd;
    border-color: #badbcc;
}

.alert-danger {
    color: #842029;
    background-color: #f8d7da;
    border-color: #f5c2c7;
}
    </style>
    
    <style>
        /* Timeline Approval Styles */
.timeline-approval {
    position: relative;
    padding-left: 30px;
}

.timeline-approval::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
    padding-bottom: 15px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #fff;
    border: 2px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
    z-index: 1;
}

.timeline-success .timeline-marker {
    background: #d4edda;
    border-color: #28a745;
    color: #28a745;
}

.timeline-info .timeline-marker {
    background: #d1ecf1;
    border-color: #17a2b8;
    color: #17a2b8;
}

.timeline-warning .timeline-marker {
    background: #fff3cd;
    border-color: #ffc107;
    color: #856404;
}

.timeline-danger .timeline-marker {
    background: #f8d7da;
    border-color: #dc3545;
    color: #dc3545;
}

.timeline-secondary .timeline-marker {
    background: #e2e3e5;
    border-color: #6c757d;
    color: #6c757d;
}

.timeline-dark .timeline-marker {
    background: #d6d8db;
    border-color: #343a40;
    color: #343a40;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #e9ecef;
}

.timeline-success .timeline-content {
    border-left-color: #28a745;
}

.timeline-info .timeline-content {
    border-left-color: #17a2b8;
}

.timeline-warning .timeline-content {
    border-left-color: #ffc107;
}

.timeline-danger .timeline-content {
    border-left-color: #dc3545;
}

.timeline-secondary .timeline-content {
    border-left-color: #6c757d;
}

.timeline-dark .timeline-content {
    border-left-color: #343a40;
}

.timeline-content h6 {
    margin-bottom: 5px;
    font-weight: 600;
}

.timeline-content small {
    color: #6c757d;
}

.timeline-content .text-info {
    color: #17a2b8 !important;
}

/* Badge styles */
.badge.badge-sm {
    font-size: 0.75em;
    padding: 0.25em 0.5em;
}

/* Modal improvements */
.modal-xl {
    max-width: 1200px;
}

/* Table improvements */
.table-responsive {
    border-radius: 0.375rem;
    overflow: hidden;
}

.table th {
    background-color: #f8f9fa;
    border-top: none;
    font-weight: 600;
    color: #495057;
}

/* Status badges */
.badge {
    font-size: 0.875em;
}

.bg-success {
    background-color: #28a745 !important;
}

.bg-warning {
    background-color: #ffc107 !important;
    color: #212529 !important;
}

.bg-info {
    background-color: #17a2b8 !important;
}

.bg-danger {
    background-color: #dc3545 !important;
}

.bg-secondary {
    background-color: #6c757d !important;
}

.bg-dark {
    background-color: #343a40 !important;
}

/* Button improvements */
.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

/* Form improvements */
.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.form-control:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* Card improvements */
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.card-header {
    background-color: rgba(0, 0, 0, 0.03);
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}
    </style>
@endsection

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <div class="page-header-title">
          <h5 class="m-b-10">Dashboard Approver</h5>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
  <div class="col-12">
    <div class="card table-card">
      <div class="card-header">
        <h5>Daftar Pengajuan ({{Auth::user()->id}})</h5>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Area</th>
                        <th>Periode</th>
                        <th>Tipe Pengajuan</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuanList as $pengajuan)
                    <tr>
                        <td>{{ $pengajuan->nomor_pengajuan }}</td>
                        <td>{{ $pengajuan->requester->nama ?? 'N/A' }}</td>
                        <td>{{ $pengajuan->requester->area ?? 'N/A' }}</td>
                        <td>
                            @php
                                $tanggalMulai = \Carbon\Carbon::parse($pengajuan->tanggal_pengajuan)->format('d/m/Y');
                                $tanggalSelesai = \Carbon\Carbon::parse($pengajuan->tanggal_kebutuhan)->format('d/m/Y');
                            @endphp
                            {{ $tanggalMulai }} s/d {{ $tanggalSelesai }}
                        </td>
                        <td>{{ $pengajuan->kategoriPengajuan->nama ?? 'N/A' }}</td>
                        <td>
                            @php
                                $statusClass = match($pengajuan->status_pengajuan) {
                                    'approved' => 'bg-success',
                                    'proses' => 'bg-warning',
                                    'in_progress' => 'bg-info',
                                    'rejected' => 'bg-danger',
                                    'revision' => 'bg-secondary',
                                    default => 'bg-light'
                                };
                                
                                $statusText = match($pengajuan->status_pengajuan) {
                                    'approved' => 'Disetujui',
                                    'proses' => 'Menunggu',
                                    'in_progress' => 'Proses',
                                    'rejected' => 'Ditolak',
                                    'revision' => 'Revisi',
                                    default => 'Unknown'
                                };
                            @endphp
                            <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-info" onclick="showDetail({{ $pengajuan->id }})">
                                Detail
                            </button>
                            <button class="btn btn-sm btn-primary" onclick="editPengajuan({{ $pengajuan->id }})">
                                Edit
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="changeStatus({{ $pengajuan->id }})">
                                Ubah Status
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data pengajuan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
      </div>                        
    </div>
  </div>
</div>
<!-- [ Main Content ] end -->

<!-- Edit Modal (Dynamic) -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <form id="editForm">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">Edit Pengajuan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="editModalBody">
          <!-- Content will be loaded dynamically -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Status Modal (Dynamic) -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="statusForm">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="statusModalLabel">Ubah Status Pengajuan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select class="form-select" id="statusSelect" name="status" required>
              <option value="approved">Diterima</option>
              <option value="revision">Revisi</option>
              <option value="rejected">Ditolak</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Catatan</label>
            <textarea class="form-control" id="statusCatatan" name="catatan" rows="3" placeholder="Berikan catatan untuk perubahan status ini..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">Simpan Status</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Detail Modal (Dynamic) -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel">Detail Pengajuan</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="detailModalBody">
        <!-- Content will be loaded dynamically -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

@endsection

@section('script')
<script>
    // Show detail pengajuan
    function showDetail(id) {
        currentPengajuanId = id;
        
        fetch(`/approvarLaporanPengajuan/${id}`)
            .then(response => response.json())
            .then(data => {
                if (!data.success && data.success === false) {
                    alert(data.message);
                    return;
                }
                
                document.getElementById('detailModalLabel').textContent = `Detail Pengajuan #${data.nomor_pengajuan}`;
                
                let detailHtml = `
                    <div class="row">
                        <div class="col-md-8">
                            <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th style="width: 35%;">Nomor Pengajuan</th>
                                            <td>${data.nomor_pengajuan}</td>
                                        </tr>
                                        <tr>
                                            <th>Nama Pemohon</th>
                                            <td>${data.requester?.nama || 'N/A'}</td>
                                        </tr>
                                        <tr>
                                            <th>Department</th>
                                            <td>${data.requester?.department?.nama || 'N/A'}</td>
                                        </tr>
                                        <tr>
                                            <th>Jabatan</th>
                                            <td>${data.requester?.jabatan || 'N/A'}</td>
                                        </tr>
                                        <tr>
                                            <th>Judul</th>
                                            <td>${data.judul || 'tidak ada'}</td>
                                        </tr>
                                        <tr>
                                            <th>Deskripsi</th>
                                            <td>${data.deskripsi || 'tidak ada'}</td>
                                        </tr>
                                        
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <span class="badge bg-${getStatusClass(data.status_pengajuan)}">
                                                    ${getStatusText(data.status_pengajuan)}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Step Saat Ini</th>
                                            <td>${data.current_step} dari ${data.total_step}</td>
                                        </tr>
                                    </tbody>
                                </table>
                        </div>
                        <div class="col-md-4">
                            <h6>Progress Approval:</h6>
                            <div class="timeline-approval">
                `;
                
                // Add progress approval timeline
                if (data.progress_approvals && data.progress_approvals.length > 0) {
                    data.progress_approvals.forEach((progress, index) => {
                        const statusIcon = getProgressIcon(progress.status);
                        const statusClass = getProgressClass(progress.status);
                        
                        detailHtml += `
                            <div class="timeline-item ${statusClass}">
                                <div class="timeline-marker">${statusIcon}</div>
                                <div class="timeline-content">
                                    <h6>${progress.step_name}</h6>
                                    <small>${progress.department?.nama || ''} - ${progress.role_level?.nama || ''}</small>
                                    <div class="mt-1">
                                        <span class="badge badge-sm bg-${getStatusClass(progress.status)}">${getStatusText(progress.status)}</span>
                                    </div>
                                    ${progress.approver ? `<small class="text-muted">Oleh: ${progress.approver.nama}</small>` : ''}
                                    ${progress.tanggal_approval ? `<small class="text-muted d-block">Tanggal: ${formatDate(progress.tanggal_approval)}</small>` : ''}
                                    ${progress.catatan ? `<small class="text-info d-block">Catatan: ${progress.catatan}</small>` : ''}
                                </div>
                            </div>
                        `;
                    });
                }
                
                detailHtml += `
                            </div>
                        </div>
                    </div>
                `;
                
                // Add detail pengajuan if exists
                if (data.detail_pengajuan && data.detail_pengajuan.length > 0) {
                    detailHtml += `
                        <hr>
                        <h6>Detail Pengajuan:</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Field</th>
                                        <th>Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    data.detail_pengajuan.forEach(detail => {
                        detailHtml += `
                            <tr>
                                <td>${detail.form_field?.label || detail.form_field?.nama_field || 'N/A'}</td>
                                <td>${detail.nilai || 'N/A'}</td>
                            </tr>
                        `;
                    });
                    
                    detailHtml += `
                                </tbody>
                            </table>
                        </div>
                    `;
                }
                
                document.getElementById('detailModalBody').innerHTML = detailHtml;
                new bootstrap.Modal(document.getElementById('detailModal')).show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memuat detail pengajuan');
            });
    }
    
    // Edit pengajuan
    function editPengajuan(id) {
        currentPengajuanId = id;
        
        fetch(`/approvarLaporanPengajuan/${id}/edit`)
            .then(response => response.json())
            .then(data => {
                if (!data.success && data.success === false) {
                    alert(data.message);
                    return;
                }
                
                document.getElementById('editModalLabel').textContent = `Edit Pengajuan #${data.nomor_pengajuan}`;
                
                // Generate edit form based on pengajuan data
                let editHtml = generateEditForm(data);
                
                document.getElementById('editModalBody').innerHTML = editHtml;
                new bootstrap.Modal(document.getElementById('editModal')).show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat memuat data pengajuan');
            });
    }
    
    // Change status
    function changeStatus(id) {
        currentPengajuanId = id;
        
        // Reset form
        document.getElementById('statusForm').reset();
        document.getElementById('statusModalLabel').textContent = `Ubah Status Pengajuan #${id}`;
        
        new bootstrap.Modal(document.getElementById('statusModal')).show();
    }
    
    // Generate edit form dynamically
    function generateEditForm(data) {
        let html = `
            <div class="mb-3">
                <label class="form-label">Judul Pengajuan</label>
                <input type="text" class="form-control" name="judul" value="${data.judul || ''}" readonly>
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea class="form-control" name="deskripsi" rows="3" readonly>${data.deskripsi || ''}</textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Catatan Approver</label>
                <textarea class="form-control" name="catatan_approver" rows="3" placeholder="Berikan catatan untuk pengajuan ini..."></textarea>
            </div>
        `;
        
        // Add form fields based on kategori pengajuan
        if (data.detail_pengajuan && data.detail_pengajuan.length > 0) {
            html += '<hr><h6>Detail Pengajuan:</h6>';
            
            data.detail_pengajuan.forEach((detail, index) => {
                const field = detail.form_field;
                const nilai = detail.nilai;
                
                html += `
                    <div class="mb-3">
                        <label class="form-label">${field?.label || field?.nama_field || 'Field ' + (index + 1)}</label>
                `;
                
                switch (field?.tipe_field) {
                    case 'textarea':
                        html += `<textarea class="form-control" name="detail[${detail.id}]" rows="3">${nilai || ''}</textarea>`;
                        break;
                    case 'number':
                    case 'currency':
                        html += `<input type="number" class="form-control" name="detail[${detail.id}]" value="${nilai || ''}">`;
                        break;
                    case 'date':
                        html += `<input type="date" class="form-control" name="detail[${detail.id}]" value="${nilai || ''}">`;
                        break;
                    default:
                        html += `<input type="text" class="form-control" name="detail[${detail.id}]" value="${nilai || ''}">`;
                }
                
                html += '</div>';
            });
        }
        
        return html;
    }
    
    // Handle edit form submission
    document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(`/approvarLaporanPengajuan/${currentPengajuanId}`, {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(Object.fromEntries(formData))
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Pengajuan berhasil diperbarui');
                bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
                location.reload();
            } else {
                alert('Terjadi kesalahan: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan perubahan');
        });
    });
    
    document.getElementById('statusForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const status = formData.get('status');
    
    // Konfirmasi sebelum mengubah status
    const confirmMessage = getConfirmMessage(status);
    if (!confirm(confirmMessage)) {
        return;
    }
    
    fetch(`/approvarLaporanPengajuan/${currentPengajuanId}/status`, {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Status pengajuan berhasil diperbarui');
            bootstrap.Modal.getInstance(document.getElementById('statusModal')).hide();
            location.reload();
        } else {
            alert('Terjadi kesalahan: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengubah status');
    });
});

// Helper functions - Updated
function getStatusClass(status) {
    const statusClasses = {
        'approved': 'success',
        'pending': 'warning',
        'proses': 'info',
        'rejected': 'danger',
        'revision': 'secondary',
        'cancelled': 'dark',
        'in_progress': 'info' // Tambahan untuk in_progress
    };
    return statusClasses[status] || 'light';
}

function getStatusText(status) {
    const statusTexts = {
        'approved': 'Disetujui',
        'pending': 'Menunggu',
        'proses': 'Sedang Diproses',
        'in_progress': 'Sedang Diproses',
        'rejected': 'Ditolak',
        'revision': 'Revisi',
        'cancelled': 'Dibatalkan'
    };
    return statusTexts[status] || 'Unknown';
}

function getProgressIcon(status) {
    const icons = {
        'approved': '✓',
        'pending': '⏳',
        'proses': '🔄',
        'in_progress': '🔄',
        'rejected': '✗',
        'revision': '↩',
        'cancelled': '⚫'
    };
    return icons[status] || '?';
}

function getProgressClass(status) {
    const classes = {
        'approved': 'timeline-success',
        'pending': 'timeline-warning',
        'proses': 'timeline-info',
        'in_progress': 'timeline-info',
        'rejected': 'timeline-danger',
        'revision': 'timeline-secondary',
        'cancelled': 'timeline-dark'
    };
    return classes[status] || '';
}

function getConfirmMessage(status) {
    const messages = {
        'approved': 'Apakah Anda yakin ingin menyetujui pengajuan ini? Pengajuan akan diteruskan ke tahap berikutnya atau diselesaikan jika ini adalah tahap terakhir.',
        'rejected': 'Apakah Anda yakin ingin menolak pengajuan ini? Pengajuan akan dihentikan dan tidak dapat dilanjutkan.',
        'revision': 'Apakah Anda yakin ingin meminta revisi untuk pengajuan ini? Pengajuan akan dikembalikan ke pemohon untuk diperbaiki.'
    };
    return messages[status] || 'Apakah Anda yakin ingin mengubah status pengajuan ini?';
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}
</script>
@endsection