@extends('Approval-app.Layout.main-admin')
@section('head')
<style>
    :root {
        --primary-color: #0e6a39;
        --primary-light: #1a8047;
        --primary-dark: #0a5229;
        --success-color: #198754;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
        --info-color: #0dcaf0;
        --secondary-color: #6c757d;
    }

    .admin-card {
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: none;
        overflow: hidden;
    }

    .admin-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .stat-card-admin {
        background: linear-gradient(135deg, var(--primary-color), var(--primary-light));
        color: white;
        position: relative;
    }

    .stat-card-admin::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 100px;
        height: 100px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        transform: translate(30px, -30px);
    }

    .stat-icon-admin {
        font-size: 3rem;
        opacity: 0.9;
        z-index: 2;
        position: relative;
    }

    .chart-card {
        min-height: 400px;
    }

    .department-item {
        padding: 1rem;
        border-radius: 10px;
        background: #f8f9fa;
        margin-bottom: 0.5rem;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .department-item:hover {
        background: rgba(14, 106, 57, 0.05);
        transform: translateX(5px);
    }

    .progress-ring {
        width: 60px;
        height: 60px;
    }

    .activity-timeline {
        position: relative;
        padding-left: 2rem;
    }

    .activity-timeline::before {
        content: '';
        position: absolute;
        left: 0.75rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: var(--primary-color);
    }

    .activity-item-admin {
        position: relative;
        padding: 1rem 0;
        border-bottom: 1px solid #eee;
    }

    .activity-item-admin::before {
        content: '';
        position: absolute;
        left: -2.2rem;
        top: 1.5rem;
        width: 12px;
        height: 12px;
        background: var(--primary-color);
        border-radius: 50%;
        border: 3px solid white;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .metric-card {
        text-align: center;
        padding: 2rem 1rem;
        border-radius: 12px;
        background: white;
        border: 1px solid #e9ecef;
    }

    .metric-value {
        font-size: 2.5rem;
        font-weight: bold;
        color: var(--primary-color);
        margin-bottom: 0.5rem;
    }

    .metric-label {
        color: #6c757d;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .trend-indicator {
        font-size: 0.8rem;
        padding: 0.2rem 0.5rem;
        border-radius: 20px;
        margin-left: 0.5rem;
    }

    .trend-up {
        background: #d4edda;
        color: #155724;
    }

    .trend-down {
        background: #f8d7da;
        color: #721c24;
    }

    .pending-item {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        border-radius: 0 8px 8px 0;
    }

    .table-admin {
        font-size: 0.9rem;
    }

    .table-admin th {
        border-top: none;
        font-weight: 600;
        color: #495057;
        background: #f8f9fa;
    }

    .badge-status-admin {
        font-size: 0.75rem;
        padding: 0.35em 0.8em;
        border-radius: 20px;
    }

    .financial-summary {
        background: linear-gradient(135deg, #0e6a39, #1a8047);
        color: white;
        border-radius: 15px;
        padding: 2rem;
    }

    .financial-item {
        text-align: center;
        padding: 1rem 0;
    }

    .financial-amount {
        font-size: 1.5rem;
        font-weight: bold;
        margin-bottom: 0.25rem;
    }

    .financial-label {
        font-size: 0.875rem;
        opacity: 0.9;
    }
    
    .text-over{
        color: #ffd009;
    }
</style>
@endsection

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Admin Dashboard</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Admin</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Overview Stats -->
<div class="row mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="admin-card stat-card-admin">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-white-50 mb-1">Total Pengajuan</h6>
                        <h2 class="mb-0 text-white">{{ number_format($overviewStats['total_pengajuan']) }}</h2>
                        @php
                            $monthlyTrend = $overviewStats['pengajuan_this_month'] - $overviewStats['pengajuan_last_month'];
                        @endphp
                        <small class="text-white-50">
                            Bulan ini: {{ number_format($overviewStats['pengajuan_this_month']) }}
                            @if($monthlyTrend > 0)
                                <span class="trend-indicator trend-up">+{{ $monthlyTrend }}</span>
                            @elseif($monthlyTrend < 0)
                                <span class="trend-indicator trend-down">{{ $monthlyTrend }}</span>
                            @endif
                        </small>
                    </div>
                    <div class="stat-icon-admin">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="admin-card" style="background: linear-gradient(135deg, var(--success-color), #20c997);">
            <div class="card-body text-white">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-white-50 mb-1">Total Karyawan</h6>
                        <h2 class="mb-0">{{ number_format($overviewStats['total_karyawan']) }}</h2>
                        <small class="text-white-50">{{ number_format($overviewStats['total_department']) }} Department</small>
                    </div>
                    <div class="stat-icon-admin">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="admin-card" style="background: linear-gradient(135deg, var(--info-color), #17a2b8);">
            <div class="card-body text-white">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-dark mb-1">Pengajuan Selesai</h6>
                        <h2 class="mb-0"> {{ number_format($overviewStats['pengajuan_completed']) }} Pengajuan</h2>
                        <small class="text-dark">Rp {{ number_format($overviewStats['total_nominal_approved'], 1) }}</small>
                    </div>
                    <div class="stat-icon-admin">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="admin-card" style="background: linear-gradient(135deg, var(--warning-color), #ffb84d);">
            <div class="card-body text-white">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="text-dark mb-1">Proses Approval</h6>
                        <h2 class="mb-0">{{ number_format($overviewStats['pengajuan_pending']) }}</h2>
                        <small class="text-dark">Rp {{ number_format($overviewStats['total_nominal_pending'], 1) }}</small>
                    </div>
                    <div class="stat-icon-admin">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Financial Summary -->
<div class="row mb-4">
    <div class="col-12">
        <div class="admin-card financial-summary">
            <div class="row">
                <div class="col-md-3 financial-item border-end" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Menampilkan total keseluruhan anggaran yang telah diajukan oleh requester sebelum melalui proses revisi oleh tim finance. nominal yang di tampilkan disini adalah nominal dari seluruh pengajuan baik ditolak ataupun diterima">
                    <div class="financial-amount">Rp {{ number_format($financialSummary['total_budget_requested'], 0, ',', '.') }}</div> 
                    <div class="financial-label">Total Budget Diminta</div>
                </div>
                <div class="col-md-3 financial-item border-end" 
                    data-bs-toggle="tooltip" 
                    data-bs-placement="top" 
                    title="Menunjukkan jumlah anggaran final yang telah disetujui oleh tim finance setelah melalui proses evaluasi dan revisi dari pengajuan awal.">
                    <div class="financial-amount"><i class="fas fa-check-circle me-2 text-success"></i>
                        Rp {{ number_format($financialSummary['total_budget_approved'], 0, ',', '.') }}</div> 
                    <div class="financial-label">Total Budget Disetujui</div>
                </div>
                <div class="col-md-3 financial-item border-end" 
                     data-bs-toggle="tooltip" 
                     data-bs-placement="top" 
                     title="Total budget yang dikembalikan oleh requester karena terdapat sisa">
                    @php
                        $savingsNet = $financialSummary['total_savings'];
                    @endphp
                    <div class="financial-amount text-success ">
                        <i class="fas fa-piggy-bank me-2"></i>
                        Rp {{ number_format(abs($savingsNet), 1) }}
                    </div>
                    <div class="financial-label">Penghematan Budget</div>
                </div>
                <div class="col-md-3 financial-item" 
                     data-bs-toggle="tooltip" 
                     data-bs-placement="top" 
                     title="Total budget yang diberikan oleh pihak perusahaan kepada requester karena mereka kelebihan budgetnya">
                    @php
                        $savingsNet = $financialSummary['total_overspend'];
                    @endphp
                    <div class="financial-amount text-over"><i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                        Rp {{ number_format(abs($savingsNet), 1) }}
                    </div>
                    <div class="financial-label">Over Budget</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="admin-card chart-card">
            <div class="card-header bg-transparent">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Tren Pengajuan & Nominal (12 Bulan)</h5>
                    <!-- Filter Chart -->
                    <div class="chart-filters">
                        <select id="chartFilter" class="form-select form-select-sm" style="width: auto;">
                            <option value="original">Nominal Asli (Sebelum Revisi)</option>
                            <option value="revised">Nominal Setelah Revisi Finance</option>
                            <option value="compare">Bandingkan Keduanya</option>
                            <option value="rejected">Pengajuan Ditolak</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="monthlyTrendChart" style="height: 360px; position: relative;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="admin-card chart-card">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Distribusi Status</h5>
            </div>
            <div class="card-body">
                <canvas id="statusChart" style="height: 360px; position: relative;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Department Performance & Top Requesters -->
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="admin-card">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Performa Department</h5>
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#departmentDetailModal">
                    <i class="fas fa-chart-bar"></i> Detail
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-admin table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Department</th>
                                <th>Pengajuan</th>
                                <th>Total Nominal</th>
                                <th>Persentase</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($departmentStats as $dept)
                            <tr class="cursor-pointer" onclick="viewDepartmentDetail({{ $dept->department_id }})">
                                <td>
                                    <strong>{{ $dept->department_name }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $dept->total_pengajuan }}</span>
                                    <div class="small text-muted">
                                        <span class="text-primary">{{ $dept->completed }}</span> /
                                        <span class="text-warning">{{ $dept->pending }}</span> /
                                        <span class="text-danger">{{ $dept->rejected }}</span>
                                    </div>
                                </td>
                                <td>Rp {{ number_format($dept->total_nominal, 1) }}</td>
                                <td>
                                    @php
                                        $approvalRate = $dept->total_pengajuan > 0 ? ($dept->completed / $dept->total_pengajuan) * 100 : 0;
                                        $progressClass = $approvalRate >= 80 ? 'bg-success' : ($approvalRate >= 60 ? 'bg-warning' : 'bg-danger');
                                    @endphp
                                    <div class="progress progress-sm mb-1">
                                        <div class="progress-bar {{ $progressClass }}" style="width: {{ $approvalRate }}%"></div>
                                    </div>
                                    <small>{{ number_format($approvalRate, 1) }}%</small>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="admin-card">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Top Requesters</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-admin table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Department</th>
                                <th>Pengajuan</th>
                                <th>Persetase Diterima</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($topRequesters as $requester)
                            <tr class="cursor-pointer" onclick="viewEmployeeDetail({{ $requester->requester_id }})">
                                <td>
                                    <strong>{{ $requester->requester_name }}</strong>
                                    <br><small class="text-primary">Rp {{ number_format($requester->total_nominal, 1) }}</small>
                                </td>
                                <td>{{ $requester->department_name }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $requester->total_pengajuan }}</span>
                                    <br><small class="text-primary">{{ $requester->completed }} selesai</small>
                                </td>
                                <td>
                                    @php
                                        $successRate = $requester->approval_rate;
                                        $badgeClass = $successRate >= 80 ? 'bg-success' : ($successRate >= 60 ? 'bg-warning' : 'bg-danger');
                                    @endphp
                                    <span class="badge badge-status-admin {{ $badgeClass }}">{{ $successRate }}%</span>
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

<!-- Category Performance -->
<div class="row mb-4">
    <div class="col-12">
        <div class="admin-card">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Performa per Kategori</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($categoryPerformance as $category)
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="metric-card border">
                            <i class="{{ $category->icon ?? 'fas fa-folder' }} fa-2x mb-2" style="color: {{ $category->warna ?? '#0e6a39' }}"></i>
                            <h6 class="mb-2">{{ $category->category_name }}</h6>
                            <div class="metric-value">{{ $category->total_pengajuan }}</div>
                            <div class="metric-label">Total Pengajuan</div>
                            <hr class="my-2">
                            
                            <div class="mt-2">
                                <small class="text-primary">Rp {{ number_format($category->total_nominal, 1) }}</small>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activities & Pending Items -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="admin-card">
            <div class="card-header bg-transparent">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Aktivitas Terbaru</h5>
                    <ul class="nav nav-pills nav-sm" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="pill" href="#recent-pengajuan">Pengajuan</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="pill" href="#recent-approvals">Approval</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="pill" href="#recent-settlements">Settlement</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="recent-pengajuan">
                        <div class="activity-timeline">
                            @foreach($recentActivities['recent_pengajuan'] as $pengajuan)
                            <div class="activity-item-admin">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $pengajuan->nomor_pengajuan }}</h6>
                                        <p class="mb-1">{{ Str::limit($pengajuan->judul, 50) }}</p>
                                        <small class="text-muted">
                                            {{ $pengajuan->requester->nama }} • {{ $pengajuan->requester->department->nama }} • 
                                            {{ $pengajuan->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        @php
                                            $statusClass = match($pengajuan->status_pengajuan) {
                                                'approved' => 'bg-success',
                                                'rejected' => 'bg-danger',
                                                'proses' => 'bg-warning',
                                                'completed' => 'bg-info',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge badge-status-admin {{ $statusClass }}">{{ ucfirst($pengajuan->status_pengajuan) }}</span>
                                        <br><small class="text-muted">Rp {{ number_format($pengajuan->nominal_pengajuan, 0, ',', '.') }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="recent-approvals">
                        <div class="activity-timeline">
                            @foreach($recentActivities['recent_approvals'] as $approval)
                            <div class="activity-item-admin">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $approval->pengajuan->nomor_pengajuan }}</h6>
                                        <p class="mb-1">{{ $approval->step_name }}</p>
                                        <small class="text-muted">
                                            {{ $approval->approver->nama }} • {{ $approval->tanggal_approval->diffForHumans() }}
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        @php
                                            $approvalStatusClass = match($approval->status) {
                                                'approved' => 'bg-success',
                                                'rejected' => 'bg-danger',
                                                'waiting' => 'bg-info',
                                                default => 'bg-warning'
                                            };
                                        @endphp
                                        <span class="badge badge-status-admin {{ $approvalStatusClass }}">{{ ucfirst($approval->status) }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="recent-settlements">
                        <div class="activity-timeline">
                            @foreach($recentActivities['recent_settlements'] as $settlement)
                            <div class="activity-item-admin">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">{{ $settlement->nomor_settlement }}</h6>
                                        <p class="mb-1">{{ $settlement->pengajuan->nomor_pengajuan }}</p>
                                        <small class="text-muted">
                                            {{ $settlement->pengajuan->requester->nama }} • {{ $settlement->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-info">{{ ucfirst($settlement->status_settlement) }}</span>
                                        <br><small class="text-muted">Rp {{ number_format($settlement->total_actual, 0, ',', '.') }}</small>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="admin-card">
            <div class="card-header bg-transparent">
                <h5 class="mb-0">Items Menunggu Proses</h5>
            </div>
            <div class="card-body">
                <!-- Pending Approvals -->
                <div class="mb-4">
                    <h6 class="text-muted mb-2">
                        <i class="fas fa-clock text-warning me-1"></i>
                        Menunggu Approval ({{ $pendingItems['pending_approvals']->count() }})
                    </h6>
                    @if($pendingItems['pending_approvals']->count() > 0)
                        @foreach($pendingItems['pending_approvals']->take(3) as $pending)
                        <div class="pending-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>{{ $pending->pengajuan->nomor_pengajuan }}</strong>
                                    <br><small>{{ $pending->step_name }}</small>
                                    <br><small class="text-muted">{{ $pending->approver->nama }}</small>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted">{{ $pending->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @if($pendingItems['pending_approvals']->count() > 3)
                            <div class="text-center">
                                <button class="btn btn-sm btn-outline-primary" onclick="viewAllPendingApprovals()">
                                    +{{ $pendingItems['pending_approvals']->count() - 3 }} lainnya
                                </button>
                            </div>
                        @endif
                    @else
                        <div class="text-center text-muted py-2">
                            <i class="fas fa-check-circle fa-2x mb-2 opacity-50"></i>
                            <p>Tidak ada pending approval</p>
                        </div>
                    @endif
                </div>

                <!-- Pending Settlements -->
                <div class="mb-4">
                    <h6 class="text-muted mb-2">
                        <i class="fas fa-file-invoice text-info me-1"></i>
                        Draft Settlement ({{ $pendingItems['pending_settlements']->count() }})
                    </h6>
                    @if($pendingItems['pending_settlements']->count() > 0)
                        @foreach($pendingItems['pending_settlements']->take(3) as $settlement)
                        <div class="pending-item" style="background: #e7f3ff; border-color: #0dcaf0;">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>{{ $settlement->nomor_settlement }}</strong>
                                    <br><small>{{ $settlement->pengajuan->requester->nama }}</small>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted">{{ $settlement->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-2">
                            <i class="fas fa-check-circle fa-2x mb-2 opacity-50"></i>
                            <p>Tidak ada draft settlement</p>
                        </div>
                    @endif
                </div>

                <!-- Pending Transactions -->
                <div>
                    <h6 class="text-muted mb-2">
                        <i class="fas fa-money-check text-success me-1"></i>
                        Pending Transfer ({{ $pendingItems['pending_transactions']->count() }})
                    </h6>
                    @if($pendingItems['pending_transactions']->count() > 0)
                        @foreach($pendingItems['pending_transactions']->take(3) as $transaction)
                        <div class="pending-item" style="background: #d1e7dd; border-color: #198754;">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>
                                        {{ $transaction->pengajuan ? $transaction->pengajuan->nomor_pengajuan : $transaction->settlement->nomor_settlement }}
                                    </strong>
                                    <br><small>
                                        {{ $transaction->pengajuan ? $transaction->pengajuan->requester->nama : $transaction->settlement->pengajuan->requester->nama }}
                                    </small>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted">{{ $transaction->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center text-muted py-2">
                            <i class="fas fa-check-circle fa-2x mb-2 opacity-50"></i>
                            <p>Tidak ada pending transfer</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Department Detail Modal -->
<div class="modal fade" id="departmentDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Department</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="departmentModalContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Employee Detail Modal -->
<div class="modal fade" id="employeeDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Karyawan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="employeeModalContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Initialize Bootstrap 5 tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart data dari controller
    const chartData = @json($chartData);
    
    // Monthly Trend Chart (Dual Axis)
    const monthlyCtx = document.getElementById('monthlyTrendChart').getContext('2d');
    let monthlyChart = new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: chartData.monthly.map(item => item.month),
            datasets: getChartDatasets('original')
        },
        options: getChartOptions()
    });

    // Chart filter event listener
    document.getElementById('chartFilter').addEventListener('change', function() {
        const filterType = this.value;
        updateChart(filterType);
    });

    function getChartDatasets(filterType) {
        const datasets = [];
        
        switch(filterType) {
            case 'original':
                datasets.push(
                    {
                        type: 'bar',
                        label: 'Jumlah Pengajuan',
                        data: chartData.monthly.map(item => item.pengajuan_count),
                        backgroundColor: 'rgba(14, 106, 57, 0.6)',
                        borderColor: '#0e6a39',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        type: 'line',
                        label: 'Nominal Asli (Juta)',
                        data: chartData.monthly.map(item => item.nominal_original/1000000),
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        borderWidth: 3,
                        fill: false,
                        tension: 0.4,
                        yAxisID: 'y1'
                    }
                );
                break;
                
            case 'revised':
                datasets.push(
                    {
                        type: 'bar',
                        label: 'Jumlah Pengajuan',
                        data: chartData.monthly.map(item => item.pengajuan_count),
                        backgroundColor: 'rgba(14, 106, 57, 0.6)',
                        borderColor: '#0e6a39',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        type: 'line',
                        label: 'Nominal Revisi (Juta)',
                        data: chartData.monthly.map(item => item.nominal_revised/1000000),
                        borderColor: '#ffc107',
                        backgroundColor: 'rgba(255, 193, 7, 0.1)',
                        borderWidth: 3,
                        fill: false,
                        tension: 0.4,
                        yAxisID: 'y1'
                    }
                );
                break;
                
            case 'compare':
                datasets.push(
                    {
                        type: 'bar',
                        label: 'Jumlah Pengajuan',
                        data: chartData.monthly.map(item => item.pengajuan_count),
                        backgroundColor: 'rgba(14, 106, 57, 0.6)',
                        borderColor: '#0e6a39',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        type: 'line',
                        label: 'Nominal Asli (Juta)',
                        data: chartData.monthly.map(item => item.nominal_original/1000000),
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.1)',
                        borderWidth: 3,
                        fill: false,
                        tension: 0.4,
                        yAxisID: 'y1'
                    },
                    {
                        type: 'line',
                        label: 'Nominal Revisi (Juta)',
                        data: chartData.monthly.map(item => item.nominal_revised/1000000),
                        borderColor: '#ffc107',
                        backgroundColor: 'rgba(255, 193, 7, 0.1)',
                        borderWidth: 3,
                        fill: false,
                        tension: 0.4,
                        yAxisID: 'y1'
                    }
                );
                break;
                
            case 'rejected':
                datasets.push(
                    {
                        type: 'bar',
                        label: 'Pengajuan Ditolak',
                        data: chartData.monthly.map(item => item.rejected_count),
                        backgroundColor: 'rgba(220, 53, 69, 0.6)',
                        borderColor: '#dc3545',
                        borderWidth: 1,
                        yAxisID: 'y'
                    },
                    {
                        type: 'line',
                        label: 'Nominal Ditolak (Juta)',
                        data: chartData.monthly.map(item => item.rejected_nominal/1000000),
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        borderWidth: 3,
                        fill: false,
                        tension: 0.4,
                        yAxisID: 'y1'
                    }
                );
                break;
        }
        
        return datasets;
    }

    function getChartOptions() {
        return {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                x: {
                    display: true,
                    title: {
                        display: true,
                        text: 'Bulan'
                    }
                },
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Jumlah Pengajuan'
                    },
                    beginAtZero: true,
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Nominal (Juta Rupiah)'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                    beginAtZero: true,
                }
            }
        };
    }

    function updateChart(filterType) {
        monthlyChart.data.datasets = getChartDatasets(filterType);
        monthlyChart.update();
    }

    // Status Distribution Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: @json(array_column($chartData['status'], 'label')),
            datasets: [{
                data: @json(array_column($chartData['status'], 'value')),
                backgroundColor: @json(array_column($chartData['status'], 'color')),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });
});

