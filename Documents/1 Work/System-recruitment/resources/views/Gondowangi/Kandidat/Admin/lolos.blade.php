<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kandidat Lolos - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 190px;
            background: linear-gradient(135deg, #0f6d3b 0%, #0a5c32 100%);
            color: white;
            z-index: 1000;
            transition: transform 0.3s ease;
            overflow-y: auto;
        }

        .sidebar-collapsed {
            transform: translateX(-200px);
        }

        .sidebar-header {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem;
            border-radius: 0;
            transition: all 0.3s;
        }

        .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .main-content {
            margin-left: 250px;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .main-content.expanded {
            margin-left: 30px;
        }

        .content-wrapper {
            padding: 2rem;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.2rem;
            transition: transform 0.3s;
        }

        .sidebar-toggle:hover {
            color: white;
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
        }

        .table-responsive {
            border-radius: 0.375rem;
        }

        .badge-status {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 500;
        }

        .badge-lanjut {
            background-color: #ffc107;
            color: #212529;
        }

        .badge-diterima {
            background-color: #198754;
            color: white;
        }

        .badge-cocok {
            background-color: #0f6d3b;
            color: white;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 999;
                display: none;
            }
        }

        .position-tab {
            cursor: pointer;
            transition: all 0.3s;
        }

        .position-tab:hover {
            background-color: #f8f9fa;
        }

        .position-tab.active {
            background-color: #0f6d3b;
            color: white;
        }
    </style>
</head>

