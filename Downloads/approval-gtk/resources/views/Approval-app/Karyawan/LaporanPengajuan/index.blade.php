@extends('Approval-app.Layout.approver-main')

@section('head')
<!-- Tambahkan di bagian <head> template Blade -->

<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="update-status-url" content="{{ url('/laporan-pengajuan/update-status') }}">
<meta name="update-settlement-status-url" content="{{ route('laporan-pengajuan.update-settlement-status', '') }}">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    .swal2-container {
        z-index: 99999 !important;
    }
    .swal2-overlay {
        z-index: 99999 !important;
    }
    
    #approvalModal.modal.show {
        background-color: rgba(0, 0, 0, 0.7) !important;
    }
    
    #approvalModal .modal-backdrop {
        background-color: rgba(0, 0, 0, 0.8) !important;
    }
    
    /* Tambahan efek blur untuk elemen di belakang */
    body.modal-open {
        overflow: hidden;
    }
    
    #approvalModal.show ~ * {
        filter: blur(2px);
        transition: filter 0.3s ease;
    }
    
    .submission-card {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    
    .submission-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .submission-card.pending {
        border-left-color: #ffc107;
        /*background: linear-gradient(135deg, rgba(255,193,7,0.1) 0%, rgba(255,255,255,1) 100%);*/
    }
    
    .submission-card.approved {
        border-left-color: #28a745;
    }
    
    .submission-card.rejected {
        border-left-color: #dc3545;
    }
    
    .avatar-initial {
        font-weight: 600;
        font-size: 0.75rem;
    }
    
    .status-badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
    }
    
    .action-buttons .btn {
        margin: 0 2px;
        transition: all 0.2s ease;
    }
    
    .action-buttons .btn:hover {
        transform: scale(1.05);
    }
    
    .priority-indicator {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }
    
    .empty-state {
        padding: 3rem 1rem;
        text-align: center;
        color: #6c757d;
    }
    
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    
    .table-responsive {
        border-radius: 0.375rem;
        overflow: hidden;
    }
    
    .table th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    
    
    .modal-header .btn-close {
        filter: invert(1);
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
    }
    
    .btn-group .btn {
        border-radius: 0.375rem !important;
        margin: 0 1px;
    }
    
    
    /* Form Detail Styles */
    .form-detail-item {
        background-color: #f8f9fa;
        border-left: 4px solid #007bff;
        transition: all 0.2s ease;
    }
    
    .form-detail-item:hover {
        background-color: #e9ecef;
        border-left-color: #0056b3;
    }
    
    /* Section Header Styles */
    .section-header {
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.75rem;
        margin-bottom: 1rem;
    }
    
    .section-header h6 {
        color: #495057;
        font-weight: 600;
        margin-bottom: 0;
    }
    
    /* Status Badge Improvements */
    .status-pending {
        background-color: #ffc107;
        color: #212529;
    }
    
    .status-approved {
        background-color: #28a745;
        color: white;
    }
    
    .status-rejected {
        background-color: #dc3545;
        color: white;
    }
    
    .status-revision {
        background-color: #6c757d;
        color: white;
    }
    
    .status-proses {
        background-color: #ffba57;
        color: white;
    }
    
    /* Custom Alert Styles */
    .custom-alert {
        border: none;
        border-radius: 0.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        backdrop-filter: blur(10px);
    }
    
    .custom-alert .fas {
        opacity: 0.8;
    }
    
    /* Loading States */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(255,255,255,0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }
    
    .spinner-border-lg {
        width: 3rem;
        height: 3rem;
    }
    
    /* File Upload Styles */
    .file-item {
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 1rem;
        transition: all 0.2s ease;
    }
    
    .file-item:hover {
        background-color: #e9ecef;
        border-color: #007bff;
        transform: translateY(-1px);
    }
    
    .file-icon {
        font-size: 2rem;
        color: #007bff;
        margin-bottom: 0.5rem;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .table-responsive {
            font-size: 0.875rem;
        }
        
        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }
        
        .btn-group .btn {
            margin: 0;
        }
        
        .timeline-item:not(:last-child)::before {
            left: 15px;
        }
        
        .modal-dialog {
            margin: 0.5rem;
        }
        
        .custom-alert {
            min-width: 280px;
            max-width: 90vw;
            right: 10px !important;
            left: 10px !important;
        }
    }
    
    @media (max-width: 576px) {
        .card-body {
            padding: 1rem 0.75rem;
        }
        
        .table th,
        .table td {
            padding: 0.5rem 0.25rem;
            font-size: 0.8rem;
        }
        
        .btn-sm {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
    }
    
    /* Print Styles */
    @media print {
        .btn, .modal, .custom-alert {
            display: none !important;
        }
        
        .card {
            border: 1px solid #ddd;
            box-shadow: none;
        }
        
        .table th {
            background-color: #f5f5f5 !important;
            -webkit-print-color-adjust: exact;
        }
    }
</style>

<!--style untuk timeline pengajuan-->
<style>
    /* Horizontal Progress Timeline Styles */
    .timeline {
        display: flex;
        align-items: center;
        justify-content: center;
        /*flex-wrap: wrap;*/
        gap: 20px;
        padding: 20px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        margin: 15px 0;
        position: relative;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #dee2e6 0%, #6c757d 50%, #dee2e6 100%);
        z-index: 1;
        transform: translateY(-50%);
    }
    
    .timeline-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        position: relative;
        z-index: 2;
        min-width: 150px;
        max-width: 180px;
        flex: 0 0 auto;
    }
    
    .timeline-marker {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 12px;
        border: 3px solid;
        background: white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        font-size: 18px;
    }
    
    /* Status-specific styles */
    .timeline-item.pending .timeline-marker {
        border-color: #ffc107;
        color: #ffc107;
        background: #fff3cd;
    }
    
    .timeline-item.active .timeline-marker {
        border-color: #007bff;
        color: #007bff;
        background: #cce5ff;
        animation: pulse 2s infinite;
    }
    
    .timeline-item.completed .timeline-marker {
        border-color: #28a745;
        color: #28a745;
        background: #d4edda;
    }
    
    .timeline-item.rejected .timeline-marker {
        border-color: #dc3545;
        color: #dc3545;
        background: #f8d7da;
    }
    
    .timeline-content {
        background: white;
        padding: 12px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
        min-height: 100px;
        width: 100%;
    }
    
    .timeline-content h6 {
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 6px;
        color: #495057;
    }
    
    .timeline-content p {
        font-size: 0.8rem;
        margin-bottom: 4px;
    }
    
    .timeline-content small {
        font-size: 0.75rem;
    }
    
    /* Pulse animation for active state */
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(0, 123, 255, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(0, 123, 255, 0);
        }
    }
    
    /* Responsive design */
    @media (max-width: 768px) {
        .timeline {
            flex-direction: column;
            gap: 15px;
        }
        
        .timeline::before {
            display: none;
        }
        
        .timeline-item {
            width: 100%;
            max-width: 100%;
        }
    }
    
    /* Perjalanan Dinas Table Styles */
    .perjalanan-table {
        font-size: 12px;
    }
    
    .perjalanan-table th,
    .perjalanan-table td {
        padding: 8px 5px;
        /*vertical-align: middle;*/
        /*text-align: center;*/
    }
    
    .perjalanan-table .total-cell {
        background-color: #f8f9fa;
        font-weight: bold;
    }
    
    .perjalanan-table .total-grand {
        background-color: #007bff;
        color: white;
        font-weight: bold;
    }
    
    /* Status badges */
    .status-badge {
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .status-pending {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }
    
    .status-approved {
        background-color: #d1edff;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .status-rejected {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    .status-revision {
        background-color: #d4edda;
        color: #0c5460;
        border: 1px solid #b8daff;
    }
    
    .status-completed {
        background-color: #d1edff;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    
    /* Settlement Row Styling */
    .table-warning {
        background-color: rgba(255, 243, 205, 0.3) !important;
        border-left: 4px solid #ffc107;
    }
    
    .status-badge.status-settlement {
        background-color: #ffc107;
        color: #212529;
        font-weight: 600;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    /* Legend untuk membantu user */
    .settlement-legend {
        /* background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); */
        border: 1px solid #ffc107;
        border-radius: 8px;
        padding: 10px 15px;
        margin-bottom: 20px;
    }
    
    .settlement-legend h6 {
        color: #856404;
        margin-bottom: 5px;
    }
    
    .settlement-legend p {
        color: #6c757d;
        margin-bottom: 0;
        font-size: 13px;
    }
    
    .settlement-timeline {
        position: relative;
        padding-left: 30px;
    }
    
    .settlement-timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #dee2e6;
    }
    
    .settlement-timeline .timeline-item {
        position: relative;
        margin-bottom: 30px;
        padding-left: 25px;
    }
    
    .settlement-timeline .timeline-item:last-child {
        margin-bottom: 0;
    }
    
    .settlement-timeline .timeline-marker {
        position: absolute;
        left: -23px;
        top: 0;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: bold;
        border: 3px solid #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .settlement-timeline .timeline-item.pending .timeline-marker {
        background: #ffc107;
        color: #000;
        border-color: #fff;
    }
    
    .settlement-timeline .timeline-item.active .timeline-marker {
        background: #007bff;
        color: #fff;
        border-color: #fff;
        animation: pulse 2s infinite;
    }
    
    .settlement-timeline .timeline-item.completed .timeline-marker {
        background: #28a745;
        color: #fff;
        border-color: #fff;
    }
    
    .settlement-timeline .timeline-item.rejected .timeline-marker {
        background: #dc3545;
        color: #fff;
        border-color: #fff;
    }
    
    .settlement-timeline .timeline-content {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .settlement-timeline .timeline-item.active .timeline-content {
        border-color: #007bff;
        background: #e3f2fd;
    }
    
    .settlement-timeline .timeline-item.completed .timeline-content {
        border-color: #28a745;
        background: #e8f5e8;
    }
    
    .settlement-timeline .timeline-item.rejected .timeline-content {
        border-color: #dc3545;
        background: #fde8e8;
    }
    
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7);
        }
        70% {
            box-shadow: 0 0 0 10px rgba(0, 123, 255, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(0, 123, 255, 0);
        }
    }
    .row-danger-left {
        border-left: 5px solid #dc3545;     /* Garis merah di kiri */
        background-color: rgba(220, 53, 69, 1); /* Merah muda tipis (10% opacity) */
    }

</style>

</style>
@endsection

@section('content')
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Daftar Laporan Pengajuan</h5>
                </div>
            </div>
        </div>
    </div>
</div>
@php
    // LOGIKA PEMISAHAN DATA (ACTIVE vs HISTORY)
    $activeItems = collect();
    $historyItems = collect();

    foreach($pengajuanList as $pengajuan) {
        // Cek apakah ada 'tugas aktif' untuk user ini di pengajuan tersebut.
        // Tugas aktif = status 'pending' atau 'proses'.
        // Kita mengecek seluruh history approval user ini (karena bisa jadi Approval Awal sudah 'approved', tapi Approval Settlement baru muncul dan statusnya 'pending')
        
        $hasActiveTask = $pengajuan->progressApprovals->contains(function ($progress) {
            return in_array($progress->status, ['pending', 'proses']);
        });

        // Jika ada tugas aktif (entah itu pengajuan awal atau settlement), masukkan ke Active Panel
        if ($hasActiveTask) {
            $activeItems->push($pengajuan);
        } else {
            // Jika semua tugas user ini sudah approved/rejected (tidak ada yang pending/proses), masukkan ke History
            $historyItems->push($pengajuan);
        }
    }
@endphp

<div class="row">
    <div class="col-12">
        <div class="card table-card shadow-sm">
            
            <div class="card-header border-bottom-0 pb-0">
                <ul class="nav nav-tabs" id="pengajuanTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#active-panel" type="button" role="tab" aria-controls="active-panel" aria-selected="true">
                            <i class="fas fa-inbox me-2"></i>Perlu Tindakan 
                            @if($activeItems->count() > 0)
                                <span class="badge-counter">{{ $activeItems->count() }}</span>
                            @endif
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-panel" type="button" role="tab" aria-controls="history-panel" aria-selected="false">
                            <i class="fas fa-history me-2"></i>Riwayat Approval
                        </button>
                    </li>
                </ul>
                
                <div class="card-header-right">
                    <div class="btn-group card-option">
                        <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="feather icon-more-horizontal"></i>
                        </button>
                        <ul class="list-unstyled card-option dropdown-menu dropdown-menu-right">
                            <li class="dropdown-item full-card"><a href="#!"><span><i class="feather icon-maximize"></i> maximize</span><span style="display:none"><i class="feather icon-minimize"></i> Restore</span></a></li>
                            <li class="dropdown-item minimize-card"><a href="#!"><span><i class="feather icon-minus"></i> collapse</span><span style="display:none"><i class="feather icon-plus"></i> expand</span></a></li>
                            <li class="dropdown-item reload-card"><a href="#!"><i class="feather icon-refresh-cw"></i> reload</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="card-body pt-3">
                <div class="tab-content" id="pengajuanTabsContent">
                    
                    <div class="tab-pane fade show active" id="active-panel" role="tabpanel" aria-labelledby="active-tab">
                        <div class="table-responsive">
                            <table class="table table-hover w-100" id="tableActive">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nomor</th>
                                        <th>Judul</th>
                                        <th>Requester</th>
                                        <th>Nominal</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                        <!--<th>Progress</th>-->
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activeItems as $index => $pengajuan)
                                        @include('Approval-app.Karyawan.LaporanPengajuan.partials.row_item', ['pengajuan' => $pengajuan, 'index' => $index, 'type' => 'active'])
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="history-panel" role="tabpanel" aria-labelledby="history-tab">
                        <div class="table-responsive">
                            <table class="table table-hover w-100" id="tableHistory">
                                <thead class="table-light">
                                    <tr>
                                        <th>No</th>
                                        <th>Nomor</th>
                                        <th>Judul</th>
                                        <th>Requester</th>
                                        <th>Nominal</th>
                                        <th>Tanggal</th>
                                        <th>Keputusan Anda</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($historyItems as $index => $pengajuan)
                                        @include('Approval-app.Karyawan.LaporanPengajuan.partials.row_item', ['pengajuan' => $pengajuan, 'index' => $index, 'type' => 'history'])
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@include('Approval-app.Karyawan.LaporanPengajuan.partials.modals')
    
    <!-- Modal -->
    <div class="modal fade" id="notifikasiModal" tabindex="-1" aria-labelledby="notifikasiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notifikasiModalLabel">
                        <i class="fas fa-bell me-2"></i>Kirim Notifikasi Pengingat
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
    
                <div class="modal-body text-break">
                    <div class="alert alert-info">
                        <h6 class="alert-heading mb-2">
                            <i class="fas fa-info-circle me-1"></i>Informasi Notifikasi
                        </h6>
                        <p class="mb-2"><strong>Requester:</strong> <span id="modal-requester-nama"></span></p>
                        <p class="mb-2"><strong>Email:</strong> <span id="modal-requester-email"></span></p>
                        <p class="mb-2"><strong>Pengajuan:</strong> <span id="modal-pengajuan-nomor"></span></p>
                        <p class="mb-0"><strong>Sisa Dana:</strong> Rp. <span id="modal-selisih"></span></p>
                    </div>
                    
                    <h6 class="mb-2">
                        <strong>Anda akan mengirimkan notifikasi kepada requester.</strong>
                    </h6>
                    <p class="mb-0">
                        Notifikasi ini berfungsi sebagai pengingat bahwa masih terdapat sisa dana dari proses settlement yang belum dikembalikan. 
                    </p>
                </div>
    
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
    
                    <form method="POST" action="{{ route('kirim-notifikasi-refund') }}" id="notifikasiForm">
                        @csrf
                        <input type="hidden" name="settlement_id" id="hidden-settlement-id" value="">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-1"></i>Kirim Notifikasi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Pengajuan -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content" style="border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                <div class="modal-header" style=" color: white; border-radius: 15px 15px 0 0; padding: 20px 25px; border: none;">
                    <h5 class="modal-title text-white" id="detailModalLabel" style="font-weight: 600; font-size: 1.1rem;">
                        <i class="fas fa-file-alt me-2"></i>Detail Pengajuan & Approval
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
                </div>
                <div class="modal-body" id="detailModalBody" style="padding: 0; background-color: #f8f9fa;">
                    <div class="d-flex justify-content-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" id="detailModalFooter" style="display: none; background-color: #f8f9fa; border-top: 1px solid #dee2e6; padding: 15px 25px; border-radius: 0 0 15px 15px;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; padding: 8px 16px;">
                        <i class="fas fa-times me-1"></i>Tutup
                    </button>
                    <button type="button" class="btn btn-info" id="btnIntervention" style="border-radius: 8px; padding: 8px 16px; display: none;">
                        <i class="fas fa-calculator me-1"></i>Koreksi Nominal
                    </button>
                    <button type="button" class="btn btn-success" id="btnApprove" style="border-radius: 8px; padding: 8px 16px;">
                        <i class="fas fa-check me-1"></i>Setujui
                    </button>
                    <button type="button" class="btn btn-warning" id="btnRevision" style="border-radius: 8px; padding: 8px 16px; color: white;">
                        <i class="fas fa-edit me-1"></i>Minta Revisi
                    </button>
                    <button type="button" class="btn btn-danger" id="btnReject" style="border-radius: 8px; padding: 8px 16px;">
                        <i class="fas fa-times me-1"></i>Tolak
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Approval Action -->
    <div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 500px;">
            <div class="modal-content" style="border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                <div class="modal-header" style=" color: white; border-radius: 15px 15px 0 0; padding: 20px 25px; border: none;">
                    <h5 class="modal-title" id="approvalModalLabel" style="font-weight: 600;">Konfirmasi Approval</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
                </div>
                <form id="approvalForm">
                    @csrf
                    <div class="modal-body" style="padding: 25px;">
                        <input type="hidden" id="pengajuanId" value="">
                        <input type="hidden" id="approvalStatus" value="">
                        
                        <div class="alert alert-info" id="approvalMessage" style="border: none; border-radius: 10px; background-color: #e3f2fd; color: #1565c0; border-left: 4px solid #2196f3; padding: 15px;"></div>
                        
                        <div class="mb-3">
                            <label for="catatan" class="form-label" style="font-weight: 600; color: #333;">Catatan <span class="text-muted">(Opsional)</span></label>
                            <textarea class="form-control" id="catatan" name="catatan" rows="3" 
                                      placeholder="Berikan catatan untuk keputusan Anda..."
                                      style="border-radius: 8px; border: 1px solid #ddd; padding: 12px; resize: vertical;"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #dee2e6; padding: 15px 25px; border-radius: 0 0 15px 15px;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; padding: 8px 16px;">
                            <i class="fas fa-times me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn" id="confirmApprovalBtn" style="border-radius: 8px; padding: 8px 16px;">
                            <i class="fas fa-check me-1"></i>Konfirmasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Settlement Detail -->
    <div class="modal fade" id="settlementDetailModal" tabindex="-1" aria-labelledby="settlementDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-dark">
                    <h5 class="modal-title text-white" id="settlementDetailModalLabel">
                        <i class="fas fa-receipt me-2"></i>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="settlementDetailModalBody">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Memuat detail settlement...</p>
                    </div>
                </div>
                <div class="modal-footer" id="settlementDetailModalFooter" style="display: none;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; padding: 8px 16px;">
                        <i class="fas fa-times me-1"></i>Tutup
                    </button>
                    <!--<button type="button" class="btn btn-primary" id="btnSendNotification" style="border-radius: 8px; padding: 8px 16px; display: none;">-->
                    <!--    <i class="fas fa-bell me-1"></i>Kirim Notifikasi-->
                    <!--</button>-->
                    <button type="button" class="btn btn-info" id="btnInterventionSettlement" style="border-radius: 8px; padding: 8px 16px;">
                        <i class="fas fa-calculator me-1"></i>Revisi Nominal
                    </button>
                    <button type="button" class="btn btn-success" id="btnSettlementApprove" style="border-radius: 8px; padding: 8px 16px;">
                        <i class="fas fa-check me-1"></i>Setujui Settlement
                    </button>
                    <button type="button" class="btn btn-warning" id="btnSettlementRevision" style="border-radius: 8px; padding: 8px 16px;">
                        <i class="fas fa-edit me-1"></i>Minta Revisi
                    </button>
                    <button type="button" class="btn btn-danger" id="btnSettlementReject" style="border-radius: 8px; padding: 8px 16px;">
                        <i class="fas fa-times me-1"></i>Tolak Settlement
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Konfirmasi Kirim Notifikasi -->
    <div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white" id="notificationModalLabel">
                        <i class="fas fa-bell me-2"></i>Kirim Notifikasi Pengembalian
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <p><strong>Nomor Settlement:</strong> <span id="notifSettlementNumber"></span></p>
                        <p><strong>Nama Requester:</strong> <span id="notifRequesterName"></span></p>
                        <p><strong>Nominal Pengembalian:</strong> <span id="notifRefundAmount" class="text-danger fw-bold"></span></p>
                    </div>
                    <div class="mb-3">
                        <label for="notificationMessage" class="form-label">Pesan Tambahan (Opsional)</label>
                        <textarea class="form-control" id="notificationMessage" rows="3" placeholder="Masukkan pesan tambahan untuk requester..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <!--<button type="button" class="btn btn-primary" id="confirmSendNotification">-->
                    <!--    <i class="fas fa-paper-plane me-1"></i>Kirim Notifikasi-->
                    <!--</button>-->
                </div>
            </div>
        </div>
    </div>
    
    <!-- TAMBAHKAN: Modal untuk Intervensi Finance -->
    <div class="modal fade" id="interventionModal" tabindex="-1" aria-labelledby="interventionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl " >
            <div class="modal-content" style="border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                <div class="modal-header" style="background: linear-gradient(135deg, #17a2b8, #138496); color: white; border-radius: 15px 15px 0 0; padding: 20px 25px; border: none;">
                    <h5 class="modal-title" id="interventionModalLabel" style="font-weight: 600;">
                        <i class="fas fa-calculator me-2"></i>Intervensi Detail Pengajuan - Finance
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
                </div>
                <form id="interventionForm">
                    @csrf
                    <div class="modal-body" style="padding: 25px;">
                        <input type="hidden" id="interventionPengajuanId" value="">
                        
                        <div class="alert alert-info" style="border: none; border-radius: 10px; background-color: #e8f4f8; color: #0c5460; border-left: 4px solid #17a2b8; padding: 15px; margin-bottom: 20px;">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Intervensi Finance:</strong> Anda dapat mengubah nilai detail pengajuan sebelum proses approval dilanjutkan.
                        </div>
                        
                        <!-- Container untuk detail items yang bisa diintervensi -->
                        <div id="detailItemsContainer">
                            <!-- Detail items akan diisi via JavaScript -->
                        </div>
                        
                        <!-- Catatan Intervensi -->
                        <div class="mb-3">
                            <label for="catatanIntervensi" class="form-label" style="font-weight: 600; color: #333;">
                                Catatan Intervensi <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="catatanIntervensi" name="catatan_intervensi" rows="4" 
                                      placeholder="Jelaskan alasan perubahan nilai detail..."
                                      style="border-radius: 8px; border: 1px solid #ddd; padding: 12px; resize: vertical;"></textarea>
                            <small class="text-muted">Wajib diisi untuk mendokumentasikan alasan intervensi</small>
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #dee2e6; padding: 15px 25px; border-radius: 0 0 15px 15px;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; padding: 8px 16px;">
                            <i class="fas fa-times me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-info" style="border-radius: 8px; padding: 8px 16px;">
                            <i class="fas fa-save me-1"></i>Simpan Intervensi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Settlement Intervention Modal - Tambahkan setelah modal settlement detail -->
    <div class="modal fade" id="settlementInterventionModal" tabindex="-1" aria-labelledby="settlementInterventionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl ">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title text-dark" id="settlementInterventionModalLabel">
                        <i class="fas fa-calculator me-2"></i>Revisi Detail Settlement - Finance
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="settlementInterventionForm">
                    <div class="modal-body">
                        <input type="hidden" id="settlementInterventionPengajuanId" value="">
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">
                                        <i class="fas fa-info-circle me-2"></i>Informasi Revisi Settlement
                                    </h6>
                                    <p class="mb-0">
                                        Sebagai departemen Finance, Anda dapat mengubah detail settlement (keterangan, nominal, dan kategori biaya) 
                                        sebelum menyetujui. Perubahan akan dihitung ulang secara otomatis dan notifikasi akan dikirim ke requester 
                                        setelah layer terakhir Finance menyetujui.
                                    </p>
                                </div>
                            </div>
                        </div>
    
                        <div class="row">
                            <div class="col-12">
                                <div id="settlementDetailItemsContainer">
                                    <!-- Dynamic content will be inserted here -->
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="settlementCatatanIntervensi" class="form-label">
                                    <i class="fas fa-sticky-note me-1"></i>
                                    Catatan Revisi <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control" 
                                          id="settlementCatatanIntervensi" 
                                          name="catatan_intervensi" 
                                          rows="3" 
                                          placeholder="Jelaskan alasan perubahan detail settlement..." 
                                          required 
                                          style="border-radius: 8px;"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" style="padding: 20px; background-color: #f8f9fa;">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px; padding: 10px 20px;">
                            <i class="fas fa-times me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-warning text-dark" style="border-radius: 8px; padding: 10px 20px; font-weight: 600;">
                            <i class="fas fa-save me-1"></i>Simpan Revisi Settlement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!--@include('Approval-app.Karyawan.LaporanPengajuan.partials.modals')-->

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.13.4/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // Script untuk mengisi data modal
        document.addEventListener('DOMContentLoaded', function() {
            const notifikasiModal = document.getElementById('notifikasiModal');
            
            notifikasiModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                
                // Ambil data dari button
                const settlementId = button.getAttribute('data-settlement-id');
                const pengajuanNomor = button.getAttribute('data-pengajuan-nomor');
                const requesterNama = button.getAttribute('data-requester-nama');
                const requesterEmail = button.getAttribute('data-requester-email');
                const selisih = button.getAttribute('data-selisih');
                
                // Isi data ke modal
                document.getElementById('modal-requester-nama').textContent = requesterNama;
                document.getElementById('modal-requester-email').textContent = requesterEmail;
                document.getElementById('modal-pengajuan-nomor').textContent = pengajuanNomor;
                document.getElementById('modal-selisih').textContent = selisih;
                document.getElementById('hidden-settlement-id').value = settlementId;
            });
        });
        
        
        
        // --- SCRIPT FIX UNTUK DATATABLE & FILTER ---
        $(document).ready(function() {
            // 1. Reset Switch ke posisi OFF saat halaman dimuat
            $('#toggleHistory').prop('checked', false);
    
            // 2. Hapus filter lama (jika ada sisa dari reload sebelumnya)
            while($.fn.dataTable.ext.search.length > 0) {
                $.fn.dataTable.ext.search.pop();
            }
    
            // 3. Buat Filter Custom Baru
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    // Ambil status switch
                    var showHistory = $('#toggleHistory').is(':checked');
                    
                    // Ambil Node baris (TR)
                    var rowNode = settings.aoData[dataIndex].nTr;
                    
                    // Ambil nilai atribut data-category
                    // Gunakan .getAttribute() native JS agar lebih akurat membaca DOM
                    var category = rowNode.getAttribute('data-category');
    
                    // Debugging di Console (Tekan F12 di browser -> Console untuk melihat ini)
                    // console.log(`Row ${dataIndex}: Category=${category}, ShowHistory=${showHistory}`);
    
                    // LOGIKA FILTER:
                    if (category === 'active') {
                        return true; // Active selalu tampil
                    }
                    
                    if (category === 'history') {
                        return showHistory; // History ikut status switch
                    }
    
                    return true; // Default tampil
                }
            );
    
            // 4. Inisialisasi DataTables dengan 'destroy: true'
            // 'destroy: true' penting untuk menimpa inisialisasi otomatis dari template (jika ada)
            var table = $('#pengajuanTable').DataTable({
                destroy: true, // <--- PENTING: Hancurkan instance lama
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
                },
                pageLength: 10,
                order: [[6, 'desc']], // Sort berdasarkan Tanggal/Status (Kolom index 6)
                columnDefs: [
                    { orderable: false, targets: [8] } // Matikan sort di kolom Aksi
                ]
            });
    
            // 5. Trigger gambar ulang saat load agar filter langsung jalan
            table.draw();
    
            // 6. Event Listener saat Switch diklik
            $('#toggleHistory').on('change', function() {
                table.draw(); // Gambar ulang tabel sesuai posisi switch
            });
        });
    </script>

    <script>
        // JavaScript untuk handle notifikasi
        $(document).ready(function() {
            let currentSettlementId = null;
            
            // Event handler untuk tombol kirim notifikasi
            $('#btnSendNotification').on('click', function() {
                if (currentSettlementId) {
                    // Load data settlement untuk modal notifikasi
                    $.ajax({
                        url: '/settlement/' + currentSettlementId + '/notification-data',
                        method: 'GET',
                        success: function(response) {
                            $('#notifSettlementNumber').text(response.nomor_settlement);
                            $('#notifRequesterName').text(response.requester_name);
                            $('#notifRefundAmount').text(response.mata_uang + ' ' + response.refund_amount);
                            $('#notificationModal').modal('show');
                        },
                        error: function() {
                            alert('Gagal memuat data settlement');
                        }
                    });
                }
            });
            
            // Konfirmasi kirim notifikasi
            $('#confirmSendNotification').on('click', function() {
                const message = $('#notificationMessage').val();
                
                $.ajax({
                    url: '/settlement/' + currentSettlementId + '/send-notification',
                    method: 'POST',
                    data: {
                        message: message,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function() {
                        $('#confirmSendNotification').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Mengirim...');
                    },
                    success: function(response) {
                        $('#notificationModal').modal('hide');
                        $('#settlementDetailModal').modal('hide');
                        alert('Notifikasi berhasil dikirim ke requester');
                        
                        // Reset form
                        $('#notificationMessage').val('');
                    },
                    error: function() {
                        alert('Gagal mengirim notifikasi');
                    },
                    // complete: function() {
                    //     $('#confirmSendNotification').prop('disabled', false).html('<i class="fas fa-paper-plane me-1"></i>Kirim Notifikasi');
                    // }
                });
            });
            
            // Update fungsi showDetailSettlement yang sudah ada
            window.showDetailSettlement = function(settlementId) {
                currentSettlementId = settlementId;
                
                // AJAX call untuk load detail settlement
                $.ajax({
                    url: '/settlement/' + settlementId + '/detail',
                    method: 'GET',
                    success: function(response) {
                        // Populate modal content...
                        $('#settlementDetailModalBody').html(response.html);
                        $('#settlementDetailModalFooter').show();
                        
                        // Show/hide notification button based on status_realisasi
                        if (response.settlement.status_realisasi === 'under') {
                            $('#btnSendNotification').show();
                        } else {
                            $('#btnSendNotification').hide();
                        }
                    },
                    error: function() {
                        $('#settlementDetailModalBody').html('<div class="alert alert-danger">Gagal memuat detail settlement</div>');
                    }
                });
            };
        });
    </script>

    <script>
        // Setup CSRF token untuk semua AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Initialize DataTable
        $(document).ready(function() {
            $('#pengajuanTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
                },
                pageLength: 10,
                order: [[6, 'desc']], // Sort by tanggal pengajuan descending
                columnDefs: [
                    { orderable: false, targets: [9] } // Disable sorting for action column
                ]
            });
        });
        
        // Variables for modal management
        let currentPengajuanId = null;
        
         // logic untuk finance intervensi
        function setupFinanceIntervention(pengajuanData) {
            const btnIntervention = document.getElementById('btnIntervention');
            
            // Tampilkan button intervensi hanya untuk Finance yang bisa approve
            if (pengajuanData.is_finance_user && pengajuanData.can_intervene) {
                btnIntervention.style.display = 'inline-block';
                
                // Event listener untuk button intervensi
                btnIntervention.onclick = function() {
                    showInterventionModal(pengajuanData);
                };
            } else {
                btnIntervention.style.display = 'none';
            }
        }
        
        function showInterventionModal(pengajuanData) {
            // Set data ke modal
            document.getElementById('interventionPengajuanId').value = pengajuanData.id;
            
            // Reset form
            document.getElementById('interventionForm').reset();
            document.getElementById('interventionPengajuanId').value = pengajuanData.id;
            
            // Generate detail items yang bisa diintervensi
            generateDetailItemsForIntervention(pengajuanData.detail_fields);
            
            // Show modal
            const interventionModal = new bootstrap.Modal(document.getElementById('interventionModal'));
            interventionModal.show();
        }
        
        function generateDetailItemsForIntervention(detailFields) {
            console.log('Detail fields received:', detailFields);
            
            const container = document.getElementById('detailItemsContainer');
            
            // Filter hanya field yang bisa diintervensi (currency, number, text dengan nilai numerik)
            const intervenableFields = detailFields.filter(field => {
                const isIntervenableType = ['currency', 'number'].includes(field.type);
                const hasValidValue = parseFloat(field.value) > 0;
                return isIntervenableType && hasValidValue;
            });
            
            if (intervenableFields.length === 0) {
                container.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Tidak ada detail pengajuan yang dapat diintervensi (tidak ada field dengan nilai numerik).
                    </div>
                `;
                return;
            }
            
            let htmlContent = `
                <div class="card" style="border: 1px solid #dee2e6; border-radius: 10px; margin-bottom: 20px;">
                    <div class="card-header" style="background-color: #f8f9fa; border-bottom: 1px solid #dee2e6; padding: 15px;">
                        <h6 class="mb-0" style="font-weight: 600; color: #495057;">
                            <i class="fas fa-list me-2"></i>Detail Pengajuan yang Dapat Diintervensi
                        </h6>
                    </div>
                    <div class="card-body p-2">
                        <div class="table-responsive p-2">
                            <table class="table table-hover mb-0">
                                <thead style="background-color: #f8f9fa;">
                                    <tr>
                                        <th style="padding: 12px 15px; font-weight: 600; color: #495057; border-bottom: 2px solid #dee2e6;">Item</th>
                                        <th style="padding: 12px 15px; font-weight: 600; color: #495057; border-bottom: 2px solid #dee2e6;">Nilai Final</th>
                                        <th style="padding: 12px 15px; font-weight: 600; color: #495057; border-bottom: 2px solid #dee2e6;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
            `;
            
            intervenableFields.forEach((field, index) => {
                const detailId = field.detail_id || field.id || 0;
                const isNumericValue = isNumeric(field.value);
                
                // Format nilai awal (1000000 -> 1.000.000)
                let displayValue = isNumericValue ? new Intl.NumberFormat('id-ID').format(parseFloat(field.value)) : field.value;
                
                const isHotelOrMakan = field.label.toLowerCase().includes('hotel') || 
                    field.label.toLowerCase().includes('makan') ||
                    field.label.toLowerCase().includes('penginapan') ||
                    field.label.toLowerCase().includes('konsumsi');
                
                htmlContent += `
                    <tr data-detail-id="${detailId}" data-field-type="${field.type}">
                        <td style="padding: 15px; vertical-align: middle;">
                            <div>
                                <strong>${field.label}</strong>
                                ${isHotelOrMakan ? '<span class="badge bg-secondary ms-2">Readonly</span>' : ''}
                                ${field.type === 'number' && !isHotelOrMakan ? '<span class="badge bg-info ms-2">Number</span>' : ''}
                                <br><small class="text-muted">${field.jumlah_hari ? (field.label.toLowerCase().includes('hotel') || field.label.toLowerCase().includes('penginapan') ? field.jumlah_hari + ' Malam' : field.jumlah_hari + ' Hari') : ''}</small>
                            </div>
                        </td>
                        <td style="padding: 15px; vertical-align: middle;">
                            <div class="input-group" style="max-width: 200px;">
                                ${field.type === 'currency' ? '<span class="input-group-text">Rp</span>' : ''}
                                <input type="text" 
                                     class="form-control intervention-input rupiah-input-intervention" 
                                     data-detail-id="${detailId}"
                                     data-original-value="${parseFloat(field.value)}"
                                     value="${displayValue}"
                                     ${isHotelOrMakan ? 'readonly style="background-color: #f8f9fa; cursor: not-allowed;"' : ''}>
                            </div>
                        </td>
                        <td style="padding: 15px; vertical-align: middle;">
                            <button type="button" class="btn btn-sm btn-outline-warning reset-btn" 
                                    data-detail-id="${detailId}" 
                                    ${isHotelOrMakan ? 'disabled' : ''}
                                    style="border-radius: 6px;">
                                <i class="fas fa-undo me-1"></i>Reset
                            </button>
                        </td>
                    </tr>
                `;
            });
            
            htmlContent += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            `;
            
            container.innerHTML = htmlContent;
            
            // Panggil Helper Setup Listeners Baru
            setupInterventionListeners(container);
        }
        
        function setupInterventionListeners(container) {
            // 1. Listener Input Rupiah
            container.querySelectorAll('.rupiah-input-intervention').forEach(input => {
                input.addEventListener('keyup', function(e) {
                    if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') return;
                    
                    let value = this.value.replace(/[^0-9]/g, '');
                    if (value) {
                        this.value = new Intl.NumberFormat('id-ID').format(value);
                    } else {
                        this.value = '';
                    }
                    updateRowHighlight(this);
                });
            });

            // 2. Listener Reset Button
            container.querySelectorAll('.reset-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const detailId = this.dataset.detailId;
                    const input = container.querySelector(`input[data-detail-id="${detailId}"]`);
                    const originalValue = parseFloat(input.dataset.originalValue);
                    
                    // Format balik ke Rupiah saat reset
                    input.value = new Intl.NumberFormat('id-ID').format(originalValue);
                    updateRowHighlight(input);
                });
            });
        }
        
        function updateRowHighlight(input) {
            const row = input.closest('tr');
            const originalValue = parseFloat(input.dataset.originalValue);
            
            // Hapus titik sebelum parse
            const currentValue = parseFloat(input.value.replace(/\./g, '') || 0);
            
            if (currentValue !== originalValue && !isNaN(currentValue) && !isNaN(originalValue)) {
                row.style.backgroundColor = '#fff3cd'; 
                row.classList.add('table-warning');
            } else {
                row.style.backgroundColor = '';
                row.classList.remove('table-warning');
            }
        }
        
        function getDetailIdFromField(field) {
            // Helper function - perlu disesuaikan dengan struktur data Anda
            // Asumsi: detail_fields sudah include detail_id
            return field.detail_id || field.id || 0;
        }
        
        document.getElementById('interventionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const pengajuanId = document.getElementById('interventionPengajuanId').value;
            const catatanIntervensi = document.getElementById('catatanIntervensi').value;
            
            if (!catatanIntervensi.trim()) {
                Swal.fire({ icon: 'warning', title: 'Catatan Diperlukan', text: 'Harap isi catatan intervensi.' });
                return;
            }
            
            const detailInterventions = [];
            const inputs = document.querySelectorAll('.intervention-input');
            
            inputs.forEach((input, index) => {
                const originalValue = parseFloat(input.dataset.originalValue);
                let rawValue = input.value; 
                
                // 1. Bersihkan format rupiah menjadi angka (Number) untuk perbandingan
                const cleanValue = parseFloat(rawValue.replace(/\./g, '').replace(',', '.')) || 0;
                
                const detailId = input.dataset.detailId;
                
                // 2. Bandingkan (Number vs Number)
                if (cleanValue !== originalValue && detailId) {
                    detailInterventions.push({
                        detail_id: parseInt(detailId),
                        // --- PERBAIKAN DISINI ---
                        // Ubah kembali ke String agar sesuai validasi Laravel 'required|string'
                        nilai_final: cleanValue.toString() 
                    });
                }
            });
            
            if (detailInterventions.length === 0) {
                Swal.fire({ icon: 'info', title: 'Tidak Ada Perubahan', text: 'Tidak ada detail yang diubah.' });
                return;
            }
            
            // Disable button
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...';
            
            // Prepare data
            const requestData = {
                detail_interventions: detailInterventions,
                catatan_intervensi: catatanIntervensi
            };
            
            fetch(`/laporan-pengajuan/${pengajuanId}/intervene-detail`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(requestData)
            })
            .then(async response => {
                const responseText = await response.text();
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (e) {
                    throw new Error('Invalid JSON response');
                }
                
                if (!response.ok) {
                    if (response.status === 422) {
                        let errorMsg = data.message || 'Data tidak valid';
                        if (data.errors) {
                            // Gabungkan pesan error agar mudah dibaca
                            errorMsg += ':\n' + Object.values(data.errors).flat().join('\n');
                        }
                        throw new Error(errorMsg);
                    }
                    throw new Error(data.message || `HTTP error! status: ${response.status}`);
                }
                
                return data;
            })
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('interventionModal')).hide();
                    Swal.fire({
                        icon: 'success',
                        title: 'Intervensi Berhasil!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    setTimeout(() => { location.reload(); }, 1500);
                } else {
                    throw new Error(data.message || 'Gagal melakukan intervensi');
                }
            })
            .catch(error => {
                console.error('Error details:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyimpan',
                    text: error.message
                });
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
        
        // Helper function yang sudah ada
        function isNumeric(value) {
            return !isNaN(parseFloat(value)) && isFinite(value);
        }
        
        function formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount);
        }
        // Akhir logic untuk finance intervensi
        
        // Function to show approval modal
        function showApprovalModal(status, title, message, btnClass) {
            $('#approvalModalLabel').text(title);
            $('#approvalMessage').html(`<i class="fas fa-question-circle me-2"></i>${message}`);
            $('#approvalStatus').val(status);
            $('#pengajuanId').val(currentPengajuanId);
            $('#confirmApprovalBtn').removeClass('btn-success btn-warning btn-danger').addClass(btnClass);
            $('#catatan').val('');
            
            // Show required for rejection and revision
            const catatanLabel = status === 'rejected' || status === 'revision' ? 'Catatan <span class="text-danger">*</span>' : 'Catatan <span class="text-muted">(Opsional)</span>';
            $('label[for="catatan"]').html(catatanLabel);
            
            const approvalModal = new bootstrap.Modal(document.getElementById('approvalModal'));
            approvalModal.show();
        }
        
        // Handle approval form submission
        $('#approvalForm').off('submit').on('submit', function (e) {
            e.preventDefault();
        
            const status = $('#approvalStatus').val();
            const catatan = $('#catatan').val().trim();
            const pengajuanId = $('#pengajuanId').val();
            const isSettlementApproval = $(this).data('settlement-approval');
        
            if ((status === 'rejected' || status === 'revision') && !catatan) {
                alert('Catatan wajib diisi untuk penolakan atau revisi');
                return;
            }
        
            const submitBtn = $('#confirmApprovalBtn');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Processing...');
        
            // Tentukan URL berdasarkan jenis approval
            let url;
            if (isSettlementApproval) {
                // Untuk settlement approval
                const baseUpdateUrl = $('meta[name="update-status-url"]').attr('content').replace('/update-status', '/settlement-status');
                url = `${baseUpdateUrl}/${pengajuanId}`;
                console.log('Using settlement endpoint:', url);
            } else {
                // Untuk pengajuan biasa
                const baseUpdateUrl = $('meta[name="update-status-url"]').attr('content');
                url = `${baseUpdateUrl}/${pengajuanId}`;
                console.log('Using regular endpoint:', url);
            }
        
            $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    _method: 'PUT',
                    status: status,
                    catatan: catatan
                },
                success: function (response) {
                    if (response && response.success) {
                        bootstrap.Modal.getInstance(document.getElementById('approvalModal'))?.hide();
                        bootstrap.Modal.getInstance(document.getElementById('detailModal'))?.hide();
                        bootstrap.Modal.getInstance(document.getElementById('settlementDetailModal'))?.hide();
                        
                        const successMessage = isSettlementApproval ? 
                            (response.message || 'Status settlement berhasil diperbarui') :
                            (response.message || 'Status pengajuan berhasil diperbarui');
                            
                        showAlert('success', successMessage);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showAlert('danger', response?.message || 'Gagal memperbarui status');
                    }
                },
                error: function (xhr, status, error) {
                    let errorMessage = 'Terjadi kesalahan saat memperbarui status';
                    if (xhr.responseJSON?.message) errorMessage = xhr.responseJSON.message;
                    else if (xhr.status === 419) errorMessage = 'Sesi kedaluwarsa / CSRF tidak valid. Muat ulang halaman.';
                    else if (xhr.status === 422) errorMessage = 'Data yang dikirim tidak valid';
                    else if (xhr.status === 404) errorMessage = isSettlementApproval ? 'Settlement tidak ditemukan' : 'Pengajuan tidak ditemukan';
                    else if (xhr.status === 403) errorMessage = 'Akses ditolak';
                    
                    console.error('AJAX Error Details:', {
                        url: url,
                        status: xhr.status,
                        responseText: xhr.responseText,
                        error: error,
                        isSettlement: isSettlementApproval
                    });
                    
                    showAlert('danger', errorMessage);
                },
                complete: function () {
                    submitBtn.prop('disabled', false).html(originalText);
                    // Reset settlement approval flag
                    $('#approvalForm').removeData('settlement-approval');
                }
            });
        });
        
        // Function to show alert - DIPERBAIKI
        function showAlert(type, message) {
            // Remove existing alerts
            $('.custom-alert').remove();
            
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
            
            const alertHtml = `
                <div class="alert ${alertClass} alert-dismissible fade show position-fixed custom-alert" 
                     style="top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px;" role="alert">
                    <i class="fas ${iconClass} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            $('body').append(alertHtml);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                $('.custom-alert').fadeOut(() => {
                    $('.custom-alert').remove();
                });
            }, 5000);
        }
        
        // Clean up modals when hidden
        $('#detailModal').on('hidden.bs.modal', function() {
            currentPengajuanId = null;
            $('#detailModalFooter').hide();
            $('#detailModalBody').html(`
                <div class="d-flex justify-content-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `);
        });
        
        $('#approvalModal').on('hidden.bs.modal', function() {
            $('#catatan').val('');
            $('#approvalStatus').val('');
            $('#pengajuanId').val('');
        });
        
        // Error handling untuk window errors
        window.addEventListener('error', function(e) {
            console.error('JavaScript Error:', e.error);
        });
        
        // Handle jQuery AJAX errors globally
        $(document).ajaxError(function(event, xhr, settings, thrownError) {
            console.error('Global AJAX Error:', {
                url: settings.url,
                method: settings.type,
                status: xhr.status,
                error: thrownError,
                responseText: xhr.responseText
            });
        });
        
        // Perbaikan pada function showDetailModal untuk memastikan footer buttons muncul
        function showDetailModal(id) {
            // Set current pengajuan ID
            currentPengajuanId = id;
            
            // Reset modal content
            document.getElementById('detailModalBody').innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Memuat detail pengajuan...</p>
                </div>
            `;
            
            // Hide footer initially
            const footer = document.getElementById('detailModalFooter');
            footer.style.display = 'none';
            
            // Show modal
            const detailModal = new bootstrap.Modal(document.getElementById('detailModal'));
            detailModal.show();
        
            // Fetch data from API
            fetch(`/laporan-pengajuan/detail/${id}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const pengajuan = data.data;
                        
                        // TAMBAHAN: Cek apakah ini settlement request
                        if (pengajuan.is_settlement_request && pengajuan.settlement_data) {
                            // Jika ini settlement request, tampilkan modal settlement
                            showSettlementDetailInModal(pengajuan);
                        } else {
                            // Jika pengajuan biasa, tampilkan modal pengajuan seperti biasa
                            showRegularDetailInModal(pengajuan);
                        }
                        
                        // Setup footer buttons (logic yang sudah ada)
                        const footer = document.getElementById('detailModalFooter');
                        if (pengajuan.can_approve === true || pengajuan.can_approve === 1) {
                            footer.style.display = 'flex';
                            footer.style.justifyContent = 'flex-end';
                            footer.style.gap = '10px';
                            setupApprovalButtons(pengajuan.id);
                            setupFinanceIntervention(pengajuan);
                        } else {
                            footer.style.display = 'flex';
                            footer.style.justifyContent = 'flex-end';
                            document.getElementById('btnApprove').style.display = 'none';
                            document.getElementById('btnRevision').style.display = 'none';
                            document.getElementById('btnReject').style.display = 'none';
                            document.getElementById('btnIntervention').style.display = 'none';
                        }
                    }
                })
                .catch(error => {
                    console.error('Fetch error details:', {
                        message: error.message,
                        stack: error.stack,
                        url: `/laporan-pengajuan/detail/${id}`,
                        timestamp: new Date().toISOString()
                    });
                    
                    footer.style.display = 'flex';
                    
                    // Tampilkan error yang lebih detail
                    let errorDisplay = '';
                    if (error.message.includes('NetworkError') || error.message.includes('fetch')) {
                        errorDisplay = `
                            <div class="alert alert-danger m-3">
                                <i class="fas fa-wifi me-2"></i>
                                <strong>Koneksi Error:</strong><br>
                                Tidak dapat terhubung ke server. Periksa koneksi internet Anda.
                                <br><br><small class="text-muted">Detail: ${error.message}</small>
                            </div>
                        `;
                    } else if (error.message.includes('HTTP 500')) {
                        errorDisplay = `
                            <div class="alert alert-danger m-3">
                                <i class="fas fa-server me-2"></i>
                                <strong>Server Error (500):</strong><br>
                                Terjadi kesalahan di server. Tim IT sedang menangani masalah ini.
                                <br><br><details>
                                    <summary>Detail Error (klik untuk lihat)</summary>
                                    <pre class="mt-2 p-2 bg-light border rounded" style="font-size: 12px; max-height: 200px; overflow-y: auto;">${error.message}</pre>
                                </details>
                            </div>
                        `;
                    } else if (error.message.includes('HTTP 404')) {
                        errorDisplay = `
                            <div class="alert alert-warning m-3">
                                <i class="fas fa-search me-2"></i>
                                <strong>Data Tidak Ditemukan (404):</strong><br>
                                Pengajuan dengan ID ${id} tidak ditemukan atau sudah dihapus.
                            </div>
                        `;
                    } else if (error.message.includes('HTTP 403')) {
                        errorDisplay = `
                            <div class="alert alert-warning m-3">
                                <i class="fas fa-lock me-2"></i>
                                <strong>Akses Ditolak (403):</strong><br>
                                Anda tidak memiliki akses untuk melihat pengajuan ini.
                            </div>
                        `;
                    } else {
                        errorDisplay = `
                            <div class="alert alert-danger m-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Error Tidak Dikenal:</strong><br>
                                ${error.message}
                                <br><br><details>
                                    <summary>Debug Info</summary>
                                    <pre class="mt-2 p-2 bg-light border rounded" style="font-size: 11px;">${error.stack || 'No stack trace available'}</pre>
                                </details>
                                <br><small class="text-muted">
                                    Jika masalah berlanjut, hubungi administrator dengan menyertakan informasi di atas.
                                </small>
                            </div>
                        `;
                    }
                    
                    document.getElementById('detailModalBody').innerHTML = errorDisplay;
                });
        }
       
        function showSettlementDetailInModal(pengajuanId) {
            // Set current pengajuan ID
            currentPengajuanId = pengajuanId;
            
            // Reset modal content
            document.getElementById('settlementDetailModalBody').innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Memuat detail settlement...</p>
                </div>
            `;
            
            // Hide footer initially
            const footer = document.getElementById('settlementDetailModalFooter');
            footer.style.display = 'none';
            
            // Show modal
            const settlementModal = new bootstrap.Modal(document.getElementById('settlementDetailModal'));
            settlementModal.show();
        
            // Fetch settlement data
            fetch(`/laporan-pengajuan/settlement-detail/${pengajuanId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const settlementData = data.data;
                        
                        // Update modal title
                        document.getElementById('settlementDetailModalLabel').innerHTML = 
                            `<i class="fas fa-receipt me-2"></i>Detail Settlement - ${settlementData.settlement.nomor_settlement}`;
                        
                        // Generate settlement detail HTML
                        document.getElementById('settlementDetailModalBody').innerHTML = 
                            generateSettlementDetailHTML(settlementData);
                        
                        // Setup footer buttons
                        setupSettlementFooterButtons(settlementData);
                    } else {
                        showSettlementError(data.message || 'Gagal memuat detail settlement');
                    }
                })
                .catch(error => {
                    console.error('Error loading settlement detail:', error);
                    showSettlementError('Gagal memuat detail settlement: ' + error.message);
                });
        }
        
        function generateSettlementDetailHTML(settlementData) {
            const settlement = settlementData.settlement;
            const pengajuan = settlementData.pengajuan;
            const details = settlementData.details || [];
            const progressData = settlementData.progress_data || [];
            
            // Tambahan: Tentukan apakah user adalah Finance
            settlementData.is_finance_user = settlementData.is_finance_user || false;

        
            let html = `
                <div class="container-fluid p-4">
                    <!-- Progress Timeline Settlement -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="mb-3 text-primary">
                                <i class="fas fa-route me-2"></i>Progress Approval Settlement
                            </h6>
                            <div class="approval-timeline-container">
                                ${generateSettlementTimeline(progressData)}
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <!-- Informasi Settlement -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100 shadow-sm border">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0 text-white"><i class="fas fa-info-circle me-2"></i>Informasi Settlement</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless">
                                        <tr><td class="fw-bold" style="width: 40%;">Nomor Settlement:</td><td><code class="text-primary">${settlement.nomor_settlement}</code></td></tr>
                                        <tr><td class="fw-bold">Pengajuan Asal:</td><td><code class="text-info">${pengajuan.nomor_pengajuan}</code></td></tr>
                                        <tr><td class="fw-bold">Judul Pengajuan:</td><td>${pengajuan.judul}</td></tr>
                                        <tr><td class="fw-bold">Tanggal Settlement:</td><td>${formatDateIndo(settlement.tanggal_settlement)}</td></tr>
                                        <tr><td class="fw-bold">Status:</td><td><span class="badge bg-${getStatusClass(settlement.status_settlement)}">${getStatusText(settlement.status_settlement)}</span></td></tr>
                                        <tr><td class="fw-bold">Progress:</td><td>${settlement.current_step}/${settlement.total_step}</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card h-100 shadow-sm border">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0 text-white"><i class="fas fa-calculator me-2"></i>Summary Keuangan</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless">
                                        <tr><td class="fw-bold" style="width: 40%;">Nominal Pengajuan:</td><td><strong class="text-success">${pengajuan.mata_uang} ${formatNumber(pengajuan.nominal_pengajuan)}</strong></td></tr>
                                        <tr><td class="fw-bold">Total Actual:</td><td><strong class="text-primary">${pengajuan.mata_uang} ${formatNumber(settlement.total_actual)}</strong></td></tr>
                                        <tr><td class="fw-bold">Selisih:</td><td><strong class="${settlement.selisih > 0 ? 'text-info' : settlement.selisih < 0 ? 'text-danger' : 'text-muted'}">${pengajuan.mata_uang} ${formatNumber(Math.abs(settlement.selisih))} ${settlement.selisih > 0 ? '(Sisa)' : settlement.selisih < 0 ? '(Lebih)' : ''}</strong></td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
            `;
            
            // File bukti transfer jika ada
            if (settlement.file_bukti_transfer) {
                html += `
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-dark text-white">
                                    <h6 class="mb-0 text-white"><i class="fas fa-file-upload me-2"></i>File Bukti Transfer</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">Tanggal Transfer:</small>
                                            <div class="fw-bold">${settlement.tanggal_transfer ? formatDateIndo(settlement.tanggal_transfer) : '-'}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted d-block">File Bukti:</small>
                                            <a <a href="/storage/${settlement.file_bukti_transfer}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-download me-1"></i>Lihat File Transfer
                                            </a>
                                        </div>
                                    </div>
                                    ${settlement.catatan_transfer ? `
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <small class="text-muted d-block">Catatan Transfer:</small>
                                                <div class="fw-bold">${settlement.catatan_transfer}</div>
                                            </div>
                                        </div>
                                    ` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            // Detail biaya actual
            if (details && details.length > 0) {
                html += `
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-list me-2"></i>Detail Biaya Actual (${details.length} item)</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 5%;">No</th>
                                                    <th style="width: 25%;">Keterangan</th>
                                                    <th style="width: 15%;">Budget Awal</th>
                                                    <th style="width: 15%;">Budget Actual</th>
                                                    <th style="width: 10%;">Bukti</th>
                                                    <th style="width: 18%;">Catatan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                `;
                
                details.forEach((detail, index) => {
                    // Indikator intervensi Finance
                    const interventionIndicator = detail.is_intervened_by_finance ? 
                        '<span class="badge bg-warning text-dark ms-1"><i class="fas fa-edit"></i> Direvisi Finance</span>' : '';
                    
                    html += `
                        <tr class="${detail.is_intervened_by_finance ? 'table-warning' : ''}">
                            <td class="text-center">${index + 1}</td>
                            
                            <td>
                                <div class="fw-bold">${detail.keterangan}${interventionIndicator}</div>
                                ${detail.is_intervened_by_finance && detail.original_keterangan !== detail.keterangan ? 
                                    `<small class="text-muted">Sebelumnya: ${detail.original_keterangan || '-'}</small>` : ''}
                            </td>
                            
                            <td class="text-center">${formatNumber(detail.nominal_awal * (detail.jumlah_hari || 1))}</td>
                            
                            <td class="text-end">
                                <strong class="text-primary">${formatNumber(detail.nominal)}</strong>
                                ${detail.is_intervened_by_finance && detail.original_nominal !== detail.nominal ? 
                                    `<br><small class="text-muted">Sebelumnya: ${pengajuan.mata_uang} ${formatNumber(detail.original_nominal)}</small>` : ''}
                            </td>
                            <td class="text-center">
                                ${detail.file_bukti ? 
                                    `<a href="/storage/${detail.file_bukti}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>` : 
                                    '<span class="text-muted">-</span>'
                                }
                            </td>
                            <td>
                                <small class="text-muted">${detail.catatan || '-'}</small>
                            </td>
                        </tr>
                    `;
                });
                
                // Total row
                html += `
                                    <tr class="table-success fw-bold">
                                        <td colspan="2" class="text-center"><strong>TOTAL</strong></td>
                                        <td class="text-end"><strong>${formatNumber(settlement.total_Awal)}</strong></td>
                                        <td class="text-end"><strong>${formatNumber(settlement.total_actual)}</strong></td>
                                        <td colspan="4"></td>
                                    </tr>
                `;
                
                html += `
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            // Tampilkan catatan intervensi Finance jika ada
            if (settlement.is_intervened_by_finance && settlement.catatan_intervensi_finance) {
                html += `
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card shadow-sm border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0"><i class="fas fa-edit me-2"></i>Catatan Intervensi Finance</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0">${settlement.catatan_intervensi_finance}</p>
                                    <small class="text-muted">Diintervensi pada: ${settlement.finance_intervention_date ? new Date(settlement.finance_intervention_date).toLocaleString('id-ID') : '-'}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            // Catatan settlement jika ada
            if (settlement.catatan_settlement) {
                html += `
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Catatan Settlement dari Requester</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0 text-muted">${settlement.catatan_settlement}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            
            
            html += '</div>'; // Close container-fluid
            
            return html;
        }
        
        function setupSettlementFinanceIntervention(settlementData) {
            const btnSettlementIntervention = document.getElementById('btnInterventionSettlement');
            
            // Tampilkan button intervensi hanya untuk Finance yang bisa approve
            if (settlementData.is_finance_user && settlementData.can_approve) {
                btnSettlementIntervention.style.display = 'inline-block';
                
                // Event listener untuk button intervensi settlement
                btnSettlementIntervention.onclick = function() {
                    showSettlementInterventionModal(settlementData);
                };
            } else {
                btnSettlementIntervention.style.display = 'none';
            }
        }
        
        // Show Settlement Intervention Modal
        function showSettlementInterventionModal(settlementData) {
            // Set data ke modal
            document.getElementById('settlementInterventionPengajuanId').value = settlementData.pengajuan.id;
            
            // Reset form
            document.getElementById('settlementInterventionForm').reset();
            document.getElementById('settlementInterventionPengajuanId').value = settlementData.pengajuan.id;
            
            // Generate detail settlement items yang bisa diintervensi
            generateSettlementDetailItemsForIntervention(settlementData.details);
            
            // Show modal
            const interventionModal = new bootstrap.Modal(document.getElementById('settlementInterventionModal'));
            interventionModal.show();
        }
        
        // Generate Settlement Detail Items untuk Intervensi
        // function generateSettlementDetailItemsForIntervention(details) {
        //     console.log('Settlement details received:', details);
            
        //     const container = document.getElementById('settlementDetailItemsContainer');
            
        //     if (!details || details.length === 0) {
        //         container.innerHTML = `
        //             <div class="alert alert-warning">
        //                 <i class="fas fa-exclamation-triangle me-2"></i>
        //                 Tidak ada detail settlement yang dapat diintervensi.
        //             </div>
        //         `;
        //         return;
        //     }
        
        //     let htmlContent = `
        //         <div class="card" style="border: 1px solid #dee2e6; border-radius: 10px; margin-bottom: 20px;">
        //             <div class="card-header" style="background-color: #f8f9fa; border-bottom: 1px solid #dee2e6; padding: 15px;">
        //                 <h6 class="mb-0" style="font-weight: 600; color: #495057;">
        //                     <i class="fas fa-receipt me-2"></i>Detail Settlement yang Dapat Direvisi
        //                 </h6>
        //             </div>
        //             <div class="card-body p-2">
        //                 <div class="table-responsive p-2">
        //                     <table class="table table-hover mb-0">
        //                         <thead style="background-color: #f8f9fa;">
        //                             <tr>
        //                                 <th style="padding: 12px 15px; font-weight: 600; color: #495057; border-bottom: 2px solid #dee2e6;">Keterangan</th>
                                        
        //                                 <th style="padding: 12px 15px; font-weight: 600; color: #495057; border-bottom: 2px solid #dee2e6;">Nominal LBS</th>
        //                                 <th style="padding: 12px 15px; font-weight: 600; color: #495057; border-bottom: 2px solid #dee2e6;">Aksi</th>
        //                             </tr>
        //                         </thead>
        //                         <tbody>
        //     `;
            
        //     details.forEach((detail, index) => {
        //         const detailId = detail.id;
        //         const displayNominal = formatCurrency(parseFloat(detail.nominal || 0));
                
        //         console.log(`Processing settlement detail ${detail.keterangan}: detail_id=${detailId}, nominal=${detail.nominal}`);
                
        //         htmlContent += `
        //             <tr data-detail-settlement-id="${detailId}">
        //                 <td style="padding: 15px; vertical-align: middle;">
        //                     <div>
        //                         <strong>${detail.keterangan || 'Tanpa keterangan'}</strong>
        //                     </div>
        //                 </td>
                        
        //                 <td style="padding: 15px; vertical-align: middle;">
        //                     <div class="input-group" style="max-width: 200px;">
        //                         <span class="input-group-text">Rp</span>
        //                         <input type="number" 
        //                               class="form-control settlement-intervention-input" 
        //                               data-detail-settlement-id="${detailId}"
        //                               data-field-type="nominal"
        //                               data-original-value="${detail.nominal || 0}"
        //                               value="${parseFloat(detail.nominal || 0)}"
        //                               min="0"
        //                               step="1000"
        //                               style="border-radius: 0 6px 6px 0;">
        //                     </div>
        //                 </td>
                        
        //                 <td style="padding: 15px; vertical-align: middle;">
        //                     <button type="button" class="btn btn-sm btn-outline-warning settlement-reset-btn" 
        //                             data-detail-settlement-id="${detailId}" style="border-radius: 6px;">
        //                         <i class="fas fa-undo me-1"></i>Reset
        //                     </button>
        //                 </td>
        //             </tr>
        //         `;
        //     });
            
        //     htmlContent += `
        //                         </tbody>
        //                     </table>
        //                 </div>
        //             </div>
        //         </div>
        //     `;
            
        //     container.innerHTML = htmlContent;
            
        //     // Setup event listeners untuk reset buttons
        //     container.querySelectorAll('.settlement-reset-btn').forEach(btn => {
        //         btn.addEventListener('click', function() {
        //             const detailId = this.dataset.detailSettlementId;
        //             const inputs = container.querySelectorAll(`input[data-detail-settlement-id="${detailId}"]`);
                    
        //             inputs.forEach(input => {
        //                 const originalValue = input.dataset.originalValue;
        //                 const fieldType = input.dataset.fieldType;
                        
        //                 if (fieldType === 'nominal') {
        //                     input.value = isNumeric(originalValue) ? parseFloat(originalValue) : 0;
        //                 } else {
        //                     input.value = originalValue || '';
        //                 }
                        
        //                 updateSettlementRowHighlight(input);
        //             });
        //         });
        //     });
            
        //     // Setup event listeners untuk input changes
        //     container.querySelectorAll('.settlement-intervention-input').forEach(input => {
        //         input.addEventListener('input', function() {
        //             updateSettlementRowHighlight(this);
        //         });
        //     });
        // }
        // GANTI FUNGSI INI SEPENUHNYA
function generateSettlementDetailItemsForIntervention(details) {
    console.log('Settlement details received:', details);
    
    const container = document.getElementById('settlementDetailItemsContainer');
    
    if (!details || details.length === 0) {
        container.innerHTML = `
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Tidak ada detail settlement yang dapat diintervensi.
            </div>
        `;
        return;
    }

    let htmlContent = `
        <div class="card" style="border: 1px solid #dee2e6; border-radius: 10px; margin-bottom: 20px;">
            <div class="card-header" style="background-color: #f8f9fa; border-bottom: 1px solid #dee2e6; padding: 15px;">
                <h6 class="mb-0" style="font-weight: 600; color: #495057;">
                    <i class="fas fa-receipt me-2"></i>Detail Settlement yang Dapat Direvisi
                </h6>
            </div>
            <div class="card-body p-2">
                <div class="table-responsive p-2">
                    <table class="table table-hover mb-0">
                        <thead style="background-color: #f8f9fa;">
                            <tr>
                                <th style="padding: 12px 15px; font-weight: 600; color: #495057; border-bottom: 2px solid #dee2e6; width: 30%;">Keterangan</th>
                                <th style="padding: 12px 15px; font-weight: 600; color: #495057; border-bottom: 2px solid #dee2e6; width: 20%; text-align: right;">Budget Awal</th>
                                <th style="padding: 12px 15px; font-weight: 600; color: #495057; border-bottom: 2px solid #dee2e6; width: 30%;">Nominal LBS (Actual)</th>
                                <th style="padding: 12px 15px; font-weight: 600; color: #495057; border-bottom: 2px solid #dee2e6; width: 20%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
    `;
    
    details.forEach((detail, index) => {
        const detailId = detail.id;
        // Hitung Budget Awal
        const budgetAwal = (parseFloat(detail.nominal_awal) || 0) * (parseFloat(detail.jumlah_hari) || 1);
        const formattedBudgetAwal = formatCurrency(budgetAwal);
        
        // Format Nominal Actual awal menjadi format rupiah (tanpa Rp dan spasi, hanya angka dan titik)
        // Contoh: 100000 -> 100.000
        let nominalValue = parseFloat(detail.nominal || 0);
        let displayValue = new Intl.NumberFormat('id-ID').format(nominalValue); 
        
        htmlContent += `
            <tr data-detail-settlement-id="${detailId}">
                <td style="padding: 15px; vertical-align: middle;">
                    <div><strong>${detail.keterangan || 'Tanpa keterangan'}</strong></div>
                </td>
                
                <td style="padding: 15px; vertical-align: middle; text-align: right;">
                    <span class="badge bg-secondary text-white" style="font-size: 0.9em; font-weight: 500;">
                        ${formattedBudgetAwal}
                    </span>
                </td>
                
                <td style="padding: 15px; vertical-align: middle;">
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" 
                             class="form-control settlement-intervention-input rupiah-input" 
                             data-detail-settlement-id="${detailId}"
                             data-field-type="nominal"
                             data-original-value="${nominalValue}" 
                             value="${displayValue}"
                             style="border-radius: 0 6px 6px 0; text-align: right;"> 
                    </div>
                </td>
                
                <td style="padding: 15px; vertical-align: middle;">
                    <button type="button" class="btn btn-sm btn-outline-warning settlement-reset-btn" 
                            data-detail-settlement-id="${detailId}" style="border-radius: 6px;">
                        <i class="fas fa-undo me-1"></i>Reset
                    </button>
                </td>
            </tr>
        `;
    });
    
    htmlContent += `</tbody></table></div></div></div>`;
    container.innerHTML = htmlContent;
    
    // Setup Listeners
    setupSettlementListeners(container);
}


        
        // FUNGSI BARU: Setup listener agar kode lebih rapi
function setupSettlementListeners(container) {
    // 1. Listener untuk Input Rupiah (Masking saat mengetik)
    container.querySelectorAll('.rupiah-input').forEach(input => {
        input.addEventListener('keyup', function(e) {
            // Skip tombol panah kiri/kanan agar user bisa edit
            if (e.key === 'ArrowLeft' || e.key === 'ArrowRight') return;
            
            // Ambil value, hapus karakter selain angka
            let value = this.value.replace(/[^0-9]/g, '');
            
            // Format ulang ke Rupiah
            if (value) {
                this.value = new Intl.NumberFormat('id-ID').format(value);
            } else {
                this.value = '';
            }
            
            updateSettlementRowHighlight(this);
        });
    });

    // 2. Listener untuk Reset Button
    container.querySelectorAll('.settlement-reset-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const detailId = this.dataset.detailSettlementId;
            const inputs = container.querySelectorAll(`input[data-detail-settlement-id="${detailId}"]`);
            
            inputs.forEach(input => {
                const originalValue = parseFloat(input.dataset.originalValue || 0);
                const fieldType = input.dataset.fieldType;
                
                if (fieldType === 'nominal') {
                    // Reset nilai dan format kembali ke rupiah
                    input.value = new Intl.NumberFormat('id-ID').format(originalValue);
                } else {
                    input.value = input.dataset.originalValue || '';
                }
                
                updateSettlementRowHighlight(input);
            });
        });
    });
}