// Modal Functions
function viewDepartmentDetail(departmentId) {
    fetch(`/admin/department/${departmentId}/detail`)
        .then(response => response.json())
        .then(data => {
            let content = `
                <div class="row">
                    <div class="col-md-6">
                        <h5>${data.department.nama}</h5>
                        <p class="text-muted">${data.department.deskripsi || 'Tidak ada deskripsi'}</p>
                        <div class="row text-center">
                            <div class="col-6">
                                <h4 class="text-primary">${data.stats.total_employees}</h4>
                                <small>Total Karyawan</small>
                            </div>
                            <div class="col-6">
                                <h4 class="text-success">${data.stats.active_employees}</h4>
                                <small>Aktif</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row text-center">
                            <div class="col-6">
                                <h4 class="text-info">${data.stats.total_pengajuan}</h4>
                                <small>Total Pengajuan</small>
                            </div>
                            <div class="col-6">
                                <h4 class="text-warning">Rp ${(data.stats.total_nominal/1000000).toFixed(1)}M</h4>
                                <small>Total Nominal</small>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <h6>Daftar Karyawan</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead><tr><th>Nama</th><th>Role</th><th>Pengajuan</th></tr></thead>
                        <tbody>
            `;
            
            data.employees.forEach(emp => {
                content += `
                    <tr>
                        <td>${emp.nama}</td>
                        <td>${emp.role_level ? emp.role_level.nama : 'N/A'}</td>
                        <td><span class="badge bg-primary">${emp.pengajuan_count}</span></td>
                    </tr>
                `;
            });
            
            content += `</tbody></table></div>`;
            
            document.getElementById('departmentModalContent').innerHTML = content;
            new bootstrap.Modal(document.getElementById('departmentDetailModal')).show();
        })
        .catch(error => {
            document.getElementById('departmentModalContent').innerHTML = '<p class="text-danger">Error loading data</p>';
        });
}

