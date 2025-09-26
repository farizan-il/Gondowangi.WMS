@extends('Gondowangi.Admin.Layout.main')
@section('head')
    <!-- Boxicons CSS CDN -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        .event-card {
            transition: transform 0.2s;
        }
        .event-card:hover {
            transform: translateY(-2px);
        }
        .featured-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #28a745;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-active {
            color: #28a745;
        }
        .status-inactive {
            color: #dc3545;
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
    <!--<div class="row mb-3">-->
    <!--    <div class="col-sm-12">-->
    <!--        <div class="page-title-box d-sm-flex align-items-center justify-content-between">-->
    <!--            <h4 class="mb-sm-0 font-size-18">Kelola Acara Mendatang</h4>-->
                
    <!--        </div>-->
    <!--    </div>-->
    <!--</div>-->
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted">Total Events</h6>
                            <h3 class="mb-0">{{ $totalEvents }}</h3>
                        </div>
                        <div class="text-primary">
                            <i class="bx bx-calendar bx-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted">Events Aktif</h6>
                            <h3 class="mb-0">{{ $activeEvents }}</h3>
                        </div>
                        <div class="text-success">
                            <i class="bx bx-check-circle bx-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title text-muted">Acara Selesai</h6>
                            <h3 class="mb-0"></h3>
                        </div>
                        <div class="text-warning">
                            <i class="bx bx-star bx-lg"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-info mb-0" role="alert">
                        <i class="bx bx-info-circle me-2"></i>
                        <strong>Info:</strong> Maksimal 3 event dapat ditampilkan di website.
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Events Table -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Daftar Events</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#eventModal" onclick="openCreateModal()">
                        <i class="bx bx-plus me-1"></i> Tambah Event
                    </button>
                </div>
                <div class="card-body">
                    @if($events->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Event</th>
                                        <th>Tanggal</th>
                                        <th>Waktu</th>
                                        <th>Lokasi</th>
                                        <th>Status</th>
                                        <th>Dipublikasi?</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($events as $key => $event)
                                    <tr>
                                       <td>
                                            <div class="d-flex align-items-center">
                                                @if($event->image_url)
                                                    <img src="{{ asset($event->image_url) }}" 
                                                         alt="{{ $event->event_name }}" 
                                                         class="rounded me-2" 
                                                         style="width: 50px; height: 50px; object-fit: cover; cursor: pointer;"
                                                         data-bs-toggle="modal" 
                                                         data-bs-target="#eventImageModal"
                                                         onclick="showEventImageModal('{{ asset($event->image_url) }}', '{{ $event->event_name }}')">
                                                @else
                                                    <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" 
                                                         style="width: 50px; height: 50px;">
                                                        <i class="bx bx-image text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h6 class="mb-0">{{ $event->event_name }}</h6>
                                                    <small class="text-muted">{{ Str::limit($event->description, 50) }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $event->event_date->format('d M Y') }}</td>
                                        <td>{{ $event->event_time ? $event->event_time->format('H:i') : '-' }}</td>
                                        <td>{{ $event->location ?? '-' }}</td>
                                        <td>
                                            @php
                                                $statusLabels = [
                                                    'upcoming' => 'Akan Datang',
                                                    'ongoing' => 'Sedang Berlangsung',
                                                    'completed' => 'Selesai',
                                                    'cancelled' => 'Dibatalkan'
                                                ];
                                                
                                                $statusColors = [
                                                    'upcoming' => 'bg-primary',
                                                    'ongoing' => 'bg-success',
                                                    'completed' => 'bg-secondary',
                                                    'cancelled' => 'bg-danger'
                                                ];
                                            @endphp
                                            <span class="badge {{ $statusColors[$event->status] ?? 'bg-secondary' }}">
                                                {{ $statusLabels[$event->status] ?? ucfirst($event->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input toggle-featured-switch"
                                                       type="checkbox"
                                                       data-id="{{ $event->id }}"
                                                       {{ $event->is_featured ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-info" 
                                                        onclick="viewEvent({{ $event->id }})"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#viewModal">
                                                    <i class="bx bx-show"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-primary" 
                                                        onclick="editEvent({{ $event->id }})"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#eventModal">
                                                    <i class="bx bx-edit"></i>
                                                </button>
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteEvent({{ $event->id }})">
                                                    <i class="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $events->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bx bx-calendar bx-lg text-muted"></i>
                            <h5 class="mt-2 text-muted">Belum ada event</h5>
                            <p class="text-muted">Klik tombol "Tambah Event" untuk membuat event pertama Anda.</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#eventModal" onclick="openCreateModal()">
                                <i class="bx bx-plus me-1"></i> Tambah Event
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Event Image -->
<div class="modal fade" id="eventImageModal" tabindex="-1" aria-labelledby="eventImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventImageModalLabel">Gambar Acara</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body text-center">
                <img id="eventImageModalContent" src="" alt="Gambar Acara" class="img-fluid rounded mb-2">
                <p id="eventImageTitle" class="fw-semibold"></p>
            </div>
        </div>
    </div>
</div>

<!-- Create/Edit Event Modal -->
<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalTitle">Tambah Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="eventForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div id="methodField"></div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="event_name" class="form-label">Nama Event <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="event_name" name="event_name" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="event_date" class="form-label">Tanggal Event <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="event_date" name="event_date" >
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="event_time" class="form-label">Waktu Event</label>
                                <input type="time" class="form-control" id="event_time" name="event_time">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="location" class="form-label">Lokasi</label>
                                <input type="text" class="form-control" id="location" name="location">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Gambar Event</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <div class="invalid-feedback"></div>
                        <small class="form-text text-muted">Format: JPEG, PNG, JPG, GIF. Maksimal 2MB.</small>
                        <div id="currentImage" class="mt-2" style="display: none;">
                            <img id="currentImagePreview" src="" alt="Current Image" class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="upcoming">Akan Datang</option>
                                    <option value="ongoing">Sedang Berlangsung</option>
                                    <option value="completed">Selesai</option>
                                    <option value="cancelled">Dibatalkan</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Event Modal -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="viewContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus event ini? Tindakan ini tidak dapat dibatalkan.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!--Script untuk ubah publikasi acara-->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const switches = document.querySelectorAll('.toggle-featured-switch');

        switches.forEach(switchEl => {
            switchEl.addEventListener('change', function () {
                const eventId = this.getAttribute('data-id');
                const checked = this.checked;

                fetch(`/admin/acara-mendatang/${eventId}/toggle-featured`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'limit') {
                        alert(data.message);
                        this.checked = false; // revert back
                    }
                })
                .catch(error => {
                    alert('Terjadi kesalahan.');
                    this.checked = !checked; // revert state
                });
            });
        });
    });
</script>

<script>
    function showEventImageModal(imageUrl, title) {
        document.getElementById('eventImageModalContent').src = imageUrl;
        document.getElementById('eventImageTitle').textContent = title;
    }
</script>


<script>
    // Function to edit event
    function editEvent(eventId) {
        document.getElementById('eventModalTitle').textContent = 'Edit Event';
        document.getElementById('submitBtn').textContent = 'Update';
        document.getElementById('eventForm').action = `{{ route('admin.acara-mendatang.update', ':id') }}`.replace(':id', eventId);
        document.getElementById('methodField').innerHTML = '@method("PUT")';
        clearValidationErrors();
        
        // Show loading state
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitBtn').textContent = 'Memuat...';
        
        // Fetch event data
        fetch(`{{ route('admin.acara-mendatang.show', ':id') }}`.replace(':id', eventId), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP error! status: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            // Populate form fields
            document.getElementById('event_name').value = data.event_name || '';
            document.getElementById('description').value = data.description || '';
            document.getElementById('event_date').value = data.event_date || '';
            document.getElementById('event_time').value = data.event_time || '';
            document.getElementById('location').value = data.location || '';
            document.getElementById('status').value = data.status || 'upcoming';
            // document.getElementById('is_featured').checked = data.is_featured || false;
            
            // Handle image preview
            if (data.image_url) {
                document.getElementById('currentImage').style.display = 'block';
                document.getElementById('currentImagePreview').src = `{{ asset('storage/') }}/${data.image_url}`;
            } else {
                document.getElementById('currentImage').style.display = 'none';
            }
            
            // Re-enable submit button
            document.getElementById('submitBtn').disabled = false;
            document.getElementById('submitBtn').textContent = 'Update';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memuat data event: ' + error.message);
            // Re-enable submit button
            document.getElementById('submitBtn').disabled = false;
            document.getElementById('submitBtn').textContent = 'Update';
        });
    }
    
    // Function to view event
    function viewEvent(eventId) {
        // Show loading state
        document.getElementById('viewContent').innerHTML = '<div class="text-center"><i class="bx bx-loader-alt bx-spin"></i> Memuat...</div>';
        
        fetch(`{{ route('admin.acara-mendatang.show', ':id') }}`.replace(':id', eventId), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP error! status: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            let imageHtml = '';
            if (data.image_url) {
                imageHtml = `
                    <div class="mb-3">
                        <img src="{{ asset('storage/') }}/${data.image_url}" 
                             alt="${data.event_name}" 
                             class="img-fluid rounded" 
                             style="max-height: 300px; width: 100%; object-fit: cover;">
                    </div>
                `;
            }
            
            // Status labels and colors
            const statusLabels = {
                'upcoming': 'Akan Datang',
                'ongoing': 'Sedang Berlangsung',
                'completed': 'Selesai',
                'cancelled': 'Dibatalkan'
            };
            
            const statusColors = {
                'upcoming': 'bg-primary',
                'ongoing': 'bg-success',
                'completed': 'bg-secondary',
                'cancelled': 'bg-danger'
            };
            
            // Format date
            const eventDate = new Date(data.event_date);
            const formattedDate = eventDate.toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            document.getElementById('viewContent').innerHTML = `
                ${imageHtml}
                <div class="row">
                    <div class="col-md-6">
                        <h6>Nama Event</h6>
                        <p>${data.event_name}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Tanggal</h6>
                        <p>${formattedDate}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h6>Waktu</h6>
                        <p>${data.event_time || '-'}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Lokasi</h6>
                        <p>${data.location || '-'}</p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <h6>Status</h6>
                        <p><span class="badge ${statusColors[data.status] || 'bg-secondary'}">${statusLabels[data.status] || data.status}</span></p>
                    </div>
                   
                </div>
                <div class="row">
                    <div class="col-12">
                        <h6>Deskripsi</h6>
                        <p>${data.description || '-'}</p>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('viewContent').innerHTML = '<p class="text-danger">Terjadi kesalahan saat memuat data event: ' + error.message + '</p>';
        });
    }
    
    // Function to delete event
    function deleteEvent(eventId) {
        const form = document.getElementById('deleteForm');
        form.action = `{{ route('admin.acara-mendatang.destroy', ':id') }}`.replace(':id', eventId);
        
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    }
    
    // Handle form submission
    document.getElementById('eventForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.textContent;
        
        submitBtn.disabled = true;
        submitBtn.textContent = 'Menyimpan...';
        clearValidationErrors();
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP error! status: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Close modal and reload page
                bootstrap.Modal.getInstance(document.getElementById('eventModal')).hide();
                location.reload();
            } else {
                // Handle validation errors
                if (data.errors) {
                    Object.keys(data.errors).forEach(key => {
                        const input = document.getElementById(key);
                        const feedback = input.nextElementSibling;
                        if (input && feedback && feedback.classList.contains('invalid-feedback')) {
                            input.classList.add('is-invalid');
                            feedback.textContent = data.errors[key][0];
                        }
                    });
                }
                if (data.message) {
                    alert(data.message);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menyimpan data: ' + error.message);
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
    });
    
    // Handle delete form submission
    document.getElementById('deleteForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP error! status: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
                location.reload();
            } else {
                alert(data.message || 'Terjadi kesalahan saat menghapus event');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus event: ' + error.message);
        });
    });
    
    function clearValidationErrors() {
        const invalidFeedbacks = document.querySelectorAll('.invalid-feedback');
        const formControls = document.querySelectorAll('.form-control, .form-select');
        
        invalidFeedbacks.forEach(feedback => {
            feedback.textContent = '';
        });
        
        formControls.forEach(control => {
            control.classList.remove('is-invalid');
        });
    }
    
    function openCreateModal() {
        document.getElementById('eventModalTitle').textContent = 'Tambah Event';
        document.getElementById('submitBtn').textContent = 'Simpan';
        document.getElementById('eventForm').action = '{{ route("admin.acara-mendatang.store") }}';
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('eventForm').reset();
        document.getElementById('currentImage').style.display = 'none';
        clearValidationErrors();
    }
    
    // Set minimum date to today
    document.addEventListener('DOMContentLoaded', function() {
        const eventDate = document.getElementById('event_date');
        if (eventDate) {
            const today = new Date().toISOString().split('T')[0];
            eventDate.min = today;
        }
        
        // Add CSRF token meta tag if not exists
        if (!document.querySelector('meta[name="csrf-token"]')) {
            const meta = document.createElement('meta');
            meta.name = 'csrf-token';
            meta.content = '{{ csrf_token() }}';
            document.head.appendChild(meta);
        }
    });
</script>
@endsection