// UPDATE: Fungsi Highlight Row (perlu membersihkan titik sebelum membandingkan)
function updateSettlementRowHighlight(input) {
    const row = input.closest('tr');
    const detailId = input.dataset.detailSettlementId;
    const inputs = row.querySelectorAll(`input[data-detail-settlement-id="${detailId}"]`);
    
    let hasChanges = false;
    inputs.forEach(inp => {
        const fieldType = inp.dataset.fieldType;
        
        // Nilai Asli (Raw Number dari DB)
        const originalValue = parseFloat(inp.dataset.originalValue || 0);
        
        // Nilai Input Saat Ini
        let currentValue = inp.value;

        if (fieldType === 'nominal') {
            // Hapus titik dari input sebelum convert ke float
            // Contoh: "1.000.000" -> "1000000"
            const cleanValue = parseFloat(currentValue.replace(/\./g, '') || 0);
            
            if (originalValue !== cleanValue) hasChanges = true;
        } else {
            if (inp.dataset.originalValue != currentValue) hasChanges = true;
        }
    });
    
    if (hasChanges) {
        row.style.backgroundColor = '#fff3cd'; 
        row.classList.add('table-warning');
    } else {
        row.style.backgroundColor = '';
        row.classList.remove('table-warning');
    }
}
        
        // Handle Settlement Intervention Form Submit
        document.getElementById('settlementInterventionForm').addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Settlement intervention form submission started');
            
            const pengajuanId = document.getElementById('settlementInterventionPengajuanId').value;
            const catatanIntervensi = document.getElementById('settlementCatatanIntervensi').value;
            
            console.log('Settlement intervention data:', {
                pengajuanId: pengajuanId,
                catatanIntervensi: catatanIntervensi
            });
            
            if (!catatanIntervensi.trim()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Catatan Diperlukan',
                    text: 'Harap isi catatan intervensi untuk mendokumentasikan perubahan settlement.'
                });
                return;
            }
            
            // Kumpulkan semua detail settlement yang berubah
            const detailInterventions = [];
            const detailIds = new Set();
            
            // Kumpulkan semua detail_settlement_id yang unik
            document.querySelectorAll('.settlement-intervention-input').forEach(input => {
                detailIds.add(input.dataset.detailSettlementId);
            });
            
            console.log('Processing settlement details, total IDs:', detailIds.size);
            
            detailIds.forEach(detailId => {
                const inputs = document.querySelectorAll(`input[data-detail-settlement-id="${detailId}"]`);
                let hasChanges = false;
                const interventionData = {
                    detail_settlement_id: parseInt(detailId),
                    keterangan: '',
                    nominal: 0,
                    kategori_biaya: ''
                };
                
                // Cari blok kode ini di dalam event listener submit form intervensi
inputs.forEach(input => {
    const fieldType = input.dataset.fieldType;
    const originalValue = parseFloat(input.dataset.originalValue || 0);
    let currentValue = input.value;
    
    // --- PERUBAHAN UTAMA DI SINI ---
    // Bersihkan format rupiah jika tipe field adalah nominal
    let cleanCurrentValue = currentValue;
    if (fieldType === 'nominal') {
        // Hapus semua titik, lalu parse ke float
        cleanCurrentValue = parseFloat(currentValue.replace(/\./g, '') || 0);
    }
    // --------------------------------

    console.log(`Processing field ${fieldType} for detail ${detailId}:`, {
        originalValue: originalValue,
        currentValue: cleanCurrentValue, // Gunakan nilai yang sudah dibersihkan
        changed: originalValue !== cleanCurrentValue
    });
    
    // Set nilai baru ke interventionData
    if (fieldType === 'keterangan') {
        interventionData.keterangan = currentValue;
        if (input.dataset.originalValue !== currentValue) hasChanges = true;
    } else if (fieldType === 'nominal') {
        // Gunakan cleanCurrentValue
        interventionData.nominal = cleanCurrentValue; 
        if (originalValue !== cleanCurrentValue) hasChanges = true;
    } else if (fieldType === 'kategori_biaya') {
        interventionData.kategori_biaya = currentValue;
        if (input.dataset.originalValue !== currentValue) hasChanges = true;
    }
});
                
                // Hanya tambahkan jika ada perubahan
                if (hasChanges) {
                    detailInterventions.push(interventionData);
                }
            });
            
            console.log('Settlement detail interventions:', detailInterventions);
            
            if (detailInterventions.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: 'Tidak Ada Perubahan',
                    text: 'Tidak ada detail settlement yang diubah. Silakan ubah nilai atau batalkan intervensi.'
                });
                return;
            }
            
            // Disable button to prevent double submission
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...';
            
            // Prepare data
            const requestData = {
                detail_interventions: detailInterventions,
                catatan_intervensi: catatanIntervensi
            };
            
            console.log('Sending settlement request data:', requestData);
            
            fetch(`/laporan-pengajuan/${pengajuanId}/intervene-settlement-detail`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(requestData)
            })
            .then(async response => {
                console.log('Response status:', response.status);
                
                const responseText = await response.text();
                console.log('Raw response:', responseText);
                
                // Cek apakah response adalah HTML (error page) atau JSON
                if (responseText.startsWith('<!DOCTYPE') || responseText.startsWith('<html')) {
                    throw new Error('Server returned HTML instead of JSON. Check server logs for errors.');
                }
                
                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    throw new Error(`Invalid JSON response: ${responseText.substring(0, 100)}...`);
                }
                
                if (!response.ok) {
                    throw new Error(data.message || `HTTP error! status: ${response.status}`);
                }
                
                return data;
            })
            // .then(data => {
            //     console.log('Success response:', data);
                
            //     if (data.success) {
            //         // Tutup modal
            //         bootstrap.Modal.getInstance(document.getElementById('settlementInterventionModal')).hide();
                    
            //         // Show success message
            //         Swal.fire({
            //             icon: 'success',
            //             title: 'Intervensi Settlement Berhasil!',
            //             html: `
            //                 <p>${data.message}</p>
            //                 <small class="text-muted">${data.data.total_items_changed} detail settlement berhasil diubah</small>
            //             `,
            //             timer: 4000,
            //             showConfirmButton: false
            //         });
                    
            //         // Refresh settlement modal dengan data terbaru
            //         showSettlementDetailInModal(pengajuanId);
                    
            //     } else {
            //         throw new Error(data.message || 'Gagal melakukan intervensi settlement');
            //     }
            // })
            .then(data => {
                console.log('Success response:', data);
                
                if (data.success) {
                    // Tutup modal
                    bootstrap.Modal.getInstance(document.getElementById('settlementInterventionModal')).hide();
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Intervensi Settlement Berhasil!',
                        html: `
                            <p>${data.message}</p>
                            <small class="text-muted">${data.data.total_items_changed} detail settlement berhasil diubah</small>
                        `,
                        timer: 4000,
                        showConfirmButton: false
                    });
            
                    // Refresh settlement modal dengan data terbaru (opsional, bisa dihapus jika tidak dibutuhkan)
                    showSettlementDetailInModal(pengajuanId);
            
                    // Refresh halaman setelah 4 detik
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                    
                } else {
                    throw new Error(data.message || 'Gagal melakukan intervensi settlement');
                }
            })

            .catch(error => {
                console.error('Error details:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    html: `
                        <p>${error.message || 'Terjadi kesalahan saat melakukan intervensi settlement'}</p>
                        <details style="margin-top: 10px;">
                            <summary>Detail Error</summary>
                            <pre style="text-align: left; font-size: 11px; margin-top: 5px;">${error.stack || error.toString()}</pre>
                        </details>
                    `
                });
            })
            .finally(() => {
                // Re-enable button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
        
        // Function untuk setup footer buttons
        function setupSettlementFooterButtons(settlementData) {
            const footer = document.getElementById('settlementDetailModalFooter');
            const canApprove = settlementData.can_approve;
            const isFinanceUser = settlementData.is_finance_user;
            const isFinanceLayer1 = settlementData.is_finance_layer_1; // Variable baru dari controller
            const settlement = settlementData.settlement;
            
            // Reset tampilan
            footer.style.display = 'flex';
            footer.style.justifyContent = '';
            const existingWarning = document.getElementById('blocking-alert');
            if (existingWarning) existingWarning.remove();

            // Reset tombol hidden
            const btnApprove = document.getElementById('btnSettlementApprove');
            const btnRevision = document.getElementById('btnSettlementRevision');
            const btnReject = document.getElementById('btnSettlementReject');
            const btnIntervention = document.getElementById('btnInterventionSettlement');
            const btnNotif = document.getElementById('btnSendNotification');

            btnApprove.style.display = 'none';
            btnRevision.style.display = 'none';
            btnReject.style.display = 'none';
            btnIntervention.style.display = 'none';
            btnNotif.style.display = 'none';

            // Data Konversi
            const selisih = parseFloat(settlement.selisih || 0);
            const hasFile = settlement.file_bukti_transfer && settlement.file_bukti_transfer !== "";

            console.log("DEBUG LAYER:", {
                role: isFinanceUser ? 'Finance' : 'Non-Finance',
                layer: isFinanceLayer1 ? 'Layer 1 (Reviewer)' : 'Layer 2+ (Verifikator)',
                selisih: selisih,
                hasFile: hasFile
            });

            // Tombol Notifikasi
            if (settlement.status_realisasi === 'under' || (selisih > 0 && !hasFile)) {
                 btnNotif.style.display = 'inline-block';
            }

            if (canApprove) {
                btnRevision.style.display = 'inline-block'; 
                btnReject.style.display = 'inline-block';
                
                // === LOGIKA BLOCKING BARU ===
                // Blocking hanya terjadi jika:
                // 1. User adalah Finance
                // 2. User BUKAN Layer 1 (artinya Finance Layer 2, 3, dst)
                // 3. Ada Sisa Uang
                // 4. Tidak ada Bukti Transfer
                const isFinanceLayer2Plus = isFinanceUser && !isFinanceLayer1;
                const isBlockingRequired = isFinanceLayer2Plus && (selisih > 0) && (!hasFile);

                if (isBlockingRequired) {
                    // Sembunyikan tombol approve
                    btnApprove.style.display = 'none'; 

                    const warningHtml = `
                        <div id="blocking-alert" class="alert alert-danger w-100 mb-2 d-flex align-items-center text-start shadow-sm" style="border-left: 5px solid #red;">
                            <div class="me-3"><i class="fas fa-hand-paper fa-2x text-danger"></i></div>
                            <div>
                                <strong >Menunggu Verifikasi Transfer</strong><br>
                                <p class="text-danger">
                                    Anda tidak dapat menyetujui settlement ini karena bukti transfer belum diunggah.<br>
                                    Silakan kirim notifikasi ke Requester.
                                </p>
                            </div>
                        </div>
                    `;
                    
                    const modalBody = document.getElementById('settlementDetailModalBody');
                    if(!document.getElementById('blocking-alert')){
                         const container = document.createElement('div');
                         container.className = 'container-fluid px-4 pb-0 mt-3';
                         container.innerHTML = warningHtml;
                         modalBody.appendChild(container);
                         setTimeout(() => { modalBody.scrollTop = modalBody.scrollHeight; }, 100);
                    }
                } else {
                    // AMAN: Finance Layer 1, atau File sudah ada, atau Tidak ada selisih
                    btnApprove.style.display = 'inline-block';
                }
                
                setupSettlementApprovalButtons(settlementData.pengajuan.id);
                
                // Tombol Intervensi (Hanya Finance Layer 1 yang paling butuh, tapi Layer 2 juga boleh koreksi jika perlu)
                if (isFinanceUser) {
                    btnIntervention.style.display = 'inline-block';
                    setupSettlementFinanceIntervention(settlementData);
                }
            } 
        }
        
        // Function untuk setup approval button events
        function setupSettlementApprovalButtons(pengajuanId) {
            // Remove existing event listeners to prevent duplication
            $('#btnSettlementApprove').off('click');
            $('#btnSettlementRevision').off('click');
            $('#btnSettlementReject').off('click');
            
            $('#btnSettlementApprove').on('click', function() {
                bootstrap.Modal.getInstance(document.getElementById('settlementDetailModal'))?.hide();
                setTimeout(() => {
                    showSettlementApprovalModal('approved', 'Setujui Settlement', 'Apakah Anda yakin ingin menyetujui settlement ini?', 'btn-success');
                }, 300);
            });
            
            $('#btnSettlementRevision').on('click', function() {
                bootstrap.Modal.getInstance(document.getElementById('settlementDetailModal'))?.hide();
                setTimeout(() => {
                    showSettlementApprovalModal('revision', 'Minta Revisi Settlement', 'Settlement akan dikembalikan untuk revisi. Berikan catatan yang jelas untuk perbaikan.', 'btn-warning');
                }, 300);
            });
            
            $('#btnSettlementReject').on('click', function() {
                bootstrap.Modal.getInstance(document.getElementById('settlementDetailModal'))?.hide();
                setTimeout(() => {
                    showSettlementApprovalModal('rejected', 'Tolak Settlement', 'Apakah Anda yakin ingin menolak settlement ini? Tindakan ini tidak dapat dibatalkan.', 'btn-danger');
                }, 300);
            });
        }
        
        // Function untuk show approval modal khusus settlement
        function showSettlementApprovalModal(status, title, message, btnClass) {
            $('#approvalModalLabel').text(title);
            $('#approvalMessage').html(`<i class="fas fa-question-circle me-2"></i>${message}`);
            $('#approvalStatus').val(status);
            $('#pengajuanId').val(currentPengajuanId);
            $('#confirmApprovalBtn').removeClass('btn-success btn-warning btn-danger').addClass(btnClass);
            $('#catatan').val('');
            
            // Show required for rejection and revision
            const catatanLabel = status === 'rejected' || status === 'revision' ? 'Catatan <span class="text-danger">*</span>' : 'Catatan <span class="text-muted">(Opsional)</span>';
            $('label[for="catatan"]').html(catatanLabel);
            
            // Mark this as settlement approval
            $('#approvalForm').data('settlement-approval', true);
            
            const approvalModal = new bootstrap.Modal(document.getElementById('approvalModal'));
            approvalModal.show();
        }
        
        // Function untuk menampilkan error
        function showSettlementError(message) {
            document.getElementById('settlementDetailModalBody').innerHTML = `
                <div class="alert alert-danger m-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Error:</strong> ${message}
                </div>
            `;
            
            const footer = document.getElementById('settlementDetailModalFooter');
            footer.style.display = 'flex';
        }
        
        // Helper functions untuk formatting
        function formatNumber(value) {
            if (!value || value === '' || value === '0') return '0';
            const numValue = typeof value === 'string' ? parseFloat(value.replace(/[^\d.-]/g, '')) : parseFloat(value);
            return isNaN(numValue) ? '0' : new Intl.NumberFormat('id-ID').format(numValue);
        }
        
        function formatDateIndo(dateStr) {
            if (!dateStr) return '-';
            try {
                return new Date(dateStr).toLocaleDateString('id-ID');
            } catch (e) {
                return dateStr;
            }
        }
        
        // Clean up settlement modal when hidden
        $('#settlementDetailModal').on('hidden.bs.modal', function() {
            currentPengajuanId = null;
            
            // Reset footer
            const footer = document.getElementById('settlementDetailModalFooter');
            footer.style.display = 'none';
            
            // Reset buttons
            document.getElementById('btnSettlementApprove').style.display = 'none';
            document.getElementById('btnSettlementRevision').style.display = 'none';
            document.getElementById('btnSettlementReject').style.display = 'none';
            
            // Reset modal body
            $('#settlementDetailModalBody').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Memuat detail settlement...</p>
                </div>
            `);
        });
        
        // Helper function untuk setup approval buttons
        function setupApprovalButtons(pengajuanId) {
            currentPengajuanId = pengajuanId;
            
            // Show all action buttons
            document.getElementById('btnApprove').style.display = 'inline-block';
            document.getElementById('btnRevision').style.display = 'inline-block';
            document.getElementById('btnReject').style.display = 'inline-block';
        }
        
        // Helper function untuk generate approval timeline
        function generateApprovalTimelineForApprover(progressData, currentStep, totalStep) {
            if (!progressData || !Array.isArray(progressData)) {
                return '<div class="alert alert-warning">Data progress tidak tersedia</div>';
            }
            
            let timelineHtml = '<div class="timeline">';
            
            progressData.forEach((progress, index) => {
                const isActive = progress.is_current;
                const isCompleted = progress.is_completed;
                const isRejected = progress.is_rejected;
                
                let statusClass = 'pending';
                let statusIcon = 'fas fa-clock';
                let statusText = 'Menunggu';
                
                if (isCompleted) {
                    statusClass = 'completed';
                    statusIcon = 'fas fa-check';
                    statusText = 'Disetujui';
                } else if (isRejected) {
                    statusClass = 'rejected';
                    statusIcon = 'fas fa-times';
                    statusText = 'Ditolak';
                } else if (isActive) {
                    statusClass = 'active';
                    statusIcon = 'fas fa-spinner fa-spin';
                    statusText = 'Sedang Diproses';
                }
                
                timelineHtml += `
                    <div class="timeline-item ${statusClass}">
                        <div class="timeline-marker">
                            <i class="${statusIcon}"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="mb-1">${progress.step_name || `Step ${progress.urutan}`}</h6>
                            <p class="mb-1"><strong>${progress.approver_name}</strong></p>
                            <p class="mb-1 text-muted">${progress.department}</p>
                            <small class="text-muted">Status: ${statusText}</small>
                            ${progress.tanggal_approval ? `<br><small class="text-muted">Tanggal: ${new Date(progress.tanggal_approval).toLocaleDateString('id-ID')}</small>` : ''}
                            ${progress.catatan ? `<br><small class="text-info">Catatan: ${progress.catatan}</small>` : ''}
                        </div>
                    </div>
                `;
            });
            
            timelineHtml += '</div>';
            return timelineHtml;
        }
        
        function showRegularDetailInModal(pengajuan) {
            // Update modal title
            document.getElementById('detailModalLabel').innerHTML = 
                `<i class="fa fa-file me-2"></i><span class="text-primary">Detail Pengajuan ----- ${pengajuan.nomor_pengajuan || 'N/A'}</span>` ;
            
            let detailHtml = generateApproverDetailHTML(pengajuan);
            document.getElementById('detailModalBody').innerHTML = detailHtml;
        }
        
        // TAMBAHAN: Function untuk generate timeline settlement
        function generateSettlementTimeline(progressData) {
            if (!progressData || !Array.isArray(progressData)) {
                return '<div class="alert alert-warning">Data progress tidak tersedia</div>';
            }
            
            // Filter hanya progress yang memiliki settlement_id
            const settlementProgress = progressData.filter(progress => progress.settlement_id);
            
            if (settlementProgress.length === 0) {
                return '<div class="alert alert-info">Timeline settlement tidak tersedia</div>';
            }
            
            let timelineHtml = '<div class="timeline">';
            
            settlementProgress.forEach((progress, index) => {
                const isActive = progress.is_current;
                const isCompleted = progress.is_completed;
                const isRejected = progress.is_rejected;
                
                let statusClass = 'pending';
                let statusIcon = 'fas fa-clock';
                let statusText = 'Menunggu';
                
                if (isCompleted) {
                    statusClass = 'completed';
                    statusIcon = 'fas fa-check';
                    statusText = 'Disetujui';
                } else if (isRejected) {
                    statusClass = 'rejected';
                    statusIcon = 'fas fa-times';
                    statusText = 'Ditolak';
                } else if (isActive) {
                    statusClass = 'active';
                    statusIcon = 'fas fa-spinner fa-spin';
                    statusText = 'Sedang Diproses';
                }
                
                timelineHtml += `
                    <div class="timeline-item ${statusClass}">
                        <div class="timeline-marker">
                            <i class="${statusIcon}"></i>
                        </div>
                        <div class="timeline-content">
                            <h6 class="mb-1">${progress.step_name || `Step ${progress.urutan}`}</h6>
                            <p class="mb-1"><strong>${progress.approver_name}</strong></p>
                            <p class="mb-1 text-muted">${progress.department}</p>
                            <small class="text-muted">Status: ${statusText}</small>
                            ${progress.tanggal_approval ? `<br><small class="text-muted">Tanggal: ${new Date(progress.tanggal_approval).toLocaleDateString('id-ID')}</small>` : ''}
                            ${progress.catatan ? `<br><small class="text-info">Catatan: ${progress.catatan}</small>` : ''}
                        </div>
                    </div>
                `;
            });
            
            timelineHtml += '</div>';
            return timelineHtml;
        }

        // Helper functions untuk status
        function getStatusClass(status) {
            const statusMap = {
                'proses': 'warning',
                'approved': 'success',
                'rejected': 'danger',
                'revision': 'info',
                'completed': 'success'
            };
            return statusMap[status] || 'secondary';
        }
        
        function getStatusText(status) {
            const statusMap = {
                'pending': 'Menunggu',
                'approved': 'Disetujui',
                'rejected': 'Ditolak',
                'revision': 'Revisi',
                'completed': 'Selesai'
            };
            return statusMap[status] || status;
        }
        
        function generateApproverDetailHTML(pengajuan) {
            let html = `
                <div class="container-fluid p-4">
                    <!-- Progress Timeline -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="mb-3 text-primary">
                                <i class="fas fa-route me-2"></i>Progress Approval
                            </h6>
                            <div class="approval-timeline-container">
                                ${generateApprovalTimelineForApprover(pengajuan.progress_data, pengajuan.current_step, pengajuan.total_step)}
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <!-- Informasi Pengajuan -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header bg-success border">
                                    <h6 class="mb-0 "><i class="fas fa-info-circle me-2"></i>Informasi Umum</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless">
                                        <tr><td class="fw-bold" style="width: 40%;">Nomor Pengajuan:</td><td>${pengajuan.nomor_pengajuan || '-'}</td></tr>
                                        <tr><td class="fw-bold">Judul:</td><td>${pengajuan.judul || '-'}</td></tr>
                                        <tr><td class="fw-bold">Tanggal Pengajuan:</td><td>${pengajuan.tanggal_pengajuan ? new Date(pengajuan.tanggal_pengajuan).toLocaleDateString('id-ID') : '-'}</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header bg-success border ">
                                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>Informasi Requester</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-borderless">
                                        <tr><td class="fw-bold" style="width: 40%;">Nama:</td><td>${pengajuan.requester?.nama || '-'}</td></tr>
                                        <tr><td class="fw-bold">Email:</td><td>${pengajuan.requester?.email || '-'}</td></tr>
                                        <tr><td class="fw-bold">Department:</td><td>${pengajuan.requester?.department || '-'}</td></tr>
                                        <tr><td class="fw-bold">Status:</td><td><span class="badge bg-${getStatusClass(pengajuan.status_pengajuan)}">${getStatusText(pengajuan.status_pengajuan)}</span></td></tr>
                                        <tr><td class="fw-bold">Progress:</td><td>${pengajuan.current_step || 0}/${pengajuan.total_step || 0}</td></tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Deskripsi -->
                    ${pengajuan.deskripsi ? `
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-secondary text-white">
                                        <h6 class="mb-0"><i class="fas fa-align-left me-2"></i>Deskripsi</h6>
                                    </div>
                                    <div class="card-body">
                                        <p class="mb-0">${pengajuan.deskripsi}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    ` : ''}
            `;
        
            // Cek apakah ini pengajuan perjalanan dinas (kategori_id = 1)
            if (pengajuan.kategori_pengajuan_id == 1 && pengajuan.detail_fields && pengajuan.detail_fields.length > 0) {
                // Render form perjalanan dinas
                html += `
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header text-primary">
                                    <h6 class="mb-0"><i class="fas fa-plane me-2"></i>Detail Perjalanan Dinas</h6>
                                </div>
                                <div class="card-body p-0">
                                    ${renderPerjalananDinasDetailForApprover(pengajuan.detail_fields)}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            } else if (pengajuan.detail_fields && pengajuan.detail_fields.length > 0) {
                // Render form biasa untuk kategori lainnya
                html += `
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0 text-dark"><i class="fas fa-list me-2"></i>Detail Pengajuan</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                `;
                
                pengajuan.detail_fields.forEach(field => {
                    let displayValue = field.value;
                    
                    // Format nilai berdasarkan tipe field
                    if (field.type === 'currency' && field.value) {
                        const numValue = typeof field.value === 'string' ? 
                            parseFloat(field.value.replace(/[^\d.-]/g, '')) : parseFloat(field.value);
                        displayValue = isNaN(numValue) ? 'Rp 0' : 'Rp ' + new Intl.NumberFormat('id-ID').format(numValue);
                    } else if (field.type === 'date' && field.value) {
                        try {
                            displayValue = new Date(field.value).toLocaleDateString('id-ID');
                        } catch (e) {
                            displayValue = field.value;
                        }
                    } else if (field.type === 'file' && field.value) {
                        displayValue = `<a href="/storage/${field.value}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-download me-1"></i>Lihat File
                        </a>`;
                    } else if (!field.value || field.value === '') {
                        displayValue = '<span class="text-muted">-</span>';
                    }
                    
                    html += `
                        <div class="col-md-6 mb-3">
                            <div class="border-start border-primary border-3 ps-3">
                                <small class="text-muted d-block">${field.label}</small>
                                <div class="fw-bold">${displayValue}</div>
                            </div>
                        </div>
                    `;
                });
                
                html += `
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
        
            // Tampilkan file pendukung jika ada
            if (pengajuan.file_pendukung && pengajuan.file_pendukung.length > 0) {
                html += `
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0"><i class="fas fa-paperclip me-2"></i>File Pendukung</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                `;
                
                pengajuan.file_pendukung.forEach((file, index) => {
                    html += `
                        <div class="col-md-4 mb-2">
                            <a href="/storage/${file}" target="_blank" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-download me-1"></i> File ${index + 1}
                            </a>
                        </div>
                    `;
                });
                
                html += `
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
        
            // Tampilkan catatan requester jika ada
            if (pengajuan.catatan_requester) {
                html += `
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light text-dark">
                                    <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Catatan Requester</h6>
                                </div>
                                <div class="card-body">
                                    <p class="mb-0 text-muted">${pengajuan.catatan_requester}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }
        
            html += '</div>'; // Close container-fluid
        
            return html;
        }
        
        function renderPerjalananDinasDetailForApprover(detailFields, hotelMakanData = null) {
        // Convert array ke object untuk memudahkan akses
        const fieldData = {};
        const fieldJumlahHari = {};
        const interventionData = {};
        
        detailFields.forEach(field => {
            fieldData[field.name] = field.value || '';
            fieldJumlahHari[field.name] = field.jumlah_hari || 0; // TAMBAHAN
            
            // BARU: Store intervention data jika ada
            if (field.is_intervened_by_finance && field.nilai_awal) {
                interventionData[field.name] = {
                    original_value: field.nilai_awal,
                    is_intervened: true,
                    intervention_date: field.finance_intervention_date,
                    intervention_by: field.finance_intervention_by
                };
            }
        });
    
        // Helper function untuk format currency
        const formatCurrency = (value) => {
            if (!value || value === '' || value === '0') return '-';
            const numValue = typeof value === 'string' ? parseFloat(value.replace(/[^\d.-]/g, '')) : parseFloat(value);
            return isNaN(numValue) ? 'Rp 0' : 'Rp ' + new Intl.NumberFormat('id-ID').format(numValue);
        };
    
        // Helper function untuk format number
        const formatNumber = (value) => {
            if (!value || value === '' || value === '0') return '0';
            const numValue = typeof value === 'string' ? parseFloat(value.replace(/[^\d.-]/g, '')) : parseFloat(value);
            return isNaN(numValue) ? '0' : new Intl.NumberFormat('id-ID').format(numValue);
        };
    
        // **PERBAIKAN UTAMA DI SINI**
        const formatDateRange = (value) => {
            if (!value || value === '') return '-';
            
            // Jika berformat "YYYY-MM-DD - YYYY-MM-DD" 
            if (value.includes(' - ')) {
                const dates = value.split(' - ');
                if (dates.length === 2) {
                    try {
                        const startDate = new Date(dates[0]);
                        const endDate = new Date(dates[1]);
                        
                        const startYear = startDate.getFullYear();
                        const endYear = endDate.getFullYear();
                        
                        // Format DD/MM/YYYY
                        const formatFull = (date) => {
                            const d = date.getDate().toString().padStart(2, '0');
                            const m = (date.getMonth() + 1).toString().padStart(2, '0');
                            const y = date.getFullYear();
                            return `${d}/${m}/${y}`;
                        };
                        
                        // Format DD/MM
                        const formatShort = (date) => {
                            const d = date.getDate().toString().padStart(2, '0');
                            const m = (date.getMonth() + 1).toString().padStart(2, '0');
                            return `${d}/${m}`;
                        };
                        
                        // Logika: Jika tahun sama, tampilkan DD/MM - DD/MM
                        if (startYear === endYear) {
                            const shortStart = formatShort(startDate);
                            const shortEnd = formatShort(endDate);
                            // REVISI: Menghilangkan tahun di akhir
                            return `${shortStart} - ${shortEnd}`; 
                        } else {
                            // Jika tahun berbeda, tampilkan DD/MM/YYYY - DD/MM/YYYY
                            const fullStart = formatFull(startDate);
                            const fullEnd = formatFull(endDate);
                            return `${fullStart} - ${fullEnd}`;
                        }

                    } catch (e) {
                        return value; // Return original value if parsing fails
                    }
                }
            }
            
            // Jika hanya satu tanggal (fallback)
            try {
                const date = new Date(value);
                const d = date.getDate().toString().padStart(2, '0');
                const m = (date.getMonth() + 1).toString().padStart(2, '0');
                const y = date.getFullYear();
                return `${d}/${m}/${y}`;
            } catch (e) {
                return value;
            }
        };
        
        const formatCurrencyWithIntervention = (value, fieldName) => {
            const formattedValue = formatCurrency(value);
            
            // Cek apakah field ini pernah di-intervensi finance
            if (interventionData[fieldName] && interventionData[fieldName].is_intervened) {
                const originalValue = formatCurrency(interventionData[fieldName].original_value);
                
                // Menggabungkan nominal awal yang dicoret dan nominal revisi (value)
                return `
                    <del class="text-danger" style="font-size: 0.85em;">
                        ${originalValue}
                    </del>
                    <br>
                    <strong>${formattedValue}</strong>
                    <i class="fas fa-edit text-warning ms-1 intervention-icon" 
                        data-bs-toggle="tooltip" data-bs-placement="top"
                        title="Revisi Finance - Nominal Awal: ${originalValue}"
                        style="cursor: pointer; font-size: 12px;">
                    </i>
                `;
            }
            
            // Jika tidak diintervensi, kembalikan nilai biasa
            return formattedValue;
        };
    
        // PERBAIKAN UTAMA: Ambil data biaya dengan jumlah hari dari database
        const transportasiDarat1 = parseFloat(fieldData['transportasi_darat']) || 0;
        const transportasiDarat2 = parseFloat(fieldData['transportasi_darat_2']) || 0;
        const transportasiDarat3 = parseFloat(fieldData['transportasi_darat_3']) || 0;
        
        const transportasiUdara1 = parseFloat(fieldData['transportasi_udara_1']) || 0;
        const transportasiUdara2 = parseFloat(fieldData['transportasi_udara_2']) || 0;
        const transportasiUdara3 = parseFloat(fieldData['transportasi_udara_3']) || 0;
        
        const transportasiTaxi1 = parseFloat(fieldData['transportasi_taxi']) || 0;
        const transportasiTaxi2 = parseFloat(fieldData['transportasi_taxi_2']) || 0;
        const transportasiTaxi3 = parseFloat(fieldData['transportasi_taxi_3']) || 0;
        
        // HOTEL: Gunakan data dari database atau fallback ke data hotelMakanData
        let hotelBiaya1, hotelBiaya2, hotelBiaya3, hotelMalam1, hotelMalam2, hotelMalam3;
        
        if (hotelMakanData && hotelMakanData.hotel) {
            // Gunakan data yang sudah dikalkulasi dari controller
            const hotel1 = hotelMakanData.hotel['hotel_biaya'] || {};
            const hotel2 = hotelMakanData.hotel['hotel_biaya_2'] || {};
            const hotel3 = hotelMakanData.hotel['hotel_biaya_3'] || {};
            
            hotelBiaya1 = hotel1.total || 0;
            hotelBiaya2 = hotel2.total || 0;
            hotelBiaya3 = hotel3.total || 0;
            
            hotelMalam1 = hotel1.jumlah_malam || 0;
            hotelMalam2 = hotel2.jumlah_malam || 0;
            hotelMalam3 = hotel3.jumlah_malam || 0;
        } else {
            // Fallback: ambil dari fieldData dan fieldJumlahHari
            const hotelRate1 = parseFloat(fieldData['hotel_biaya']) || 0;
            const hotelRate2 = parseFloat(fieldData['hotel_biaya_2']) || 0;
            const hotelRate3 = parseFloat(fieldData['hotel_biaya_3']) || 0;
            
            hotelMalam1 = fieldJumlahHari['hotel_biaya'] || 0;
            hotelMalam2 = fieldJumlahHari['hotel_biaya_2'] || 0;
            hotelMalam3 = fieldJumlahHari['hotel_biaya_3'] || 0;
            
            hotelBiaya1 = hotelRate1 * hotelMalam1;
            hotelBiaya2 = hotelRate2 * hotelMalam2;
            hotelBiaya3 = hotelRate3 * hotelMalam3;
        }
        
        // MAKAN: Gunakan data dari database atau fallback ke data hotelMakanData
        let makanBiaya1, makanBiaya2, makanBiaya3, makanHari1, makanHari2, makanHari3;
        
        if (hotelMakanData && hotelMakanData.makan) {
            // Gunakan data yang sudah dikalkulasi dari controller
            const makan1 = hotelMakanData.makan['makan_biaya'] || {};
            const makan2 = hotelMakanData.makan['makan_biaya_2'] || {};
            const makan3 = hotelMakanData.makan['makan_biaya_3'] || {};
            
            makanBiaya1 = makan1.total || 0;
            makanBiaya2 = makan2.total || 0;
            makanBiaya3 = makan3.total || 0;
            
            makanHari1 = makan1.jumlah_hari || 0;
            makanHari2 = makan2.jumlah_hari || 0;
            makanHari3 = makan3.jumlah_hari || 0;
        } else {
            // Fallback: ambil dari fieldData dan fieldJumlahHari
            const makanRate1 = parseFloat(fieldData['makan_biaya']) || 0;
            const makanRate2 = parseFloat(fieldData['makan_biaya_2']) || 0;
            const makanRate3 = parseFloat(fieldData['makan_biaya_3']) || 0;
            
            makanHari1 = fieldJumlahHari['makan_biaya'] || 0;
            makanHari2 = fieldJumlahHari['makan_biaya_2'] || 0;
            makanHari3 = fieldJumlahHari['makan_biaya_3'] || 0;
            
            makanBiaya1 = makanRate1 * makanHari1;
            makanBiaya2 = makanRate2 * makanHari2;
            makanBiaya3 = makanRate3 * makanHari3;
        }
        
        const uangSaku1 = parseFloat(fieldData['uang_saku']) || 0;
        const uangSaku2 = parseFloat(fieldData['uang_saku_2']) || 0;
        const uangSaku3 = parseFloat(fieldData['uang_saku_3']) || 0;
        
        const telephoneFax1 = parseFloat(fieldData['telephone_fax']) || 0;
        const telephoneFax2 = parseFloat(fieldData['telephone_fax_2']) || 0;
        const telephoneFax3 = parseFloat(fieldData['telephone_fax_3']) || 0;
        
        const entertainment1 = parseFloat(fieldData['entertainment']) || 0;
        const entertainment2 = parseFloat(fieldData['entertainment_2']) || 0;
        const entertainment3 = parseFloat(fieldData['entertainment_3']) || 0;
        
        const dokumentasi1 = parseFloat(fieldData['dokumentasi']) || 0;
        const dokumentasi2 = parseFloat(fieldData['dokumentasi_2']) || 0;
        const dokumentasi3 = parseFloat(fieldData['dokumentasi_3']) || 0;
        
        const lainLain1 = parseFloat(fieldData['lain_lain']) || 0;
        const lainLain2 = parseFloat(fieldData['lain_lain_2']) || 0;
        const lainLain3 = parseFloat(fieldData['lain_lain_3']) || 0;
    
        // Hitung total per row
        const totalTransportasiDarat = transportasiDarat1 + transportasiDarat2 + transportasiDarat3;
        const totalTransportasiUdara = transportasiUdara1 + transportasiUdara2 + transportasiUdara3;
        const totalTransportasiTaxi = transportasiTaxi1 + transportasiTaxi2 + transportasiTaxi3;
        const totalHotel = hotelBiaya1 + hotelBiaya2 + hotelBiaya3;
        const totalMakan = makanBiaya1 + makanBiaya2 + makanBiaya3;
        const totalUangSaku = uangSaku1 + uangSaku2 + uangSaku3;
        const totalTelephoneFax = telephoneFax1 + telephoneFax2 + telephoneFax3;
        const totalEntertainment = entertainment1 + entertainment2 + entertainment3;
        const totalDokumentasi = dokumentasi1 + dokumentasi2 + dokumentasi3;
        const totalLainLain = lainLain1 + lainLain2 + lainLain3;
    
        // Hitung total per kolom
        const totalPerjalanan1 = transportasiDarat1 + transportasiTaxi1 + transportasiUdara1 + hotelBiaya1 + makanBiaya1 + uangSaku1 + telephoneFax1 + entertainment1 + dokumentasi1 + lainLain1;
        const totalPerjalanan2 = transportasiDarat2 + transportasiTaxi2 + transportasiUdara2 + hotelBiaya2 + makanBiaya2 + uangSaku2 + telephoneFax2 + entertainment2 + dokumentasi2 + lainLain2;
        const totalPerjalanan3 = transportasiDarat3 + transportasiTaxi3 + transportasiUdara3 + hotelBiaya3 + makanBiaya3 + uangSaku3 + telephoneFax3 + entertainment3 + dokumentasi3 + lainLain3;
    
        const grandTotal = totalPerjalanan1 + totalPerjalanan2 + totalPerjalanan3;
    
        // Hitung totals untuk detail perjalanan
        const perjalanan1SalesRate = parseFloat(fieldData['perjalanan1_sales_rate']) || 0;
        const perjalanan1Estimasi = parseFloat(fieldData['perjalanan1_estimasi']) || 0;
        const perjalanan1Outlet = parseFloat(fieldData['perjalanan1_outlet']) || 0;
        const perjalanan2SalesRate = parseFloat(fieldData['perjalanan2_sales_rate']) || 0;
        const perjalanan2Estimasi = parseFloat(fieldData['perjalanan2_estimasi']) || 0;
        const perjalanan2Outlet = parseFloat(fieldData['perjalanan2_outlet']) || 0;
        const perjalanan3SalesRate = parseFloat(fieldData['perjalanan3_sales_rate']) || 0;
        const perjalanan3Estimasi = parseFloat(fieldData['perjalanan3_estimasi']) || 0;
        const perjalanan3Outlet = parseFloat(fieldData['perjalanan3_outlet']) || 0;
    
        const totalSalesRate = perjalanan1SalesRate + perjalanan2SalesRate + perjalanan3SalesRate;
        const totalEstimasi = perjalanan1Estimasi + perjalanan2Estimasi + perjalanan3Estimasi;
        const totalOutlet = perjalanan1Outlet + perjalanan2Outlet + perjalanan3Outlet;
        
        const initializeTooltips = () => {
            // Initialize Bootstrap tooltips untuk intervention icons
            setTimeout(() => {
                const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
                const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
            }, 100);
        };
    
        const htmlContent = `
            <div class="row mt-2">
                <div class="col-12">
                    <div class="perjalanan-dinas-form">
                        <!-- Header Section -->
                        <div class="card mb-4">
                            <div class="card-header text-white">
                                <h6 class="mb-0 text-primary">PT. GONDOWANGI TRADISIONAL KOSMETIKA</h6>
                                <h6 class="mb-0 text-primary">PENGAJUAN BIAYA PERJALANAN DINAS</h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label"><strong>Nama:</strong></label>
                                        <p class="form-control-plaintext">${fieldData['nama_karyawan'] || '-'}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label"><strong>Periode:</strong></label>
                                        <p class="form-control-plaintext">${fieldData['periode'] || '-'}</p>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label"><strong>Area:</strong></label>
                                        <p class="form-control-plaintext">${fieldData['area'] || '-'}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
    
                        <!-- Section A: Biaya yang Diperlukan -->
                        <div class="card mb-4">
                            <div class="card-header text-white">
                                <h6 class="mb-0 text-primary">A. BIAYA YANG DIPERLUKAN</h6>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-0 perjalanan-table">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="m-auto text-center" rowspan="3" style="vertical-align: middle; width: 40px;">#</th>
                                                <th class="text-center" rowspan="3" style="vertical-align: middle; min-width: 200px;">URAIAN</th>
                                                <th colspan="3" class="text-center">PERJALANAN</th>
                                                <th rowspan="3" style="vertical-align: middle; width: 120px;" class="text-center">TOTAL</th>
                                            </tr>
                                            <tr>
                                                <th class="text-center" style="width: 120px;">Perjalanan 1</th>
                                                <th class="text-center" style="width: 120px;">Perjalanan 2</th>
                                                <th class="text-center" style="width: 120px;">Perjalanan 3</th>
                                            </tr>
                                            <tr>
                                               <th class="text-center" style="width: 120px;">
                                                  ${formatDateRange(fieldData['perjalanan1_tanggal'])}
                                                </th>
                                                <th class="text-center" style="width: 120px;">
                                                  ${formatDateRange(fieldData['perjalanan2_tanggal'])}
                                                </th>
                                                <th class="text-center" style="width: 120px;">
                                                  ${formatDateRange(fieldData['perjalanan3_tanggal'])}
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="text-center">1</td>
                                                <td><strong>TRANSPORTASI</strong></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td class="ps-4">a. Darat</td>
                                                <td class="text-center">${formatCurrencyWithIntervention(transportasiDarat1, 'transportasi_darat')}</td>
                                                <td class="text-center">${formatCurrencyWithIntervention(transportasiDarat2, 'transportasi_darat_2')}</td>
                                                <td class="text-center">${formatCurrencyWithIntervention(transportasiDarat3, 'transportasi_darat_3')}</td>
                                                <td class="total-cell text-center">${formatCurrency(totalTransportasiDarat)}</td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td class="ps-4">b. Udara (Pesawat)</td>
                                                <td class="text-center">${formatCurrencyWithIntervention(transportasiUdara1, 'transportasi_udara_1')}</td>
                                                <td class="text-center">${formatCurrencyWithIntervention(transportasiUdara2, 'transportasi_udara_2')}</td>
                                                <td class="text-center">${formatCurrencyWithIntervention(transportasiUdara3, 'transportasi_udara_3')}</td>
                                                <td class="total-cell text-center">${formatCurrency(totalTransportasiUdara)}</td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td class="ps-4">c. Airport Tax</td>
                                                <td class="text-center">${formatCurrencyWithIntervention(transportasiTaxi1, 'transportasi_taxi')}</td>
                                                <td class="text-center">${formatCurrencyWithIntervention(transportasiTaxi2, 'transportasi_taxi_2')}</td>
                                                <td class="text-center">${formatCurrencyWithIntervention(transportasiTaxi3, 'transportasi_taxi_3')}</td>
                                                <td class="total-cell text-center">${formatCurrency(totalTransportasiTaxi)}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">2</td>
                                                <td><strong>HOTEL</strong></td>
                                                <td class="text-center">${formatCurrencyWithIntervention(hotelBiaya1, 'hotel_biaya')}</td>
                                                <td class="text-center">${formatCurrencyWithIntervention(hotelBiaya2, 'hotel_biaya_2')}</td>
                                                <td class="text-center">${formatCurrencyWithIntervention(hotelBiaya3, 'hotel_biaya_3')}</td>
                                                <td class="total-cell text-center">${formatCurrency(totalHotel)}</td>
                                            </tr>
                                            <tr>
                                            <td class="text-center">3</td>
                                            <td><strong>MAKAN</strong></td>
                                            <td class="text-center">${formatCurrencyWithIntervention(makanBiaya1, 'makan_biaya')}</td>
                                            <td class="text-center">${formatCurrencyWithIntervention(makanBiaya2, 'makan_biaya_2')}</td>
                                            <td class="text-center">${formatCurrencyWithIntervention(makanBiaya3, 'makan_biaya_3')}</td>
                                            <td class="total-cell text-center">${formatCurrency(totalMakan)}</td>
                                            </tr>
                                            <tr>
                                            <td class="text-center">4</td>
                                            <td><strong>UANG SAKU</strong></td>
                                            <td class="text-center">${formatCurrencyWithIntervention(uangSaku1, 'uang_saku')}</td>
                                            <td class="text-center">${formatCurrencyWithIntervention(uangSaku2, 'uang_saku_2')}</td>
                                            <td class="text-center">${formatCurrencyWithIntervention(uangSaku3, 'uang_saku_3')}</td>
                                            <td class="total-cell text-center">${formatCurrency(totalUangSaku)}</td>
                                            </tr>
                                            <tr>
                                            <td class="text-center">5</td>
                                            <td><strong>TELEPHONE & FAX</strong></td>
                                            <td class="text-center">${formatCurrencyWithIntervention(telephoneFax1, 'telephone_fax')}</td>
<td class="text-center">${formatCurrencyWithIntervention(telephoneFax2, 'telephone_fax_2')}</td>
<td class="text-center">${formatCurrencyWithIntervention(telephoneFax3, 'telephone_fax_3')}</td>
<td class="total-cell text-center">${formatCurrency(totalTelephoneFax)}</td>
</tr>
<tr>
 <td class="text-center">6</td>
<td><strong>ENTERTAINMENT</strong></td>
<td class="text-center">${formatCurrencyWithIntervention(entertainment1, 'entertainment')}</td>
<td class="text-center">${formatCurrencyWithIntervention(entertainment2, 'entertainment_2')}</td>
<td class="text-center">${formatCurrencyWithIntervention(entertainment3, 'entertainment_3')}</td>
<td class="total-cell text-center">${formatCurrency(totalEntertainment)}</td>
</tr>
<tr>
<td class="text-center">7</td>
 <td><strong>DOKUMENTASI</strong></td>
 <td class="text-center">${formatCurrencyWithIntervention(dokumentasi1, 'dokumentasi')}</td>
<td class="text-center">${formatCurrencyWithIntervention(dokumentasi2, 'dokumentasi_2')}</td>
<td class="text-center">${formatCurrencyWithIntervention(dokumentasi3, 'dokumentasi_3')}</td>
 <td class="total-cell text-center">${formatCurrency(totalDokumentasi)}</td>
</tr>
 <tr>
<td class="text-center">8</td>
<td><strong>LAIN-LAIN</strong></td>
<td class="text-center">${formatCurrencyWithIntervention(lainLain1, 'lain_lain')}</td>
<td class="text-center">${formatCurrencyWithIntervention(lainLain2, 'lain_lain_2')}</td>
<td class="text-center">${formatCurrencyWithIntervention(lainLain3, 'lain_lain_3')}</td>
<td class="total-cell text-center">${formatCurrency(totalLainLain)}</td>
                                            </tr>
                                            <tr class="table-primary">
                                                <td colspan="2" class="text-center"><strong>TOTAL</strong></td>
                                                <td class="text-center"><strong>${formatCurrency(totalPerjalanan1)}</strong></td>
                                                <td class="text-center"><strong>${formatCurrency(totalPerjalanan2)}</strong></td>
                                                <td class="text-center"><strong>${formatCurrency(totalPerjalanan3)}</strong></td>
                                                <td class="text-center total-grand"><strong>${formatCurrency(grandTotal)}</strong></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <!-- BARU: Custom CSS untuk intervention icons -->
                        <style>
                            .intervention-icon {
                                transition: all 0.2s ease;
                            }
                            .intervention-icon:hover {
                                color: #ff6b35 !important;
                                transform: scale(1.1);
                            }
                            .tooltip.show {
                                opacity: 1;
                            }
                            .tooltip-inner {
                                background-color: #333;
                                color: #fff;
                                border-radius: 6px;
                                padding: 8px 12px;
                                font-size: 12px;
                                max-width: 300px;
                            }
                            .bs-tooltip-top .tooltip-arrow::before {
                                border-top-color: #333;
                            }
                        </style>
    
                        <!-- Section B: Tujuan Perjalanan -->
                        <div class="card mb-4">
                            <div class="card-header text-white">
                                <h6 class="mb-0 text-primary">B. TUJUAN PERJALANAN</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">${fieldData['tujuan_perjalanan'] || '-'}</p>
                            </div>
                        </div>
            
                                <!-- Detail Perjalanan -->
                                <div class="card mb-4">
                                    <div class="card-header text-white">
                                        <h6 class="mb-0 text-primary">DETAIL PERJALANAN</h6>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-bordered mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 40px;" class="text-center">NO</th>
                                                        <th style="min-width: 200px;" class="text-center">TANGGAL</th>
                                                        <th style="min-width: 150px;" class="text-center">DAERAH</th>
                                                        <th style="min-width: 130px;" class="text-center">SALES RATE - RATA PER BULAN</th>
                                                        <th style="min-width: 130px;" class="text-center">ESTIMASI SALES</th>
                                                        <th style="min-width: 130px;" class="text-center">JUMLAH OUTLET YG AKAN DIKUNJUNGI</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td class="text-center">1</td>
                                                        <td class="text-center">${fieldData['perjalanan1_tanggal'] || '-'}</td>
                                                        <td class="text-center">${fieldData['perjalanan1_daerah'] || '-'}</td>
                                                        <td class="text-center">${formatCurrency(perjalanan1SalesRate)}</td>
                                                        <td class="text-center">${formatCurrency(perjalanan1Estimasi)}</td>
                                                        <td class="text-center">${formatNumber(perjalanan1Outlet)}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center">2</td>
                                                        <td class="text-center">${fieldData['perjalanan2_tanggal'] || '-'}</td>
                                                        <td class="text-center">${fieldData['perjalanan2_daerah'] || '-'}</td>
                                                        <td class="text-center">${formatCurrency(perjalanan2SalesRate)}</td>
                                                        <td class="text-center">${formatCurrency(perjalanan2Estimasi)}</td>
                                                        <td class="text-center">${formatNumber(perjalanan2Outlet)}</td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-center">3</td>
                                                        <td class="text-center">${fieldData['perjalanan3_tanggal'] || '-'}</td>
                                                        <td class="text-center">${fieldData['perjalanan3_daerah'] || '-'}</td>
                                                        <td class="text-center">${formatCurrency(perjalanan3SalesRate)}</td>
                                                        <td class="text-center">${formatCurrency(perjalanan3Estimasi)}</td>
                                                        <td class="text-center">${formatNumber(perjalanan3Outlet)}</td>
                                                    </tr>
                                                    <tr class="table-primary">
                                                        <td colspan="3" class="text-center"><strong>TOTAL</strong></td>
                                                        <td class="text-center"><strong>${formatCurrency(totalSalesRate)}</strong></td>
                                                        <td class="text-center"><strong>${formatCurrency(totalEstimasi)}</strong></td>
                                                        <td class="text-center"><strong>${formatNumber(totalOutlet)}</strong></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                initializeTooltips();
    
                return htmlContent;
            }
            
            // Event handlers for approval buttons
            $('#btnApprove').on('click', function() {
                showApprovalModal('approved', 'Setujui Pengajuan', 'Apakah Anda yakin ingin menyetujui pengajuan ini?', 'btn-success');
            });
            
            // Clean up modals when hidden - PERBAIKAN
            $('#detailModal').on('hidden.bs.modal', function() {
                currentPengajuanId = null;
                
                // Reset footer visibility dan buttons
                const footer = document.getElementById('detailModalFooter');
                footer.style.display = 'none';
                
                // Show all buttons for next use
                document.getElementById('btnApprove').style.display = 'inline-block';
                document.getElementById('btnRevision').style.display = 'inline-block';
                document.getElementById('btnReject').style.display = 'inline-block';
                
                // Reset modal body
                $('#detailModalBody').html(`
                    <div class="d-flex justify-content-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
            });
            
            $('#btnRevision').on('click', function() {
                showApprovalModal('revision', 'Minta Revisi', 'Pengajuan akan dikembalikan untuk revisi. Berikan catatan yang jelas untuk perbaikan.', 'btn-warning');
            });
            
            $('#btnReject').on('click', function() {
                showApprovalModal('rejected', 'Tolak Pengajuan', 'Apakah Anda yakin ingin menolak pengajuan ini? Tindakan ini tidak dapat dibatalkan.', 'btn-danger');
            });
    </script>
@endsection