@extends('Approval-app.Layout.approver-main')
@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    :root {
        --primary-color: #0e6a39;
        --primary-light: #1a8047;
        --primary-dark: #0a5229;
        --success-color: #198754;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
        --info-color: #0dcaf0;
    }

    .card-custom {
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s, box-shadow 0.2s;
        border: none;
    }

    .card-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .stat-card {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        color: white;
    }

    .stat-icon {
        font-size: 2.5rem;
        opacity: 0.8;
    }

    .chart-container {
        height: 300px;
        position: relative;
    }

    .progress-custom {
        height: 8px;
        border-radius: 10px;
    }

    .badge-status {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }

    .activity-item {
        border-left: 3px solid var(--primary-color);
        padding-left: 15px;
        margin-bottom: 15px;
        position: relative;
    }

    .activity-item:before {
        content: '';
        width: 8px;
        height: 8px;
        background: var(--primary-color);
        border-radius: 50%;
        position: absolute;
        left: -6px;
        top: 8px;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(14, 106, 57, 0.05);
    }

    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .btn-primary:hover {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
    }

    .text-primary {
        color: var(--primary-color) !important;
    }

    .bg-primary {
        background-color: var(--primary-color) !important;
    }
    
    .status-paid {
        background-color: #d1fae5;
        color: #065f46;
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
                    <h5 class="m-b-10">Dashboard</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<div class="row">
    <!-- Stats Cards -->
    <div class="col-xl-3 col-md-6">
        <div class="card card-custom stat-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-white">Total Pengajuan</h6>
                        <h3 class="mb-0 text-white">{{ number_format($stats['total_pengajuan']) }}</h3>
                        <small class="text-white-50">Keseluruhan pengajuan</small>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-custom" style="background: linear-gradient(135deg, var(--success-color), #20a15a);">
            <div class="card-body text-white">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-white">Disetujui</h6>
                        <h3 class="mb-0 text-white">{{ number_format($stats['pengajuan_approved']) }}</h3>
                        <small class="text-white">Pengajuan approved</small>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-custom" style="background: linear-gradient(135deg, var(--warning-color), #ffcd39);">
            <div class="card-body text-white">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-black">Masih Diproses</h6>
                        <h3 class="mb-0">{{ number_format($stats['pengajuan_pending']) }}</h3>
                        <small class="text-dark">Proses approval</small>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card card-custom" style="background: linear-gradient(135deg, var(--info-color), #31d2f2);">
            <div class="card-body text-white">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-dark">Total Nominal</h6>
                        <h3 class="mb-0">{{ number_format($stats['total_nominal'], 0, ',', '.') }}</h3>
                        <small class="text-dark">Rupiah</small>
                    </div>
                    <div class="stat-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
   <!--@if($notifikasiPayments && $notifikasiPayments->count() > 0)-->
   <!--     @foreach($notifikasiPayments as $notif)-->
   <!--         <div class="col-12 mb-2">-->
   <!--             <div class="alert alert-success alert-dismissible fade show p-3" role="alert" id="notification-{{ $notif->id }}">-->
   <!--                 <div class="d-flex align-items-start">-->
   <!--                     <i class="fas fa-exclamation-triangle me-2 mt-1"></i>-->
   <!--                     <div class="flex-grow-1">-->
   <!--                         <strong>Notifikasi Pembayaran</strong><br>-->
   <!--                         <span>{{ $notif->message }}</span>-->
   <!--                         @if($notif->pengajuan)-->
   <!--                             <br><small class="text-muted">Pengajuan: {{ $notif->pengajuan->nomor_pengajuan }}</small>-->
   <!--                         @endif-->
   <!--                         @if($notif->settlement)-->
   <!--                             <br><small class="text-muted">Settlement: {{ $notif->settlement->nomor_settlement }}</small>-->
   <!--                         @endif-->
   <!--                     </div>-->
   <!--                 </div>-->
   <!--                 <button type="button" -->
   <!--                         class="btn-close" -->
   <!--                         onclick="markAsRead({{ $notif->id }})" -->
   <!--                         aria-label="Close">-->
   <!--                 </button>-->
   <!--             </div>-->
   <!--         </div>-->
   <!--     @endforeach-->
   <!-- @endif-->
    
     <!-- Recent Activities -->
    <div class="col-lg-8">
        <div class="card card-custom">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Pengajuan Terbaru</h5>
                <a href="/BuatPengajuan" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-eye"></i> Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nomor</th>
                                <th>Kategori</th>
                                <th>Nominal</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentPengajuan as $pengajuan)
                            <tr>
                                <td>
                                    <small class="text-muted">{{ $pengajuan->nomor_pengajuan }}</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="{{ $pengajuan->kategoriPengajuan->icon ?? 'fas fa-file' }} text-primary me-2"></i>
                                        <span>{{ $pengajuan->kategoriPengajuan->nama }}</span>
                                    </div>
                                </td>
                                <td>Rp {{ number_format($pengajuan->nominal_pengajuan, 0, ',', '.') }}</td>
                                <td>
                                    @php
                                        $statusClass = match($pengajuan->status_pengajuan) {
                                            'rejected' => 'bg-danger', 
                                            'proses' => 'bg-warning',
                                            'proses_settlement' => 'bg-warning text-dark',
                                            'settlement_created' => 'bg-warning text-dark',
                                            'completed', 'approved' => 'status-paid',
                                            default => 'bg-secondary'
                                        };
                                    @endphp
                                    <span class="badge badge-status {{ $statusClass }}">
                                        @if($pengajuan->status_pengajuan == 'completed')
                                            🎉 Pengajuan Selesai
                                        @elseif($pengajuan->status_pengajuan == 'proses_settlement' || $pengajuan->status_pengajuan == 'settlement_created')
                                            Proses Settlement
                                        @else
                                            {{ ucfirst($pengajuan->status_pengajuan) }}
                                        @endif
                                    </span>
                                </td>
                                <td>{{ $pengajuan->tanggal_pengajuan->format('d/m/Y') }}</td>
                                
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card card-custom">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Menunggu Persetujuan Saya  <span class="badge bg-danger">{{ $pendingApprovals->count() }}</span></h5>
            </div>
            <div class="card-body">
                @if($pendingApprovals->count() > 0)
                    @foreach($pendingApprovals as $approval)
                    <div class="activity-item">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $approval->pengajuan->nomor_pengajuan }}</h6>
                                <p class="mb-1 text-muted small">{{ $approval->pengajuan->judul }}</p>
                                <small class="text-muted">dari {{ $approval->pengajuan->requester->nama }}</small>
                            </div>
                            <a class="btn btn-sm btn-outline-primary" href="/LaporanPengajuan">
                                <i class="fas fa-check"></i> Lihat Detail
                            </a>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-3x mb-3 opacity-50"></i>
                        <p>Tidak ada pengajuan yang menunggu persetujuan</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="col-lg-8">
        <div class="card card-custom">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Tren Pengajuan (6 Bulan Terakhir)</h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    

    <!-- Pending Approvals -->
     <div class="col-lg-4">
        <div class="card card-custom">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Status Pengajuan</h5>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Kategori Stats -->
    <div class="col-12">
        <div class="card card-custom">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Statistik per Kategori</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($kategoriStats as $kategori)
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card border">
                            <div class="card-body text-center">
                                <i class="{{ $kategori->icon ?? 'fas fa-folder' }} fa-2x mb-2" style="color: {{ $kategori->warna ?? '#0e6a39' }}"></i>
                                <h6 class="mb-1">{{ $kategori->nama }}</h6>
                                <h4 class="text-primary mb-1">{{ $kategori->total }}</h4>
                                <small class="text-muted">Rp {{ number_format($kategori->total_nominal, 0, ',', '.') }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Pengajuan -->
<div class="modal fade" id="detailPengajuanModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pengajuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Process Approval -->
<div class="modal fade" id="processApprovalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Proses Persetujuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="approvalModalContent">
                <!-- Content will be loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function markAsRead(notificationId) {
        // Sembunyikan alert terlebih dahulu
        const alertElement = document.getElementById('notification-' + notificationId);
        if (alertElement) {
            alertElement.style.display = 'none';
        }
    
        // Kirim request AJAX untuk mark as read
        fetch('{{ route("dashboard.mark-notification-read") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                notification_id: notificationId 
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers.get('content-type'));
            
            // Jika response bukan 200, throw error
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Server response:', text);
                    throw new Error(`Server error: ${response.status}`);
                });
            }
            
            // Periksa content type
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                return response.text().then(text => {
                    console.error('Non-JSON response:', text);
                    throw new Error('Response bukan JSON');
                });
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            
            if (data.success) {
                console.log('Notifikasi berhasil ditandai sebagai dibaca');
                // Notifikasi sudah disembunyikan, tidak perlu action lain
            } else {
                console.error('Failed to mark notification as read:', data.message);
                // Tampilkan kembali alert jika gagal
                if (alertElement) {
                    alertElement.style.display = 'block';
                }
                alert('Gagal menandai notifikasi: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error details:', error);
            
            // Tampilkan kembali alert jika error
            if (alertElement) {
                alertElement.style.display = 'block';
            }
            
            alert('Terjadi kesalahan saat menandai notifikasi. Silakan coba lagi.');
        });
    }
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyChart = new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: @json(array_column($chartData['monthly'], 'month')),
            datasets: [{
                label: 'Jumlah Pengajuan',
                data: @json(array_column($chartData['monthly'], 'count')),
                borderColor: '#0e6a39',
                backgroundColor: 'rgba(14, 106, 57, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: @json(array_column($chartData['status'], 'label')),
            datasets: [{
                data: @json(array_column($chartData['status'], 'value')),
                backgroundColor: [
                    
                    '#ffc107', // Warning  
                    '#dc3545', // Danger
                    '#198754', // Success
                    '#16ccf0'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});

function viewPengajuan(id) {
    // Load pengajuan details via AJAX
    fetch(`/pengajuan/${id}/detail`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('modalContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('detailPengajuanModal')).show();
        })
        .catch(error => {
            document.getElementById('modalContent').innerHTML = '<p class="text-danger">Error loading data</p>';
        });
}

function processApproval(approvalId) {
    // Load approval form via AJAX
    fetch(`/approval/${approvalId}/process`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('approvalModalContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('processApprovalModal')).show();
        })
        .catch(error => {
            document.getElementById('approvalModalContent').innerHTML = '<p class="text-danger">Error loading form</p>';
        });
}
</script>
@endsection