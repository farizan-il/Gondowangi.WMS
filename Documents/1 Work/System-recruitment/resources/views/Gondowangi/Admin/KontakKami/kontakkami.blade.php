@extends('Gondowangi.Admin.Layout.main')
@section('head')
    <!-- Boxicons CSS CDN -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        .badge-status {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        .status-pending { background-color: #ffc107; color: #000; }
        .status-responded { background-color: #17a2b8; color: #fff; }
        .status-resolved { background-color: #28a745; color: #fff; }
        
        .unread-row {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        
        .action-buttons {
            white-space: nowrap;
        }
        
        .modal-body {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .contact-info {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
            background-color: #f8f9fa;
            margin-bottom: 1rem;
        }
    </style>
@endsection

@section('content')
<div class="content-wrapper">
    <!-- Success/Error Messages -->
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

    <!-- Page Header -->
    <div class="row mb-3">
        <div class="col-sm-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Manajemen Kontak</h4>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Daftar Kontak</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="contactsTable" class="table table-bordered dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nama Lengkap</th>
                                    <th>Email</th>
                                    <th>Subjek</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contacts as $index => $contact)
                                <tr class="{{ !$contact->is_read ? 'unread-row' : '' }}">
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        {{ $contact->nama_lengkap }}
                                        @if(!$contact->is_read)
                                            <span class="text-danger" style="font-size: 12px;">(belum dibaca)</span>
                                        @endif
                                    </td>
                                    <td>{{ $contact->alamat_email }}</td>
                                    <td>{{ $contact->subjek ?? '-' }}</td>
                                    <td>
                                        @if($contact->status == 'pending')
                                            <span class="badge status-pending">Menunggu</span>
                                        @elseif($contact->status == 'responded')
                                            <span class="badge status-responded">Direspon</span>
                                        @else
                                            <span class="badge status-resolved">Selesai</span>
                                        @endif
                                    </td>
                                    <td>{{ $contact->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="action-buttons">
                                        <button type="button" class="btn btn-sm btn-info" data-contact-id="{{ $contact->id }}" onclick="viewContact(this)">
                                            <i class="bx bx-show"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal View Contact (READ) -->
<div class="modal fade" id="viewContactModal" tabindex="-1" aria-labelledby="viewContactModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewContactModalLabel">Detail Kontak</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Nama Lengkap:</strong>
                        <p id="view_nama_lengkap"></p>
                    </div>
                    <div class="col-md-6">
                        <strong>Email:</strong>
                        <p id="view_alamat_email"></p>
                    </div>
                    
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Subjek:</strong>
                        <p id="view_subjek"></p>
                    </div>
                    <div class="col-md-6">
                        
                        <strong>Tanggal Dibuat:</strong>
                        <p id="view_created_at"></p>
                        <p id="view_status"></p>
                        
                        <strong>Status Baca:</strong>
                        <p id="view_is_read"></p>
                    </div>
                    
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <strong>Komentar/Pesan:</strong>
                        <p id="view_komentar_pesan" style="white-space: pre-wrap;"></p>
                    </div>
                    
                </div>
                <div class="row">
                    
                    <div class="col-md-6">
                        
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Delete -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus kontak ini?</p>
                <div class="alert alert-warning">
                    <strong>Perhatian:</strong> Data yang dihapus tidak dapat dikembalikan!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
            </div>
        </div>
    </div>
</div>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
 // Pastikan jQuery dan DataTables sudah loaded
$(document).ready(function() {
    // Inisialisasi DataTable jika belum ada
    if (!$.fn.DataTable.isDataTable('#contactsTable')) {
        $('#contactsTable').DataTable({
            "responsive": true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
            }
        });
    }
});

// Fungsi untuk view contact dengan auto mark as complete
function viewContact(button) {
    const contactId = button.getAttribute('data-contact-id');
    
    // Ambil data contact
    fetch(`{{ url('admin/contacts') }}/${contactId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const contact = data.contact;
            
            // Populate modal dengan data
            document.getElementById('view_nama_lengkap').textContent = contact.nama_lengkap;
            document.getElementById('view_alamat_email').textContent = contact.alamat_email;
            document.getElementById('view_subjek').textContent = contact.subjek || '-';
            document.getElementById('view_komentar_pesan').textContent = contact.komentar_pesan;
            document.getElementById('view_created_at').textContent = new Date(contact.created_at).toLocaleString('id-ID');
            document.getElementById('view_is_read').textContent = contact.is_read ? 'Sudah dibaca' : 'Belum dibaca';
            
            // Update status display
            let statusBadge = '';
            if (contact.status === 'pending') {
                statusBadge = '<span class="badge status-pending">Menunggu</span>';
            } else if (contact.status === 'responded') {
                statusBadge = '<span class="badge status-responded">Direspon</span>';
            } else {
                statusBadge = '<span class="badge status-resolved">Selesai</span>';
            }
            document.getElementById('view_status').innerHTML = '<strong>Status:</strong><br>' + statusBadge;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('viewContactModal'));
            modal.show();
            
            // Auto mark as complete setelah modal dibuka
            setTimeout(() => {
                markAsComplete(contactId, button);
            }, 1000); // Delay 1 detik agar user sempat melihat
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Gagal memuat data kontak'
        });
    });
}

// Fungsi untuk mark as complete
function markAsComplete(contactId, buttonElement) {
    fetch(`{{ url('admin/contacts') }}/${contactId}/mark-complete`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update status di tabel tanpa refresh
            updateTableRow(contactId, data.contact);
            
            // Update status di modal jika masih terbuka
            document.getElementById('view_status').innerHTML = 
                '<strong>Status:</strong><br><span class="badge status-resolved">Selesai</span>';
            document.getElementById('view_is_read').textContent = 'Sudah dibaca';
            
            // Update badge counter di navigation
            updateMessageBadge();
            
            // Show success toast (optional)
            showToast('success', 'Pesan telah ditandai sebagai selesai');
        }
    })
    .catch(error => {
        console.error('Error marking as complete:', error);
    });
}

// Fungsi untuk update row di tabel
function updateTableRow(contactId, contactData) {
    try {
        // Cek apakah DataTable sudah diinisialisasi
        if ($.fn.DataTable.isDataTable('#contactsTable')) {
            const table = $('#contactsTable').DataTable();
            
            // Cari row berdasarkan contact ID
            table.rows().every(function(rowIdx, tableLoop, rowLoop) {
                const row = this.node();
                const button = row.querySelector(`[data-contact-id="${contactId}"]`);
                
                if (button) {
                    // Remove unread styling
                    row.classList.remove('unread-row');
                    
                    // Update nama lengkap (remove "belum dibaca" text)
                    const namaCell = row.cells[1];
                    const namaText = namaCell.textContent.replace(' (belum dibaca)', '');
                    namaCell.innerHTML = namaText;
                    
                    // Update status badge
                    const statusCell = row.cells[4];
                    statusCell.innerHTML = '<span class="badge status-resolved">Selesai</span>';
                    
                    // Redraw the row
                    table.row(row).invalidate('dom').draw(false);
                }
            });
        } else {
            // Fallback: Update DOM langsung tanpa DataTable API
            updateTableRowDirectly(contactId, contactData);
        }
    } catch (error) {
        console.error('Error updating table with DataTable API:', error);
        // Fallback ke update DOM langsung
        updateTableRowDirectly(contactId, contactData);
    }
}

// Fungsi fallback untuk update row langsung di DOM
function updateTableRowDirectly(contactId, contactData) {
    try {
        const button = document.querySelector(`[data-contact-id="${contactId}"]`);
        if (button) {
            const row = button.closest('tr');
            
            // Remove unread styling
            row.classList.remove('unread-row');
            
            // Update nama lengkap (remove "belum dibaca" text)
            const namaCell = row.cells[1];
            const namaText = namaCell.textContent.replace(' (belum dibaca)', '');
            namaCell.innerHTML = namaText;
            
            // Update status badge
            const statusCell = row.cells[4];
            statusCell.innerHTML = '<span class="badge status-resolved">Selesai</span>';
        }
    } catch (error) {
        console.error('Error updating table row directly:', error);
    }
}

// Fungsi untuk update message badge di navigation (jika menggunakan AJAX approach)
function updateMessageBadge() {
    const badge = document.getElementById('message-badge');
    if (badge) {
        fetch('{{ route("admin.contacts.unread-count") }}', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.count > 0) {
                badge.textContent = data.count;
                badge.style.display = 'inline';
            } else {
                badge.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error updating badge:', error);
        });
    }
}

// Fungsi untuk show toast notification
function showToast(type, message) {
    // Menggunakan SweetAlert2 toast
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    Toast.fire({
        icon: type,
        title: message
    });
}
</script>
@endsection