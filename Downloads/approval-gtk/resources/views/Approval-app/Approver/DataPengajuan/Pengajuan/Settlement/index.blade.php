@extends('Approval-app.Layout.main')

@section('head')
<style>
.settlement-card {
    transition: transform 0.2s ease-in-out;
    border-left: 4px solid transparent;
}
.settlement-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.settlement-card.pending {
    border-left-color: #ffc107;
}
.settlement-card.proses {
    border-left-color: #17a2b8;
}
.settlement-card.approved {
    border-left-color: #28a745;
}
.settlement-card.rejected {
    border-left-color: #dc3545;
}
.selisih-positive {
    color: #28a745;
    font-weight: bold;
}
.selisih-negative {
    color: #dc3545;
    font-weight: bold;
}
.selisih-zero {
    color: #6c757d;
    font-weight: bold;
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
                    <h5 class="m-b-10">Daftar Settlement</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('kategori-pengajuan.index') }}">Pengajuan</a></li>
                    <li class="breadcrumb-item active">Settlement</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <!-- Summary Cards -->
    <div class="col-xl-3 col-md-6">
        <div class="card bg-c-blue order-card">
            <div class="card-body">
                <h6 class="text-white">Total Settlement</h6>
                <h2 class="text-white">{{ $settlements->count() }}</h2>
                <p class="m-b-0">Settlement yang dibuat</p>
                <i class="feather icon-file-text f-right f-26 text-white"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-c-green order-card">
            <div class="card-body">
                <h6 class="text-white">Approved</h6>
                <h2 class="text-white">{{ $settlements->where('status_settlement', 'approved')->count() }}</h2>
                <p class="m-b-0">Settlement disetujui</p>
                <i class="feather icon-check-circle f-right f-26 text-white"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-c-yellow order-card">
            <div class="card-body">
                <h6 class="text-white">Dalam Proses</h6>
                <h2 class="text-white">{{ $settlements->whereIn('status_settlement', ['pending', 'proses'])->count() }}</h2>
                <p class="m-b-0">Menunggu approval</p>
                <i class="feather icon-clock f-right f-26 text-white"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-c-red order-card">
            <div class="card-body">
                <h6 class="text-white">Total Actual</h6>
                <h2 class="text-white">IDR {{ number_format($settlements->sum('total_actual'), 0, ',', '.') }}</h2>
                <p class="m-b-0">Total pengeluaran</p>
                <i class="feather icon-dollar-sign f-right f-26 text-white"></i>
            </div>
        </div>
    </div>

    <!-- Settlement List -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Settlement</h5>
                <div class="card-header-right">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-secondary filter-btn active" data-filter="all">
                            Semua
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning filter-btn" data-filter="pending">
                            Pending
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info filter-btn" data-filter="proses">
                            Proses
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success filter-btn" data-filter="approved">
                            Approved
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @forelse($settlements as $settlement)
                <div class="settlement-card card mb-3 {{ $settlement->status_settlement }}" data-status="{{ $settlement->status_settlement }}">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-3">
                                <h6 class="mb-1 text-primary">{{ $settlement->nomor_settlement }}</h6>
                                <p class="text-muted mb-0">
                                    <small>{{ $settlement->tanggal_settlement->format('d/m/Y H:i') }}</small>
                                </p>
                                <span class="badge badge-light mt-1">
                                    {{ $settlement->pengajuan->kategoriPengajuan->nama ?? 'N/A' }}
                                </span>
                            </div>
                            
                            <div class="col-md-2">
                                <strong>Pengajuan:</strong><br>
                                <a href="#" onclick="showDetailPengajuan({{ $settlement->pengajuan_id }})" class="text-decoration-none">
                                    {{ $settlement->pengajuan->nomor_pengajuan }}
                                </a>
                            </div>
                            
                            <div class="col-md-2">
                                <strong>Total Actual:</strong><br>
                                <span class="text-success">IDR {{ number_format($settlement->total_actual, 0, ',', '.') }}</span>
                            </div>
                            
                            <div class="col-md-2">
                                <strong>Selisih:</strong><br>
                                @php
                                    $selisihClass = 'selisih-zero';
                                    $selisihPrefix = '';
                                    if ($settlement->selisih > 0) {
                                        $selisihClass = 'selisih-positive';
                                        $selisihPrefix = '+ ';
                                    } elseif ($settlement->selisih < 0) {
                                        $selisihClass = 'selisih-negative';
                                        $selisihPrefix = '- ';
                                    }
                                @endphp
                                <span class="{{ $selisihClass }}">
                                    {{ $selisihPrefix }}IDR {{ number_format(abs($settlement->selisih), 0, ',', '.') }}
                                </span>
                            </div>
                            
                            <div class="col-md-2">
                                @php
                                    $statusClass = '';
                                    switch($settlement->status_settlement) {
                                        case 'pending':
                                            $statusClass = 'badge-warning';
                                            break;
                                        case 'proses':
                                            $statusClass = 'badge-info';
                                            break;
                                        case 'approved':
                                            $statusClass = 'badge-success';
                                            break;
                                        case 'rejected':
                                            $statusClass = 'badge-danger';
                                            break;
                                        default:
                                            $statusClass = 'badge-secondary';
                                    }
                                @endphp
                                <span class="badge {{ $statusClass }}">
                                    {{ ucfirst($settlement->status_settlement) }}
                                </span>
                                
                                @if($settlement->status_settlement == 'proses')
                                <div class="progress mt-2" style="height: 6px;">
                                    @php
                                        $progressPercentage = ($settlement->current_step / $settlement->total_step) * 100;
                                    @endphp
                                    <div class="progress-bar bg-info" style="width: {{ $progressPercentage }}%"></div>
                                </div>
                                <small class="text-muted">{{ $settlement->current_step }}/{{ $settlement->total_step }}</small>
                                @endif
                            </div>
                            
                            <div class="col-md-1">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="feather icon-more-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="{{ route('settlement.show', $settlement->id) }}">
                                            <i class="feather icon-eye me-2"></i>Detail
                                        </a>
                                        <button class="dropdown-item" onclick="showTimelineSettlement({{ $settlement->id }})">
                                            <i class="feather icon-clock me-2"></i>Timeline
                                        </button>
                                        @if(in_array($settlement->status_settlement, ['pending', 'proses']))
                                            @if($settlement->pengajuan->requester_id == Auth::user()->karyawan_id)
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item" href="{{ route('settlement.edit', $settlement->id) }}">
                                                <i class="feather icon-edit me-2"></i>Edit
                                            </a>
                                            @endif
                                        @endif
                                        @if($settlement->canBeApproved())
                                            @php
                                                $currentStep = $settlement->getCurrentApprovalStep();
                                            @endphp
                                            @if($currentStep && $currentStep->canBeApprovedBy(Auth::user()->karyawan_id))
                                            <div class="dropdown-divider"></div>
                                            <button class="dropdown-item text-success" onclick="approveSettlement({{ $settlement->id }})">
                                                <i class="feather icon-check me-2"></i>Approve
                                            </button>
                                            <button class="dropdown-item text-danger" onclick="rejectSettlement({{ $settlement->id }})">
                                                <i class="feather icon-x me-2"></i>Reject
                                            </button>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="feather icon-file-text" style="font-size: 64px; color: #ccc;"></i>
                    <h5 class="mt-3 text-muted">Belum ada settlement</h5>
                    <p class="text-muted">Settlement akan muncul setelah pengajuan disetujui dan membutuhkan settlement</p>
                    <a href="{{ route('kategori-pengajuan.index') }}" class="btn btn-primary">
                        <i class="feather icon-arrow-left"></i> Kembali ke Pengajuan
                    </a>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Timeline Modal -->