function viewEmployeeDetail(employeeId) {
    fetch(`/admin/employee/${employeeId}/detail`)
        .then(response => response.json())
        .then(data => {
            let content = `
                <div class="row">
                    <div class="col-md-6">
                        <h5>${data.employee.nama}</h5>
                        <p class="text-muted">
                            ${data.employee.department.nama}<br>
                            ${data.employee.role_level ? data.employee.role_level.nama : 'N/A'}<br>
                            ${data.employee.email}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <div class="row text-center">
                            <div class="col-3">
                                <h4 class="text-primary">${data.pengajuan_stats.total}</h4>
                                <small>Total</small>
                            </div>
                            <div class="col-3">
                                <h4 class="text-success">${data.pengajuan_stats.approved}</h4>
                                <small>Approved</small>
                            </div>
                            <div class="col-3">
                                <h4 class="text-warning">${data.pengajuan_stats.pending}</h4>
                                <small>Pending</small>
                            </div>
                            <div class="col-3">
                                <h4 class="text-danger">${data.pengajuan_stats.rejected}</h4>
                                <small>Rejected</small>
                            </div>
                        </div>
                        <div class="text-center mt-2">
                            <h5 class="text-info">Rp ${(data.pengajuan_stats.total_nominal/1000000).toFixed(1)}M</h5>
                            <small>Total Nominal</small>
                        </div>
                    </div>
                </div>
                <hr>
                <h6>Pengajuan Terbaru</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead><tr><th>Nomor</th><th>Kategori</th><th>Nominal</th><th>Status</th></tr></thead>
                        <tbody>
            `;
            
            data.recent_pengajuan.forEach(pengajuan => {
                let statusClass = pengajuan.status_pengajuan === 'approved' ? 'success' : 
                                 pengajuan.status_pengajuan === 'rejected' ? 'danger' : 'warning';
                content += `
                    <tr>
                        <td>${pengajuan.nomor_pengajuan}</td>
                        <td>${pengajuan.kategori_pengajuan.nama}</td>
                        <td>Rp ${new Intl.NumberFormat('id-ID').format(pengajuan.nominal_pengajuan)}</td>
                        <td><span class="badge bg-${statusClass}">${pengajuan.status_pengajuan}</span></td>
                    </tr>
                `;
            });
            
            content += `</tbody></table></div>`;
            
            document.getElementById('employeeModalContent').innerHTML = content;
            new bootstrap.Modal(document.getElementById('employeeDetailModal')).show();
        })
        .catch(error => {
            document.getElementById('employeeModalContent').innerHTML = '<p class="text-danger">Error loading data</p>';
        });
}

function viewAllPendingApprovals() {
    // Redirect to detailed pending approvals page
    window.location.href = '/admin/pending-approvals';
}
</script>
@endsection