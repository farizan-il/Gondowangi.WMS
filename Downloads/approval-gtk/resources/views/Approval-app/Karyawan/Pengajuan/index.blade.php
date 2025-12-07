@extends('Approval-app.Layout.approver-main')

@section('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
    
        @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
        
        .alert {
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert .badge {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }

        /* CSS untuk Timeline Approval Horizontal */
        .approval-timeline {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 0;
            margin: 20px 0;
        }
        
        .approval-timeline::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(to right, #e9ecef 0%, #e9ecef 100%);
            z-index: 1;
            transform: translateY(-50%);
        }
        
        .approval-step {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 120px;
            z-index: 2;
            background: white;
            padding: 0 10px;
        }
        
        .step-circle {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            margin-bottom: 10px;
            transition: all 0.3s ease;
            border: 3px solid transparent;
            position: relative;
        }
        
        /* Status Colors */
        .step-circle.pending {
            background-color: #6c757d;
            border-color: #6c757d;
        }
        
        .step-circle.proses {
            background-color: #ffc107;
            border-color: #ffc107;
            animation: pulse 2s infinite;
            box-shadow: 0 0 15px rgba(255, 193, 7, 0.5);
        }
        
        .step-circle.approved {
            background-color: #28a745;
            border-color: #28a745;
            transform: scale(1.1);
        }
        
        .step-circle.rejected {
            background-color: #dc3545;
            border-color: #dc3545;
        }
        
        /* Icon dalam circle */
        .step-circle i {
            font-size: 20px;
        }
        
        /* Step info di bawah circle */
        .step-info {
            text-align: center;
            min-height: 80px;
        }
        
        .step-title {
            font-weight: bold;
            font-size: 12px;
            color: #333;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .step-approver {
            font-size: 11px;
            color: #666;
            margin-bottom: 3px;
        }
        
        .step-date {
            font-size: 10px;
            color: #999;
            font-style: italic;
        }
        
        .step-status {
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 10px;
            margin-top: 5px;
            display: inline-block;
        }
        
        .step-status.pending {
            background-color: #6c757d;
            color: white;
        }
        
        .step-status.proses {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .step-status.approved {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .step-status.rejected {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        /* Progress line animation */
        .approval-timeline.progress-line::before {
            background: linear-gradient(to right, #28a745 var(--progress, 0%), #e9ecef var(--progress, 0%));
            transition: all 0.5s ease;
        }
        
        /* Pulse animation untuk step yang sedang aktif */
        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
            }
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .approval-timeline {
                flex-direction: column;
                align-items: stretch;
            }
            
            .approval-timeline::before {
                top: 0;
                bottom: 0;
                left: 25px;
                right: auto;
                width: 3px;
                height: auto;
            }
            
            .approval-step {
                flex-direction: row;
                text-align: left;
                min-width: auto;
                padding: 10px 0;
                margin-left: 60px;
            }
            
            .step-circle {
                position: absolute;
                left: -60px;
                margin-bottom: 0;
                margin-right: 15px;
            }
            
            .step-info {
                text-align: left;
                min-height: auto;
            }
        }
        
        /* Additional hover effects */
        .approval-step:hover .step-circle {
            transform: scale(1.1);
            transition: transform 0.3s ease;
        }
        
        .approval-step:hover .step-info {
            background-color: rgba(0, 123, 255, 0.1);
            border-radius: 8px;
            padding: 5px;
            transition: all 0.3s ease;
        }


       /* ANIMASI BUTTON REALISASI*/
       .realisasi-btn {
            background-color: #696cff;
            color: #ffffff;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .realisasi-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        .realisasi-btn i {
            transition: transform 0.2s ease;
        }
        .realisasi-btn:hover i {
            transform: rotate(15deg) scale(1.1);
        }
        
        /* Status Badge Styles */
        .badge-pending { background-color: #ffc107; }
        .badge-approved { background-color: #28a745; }
        .badge-rejected { background-color: #dc3545; }
        .badge-completed { background-color: #17a2b8; }

        /* Action Button Styles */
        .btn-sm { padding: 0.25rem 0.5rem; font-size: 0.875rem; }
        .text-truncate { max-width: 200px; }
        /* AKHIR ANIMASI BUTTON REALISASI*/
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .status-waiting { background-color: #fef3c7; color: #92400e; }
        .status-paid { background-color: #d1fae5; color: #065f46; }
        .status-rejected { background-color: #fecaca; color: #991b1b; }
        .status-pending { background-color: #e0e7ff; color: #3730a3; }
        .status-approved { background-color: #d1fae5; color: #065f46; }
        
        .approval-timeline-container {
            overflow-x: auto;   /* Scroll horizontal */
            overflow-y: hidden; /* Sembunyikan scroll vertikal */
            white-space: nowrap; /* Biar item timeline tetap dalam satu baris */
            padding-bottom: 5px; /* Tambahin jarak biar scrollbar tidak terlalu nempel */
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
                </div>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <!-- prject ,team member start -->
    <div class="col-12">
        <div class="card table-card">
            <div class="card-header">
                <a href="kategori-pengajuan/create">
                    <button type="button" class="btn btn-outline-info rounded">Buat Pengajuan</button>
                </a>
                <div class="card-header-right">
                    <div class="btn-group card-option">
                        <button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="feather icon-more-horizontal"></i>
                        </button>
                        <ul class="list-unstyled card-option dropdown-menu dropdown-menu-right">
                            <li class="dropdown-item full-card">
                                <a href="#!">
                                    <span><i class="feather icon-maximize"></i> maximize</span>
                                    <span style="display:none"><i class="feather icon-minimize"></i> Restore</span>
                                </a>
                            </li>
                            <li class="dropdown-item minimize-card">
                                <a href="#!">
                                    <span><i class="feather icon-minus"></i> collapse</span>
                                    <span style="display:none"><i class="feather icon-plus"></i> expand</span>
                                </a>
                            </li>
                            <li class="dropdown-item reload-card">
                                <a href="#!"><i class="feather icon-refresh-cw"></i> reload</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Aksi</th>
                                <th>No. Pengajuan</th>
                                <th>Kategori</th>
                                <th>Judul</th>
                                <th>Nominal</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Progress</th>
                                <th>Status Pengajuan</th>
                                <th>Status Pembayaran</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengajuanList as $pengajuan)
                            {{-- Alert Argo - Tampil di atas setiap pengajuan dengan spacing yang rapi --}}
                            @php
                                $today = now();
                                $argoDate = $pengajuan->argo ? \Carbon\Carbon::parse($pengajuan->argo) : null;
                                $daysRemaining = $argoDate ? $today->diffInDays($argoDate, false) : null;
                                
                                // Tentukan jenis alert berdasarkan sisa hari
                                $alertClass = '';
                                $alertIcon = '';
                                $alertMessage = '';
                                
                                $hasSettlement = $pengajuan->settlement !== null;
                                
                                if ($argoDate && !$hasSettlement) {
                                    if ($daysRemaining < 0) {
                                        // Sudah lewat argo
                                        $alertClass = 'alert-danger';
                                        $alertIcon = 'fas fa-exclamation-triangle';
                                        $alertMessage = 'Pengajuan sudah melewati tanggal argo ' . abs($daysRemaining) . ' hari yang lalu (' . $argoDate->format('d/m/Y') . ')';
                                    } elseif ($daysRemaining == 0) {
                                        // Hari ini adalah argo
                                        $alertClass = 'alert-warning';
                                        $alertIcon = 'fas fa-clock';
                                        $alertMessage = 'Hari ini adalah tanggal argo pengajuan (' . $argoDate->format('d/m/Y') . ')';
                                    } elseif ($daysRemaining <= 3) {
                                        // Mendekati argo (1-3 hari)
                                        $alertClass = 'alert-warning';
                                        $alertIcon = 'fas fa-clock';
                                        $alertMessage = 'Pengajuan akan mencapai argo dalam ' . $daysRemaining . ' hari (' . $argoDate->format('d/m/Y') . ')';
                                    } elseif ($daysRemaining <= 7) {
                                        // Masih ada waktu (4-7 hari)
                                        $alertClass = 'alert-info';
                                        $alertIcon = 'fas fa-info-circle';
                                        $alertMessage = 'Pengajuan akan mencapai argo dalam ' . $daysRemaining . ' hari (' . $argoDate->format('d/m/Y') . ')';
                                    } else {
                                        // Masih banyak waktu (>7 hari)
                                        $alertClass = 'alert-success';
                                        $alertIcon = 'fas fa-check-circle';
                                        $alertMessage = 'Pengajuan akan mencapai argo dalam ' . $daysRemaining . ' hari (' . $argoDate->format('d/m/Y') . ')';
                                    }
                                }
                            @endphp
                            
                            @if($argoDate)
                            <tr class="table-success">
                                @if(!in_array($pengajuan->status_pengajuan, ['completed', 'settlement_created']))
                                <td colspan="10" style="padding: 0; border: none;">
                                    <div style="
                                        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                                        margin: 0;
                                        padding: 12px 20px;
                                        border-left: 5px solid;
                                        border-left-color: {{ $alertClass === 'alert-danger' ? '#dc3545' : ($alertClass === 'alert-warning' ? '#ffc107' : ($alertClass === 'alert-info' ? '#0dcaf0' : '#198754')) }};
                                        box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
                                    ">
                                        <div class="d-flex align-items-center justify-content-between">
                                            
                                            
                                            <div class="d-flex align-items-center">
                                                <div style="
                                                    width: 32px;
                                                    height: 32px;
                                                    border-radius: 50%;
                                                    background: {{ $alertClass === 'alert-danger' ? 'linear-gradient(135deg, #dc3545, #c82333)' : ($alertClass === 'alert-warning' ? 'linear-gradient(135deg, #ffc107, #e0a800)' : ($alertClass === 'alert-info' ? 'linear-gradient(135deg, #0dcaf0, #0baccc)' : 'linear-gradient(135deg, #198754, #146c43)')) }};
                                                    display: flex;
                                                    align-items: center;
                                                    justify-content: center;
                                                    margin-right: 12px;
                                                    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
                                                ">
                                                    <i class="{{ $alertIcon }}" style="color: white; font-size: 14px;"></i>
                                                </div>
                                                <div>
                                                    <div style="
                                                        font-size: 13px;
                                                        font-weight: 600;
                                                        color: #495057;
                                                        margin-bottom: 2px;
                                                    ">
                                                        📅 Informasi Argo Pengajuan {{ $pengajuan->nomor_pengajuan }}
                                                    </div>
                                                    <div style="
                                                        font-size: 12px;
                                                        color: #6c757d;
                                                        line-height: 1.4;
                                                    ">
                                                        {{ $alertMessage }}
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="text-end">
                                                @if($daysRemaining < 0)
                                                    <span class="badge" style="
                                                        background: linear-gradient(135deg, #dc3545, #c82333);
                                                        color: white;
                                                        font-size: 11px;
                                                        padding: 6px 12px;
                                                        border-radius: 20px;
                                                        font-weight: 600;
                                                        box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
                                                    ">
                                                        ⚠️ TERLAMBAT {{ abs($daysRemaining) }} HARI
                                                    </span>
                                                @elseif($daysRemaining == 0)
                                                    <span class="badge" style="
                                                        background: linear-gradient(135deg, #ffc107, #e0a800);
                                                        color: #000;
                                                        font-size: 11px;
                                                        padding: 6px 12px;
                                                        border-radius: 20px;
                                                        font-weight: 600;
                                                        box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);
                                                    ">
                                                        🕐 HARI INI
                                                    </span>
                                                @elseif($daysRemaining <= 3)
                                                    <span class="badge" style="
                                                        background: linear-gradient(135deg, #ffc107, #e0a800);
                                                        color: #000;
                                                        font-size: 11px;
                                                        padding: 6px 12px;
                                                        border-radius: 20px;
                                                        font-weight: 600;
                                                        box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);
                                                    ">
                                                        ⏰ {{ $daysRemaining }} HARI LAGI
                                                    </span>
                                                @elseif($daysRemaining <= 7)
                                                    <span class="badge" style="
                                                        background: linear-gradient(135deg, #0dcaf0, #0baccc);
                                                        color: white;
                                                        font-size: 11px;
                                                        padding: 6px 12px;
                                                        border-radius: 20px;
                                                        font-weight: 600;
                                                        box-shadow: 0 2px 8px rgba(13, 202, 240, 0.3);
                                                    ">
                                                        📊 {{ $daysRemaining }} HARI LAGI
                                                    </span>
                                                @else
                                                    <span class="badge" style="
                                                        background: linear-gradient(135deg, #198754, #146c43);
                                                        color: white;
                                                        font-size: 11px;
                                                        padding: 6px 12px;
                                                        border-radius: 20px;
                                                        font-weight: 600;
                                                        box-shadow: 0 2px 8px rgba(25, 135, 84, 0.3);
                                                    ">
                                                        ✅ {{ $daysRemaining }} HARI LAGI
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                @endif
                            </tr>
                            @endif
                            
                            @if($argoDate  && !$hasSettlement)
                                <tr class="pengajuan-row table-success" >
                            @else
                                <tr class="pengajuan-row">
                            @endif
                                <td>
                                    @if($pengajuan->settlement && $pengajuan->settlement->status_settlement === 'draft')
                                        <button type="button" 
                                                class="btn btn-sm btn-primary rounded mr-2" 
                                                onclick="confirmSubmitSettlement({{ $pengajuan->settlement->id }}, '{{ $pengajuan->nomor_pengajuan }}')"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#confirmSubmitModal">
                                            <i class="feather icon-send"></i> Ajukan
                                        </button>
                                    @endif
                                    
                                    {{-- Button Lihat Settlement - Muncul jika sudah ada settlement --}}
                                    @if($pengajuan->settlement)
                                        <!--<button type="button" -->
                                        <!--        class="btn btn-sm btn-info rounded" -->
                                        <!--        onclick="showDetailSettlement({{ $pengajuan->settlement->id }})"-->
                                        <!--        data-bs-toggle="modal" -->
                                        <!--        data-bs-target="#settlementModal">-->
                                        <!--    <i class="feather icon-file-text"></i> Lihat Settlement-->
                                        <!--</button>-->
                                        <a href="/settlements" type="button" 
                                                class="btn btn-sm btn-info rounded" >
                                            <i class="feather icon-file-text"></i> Lihat Settlement
                                        </a>
                                    @endif
                                    
                                    {{-- Button Settlement - Muncul jika pengajuan sudah approved dan belum ada settlement serta status TR nya paid dan milik user yang login --}}
                                    @if(
                                        $pengajuan->status_pengajuan === "proses_settlement" && 
                                        !$pengajuan->settlement && 
                                        $pengajuan->transactionRequest && 
                                        $pengajuan->transactionRequest->status === "paid" &&
                                        $pengajuan->requester_id === Auth::id()
                                    )
                                        <a href="{{ route('settlement.create', $pengajuan->id) }}" 
                                           class="btn btn-sm btn-success rounded text-dark">
                                            <i class="feather icon-file-plus"></i> <strong>Buat Settlement</strong>
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $pengajuan->nomor_pengajuan }}</strong>
                                </td>
                                <td>
                                    <span class="badge badge-light">
                                        {{ $pengajuan->kategoriPengajuan->nama ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="text-truncate" title="{{ $pengajuan->judul }}">
                                        {{ $pengajuan->judul }}
                                    </div>
                                </td>
                                <td>
                                    <strong> {{ number_format($pengajuan->nominal_pengajuan, 0, ',', '.') }}</strong>
                                </td>
                                <td>
                                    {{ $pengajuan->tanggal_pengajuan->format('d/m/Y') }}
                                </td>
                                <td>
                                    <div class="progress mt-2" style="width: 100px;">
                                        @php
                                            $progressPercentage = ($pengajuan->current_step / $pengajuan->total_step) * 100;
                                        @endphp
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: {{ $progressPercentage }}%;" 
                                             aria-valuenow="{{ $progressPercentage }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            {{ $pengajuan->current_step }}/{{ $pengajuan->total_step }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $statusClass = match($pengajuan->status_pengajuan) {
                                            'proses' => 'status-waiting',
                                            'approved' => 'status-paid',
                                            'rejected' => 'status-rejected',
                                            'revision' => 'status-warning', // Tambahkan ini (gunakan class kuning/orange)
                                            'proses_settlement' => 'status-waiting',
                                            'settlement_created' => 'status-waiting',
                                            'completed' => 'status-paid',
                                            default => '-'
                                        };
                                        $statusText = match($pengajuan->status_pengajuan) {
                                            'proses' => 'Proses',
                                            'approved' => 'Disetujui',
                                            'rejected' => 'Ditolak',
                                            'revision' => 'Perlu Revisi', // Tambahkan text ini
                                            'completed' => '🎉 Pengajuan Selesai',
                                            'proses_settlement' => 'Proses Settlement',
                                            'settlement_created' => 'Proses Settlement',
                                            default => '-'
                                        };
                                    @endphp
                                    
                                    <span class="status-badge {{ $statusClass }}">
                                        <strong>{{ $statusText }}</strong>
                                    </span>
                                </td>
                                
                                <td>
                                    @php
                                        // Cek apakah transactionRequest ada
                                        $transactionStatus = $pengajuan->transactionRequest ? $pengajuan->transactionRequest->status : null;
                                        
                                        $statusClass = match($transactionStatus) {
                                            'waiting' => 'status-waiting',
                                            'paid' => 'status-paid',
                                            'rejected' => 'status-rejected',
                                            default => 'status-waiting' // untuk null/empty
                                        };
                                        
                                        $statusText = match($transactionStatus) {
                                            'waiting' => 'Menunggu',
                                            'paid' => 'Dibayarkan',
                                            'rejected' => 'Ditolak',
                                            default => 'Menunggu' // untuk null/empty
                                        };
                                        
                                        $hasBukti = $pengajuan->transactionRequest && $pengajuan->transactionRequest->bukti_transfer;
                                    @endphp
                                    
                                    @if($hasBukti)
                                    <span class="status-badge {{ $statusClass }} cursor-pointer" 
                                          style="text-decoration: underline;" 
                                          onclick="showBuktiTransfer({{ $pengajuan->transactionRequest->id }}, '{{ $pengajuan->nomor_pengajuan }}')"
                                          data-bs-toggle="modal" 
                                          data-bs-target="#buktiTransferModal">
                                        <strong><i class="feather icon-external-link"></i> {{ $statusText }}</strong>
                                    </span>
                                    @else
                                    <span class="status-badge {{ $statusClass }}">
                                        <strong>{{ $statusText }}</strong>
                                    </span>
                                    @endif
                                </td>
                                
                                <td>
                                    <div class="btn-group" role="group">
                                        {{-- Tombol Edit Revisi: Muncul HANYA jika status revision --}}
                                        @if($pengajuan->status_pengajuan === 'revision')
                                            <button type="button" 
                                                    class="btn btn-sm btn-warning rounded mr-2 text-dark"
                                                    onclick="loadRevisiModal({{ $pengajuan->id }})">
                                                <i class="feather icon-edit"></i> <strong>Revisi</strong>
                                            </button>
                                        @endif
                                
                                        {{-- Tombol Detail (Kode Lama) --}}
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary rounded mr-2" 
                                                onclick="showDetailPengajuan({{ $pengajuan->id }})"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#detailModal">
                                            <i class="feather icon-eye"></i> Detail 
                                        </button>
                                        
                                        {{-- Button Edit Settlement - Muncul jika sudah ada settlement tapi statusnya masih draft/pending --}}
                                        @if($pengajuan->settlement && in_array($pengajuan->settlement->status_settlement, ['draft', 'pending', 'submitted']))
                                            <a href="{{ route('settlement.edit', $pengajuan->settlement->id) }}" 
                                               class="btn btn-sm btn-warning rounded mr-2">
                                                <i class="feather icon-edit"></i> Edit Settlement
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="feather icon-inbox" style="font-size: 48px;"></i>
                                        <h6 class="mt-2">Belum ada pengajuan dalam proses</h6>
                                        <p>Klik tombol "Buat Pengajuan" untuk membuat pengajuan baru</p>
                                    </div>
                                </td>
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

<!-- Modal Konfirmasi Submit Settlement -->
<div class="modal fade" id="confirmSubmitModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content modal-content-centered">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Pengajuan Settlement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Apakah Anda yakin ingin mengajukan settlement untuk pengajuan</strong><strong id="confirmPengajuanNumber"></strong>?</p>
                <p class="">Setelah diajukan, settlement tidak dapat diedit lagi dan akan masuk ke proses approval.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="confirmSubmitBtn">Ya, Ajukan</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Bukti Transfer -->
<div class="modal fade" id="buktiTransferModal" tabindex="-1" aria-labelledby="buktiTransferLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="buktiTransferLabel">
                    <i class="feather icon-file-text"></i> Bukti Transfer
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="buktiTransferBody">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat bukti transfer...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a href="#" id="downloadBuktiBtn" class="btn btn-primary" target="_blank" style="display:none;">
                    <i class="feather icon-download"></i> Download Bukti
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk Gambar Diperbesar -->
<div class="modal fade" id="imageEnlargeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body p-0 text-center">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" 
                        data-bs-dismiss="modal" aria-label="Close" style="z-index: 1060;"></button>
                <img id="enlargedBuktiImage" src="" alt="Bukti Transfer" class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="timelineModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Riwayat Pengajuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="timelineContent">
                <!-- Content akan diisi via JavaScript -->
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Pengajuan Modal - Updated Version -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailPengajuanLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content" id="detailPengajuanContent">
      <div class="modal-header bg-primary text-white">
            <h5 class="modal-title text-white" id="detailPengajuanLabel">
                <i class="feather icon-file-text me-2"></i>
                Detail Pengajuan
            </h5>
            <div class="ms-auto d-flex align-items-center">
            <!-- Hapus data-id statis, akan diset via JavaScript -->
                <button class="btn btn-sm btn-outline-light ms-2 downloadPdfBtn" id="downloadPdfBtn">
                    <i class="feather icon-download"></i> Unduh PDF
                </button>
            </div>
        </div>

        <div class="modal-body" id="detailPengajuanBody">
            <!-- Content akan diisi via JavaScript -->
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Memuat detail pengajuan...</p>
            </div>
        </div>
      
        <div class="modal-footer bg-light">
            <button class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="feather icon-x me-2"></i>
                Tutup
            </button>
        </div>
    </div>
  </div>
</div>

<!-- Modal Settlement Detail -->
<div class="modal fade" id="settlementModal" tabindex="-1" aria-labelledby="settlementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="settlementModalLabel">Detail Settlement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="settlementModalBody">
                <!-- Content akan diisi via JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="revisiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        
        <form class="modal-content" id="formRevisiPengajuan" enctype="multipart/form-data">
            
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-dark">
                    <i class="feather icon-edit-2 me-2"></i> Revisi Pengajuan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body" id="revisiModalBody">
                <div class="text-center py-5">
                    <div class="spinner-border text-warning" role="status"></div>
                    <p class="mt-2">Memuat formulir revisi...</p>
                </div>
            </div>
            
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary" id="btnSubmitRevisi">
                    <i class="feather icon-send"></i> Kirim Revisi
                </button>
            </div>

        </form>
    </div>
</div>
@endsection

@section('script')
    <!--script ubah status settlemt-->
    <script>
        let currentSettlementId = null;
        
        function confirmSubmitSettlement(settlementId, nomorPengajuan) {
            currentSettlementId = settlementId;
            document.getElementById('confirmPengajuanNumber').textContent = nomorPengajuan;
        }
        
        document.getElementById('confirmSubmitBtn').addEventListener('click', function() {
            if (currentSettlementId) {
                submitSettlement(currentSettlementId);
            }
        });
        
        function submitSettlement(settlementId) {
            fetch(`/settlement/${settlementId}/submit`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Tutup modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('confirmSubmitModal'));
                    modal.hide();
                    
                    // Reload halaman atau update status
                    location.reload();
                } else {
                    alert('Gagal mengajukan settlement: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengajukan settlement');
            });
        }
        
        function loadRevisiModal(id) {
        currentRevisiId = id;
        const modal = new bootstrap.Modal(document.getElementById('revisiModal'));
        modal.show();

        // Tampilkan Spinner
        $('#revisiModalBody').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-warning" role="status"></div>
                <p class="mt-2">Sedang memuat data revisi...</p>
            </div>
        `);

        // AJAX Get Form
        $.ajax({
            url: `/pengajuan/${id}/edit-form`,
            method: 'GET',
            success: function(response) {
                $('#revisiModalBody').html(response);
                // Script kalkulasi yang ada di dalam response (form_revisi.blade.php) akan otomatis jalan
            },
            error: function(err) {
                $('#revisiModalBody').html('<div class="alert alert-danger">Gagal memuat form.</div>');
            }
        });
    }

    // 2. Submit Form
    $('#formRevisiPengajuan').on('submit', function(e) {
        e.preventDefault();
        if(!currentRevisiId) return;

        const btn = $(this).find('button[type="submit"]');
        const oldText = btn.html();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Mengirim...');

        let formData = new FormData(this);
        formData.append('_method', 'PUT'); // Method Spoofing untuk Laravel Resource

        $.ajax({
            url: `/pengajuan/${currentRevisiId}/update-revisi`,
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(res) {
                if(res.success) {
                    bootstrap.Modal.getInstance(document.getElementById('revisiModal')).hide();
                    // Ganti dengan Swal jika ada, atau alert biasa
                    alert(res.message); 
                    location.reload();
                } else {
                    alert('Gagal: ' + res.message);
                    btn.prop('disabled', false).html(oldText);
                }
            },
            error: function(err) {
                console.error(err);
                alert('Terjadi kesalahan server.');
                btn.prop('disabled', false).html(oldText);
            }
        });
    });
    </script>

    <script>
        function showBuktiTransfer(transactionRequestId, nomorPengajuan) {
            // Reset modal content
            $('#buktiTransferLabel').html('<i class="feather icon-file-text"></i> Bukti Transfer - ' + nomorPengajuan);
            $('#buktiTransferBody').html(`
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat bukti transfer...</p>
                </div>
            `);
            $('#downloadBuktiBtn').hide();
            
            console.log('Loading bukti transfer for ID:', transactionRequestId); // Debug log
            
            // AJAX call untuk mendapatkan detail bukti transfer
            $.ajax({
                url: `/TransactionRequest/${transactionRequestId}/bukti-detail`,
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                timeout: 10000, // 10 second timeout
                success: function(response) {
                    console.log('Response received:', response); // Debug log
                    
                    if (response.success && response.data) {
                        const tr = response.data;
                        
                        // PERBAIKAN: Gunakan route preview yang baru
                        const buktiPreviewUrl = `/TransactionRequest/${transactionRequestId}/preview-bukti`;
                        const buktiDownloadUrl = `/TransactionRequest/${transactionRequestId}/download-bukti`;
                        
                        // Check if it's an image file
                        const isImage = tr.bukti_transfer && /\.(jpg|jpeg|png|gif|bmp|webp)$/i.test(tr.bukti_transfer);
                        
                        let buktiHtml = `
                            <div class="row">
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">
                                                <i class="feather icon-info"></i> Informasi Transfer
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <table class="table table-borderless table-sm">
                                                        <tr>
                                                            <td width="40%"><strong>Status</strong></td>
                                                            <td>: <span class="badge badge-success">Dibayarkan</span></td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Diproses Oleh</strong></td>
                                                            <td>: ${tr.processed_by ? tr.processed_by.nama : '-'}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <table class="table table-borderless table-sm">
                        `;
                        
                        if (tr.tanggal_transfer) {
                            buktiHtml += `
                                                        <tr>
                                                            <td width="40%"><strong>Tanggal Transfer</strong></td>
                                                            <td>: ${formatDate(tr.tanggal_transfer)}</td>
                                                        </tr>
                            `;
                        }
                        
                        buktiHtml += `
                                                        <tr>
                                                            <td><strong>Diproses Pada</strong></td>
                                                            <td>: ${formatDateTime(tr.updated_at)}</td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                        `;
                        
                        if (tr.catatan_finance) {
                            buktiHtml += `
                                            <div class="row mt-2">
                                                <div class="col-12">
                                                    <div class="alert alert-info">
                                                        <strong><i class="feather icon-message-circle"></i> Catatan Finance:</strong><br>
                                                        ${tr.catatan_finance}
                                                    </div>
                                                </div>
                                            </div>
                            `;
                        }
                        
                        buktiHtml += `
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        if (tr.bukti_transfer) {
                            buktiHtml += `
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">
                                                    <i class="feather icon-image"></i> Bukti Transfer
                                                </h6>
                                            </div>
                                            <div class="card-body text-center">
                            `;
                            
                            if (isImage) {
                                buktiHtml += `
                                                <div class="mb-3">
                                                    <img src="${buktiPreviewUrl}" 
                                                         alt="Bukti Transfer" 
                                                         class="img-fluid rounded border"
                                                         style="max-height: 400px; cursor: pointer;"
                                                         onclick="enlargeBuktiImage('${buktiPreviewUrl}')"
                                                         onload="console.log('Image loaded successfully')"
                                                         onerror="handleImageError(this)">
                                                    <div id="image-error-${transactionRequestId}" style="display:none;" class="alert alert-warning">
                                                        <i class="feather icon-alert-triangle"></i> Gambar tidak dapat dimuat<br>
                                                        <small>File: ${tr.bukti_transfer}</small>
                                                    </div>
                                                </div>
                                                <p class="text-muted small">
                                                    <i class="feather icon-zoom-in"></i> Klik gambar untuk memperbesar
                                                </p>
                                `;
                            } else {
                                buktiHtml += `
                                                <div class="alert alert-info">
                                                    <i class="feather icon-file"></i> File: ${tr.bukti_transfer}<br>
                                                    <small class="text-muted">Klik tombol download untuk melihat file</small>
                                                </div>
                                `;
                            }
                            
                            buktiHtml += `
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                            
                            // Show download button dengan URL yang benar
                            $('#downloadBuktiBtn').attr('href', buktiDownloadUrl).show();
                        } else {
                            buktiHtml += `
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <div class="alert alert-warning text-center">
                                            <i class="feather icon-file-x"></i><br>
                                            <strong>Bukti transfer belum tersedia</strong>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
                        
                        $('#buktiTransferBody').html(buktiHtml);
                    } else {
                        console.error('Invalid response format:', response);
                        $('#buktiTransferBody').html(`
                            <div class="alert alert-danger text-center">
                                <i class="feather icon-alert-circle"></i><br>
                                <strong>Data tidak valid</strong><br>
                                <small>${response.message || 'Format response tidak sesuai'}</small>
                            </div>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        error: error
                    });
                    
                    let errorMessage = 'Terjadi kesalahan yang tidak diketahui';
                    
                    if (xhr.status === 404) {
                        errorMessage = 'Data tidak ditemukan';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Kesalahan server internal';
                        try {
                            const errorResponse = JSON.parse(xhr.responseText);
                            if (errorResponse.message) {
                                errorMessage = errorResponse.message;
                            }
                        } catch (e) {
                            // Response bukan JSON
                        }
                    } else if (xhr.status === 0) {
                        errorMessage = 'Tidak dapat terhubung ke server';
                    }
                    
                    $('#buktiTransferBody').html(`
                        <div class="alert alert-danger text-center">
                            <i class="feather icon-wifi-off"></i><br>
                            <strong>Gagal memuat data</strong><br>
                            <small>${errorMessage}</small>
                            <br><br>
                            <button class="btn btn-sm btn-outline-primary" onclick="showBuktiTransfer(${transactionRequestId}, '${nomorPengajuan}')">
                                <i class="feather icon-refresh-cw"></i> Coba Lagi
                            </button>
                        </div>
                    `);
                }
            });
        }
        
        // Function untuk handle error saat loading image
        function handleImageError(img) {
            console.error('Image failed to load:', img.src);
            img.style.display = 'none';
            const errorDiv = img.nextElementSibling;
            if (errorDiv) {
                errorDiv.style.display = 'block';
            }
            
            // Tampilkan tombol download sebagai alternatif
            $('#downloadBuktiBtn').show();
        }
        
        // Helper functions untuk format tanggal
        function formatDate(dateString) {
            if (!dateString) return '-';
            try {
                return new Date(dateString).toLocaleDateString('id-ID');
            } catch (e) {
                return dateString;
            }
        }
        
        function formatDateTime(dateString) {
            if (!dateString) return '-';
            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID') + ' ' + date.toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'});
            } catch (e) {
                return dateString;
            }
        }
        
        function enlargeBuktiImage(imageSrc) {
            $('#enlargedBuktiImage').attr('src', imageSrc);
            $('#imageEnlargeModal').modal('show');
        }
        
        // Handle modal cleanup
        $('#buktiTransferModal').on('hidden.bs.modal', function () {
            $('#buktiTransferBody').html(`
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat bukti transfer...</p>
                </div>
            `);
            $('#downloadBuktiBtn').hide();
        });
        
        // Function untuk debugging - panggil di console jika perlu
        function debugBuktiTransfer(transactionRequestId) {
            $.ajax({
                url: `/TransactionRequest/${transactionRequestId}/debug-bukti`,
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('Debug response:', response);
                },
                error: function(xhr, status, error) {
                    console.error('Debug error:', xhr.responseText);
                }
            });
        }
    </script>

    <script>
        // Function untuk menampilkan detail pengajuan
        function showDetailPengajuan(id) {
            // Reset modal content
            document.getElementById('detailPengajuanBody').innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Memuat detail pengajuan...</p>
                </div>
            `;
            
            const downloadBtn = document.getElementById('downloadPdfBtn');
            if (downloadBtn) {
                downloadBtn.setAttribute('data-id', id);
            }
        
            fetch(`/pengajuan/detail/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const pengajuan = data.data;
                        
                        let detailHtml = `
                            <!-- Timeline Approval Section -->
                            <div class="row mb-2">
                                <div class="col-12">
                                    <h6 class="mb-3">
                                        <i class="feather icon-git-commit me-2"></i>
                                        Progress Approval
                                    </h6>
                                    <div class="approval-timeline-container">
                                        ${generateApprovalTimeline(pengajuan.progress_data, pengajuan.current_step, pengajuan.total_step)}
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Detail Pengajuan Section -->
                            <div class="row mt-0">
                                <div class="col-md-6" style="overflow-x: auto; white-space: nowrap;">
                                    <h6>Informasi Umum</h6>
                                    <table class="table table-sm">
                                        <tr><td>Nomor Pengajuan</td><td><strong>${pengajuan.nomor_pengajuan || '-'}</strong></td></tr>
                                        <tr><td>Kategori</td><td>${pengajuan.kategori_pengajuan ? pengajuan.kategori_pengajuan.nama : '-'}</td></tr>
                                        <tr><td>Tanggal Pengajuan</td><td>${pengajuan.tanggal_pengajuan ? new Date(pengajuan.tanggal_pengajuan).toLocaleDateString('id-ID') : '-'}</td></tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6>Status & Progress</h6>
                                    <table class="table table-sm">
                                        <tr><td>Status</td><td><span class="badge badge-${getStatusClass(pengajuan.status_pengajuan)}">${getStatusText(pengajuan.status_pengajuan)}</span></td></tr>
                                        <tr><td>Progress</td><td>${pengajuan.current_step || 0}/${pengajuan.total_step || 0}</td></tr>
                                        <tr><td>Requester</td><td>${pengajuan.requester ? pengajuan.requester.nama : '-'}</td></tr>
                                    </table>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h6>Deskripsi</h6>
                                    <p>${pengajuan.deskripsi || 'Tidak ada deskripsi'}</p>
                                </div>
                            </div>
                        `;
        
                        // Cek apakah ini pengajuan perjalanan dinas (kategori_id = 1)
                        if (pengajuan.kategori_pengajuan_id == 1 && pengajuan.detail_fields && pengajuan.detail_fields.length > 0) {
                            // Render form perjalanan dinas
                            detailHtml += renderPerjalananDinasDetail(pengajuan.detail_fields);
                        } else if (pengajuan.detail_fields && pengajuan.detail_fields.length > 0) {
                            // Render form biasa untuk kategori lainnya
                            detailHtml += `
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6>Detail Pengajuan</h6>
                                        <div class="row">
                            `;
                            
                            pengajuan.detail_fields.forEach(field => {
                                let displayValue = field.value;
                                
                                // Format nilai berdasarkan tipe field
                                if (field.type === 'currency' && field.value) {
                                    displayValue = 'Rp ' + new Intl.NumberFormat('id-ID').format(field.value);
                                } else if (field.type === 'date' && field.value) {
                                    displayValue = new Date(field.value).toLocaleDateString('id-ID');
                                } else if (field.type === 'file' && field.value) {
                                    displayValue = `<a href="/storage/${field.value}" target="_blank" class="btn btn-sm btn-outline-primary">Lihat File</a>`;
                                } else if (!field.value || field.value === '') {
                                    displayValue = '<span class="text-muted">-</span>';
                                }
                                
                                detailHtml += `
                                    <div class="col-md-6 mb-2">
                                        <strong>${field.label}:</strong><br>
                                        <span>${displayValue}</span>
                                    </div>
                                `;
                            });
                            
                            detailHtml += `
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
        
                        // Tampilkan file pendukung jika ada
                        if (pengajuan.file_pendukung && pengajuan.file_pendukung.length > 0) {
                            detailHtml += `
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6>File Pendukung</h6>
                                        <div class="row">
                            `;
                            
                            pengajuan.file_pendukung.forEach((file, index) => {
                                detailHtml += `
                                    <div class="col-md-4 mb-2">
                                        <a href="/storage/${file}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                            <i class="feather icon-download"></i> File ${index + 1}
                                        </a>
                                    </div>
                                `;
                            });
                            
                            detailHtml += `
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
        
                        // Tampilkan catatan requester jika ada
                        if (pengajuan.catatan_requester) {
                            detailHtml += `
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6>Catatan Requester</h6>
                                        <p class="text-muted">${pengajuan.catatan_requester}</p>
                                    </div>
                                </div>
                            `;
                        }
                        
                        // Update modal title dan content
                        document.getElementById('detailPengajuanLabel').textContent = `Detail Pengajuan ${pengajuan.nomor_pengajuan || 'N/A'}`;
                        document.getElementById('detailPengajuanBody').innerHTML = detailHtml;
                    } else {
                        document.getElementById('detailPengajuanBody').innerHTML = 
                            `<div class="alert alert-danger">Gagal memuat data: ${data.message || 'Terjadi kesalahan'}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('detailPengajuanBody').innerHTML = 
                        '<div class="alert alert-danger">Gagal memuat data. Silakan coba lagi.</div>';
                });
        }
                
        function renderPerjalananDinasDetail(detailFields, hotelMakanData = null) {
            // Convert array ke object untuk memudahkan akses
            const fieldData = {};
            const fieldJumlahHari = {}; // TAMBAHAN: Store jumlah hari per field
            
            detailFields.forEach(field => {
                fieldData[field.name] = field.value || '';
                fieldJumlahHari[field.name] = field.jumlah_hari || 0; // TAMBAHAN
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
        
            // Helper function untuk format date range
            const formatDateRange = (value) => {
                if (!value || value === '') return '-';
                
                // Jika berformat "YYYY-MM-DD - YYYY-MM-DD" 
                if (value.includes(' - ')) {
                    const dates = value.split(' - ');
                    if (dates.length === 2) {
                        try {
                            const startDate = new Date(dates[0]).toLocaleDateString('id-ID');
                            const endDate = new Date(dates[1]).toLocaleDateString('id-ID');
                            return `${startDate} - ${endDate}`;
                        } catch (e) {
                            return value;
                        }
                    }
                }
                
                // Jika hanya satu tanggal
                try {
                    return new Date(value).toLocaleDateString('id-ID');
                } catch (e) {
                    return value;
                }
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
            const totalTransportasiTaxi = transportasiTaxi1 + transportasiTaxi2 + transportasiTaxi3;
            const totalTransportasiUdara = transportasiUdara1 + transportasiUdara2 + transportasiUdara3;
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
        
            return `
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
                                                    <th rowspan="3" style="vertical-align: middle; width: 40px;">#</th>
                                                    <th rowspan="3" style="vertical-align: middle; min-width: 200px;">URAIAN</th>
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
                                                    <td class="text-center">${formatCurrency(transportasiDarat1)}</td>
                                                    <td class="text-center">${formatCurrency(transportasiDarat2)}</td>
                                                    <td class="text-center">${formatCurrency(transportasiDarat3)}</td>
                                                    <td class="total-cell text-center">${formatCurrency(totalTransportasiDarat)}</td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td class="ps-4">c. Udara</td>
                                                    <td class="text-center">${formatCurrency(transportasiUdara1)}</td>
                                                    <td class="text-center">${formatCurrency(transportasiUdara2)}</td>
                                                    <td class="text-center">${formatCurrency(transportasiUdara3)}</td>
                                                    <td class="total-cell text-center">${formatCurrency(totalTransportasiUdara)}</td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td class="ps-4">b. Airport Tax</td>
                                                    <td class="text-center">${formatCurrency(transportasiTaxi1)}</td>
                                                    <td class="text-center">${formatCurrency(transportasiTaxi2)}</td>
                                                    <td class="text-center">${formatCurrency(transportasiTaxi3)}</td>
                                                    <td class="total-cell text-center">${formatCurrency(totalTransportasiTaxi)}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">2</td>
                                                    <td><strong>HOTEL</strong></td>
                                                    <td class="text-center">${formatCurrency(hotelBiaya1)}</td>
                                                    <td class="text-center">${formatCurrency(hotelBiaya2)}</td>
                                                    <td class="text-center">${formatCurrency(hotelBiaya3)}</td>
                                                    <td class="total-cell text-center">${formatCurrency(totalHotel)}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">3</td>
                                                    <td><strong>MAKAN</strong></td>
                                                    <td class="text-center">${formatCurrency(makanBiaya1)}</td>
                                                    <td class="text-center">${formatCurrency(makanBiaya2)}</td>
                                                    <td class="text-center">${formatCurrency(makanBiaya3)}</td>
                                                    <td class="total-cell text-center">${formatCurrency(totalMakan)}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">4</td>
                                                    <td><strong>UANG SAKU</strong></td>
                                                    <td class="text-center">${formatCurrency(uangSaku1)}</td>
                                                    <td class="text-center">${formatCurrency(uangSaku2)}</td>
                                                    <td class="text-center">${formatCurrency(uangSaku3)}</td>
                                                    <td class="total-cell text-center">${formatCurrency(totalUangSaku)}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">5</td>
                                                    <td><strong>TELEPHONE & FAX</strong></td>
                                                    <td class="text-center">${formatCurrency(telephoneFax1)}</td>
                                                    <td class="text-center">${formatCurrency(telephoneFax2)}</td>
                                                    <td class="text-center">${formatCurrency(telephoneFax3)}</td>
                                                    <td class="total-cell text-center">${formatCurrency(totalTelephoneFax)}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">6</td>
                                                    <td><strong>ENTERTAINMENT</strong></td>
                                                    <td class="text-center">${formatCurrency(entertainment1)}</td>
                                                    <td class="text-center">${formatCurrency(entertainment2)}</td>
                                                    <td class="text-center">${formatCurrency(entertainment3)}</td>
                                                    <td class="total-cell text-center">${formatCurrency(totalEntertainment)}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">7</td>
                                                    <td><strong>DOKUMENTASI</strong></td>
                                                    <td class="text-center">${formatCurrency(dokumentasi1)}</td>
                                                    <td class="text-center">${formatCurrency(dokumentasi2)}</td>
                                                    <td class="text-center">${formatCurrency(dokumentasi3)}</td>
                                                    <td class="total-cell text-center">${formatCurrency(totalDokumentasi)}</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">8</td>
                                                    <td><strong>LAIN-LAIN</strong></td>
                                                    <td class="text-center">${formatCurrency(lainLain1)}</td>
                                                    <td class="text-center">${formatCurrency(lainLain2)}</td>
                                                    <td class="text-center">${formatCurrency(lainLain3)}</td>
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
        }
        
        // Function untuk generate timeline approval horizontal
        function generateApprovalTimeline(progressData, currentStep, totalStep) {
            if (!progressData || progressData.length === 0) {
                return '<div class="alert alert-info">Belum ada data progress approval</div>';
            }
        
            // Hitung progress percentage untuk line
            const progressPercentage = ((currentStep - 1) / Math.max(totalStep - 1, 1)) * 100;
            
            let timelineHtml = `
                <div class="approval-timeline progress-line" style="--progress: ${progressPercentage}%;">
            `;
        
            progressData.forEach((progress, index) => {
                const stepIcon = getStepIcon(progress.status, progress.is_current);
                const stepClass = getStepClass(progress.status, progress.is_current);
                const statusText = getProgressStatusText(progress.status);
                
                // Format tanggal approval jika ada
                const approvalDate = progress.tanggal_approval ? 
                    new Date(progress.tanggal_approval).toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    }) : '';
        
                timelineHtml += `
                    <div class="approval-step">
                        <div class="step-circle ${stepClass}">
                            <i class="feather ${stepIcon}"></i>
                        </div>
                        <div class="step-info">
                            <div class="step-approver">${progress.approver_name}</div>
                            <div class="step-approver">${progress.approver_jabatan}</div>
                            ${approvalDate ? `<div class="step-date">${approvalDate}</div>` : ''}
                            <div class="step-status ${progress.status}">${statusText}</div>
                            ${progress.catatan ? `<div class="step-note text-small"><i class="feather icon-message-circle"></i>${progress.catatan}</div>` : ''}
                        </div>
                    </div>
                `;
            });
        
            timelineHtml += '</div>';
            return timelineHtml;
        }

        // Function untuk menampilkan detail settlement
        function showDetailSettlement(id) {
            fetch(`/settlement/detail/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const settlement = data.data;
                        let settlementHtml = `
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Informasi Settlement</h6>
                                    <table class="table table-sm">
                                        <tr><td><strong>Nomor Settlement:</strong></td><td>${settlement.nomor_settlement}</td></tr>
                                        <tr><td><strong>Tanggal Settlement:</strong></td><td>${new Date(settlement.tanggal_settlement).toLocaleDateString('id-ID')}</td></tr>
                                        <tr><td><strong>Status:</strong></td><td><span class="badge badge-warning">${settlement.status_settlement}</span></td></tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6>Ringkasan Biaya</h6>
                                    <table class="table table-sm">
                                        <tr><td><strong>Nominal Pengajuan:</strong></td><td>Rp. ${new Intl.NumberFormat('id-ID').format(settlement.pengajuan.nominal_pengajuan)}</td></tr>
                                        <tr><td><strong>Total Actual:</strong></td><td>Rp. ${new Intl.NumberFormat('id-ID').format(settlement.total_actual)}</td></tr>
                                        <tr class="${settlement.selisih >= 0 ? 'text-success' : 'text-danger'}">
                                            <td><strong>Selisih:</strong></td>
                                            <td><strong>Rp. ${new Intl.NumberFormat('id-ID').format(settlement.selisih)}</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        `;
        
                        // Tampilkan detail items jika ada
                        if (settlement.details && settlement.details.length > 0) {
                            settlementHtml += `
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h6>Detail Biaya Actual</h6>
                                        <div class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Tanggal</th>
                                                        <th>Keterangan</th>
                                                        
                                                        <th>Nominal Actual</th>
                                                        <th>File Bukti</th>
                                                        <th>Catatan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                            `;
                            
                            settlement.details.forEach((detail, index) => {
                                const fileBukti = detail.file_bukti ? 
                                    `<a href="/storage/${detail.file_bukti}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="feather icon-download"></i> Lihat
                                    </a>` : 
                                    '<span class="text-muted">-</span>';
                                
                                settlementHtml += `
                                    <tr>
                                        <td>${index + 1}</td>
                                        <td>${new Date(detail.tanggal_transaksi).toLocaleDateString('id-ID')}</td>
                                        <td>${detail.keterangan}</td>
                                        
                                        <td>Rp. ${new Intl.NumberFormat('id-ID').format(detail.nominal)}</td>
                                        <td>${fileBukti}</td>
                                        <td>${detail.catatan || '-'}</td>
                                    </tr>
                                `;
                            });
                            
                            settlementHtml += `
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
        
                        // Tampilkan catatan settlement jika ada
                        if (settlement.catatan_settlement) {
                            settlementHtml += `
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6>Catatan Settlement</h6>
                                        <p class="text-muted">${settlement.catatan_settlement}</p>
                                    </div>
                                </div>
                            `;
                        }
                        
                        document.getElementById('settlementModalLabel').textContent = `Detail Settlement ${settlement.nomor_settlement}`;
                        document.getElementById('settlementModalBody').innerHTML = settlementHtml;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('settlementModalBody').innerHTML = '<div class="alert alert-danger">Gagal memuat data settlement</div>';
                });
        }

        // Helper function untuk mendapatkan class status
        // Function untuk mendapatkan icon berdasarkan status
        function getStepIcon(status, isCurrent) {
            switch(status) {
                case 'approved':
                case 'completed':
                    return 'icon-check';
                case 'rejected':
                    return 'icon-x';
                case 'proses':
                    return 'icon-clock';
                case 'pending':
                default:
                    return 'icon-circle';
            }
        }
        
        // Function untuk mendapatkan class berdasarkan status
        function getStepClass(status, isCurrent) {
            if (isCurrent && status === 'proses') return 'proses';
            
            switch(status) {
                case 'approved':
                case 'completed':
                    return 'approved';
                case 'rejected':
                    return 'rejected';
                case 'proses':
                    return 'proses';
                case 'pending':
                default:
                    return 'pending';
            }
        }
        
        // Function untuk mendapatkan text status progress
        function getProgressStatusText(status) {
            switch(status) {
                case 'approved':
                    return 'Disetujui';
                case 'rejected':
                    return 'Ditolak';
                case 'proses':
                    return 'Sedang Diproses';
                case 'pending':
                    return 'Menunggu';
                case 'completed':
                    return 'Selesai';
                default:
                    return 'Belum Diproses';
            }
        }
        
        // Helper function untuk mendapatkan class status pengajuan
        function getStatusClass(status) {
            switch(status) {
                case 'pending': return 'warning';
                case 'proses': return 'warning';
                case 'proses_settlement': return 'warning';
                case 'approved': return 'success';
                case 'rejected': return 'danger';
                case 'completed': return 'info';
                case 'settlement_created': return 'primary';
                default: return 'secondary';
            }
        }
        
        // Helper function untuk mendapatkan text status pengajuan
        function getStatusText(status) {
            switch(status) {
                case 'pending': return 'Pending';
                case 'proses_settlement': return 'Proses Settlement';
                case 'proses': return 'Proses';
                case 'approved': return 'Disetujui';
                case 'rejected': return 'Ditolak';
                case 'completed': return 'Completed';
                case 'settlement_created': return 'Settlement Created';
                default: return status;
            }
        }

        document.querySelectorAll('.downloadPdfBtn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const pengajuanId = btn.getAttribute('data-id');
                if (pengajuanId) {
                    window.location.href = `/pengajuan/${pengajuanId}/print-pdf`;
                }
            });
        });

        // Timeline animation
        document.addEventListener('DOMContentLoaded', () => {
            const items = document.querySelectorAll('.timeline-modern .timeline-item');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('show');
                    observer.unobserve(entry.target);
                }
                });
            }, { threshold: 0.2 });
            
            items.forEach(item => observer.observe(item));
        });
    </script>
@endsection