<div class="modal fade" id="timelineModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Timeline Settlement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="timelineContent">
                <!-- Timeline content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Detail Pengajuan Modal -->
<div class="modal fade" id="detailPengajuanModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pengajuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailPengajuanContent">
                <!-- Detail content will be loaded here -->
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@endsection

@section('script')
<script>
$(document).ready(function() {
    // Filter functionality
    $('.filter-btn').on('click', function() {
        const filter = $(this).data('filter');
        
        // Update active button
        $('.filter-btn').removeClass('active');
        $(this).addClass('active');
        
        // Filter settlements
        if (filter === 'all') {
            $('.settlement-card').show();
        } else {
            $('.settlement-card').hide();
            $(`.settlement-card[data-status="${filter}"]`).show();
        }
    });
});

function showTimelineSettlement(settlementId) {
    // Show loading
    $('#timelineContent').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Memuat timeline...</p>
        </div>
    `);
    
    $('#timelineModal').modal('show');
    
    // Load timeline via AJAX
    $.ajax({
        url: `/settlement/${settlementId}/timeline`,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                let timelineHtml = '<div class="timeline">';
                
                response.data.forEach(function(item, index) {
                    const isCompleted = item.status === 'approved';
                    const isCurrent = item.status === 'proses';
                    const statusClass = isCompleted ? 'bg-success' : (isCurrent ? 'bg-info' : 'bg-secondary');
                    
                    timelineHtml += `
                        <div class="timeline-item">
                            <div class="timeline-marker ${statusClass}">
                                <i class="feather ${isCompleted ? 'icon-check' : (isCurrent ? 'icon-clock' : 'icon-circle')}"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1">${item.step_name}</h6>
                                <p class="text-muted mb-1">${item.approver ? item.approver.nama : 'Menunggu assignment'}</p>
                                <small class="text-muted">
                                    ${item.tanggal_approval ? new Date(item.tanggal_approval).toLocaleDateString('id-ID') : 'Belum diproses'}
                                </small>
                                ${item.catatan ? `<p class="mt-2 text-sm">${item.catatan}</p>` : ''}
                            </div>
                        </div>
                    `;
                });
                
                timelineHtml += '</div>';
                $('#timelineContent').html(timelineHtml);
            } else {
                $('#timelineContent').html(`
                    <div class="alert alert-danger">
                        <i class="feather icon-alert-circle"></i>
                        ${response.message || 'Gagal memuat timeline'}
                    </div>
                `);
            }
        },
        error: function() {
            $('#timelineContent').html(`
                <div class="alert alert-danger">
                    <i class="feather icon-alert-circle"></i>
                    Terjadi kesalahan saat memuat timeline
                </div>
            `);
        }
    });
}

function showDetailPengajuan(pengajuanId) {
    // Show loading
    $('#detailPengajuanContent').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Memuat detail pengajuan...</p>
        </div>
    `);
    
    $('#detailPengajuanModal').modal('show');
    
    // Load detail via AJAX - reuse existing function from pengajuan
    $.ajax({
        url: `/pengajuan/${pengajuanId}/detail`,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const pengajuan = response.data;
                let detailHtml = `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>No. Pengajuan:</strong> ${pengajuan.nomor_pengajuan}<br>
                            <strong>Kategori:</strong> ${pengajuan.kategori_pengajuan?.nama || 'N/A'}<br>
                            <strong>Judul:</strong> ${pengajuan.judul}
                        </div>
                        <div class="col-md-6">
                            <strong>Requester:</strong> ${pengajuan.requester?.nama || 'N/A'}<br>
                            <strong>Tanggal:</strong> ${new Date(pengajuan.tanggal_pengajuan).toLocaleDateString('id-ID')}<br>
                            <strong>Nominal:</strong> ${pengajuan.mata_uang} ${new Intl.NumberFormat('id-ID').format(pengajuan.nominal_pengajuan)}
                        </div>
                    </div>
                `;
                
                if (pengajuan.detail_fields && pengajuan.detail_fields.length > 0) {
                    detailHtml += '<h6 class="mt-3">Detail Pengajuan:</h6><div class="row">';
                    pengajuan.detail_fields.forEach(function(field) {
                        detailHtml += `
                            <div class="col-md-6 mb-2">
                                <strong>${field.label}:</strong><br>
                                <span class="text-muted">${field.value || '-'}</span>
                            </div>
                        `;
                    });
                    detailHtml += '</div>';
                }
                
                $('#detailPengajuanContent').html(detailHtml);
            } else {
                $('#detailPengajuanContent').html(`
                    <div class="alert alert-danger">
                        <i class="feather icon-alert-circle"></i>
                        ${response.message || 'Gagal memuat detail pengajuan'}
                    </div>
                `);
            }
        },
        error: function() {
            $('#detailPengajuanContent').html(`
                <div class="alert alert-danger">
                    <i class="feather icon-alert-circle"></i>
                    Terjadi kesalahan saat memuat detail pengajuan
                </div>
            `);
        }
    });
}
</script>