<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4>
                <i class="fas fa-user-shield me-2"></i>
                <span class="sidebar-text">Admin</span>
            </h4>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.kandidat.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('admin.kandidat.lolos') }}">
                    <i class="fas fa-user-check"></i>
                    <span class="sidebar-text">Kandidat Lolos</span>
                </a>
            </li>

            <li class="nav-item mt-4">
                <a class="nav-link" href="/">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="sidebar-text">Keluar</span>
                </a>
            </li>
            
            <li class="nav-item mt-4 text-center align-items-center">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-chevron-left" id="toggleIcon"></i>
                </button>
            </li>
        </ul>
    </nav>

    <!-- Top Navbar -->
    <nav class="p-0 m-0" id="navbar"></nav>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <div class="content-wrapper mt-1">
            
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-1">Kelola Kandidat Lolos</h3>
                            <p class="text-muted mb-0">Kelola kandidat yang lolos ke tahap selanjutnya</p>
                        </div>
                        <div>
                            <button class="btn btn-outline-primary me-2" onclick="refreshData()">
                                <i class="fas fa-refresh me-1"></i>Refresh
                            </button>
                            <a class="btn btn-danger" href="/">
                                <i class="fas fa-sign-out-alt me-1"></i>Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-warning">Kandidat Lolos</h5>
                            <h2 class="text-warning" id="totalLolos">0</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-success">Diterima</h5>
                            <h2 class="text-success" id="totalDiterima">0</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Cocok</h5>
                            <h2 class="text-primary" id="totalCocok">0</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-info">Total Posisi</h5>
                            <h2 class="text-info" id="totalPosisi">0</h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Position Tabs -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Filter Berdasarkan Posisi</h5>
                        </div>
                        <div class="card-body">
                            <div class="row" id="positionTabs">
                                <div class="col-md-2 mb-2">
                                    <div class="position-tab p-3 border rounded active" data-position="all">
                                        <div class="text-center">
                                            <i class="fas fa-users fa-2x mb-2"></i>
                                            <h6>Semua Posisi</h6>
                                            <span class="badge bg-success" id="countAll">0</span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Position tabs akan dimuat secara dinamis -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Table -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Daftar Kandidat Lolos</h5>
                            <div>
                                <select class="form-select d-inline-block" style="width: auto;" id="statusFilter">
                                    <option value="">Semua Status</option>
                                    <option value="Lanjut">Lolos</option>
                                    <option value="Diterima">Diterima</option>
                                    <option value="Cocok">Cocok</option>
                                </select>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="kandidatTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>No</th>
                                            <th>Nama Kandidat</th>
                                            <th>Email</th>
                                            <th>Posisi Dilamar</th>
                                            <th>Kota</th>
                                            <th>No. Telepon</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data akan dimuat secara dinamis -->
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Loading indicator -->
                            <div class="text-center py-4 d-none" id="loadingIndicator">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            
                            <!-- Empty state -->
                            <div class="text-center py-4 d-none" id="emptyState">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Tidak ada kandidat yang lolos</h5>
                                <p class="text-muted">Belum ada kandidat yang lolos untuk posisi yang dipilih</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal untuk Update Status -->
    <div class="modal fade" id="updateStatusModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Status Kandidat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="updateStatusForm">
                        <div class="mb-3">
                            <label class="form-label">Nama Kandidat</label>
                            <input type="text" class="form-control" id="kandidatNama" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Posisi Dilamar</label>
                            <input type="text" class="form-control" id="kandidatPosisi" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="statusBaru" class="form-label">Status Baru</label>
                            <select class="form-select" id="statusBaru" required>
                                <option value="">Pilih Status</option>
                                <option value="Diterima">Diterima</option>
                                <option value="Cocok">Cocok (Dapat diarahkan ke posisi lain)</option>
                                <option value="Lanjut">Kembali ke Lolos</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control" id="catatan" rows="3" placeholder="Tambahkan catatan untuk status ini..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveStatusBtn">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Kandidat -->
    <div class="modal fade" id="detailKandidatModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Kandidat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary">Informasi Pribadi</h6>
                            <table class="table table-borderless">
                                <tr><td><strong>Nama:</strong></td><td id="detailNama">-</td></tr>
                                <tr><td><strong>Email:</strong></td><td id="detailEmail">-</td></tr>
                                <tr><td><strong>Telepon:</strong></td><td id="detailTelepon">-</td></tr>
                                <tr><td><strong>Kota:</strong></td><td id="detailKota">-</td></tr>
                                <tr><td><strong>Tanggal Lahir:</strong></td><td id="detailTanggalLahir">-</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-primary">Informasi Lamaran</h6>
                            <table class="table table-borderless">
                                <tr><td><strong>Posisi:</strong></td><td id="detailPosisiDilamar">-</td></tr>
                                <tr><td><strong>Status:</strong></td><td id="detailStatus">-</td></tr>
                                <tr><td><strong>Gaji Diharapkan:</strong></td><td id="detailGajiDiharapkan">-</td></tr>
                                <tr><td><strong>Jabatan Diminati:</strong></td><td id="detailJabatanDiminati">-</td></tr>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6 class="text-primary">Informasi Tambahan</h6>
                            <p id="detailInformasiTambahan" class="text-muted">-</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Global variables
        let currentPosition = 'all';
        let currentKandidatId = null;
        let allKandidats = [];
        let positions = [];

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadPositions();
            loadKandidats();
            setupEventListeners();
        });

        // Setup event listeners
        function setupEventListeners() {
            // Sidebar toggle
            document.getElementById('sidebarToggle').addEventListener('click', function() {
                document.getElementById('sidebar').classList.toggle('sidebar-collapsed');
                document.getElementById('mainContent').classList.toggle('expanded');
            });

            // Status filter
            document.getElementById('statusFilter').addEventListener('change', function() {
                filterKandidats();
            });

            // Save status button
            document.getElementById('saveStatusBtn').addEventListener('click', function() {
                updateKandidatStatus();
            });
        }

        // Load positions from server
        function loadPositions() {
            fetch('{{ route('admin.kandidat.filter.options') }}')
                .then(response => response.json())
                .then(data => {
                    positions = data.positions || [];
                    renderPositionTabs();
                })
                .catch(error => {
                    console.error('Error loading positions:', error);
                });
        }

        // Render position tabs
        function renderPositionTabs() {
            const container = document.getElementById('positionTabs');
            let html = `
                <div class="col-md-2 mb-2">
                    <div class="position-tab p-3 border rounded active" data-position="all" onclick="selectPosition('all')">
                        <div class="text-center">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <h6>Semua Posisi</h6>
                            <span class="badge bg-primary" id="countAll">0</span>
                        </div>
                    </div>
                </div>
            `;

            positions.forEach((position, index) => {
                if (index < 5) { // Limit to 5 positions to fit in one row
                    html += `
                        <div class="col-md-2 mb-2">
                            <div class="position-tab p-3 border rounded" data-position="${position.id}" onclick="selectPosition('${position.id}')">
                                <div class="text-center">
                                    <i class="fas fa-briefcase fa-2x mb-2"></i>
                                    <h6 class="small">${position.position_title}</h6>
                                                                                <span class="badge bg-success" id="count${position.id}">0</span>
                                </div>
                            </div>
                        </div>
                    `;
                }
            });

            container.innerHTML = html;
            document.getElementById('totalPosisi').textContent = positions.length;
        }

        // Select position tab
        function selectPosition(positionId) {
            currentPosition = positionId;
            
            // Update tab appearance
            document.querySelectorAll('.position-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelector(`[data-position="${positionId}"]`).classList.add('active');
            
            // Filter kandidats
            filterKandidats();
        }

        // Load kandidats data
        function loadKandidats() {
            document.getElementById('loadingIndicator').classList.remove('d-none');
            
            fetch('{{ route('admin.kandidat.lolos.data') }}')
                .then(response => response.json())
                .then(data => {
                    allKandidats = data.kandidats || [];
                    updateStatistics();
                    filterKandidats();
                })
                .catch(error => {
                    console.error('Error loading kandidats:', error);
                    showError('Gagal memuat data kandidat');
                })
                .finally(() => {
                    document.getElementById('loadingIndicator').classList.add('d-none');
                });
        }

        // Update statistics
        function updateStatistics() {
            const totalLolos = allKandidats.filter(k => k.status === 'Lanjut').length;
            const totalDiterima = allKandidats.filter(k => k.status === 'Diterima').length;
            const totalCocok = allKandidats.filter(k => k.status === 'Cocok').length;

            document.getElementById('totalLolos').textContent = totalLolos;
            document.getElementById('totalDiterima').textContent = totalDiterima;
            document.getElementById('totalCocok').textContent = totalCocok;

            // Update position counts
            document.getElementById('countAll').textContent = allKandidats.length;
            
            positions.forEach(position => {
                const count = allKandidats.filter(k => k.posisi_dilamar_id == position.id).length;
                const countElement = document.getElementById(`count${position.id}`);
                if (countElement) {
                    countElement.textContent = count;
                }
            });
        }

        // Filter kandidats based on position and status
        function filterKandidats() {
            let filteredKandidats = allKandidats;

            // Filter by position
            if (currentPosition !== 'all') {
                filteredKandidats = filteredKandidats.filter(k => k.posisi_dilamar_id == currentPosition);
            }

            // Filter by status
            const statusFilter = document.getElementById('statusFilter').value;
            if (statusFilter) {
                filteredKandidats = filteredKandidats.filter(k => k.status === statusFilter);
            }

            renderKandidatTable(filteredKandidats);
        }

        // Render kandidat table
        function renderKandidatTable(kandidats) {
            const tbody = document.querySelector('#kandidatTable tbody');
            const emptyState = document.getElementById('emptyState');

            if (kandidats.length === 0) {
                tbody.innerHTML = '';
                emptyState.classList.remove('d-none');
                return;
            }

            emptyState.classList.add('d-none');

            const html = kandidats.map((kandidat, index) => {
                const statusBadge = getStatusBadge(kandidat.status);
                return `
                    <tr>
                        <td>${index + 1}</td>
                        <td>
                            <strong>${kandidat.nama}</strong><br>
                            <small class="text-muted">${formatDate(kandidat.tanggal_lahir)}</small>
                        </td>
                        <td>${kandidat.email}</td>
                        <td><strong>${kandidat.posisilamaran?.position_title || 'N/A'}</strong></td>
                        <td>${kandidat.kota_domisili}</td>
                        <td>${kandidat.no_telepon}</td>
                        <td>${statusBadge}</td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" onclick="showDetailModal('${kandidat.id}')">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-warning" onclick="showUpdateStatusModal('${kandidat.id}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');

            tbody.innerHTML = html;
        }

        // Get status badge HTML
        function getStatusBadge(status) {
            switch(status) {
                case 'Lanjut':
                    return '<span class="badge badge-status badge-lanjut">Lolos</span>';
                case 'Diterima':
                    return '<span class="badge badge-status badge-diterima">Diterima</span>';
                case 'Cocok':
                    return '<span class="badge badge-status badge-cocok">Cocok</span>';
                default:
                    return '<span class="badge badge-status bg-secondary">' + status + '</span>';
            }
        }

        // Show detail modal
        function showDetailModal(kandidatId) {
            const kandidat = allKandidats.find(k => k.id === kandidatId);
            if (!kandidat) return;

            document.getElementById('detailNama').textContent = kandidat.nama;
            document.getElementById('detailEmail').textContent = kandidat.email;
            document.getElementById('detailTelepon').textContent = kandidat.no_telepon;
            document.getElementById('detailKota').textContent = kandidat.kota_domisili;
            document.getElementById('detailTanggalLahir').textContent = formatDate(kandidat.tanggal_lahir);
            document.getElementById('detailPosisiDilamar').textContent = kandidat.posisilamaran?.position_title || 'N/A';
            document.getElementById('detailStatus').innerHTML = getStatusBadge(kandidat.status);
            document.getElementById('detailGajiDiharapkan').textContent = formatCurrency(kandidat.gaji_diharapkan);
            document.getElementById('detailJabatanDiminati').textContent = kandidat.jabatan_diminati || '-';
            document.getElementById('detailInformasiTambahan').textContent = kandidat.informasi_tambahan || 'Tidak ada informasi tambahan';

            new bootstrap.Modal(document.getElementById('detailKandidatModal')).show();
        }

        // Show update status modal
        function showUpdateStatusModal(kandidatId) {
            currentKandidatId = kandidatId;
            const kandidat = allKandidats.find(k => k.id === kandidatId);
            if (!kandidat) return;

            document.getElementById('kandidatNama').value = kandidat.nama;
            document.getElementById('kandidatPosisi').value = kandidat.posisilamaran?.position_title || 'N/A';
            document.getElementById('statusBaru').value = '';
            document.getElementById('catatan').value = '';

            new bootstrap.Modal(document.getElementById('updateStatusModal')).show();
        }

        // Update kandidat status
        function updateKandidatStatus() {
            if (!currentKandidatId) return;

            const statusBaru = document.getElementById('statusBaru').value;
            const catatan = document.getElementById('catatan').value;

            if (!statusBaru) {
                alert('Silakan pilih status baru');
                return;
            }

            const saveBtn = document.getElementById('saveStatusBtn');
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

            fetch(`{{ route('admin.kandidat.update.status', '') }}/${currentKandidatId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    status: statusBaru,
                    catatan: catatan
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update kandidat in allKandidats array
                    const kandidatIndex = allKandidats.findIndex(k => k.id === currentKandidatId);
                    if (kandidatIndex !== -1) {
                        allKandidats[kandidatIndex].status = statusBaru;
                    }

                    updateStatistics();
                    filterKandidats();
                    
                    bootstrap.Modal.getInstance(document.getElementById('updateStatusModal')).hide();
                    showSuccess('Status kandidat berhasil diupdate');
                } else {
                    showError(data.message || 'Gagal mengupdate status kandidat');
                }
            })
            .catch(error => {
                console.error('Error updating status:', error);
                showError('Terjadi kesalahan saat mengupdate status');
            })
            .finally(() => {
                saveBtn.disabled = false;
                saveBtn.innerHTML = 'Simpan Perubahan';
            });
        }

        // Utility functions
        function formatDate(dateString) {
            if (!dateString) return '-';
            const date = new Date(dateString);
            return date.toLocaleDateString('id-ID');
        }

        function formatCurrency(amount) {
            if (!amount) return '-';
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }).format(amount);
        }

        function showSuccess(message) {
            // You can implement a toast notification here
            alert(message);
        }

        function showError(message) {
            // You can implement a toast notification here  
            alert(message);
        }

        function refreshData() {
            loadKandidats();
            loadPositions();
        }
    </script>
    
    <script>
        /**
         * Kelola Kandidat Lolos JavaScript
         * File untuk mengelola kandidat yang lolos tahap selanjutnya
         */
        
        class LolosKandidatManager {
            constructor() {
                this.currentPosition = 'all';
                this.currentKandidatId = null;
                this.allKandidats = [];
                this.positions = [];
                this.baseUrl = window.location.origin;
                
                this.init();
            }
        
            init() {
                this.setupEventListeners();
                this.loadInitialData();
            }
        
            setupEventListeners() {
                // Sidebar toggle
                const sidebarToggle = document.getElementById('sidebarToggle');
                if (sidebarToggle) {
                    sidebarToggle.addEventListener('click', () => this.toggleSidebar());
                }
        
                // Status filter
                const statusFilter = document.getElementById('statusFilter');
                if (statusFilter) {
                    statusFilter.addEventListener('change', () => this.filterKandidats());
                }
        
                // Save status button
                const saveStatusBtn = document.getElementById('saveStatusBtn');
                if (saveStatusBtn) {
                    saveStatusBtn.addEventListener('click', () => this.updateKandidatStatus());
                }
        
                // Mobile sidebar overlay
                const sidebarOverlay = document.getElementById('sidebarOverlay');
                if (sidebarOverlay) {
                    sidebarOverlay.addEventListener('click', () => this.closeMobileSidebar());
                }
        
                // Search functionality (if implemented)
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.addEventListener('input', (e) => this.searchKandidats(e.target.value));
                }
            }
        
            async loadInitialData() {
                try {
                    await Promise.all([
                        this.loadPositions(),
                        this.loadKandidats()
                    ]);
                } catch (error) {
                    console.error('Error loading initial data:', error);
                    this.showError('Gagal memuat data awal');
                }
            }
        
            async loadPositions() {
                try {
                    const response = await fetch(`${this.baseUrl}/admin-kandidat/lolos/filter-options`);
                    const data = await response.json();
                    
                    if (data.success) {
                        this.positions = data.positions || [];
                        this.renderPositionTabs();
                        this.updatePositionCounts();
                    } else {
                        throw new Error(data.message || 'Failed to load positions');
                    }
                } catch (error) {
                    console.error('Error loading positions:', error);
                    this.showError('Gagal memuat data posisi');
                }
            }
        
            async loadKandidats() {
                try {
                    this.showLoading(true);
                    
                    const response = await fetch(`${this.baseUrl}/admin-kandidat/lolos-data`);
                    const data = await response.json();
                    
                    if (data.success) {
                        this.allKandidats = data.kandidats || [];
                        this.updateStatistics(data.statistics);
                        this.filterKandidats();
                    } else {
                        throw new Error(data.message || 'Failed to load candidates');
                    }
                } catch (error) {
                    console.error('Error loading candidates:', error);
                    this.showError('Gagal memuat data kandidat');
                } finally {
                    this.showLoading(false);
                }
            }
        
            renderPositionTabs() {
                const container = document.getElementById('positionTabs');
                if (!container) return;
        
                let html = `
                    <div class="col-md-2 mb-2">
                        <div class="position-tab p-3 border rounded active" data-position="all" onclick="lolosManager.selectPosition('all')">
                            <div class="text-center">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <h6>Semua Posisi</h6>
                                <span class="badge bg-primary" id="countAll">0</span>
                            </div>
                        </div>
                    </div>
                `;
        
                this.positions.slice(0, 5).forEach(position => {
                    const safeId = this.sanitizeId(position.id);
                    html += `
                        <div class="col-md-2 mb-2">
                            <div class="position-tab p-3 border rounded" data-position="${position.id}" onclick="lolosManager.selectPosition('${position.id}')">
                                <div class="text-center">
                                    <i class="fas fa-briefcase fa-2x mb-2"></i>
                                    <h6 class="small" title="${this.escapeHtml(position.position_title)}">${this.truncateText(position.position_title, 20)}</h6>
                                    <span class="badge bg-primary" id="count${safeId}">0</span>
                                </div>
                            </div>
                        </div>
                    `;
                });
        
                container.innerHTML = html;
                
                // Update total positions
                const totalPosisiElement = document.getElementById('totalPosisi');
                if (totalPosisiElement) {
                    totalPosisiElement.textContent = this.positions.length;
                }
            }
        
            selectPosition(positionId) {
                this.currentPosition = positionId;
                
                // Update tab appearance
                document.querySelectorAll('.position-tab').forEach(tab => {
                    tab.classList.remove('active');
                });
                
                const selectedTab = document.querySelector(`[data-position="${positionId}"]`);
                if (selectedTab) {
                    selectedTab.classList.add('active');
                }
                
                // Filter kandidats
                this.filterKandidats();
            }
        
            updateStatistics(statistics = null) {
                if (statistics) {
                    // Update from API response
                    const totalLolosEl = document.getElementById('totalLolos');
                    const totalDiterimaEl = document.getElementById('totalDiterima');
                    const totalCocokEl = document.getElementById('totalCocok');
                    
                    if (totalLolosEl) totalLolosEl.textContent = statistics.total_lolos || 0;
                    if (totalDiterimaEl) totalDiterimaEl.textContent = statistics.total_diterima || 0;
                    if (totalCocokEl) totalCocokEl.textContent = statistics.total_cocok || 0;
                } else {
                    // Calculate from current data
                    const totalLolos = this.allKandidats.filter(k => k.status === 'Lanjut').length;
                    const totalDiterima = this.allKandidats.filter(k => k.status === 'Diterima').length;
                    const totalCocok = this.allKandidats.filter(k => k.status === 'Cocok').length;
        
                    const totalLolosEl = document.getElementById('totalLolos');
                    const totalDiterimaEl = document.getElementById('totalDiterima');
                    const totalCocokEl = document.getElementById('totalCocok');
                    
                    if (totalLolosEl) totalLolosEl.textContent = totalLolos;
                    if (totalDiterimaEl) totalDiterimaEl.textContent = totalDiterima;
                    if (totalCocokEl) totalCocokEl.textContent = totalCocok;
                }
                
                this.updatePositionCounts();
            }
        
            updatePositionCounts() {
                // Update count for all positions
                const countAllEl = document.getElementById('countAll');
                if (countAllEl) {
                    countAllEl.textContent = this.allKandidats.length;
                }
                
                // Update count for each position
                this.positions.forEach(position => {
                    const count = this.allKandidats.filter(k => k.posisi_dilamar_id == position.id).length;
                    const safeId = this.sanitizeId(position.id);
                    const countElement = document.getElementById(`count${safeId}`);
                    if (countElement) {
                        countElement.textContent = count;
                    }
                });
            }
        
            filterKandidats() {
                let filteredKandidats = [...this.allKandidats];
        
                // Filter by position
                if (this.currentPosition !== 'all') {
                    filteredKandidats = filteredKandidats.filter(k => k.posisi_dilamar_id == this.currentPosition);
                }
        
                // Filter by status
                const statusFilter = document.getElementById('statusFilter');
                if (statusFilter && statusFilter.value) {
                    filteredKandidats = filteredKandidats.filter(k => k.status === statusFilter.value);
                }
        
                this.renderKandidatTable(filteredKandidats);
            }
        
            renderKandidatTable(kandidats) {
                const tbody = document.querySelector('#kandidatTable tbody');
                const emptyState = document.getElementById('emptyState');
                
                if (!tbody) return;
        
                if (kandidats.length === 0) {
                    tbody.innerHTML = '';
                    if (emptyState) emptyState.classList.remove('d-none');
                    return;
                }
        
                if (emptyState) emptyState.classList.add('d-none');
        
                const html = kandidats.map((kandidat, index) => {
                    const statusBadge = this.getStatusBadge(kandidat.status);
                    const positionTitle = kandidat.posisilamaran?.position_title || 'N/A';
                    
                    return `
                        <tr>
                            <td>${index + 1}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div>
                                        <strong>${this.escapeHtml(kandidat.nama)}</strong><br>
                                        <small class="text-muted">${this.formatDate(kandidat.tanggal_lahir)}</small>
                                    </div>
                                </div>
                            </td>
                            <td>${this.escapeHtml(kandidat.email)}</td>
                            <td><strong>${this.escapeHtml(positionTitle)}</strong></td>
                            <td>${this.escapeHtml(kandidat.kota_domisili || '-')}</td>
                            <td>${this.escapeHtml(kandidat.no_telepon || '-')}</td>
                            <td>${statusBadge}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" onclick="lolosManager.showDetailModal('${kandidat.id}')" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-outline-warning" onclick="lolosManager.showUpdateStatusModal('${kandidat.id}')" title="Update Status">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('');
        
                tbody.innerHTML = html;
            }
        
            getStatusBadge(status) {
                const badges = {
                    'Lanjut': '<span class="badge badge-status badge-lanjut">Lolos</span>',
                    'Diterima': '<span class="badge badge-status badge-diterima">Diterima</span>',
                    'Cocok': '<span class="badge badge-status badge-cocok">Cocok</span>'
                };
                
                return badges[status] || `<span class="badge badge-status bg-secondary">${this.escapeHtml(status)}</span>`;
            }
        
            showDetailModal(kandidatId) {
                const kandidat = this.allKandidats.find(k => k.id === kandidatId);
                if (!kandidat) {
                    this.showError('Kandidat tidak ditemukan');
                    return;
                }
        
                // Populate modal with candidate data
                this.setElementText('detailNama', kandidat.nama);
                this.setElementText('detailEmail', kandidat.email);
                this.setElementText('detailTelepon', kandidat.no_telepon || '-');
                this.setElementText('detailKota', kandidat.kota_domisili || '-');
                this.setElementText('detailTanggalLahir', this.formatDate(kandidat.tanggal_lahir));
                this.setElementText('detailPosisiDilamar', kandidat.posisilamaran?.position_title || 'N/A');
                this.setElementText('detailGajiDiharapkan', this.formatCurrency(kandidat.gaji_diharapkan));
                this.setElementText('detailJabatanDiminati', kandidat.jabatan_diminati || '-');
                this.setElementText('detailInformasiTambahan', kandidat.informasi_tambahan || 'Tidak ada informasi tambahan');
                
                const detailStatusEl = document.getElementById('detailStatus');
                if (detailStatusEl) {
                    detailStatusEl.innerHTML = this.getStatusBadge(kandidat.status);
                }
        
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('detailKandidatModal'));
                modal.show();
            }
        
            showUpdateStatusModal(kandidatId) {
                this.currentKandidatId = kandidatId;
                const kandidat = this.allKandidats.find(k => k.id === kandidatId);
                if (!kandidat) {
                    this.showError('Kandidat tidak ditemukan');
                    return;
                }
        
                // Populate modal
                this.setElementValue('kandidatNama', kandidat.nama);
                this.setElementValue('kandidatPosisi', kandidat.posisilamaran?.position_title || 'N/A');
                this.setElementValue('statusBaru', '');
                this.setElementValue('catatan', '');
        
                // Show modal
                const modal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
                modal.show();
            }
        
            async updateKandidatStatus() {
                if (!this.currentKandidatId) {
                    this.showError('ID kandidat tidak valid');
                    return;
                }
        
                const statusBaru = document.getElementById('statusBaru')?.value;
                const catatan = document.getElementById('catatan')?.value;
        
                if (!statusBaru) {
                    this.showError('Silakan pilih status baru');
                    return;
                }
        
                const saveBtn = document.getElementById('saveStatusBtn');
                if (saveBtn) {
                    saveBtn.disabled = true;
                    saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';
                }
        
                try {
                    const response = await fetch(`${this.baseUrl}/admin-kandidat/lolos/${this.currentKandidatId}/status`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                        },
                        body: JSON.stringify({
                            status: statusBaru,
                            catatan: catatan
                        })
                    });
        
                    const data = await response.json();
        
                    if (data.success) {
                        // Update kandidat in local array
                        const kandidatIndex = this.allKandidats.findIndex(k => k.id === this.currentKandidatId);
                        if (kandidatIndex !== -1) {
                            this.allKandidats[kandidatIndex].status = statusBaru;
                            if (catatan) {
                                const currentInfo = this.allKandidats[kandidatIndex].informasi_tambahan || '';
                                this.allKandidats[kandidatIndex].informasi_tambahan = currentInfo + `\n[${new Date().toLocaleString('id-ID')}] ${catatan}`;
                            }
                        }
        
                        this.updateStatistics();
                        this.filterKandidats();
                        
                        // Hide modal
                        const modalElement = document.getElementById('updateStatusModal');
                        const modal = bootstrap.Modal.getInstance(modalElement);
                        if (modal) modal.hide();
                        
                        this.showSuccess('Status kandidat berhasil diupdate');
                    } else {
                        this.showError(data.message || 'Gagal mengupdate status kandidat');
                    }
                } catch (error) {
                    console.error('Error updating status:', error);
                    this.showError('Terjadi kesalahan saat mengupdate status');
                } finally {
                    if (saveBtn) {
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = 'Simpan Perubahan';
                    }
                }
            }
        
            searchKandidats(searchTerm) {
                if (!searchTerm.trim()) {
                    this.filterKandidats();
                    return;
                }
        
                const searchLower = searchTerm.toLowerCase();
                const filteredKandidats = this.allKandidats.filter(kandidat => {
                    return kandidat.nama.toLowerCase().includes(searchLower) ||
                           kandidat.email.toLowerCase().includes(searchLower) ||
                           (kandidat.posisilamaran?.position_title || '').toLowerCase().includes(searchLower) ||
                           (kandidat.kota_domisili || '').toLowerCase().includes(searchLower);
                });
        
                this.renderKandidatTable(filteredKandidats);
            }
        
            toggleSidebar() {
                const sidebar = document.getElementById('sidebar');
                const mainContent = document.getElementById('mainContent');
                const toggleIcon = document.getElementById('toggleIcon');
                
                if (sidebar && mainContent) {
                    sidebar.classList.toggle('sidebar-collapsed');
                    mainContent.classList.toggle('expanded');
                    
                    if (toggleIcon) {
                        toggleIcon.classList.toggle('fa-chevron-left');
                        toggleIcon.classList.toggle('fa-chevron-right');
                    }
                }
            }
        
            closeMobileSidebar() {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                
                if (sidebar) sidebar.classList.remove('show');
                if (overlay) overlay.style.display = 'none';
            }
        
            showLoading(show) {
                const loadingIndicator = document.getElementById('loadingIndicator');
                if (loadingIndicator) {
                    if (show) {
                        loadingIndicator.classList.remove('d-none');
                    } else {
                        loadingIndicator.classList.add('d-none');
                    }
                }
            }
        
            // Utility methods
            formatDate(dateString) {
                if (!dateString) return '-';
                try {
                    const date = new Date(dateString);
                    return date.toLocaleDateString('id-ID');
                } catch (error) {
                    return '-';
                }
            }
        
            formatCurrency(amount) {
                if (!amount || isNaN(amount)) return '-';
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(amount);
            }
        
            escapeHtml(text) {
                if (!text) return '';
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
        
            truncateText(text, maxLength) {
                if (!text || text.length <= maxLength) return text;
                return text.substring(0, maxLength) + '...';
            }
        
            sanitizeId(id) {
                return String(id).replace(/[^a-zA-Z0-9]/g, '_');
            }
        
            setElementText(elementId, text) {
                const element = document.getElementById(elementId);
                if (element) element.textContent = text || '-';
            }
        
            setElementValue(elementId, value) {
                const element = document.getElementById(elementId);
                if (element) element.value = value || '';
            }
        
            showSuccess(message) {
                // Simple alert for now - you can implement a toast notification
                alert(message);
            }
        
            showError(message) {
                // Simple alert for now - you can implement a toast notification
                console.error(message);
                alert(message);
            }
        
            refreshData() {
                this.loadInitialData();
            }
        }
        
        // Initialize the manager when DOM is loaded
        let lolosManager;
        document.addEventListener('DOMContentLoaded', function() {
            lolosManager = new LolosKandidatManager();
            
            // Make refreshData available globally for the refresh button
            window.refreshData = () => lolosManager.refreshData();
        });
    </script>
</body>
</html>