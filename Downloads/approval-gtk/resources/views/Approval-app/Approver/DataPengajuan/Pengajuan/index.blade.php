@extends('Approval-app.Layout.approver-main')

@section('head')
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
    </style>
@endsection

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Pengajuan Terbaru {{ Auth::id() }}</h5>
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
                    <button type="button" class="btn btn-info rounded">Buat Pengajuan</button>
                </a>
                <!--<div class="alert alert-warning mt-3 mb-0 alert-dismissible fade show" role="alert">-->
                <!--    Untuk melihat log proses pengajuan anda, klik <strong>Status pengajuan!</strong>-->
                <!--    <button type="button" class="btn-close pb-2" data-bs-dismiss="alert" aria-label="Close"></button>-->
                <!--</div>-->
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
                                <th>No. Pengajuan</th>
                                <th>Kategori</th>
                                <th>Judul</th>
                                <th>Nominal</th>
                                <th>Tanggal Pengajuan</th>
                                <th>Status</th>
                                <th>Progress</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pengajuanList as $pengajuan)
                            <tr>
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
                                    <strong>{{ $pengajuan->mata_uang }} {{ number_format($pengajuan->nominal_pengajuan, 0, ',', '.') }}</strong>
                                </td>
                                <td>
                                    {{ $pengajuan->tanggal_pengajuan->format('d/m/Y') }}
                                </td>
                                <td>
                                    @php
                                        $statusClass = '';
                                        switch($pengajuan->status_pengajuan) {
                                            case 'pending':
                                                $statusClass = 'badge-pending';
                                                break;
                                            case 'approved':
                                                $statusClass = 'badge-approved';
                                                break;
                                            case 'rejected':
                                                $statusClass = 'badge-rejected';
                                                break;
                                            case 'completed':
                                                $statusClass = 'badge-completed';
                                                break;
                                            default:
                                                $statusClass = 'badge-secondary';
                                        }
                                    @endphp
                                    <span class="badge {{ $statusClass }}">
                                        {{ ucfirst($pengajuan->status_pengajuan) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="progress" style="width: 100px;">
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
                                    <div class="btn-group" role="group">
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary" 
                                                onclick="showDetailPengajuan({{ $pengajuan->id }})"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#detailModal">
                                            <i class="feather icon-eye"></i> Detail
                                        </button>
                                        
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-info" 
                                                onclick="showTimelinePengajuan({{ $pengajuan->id }})"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#timelineModal">
                                            <i class="feather icon-clock"></i> Status
                                        </button>
                                        
                                        {{-- Button Settlement - Muncul jika pengajuan sudah approved dan belum ada settlement --}}
                                        @if($pengajuan->canCreateSettlement())
                                            <a href="{{ route('settlement.create', $pengajuan->id) }}" 
                                               class="btn btn-sm btn-success">
                                                <i class="feather icon-file-plus"></i> Buat Settlement
                                            </a>
                                        @endif
                                        
                                        {{-- Button Lihat Settlement - Muncul jika sudah ada settlement --}}
                                        @if($pengajuan->settlement)
                                            <button type="button" 
                                                    class="btn btn-sm btn-warning" 
                                                    onclick="showDetailSettlement({{ $pengajuan->settlement->id }})">
                                                <i class="feather icon-file-text"></i> Settlement
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="feather icon-inbox" style="font-size: 48px;"></i>
                                        <h6 class="mt-2">Belum ada pengajuan</h6>
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

<!-- Detail Pengajuan Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailPengajuanLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content" id="detailPengajuanContent">
      <div class="modal-header">
          <h5 class="modal-title" id="detailPengajuanLabel">Detail Pengajuan</h5>
          <div class="ms-auto d-flex align-items-center">
            
            <button class="btn btn-sm btn-outline-primary ms-2" id="downloadPdfBtn">
              <i class="bi bi-download"></i> Unduh PDF
            </button>
          </div>
        </div>

      <div class="modal-body" id="detailPengajuanBody">
        <!-- Content akan diisi via JavaScript -->
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
      </div>
      
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>

<!-- Detail Settlement Modal -->
<div class="modal fade" id="detailSettlementModal" tabindex="-1" aria-labelledby="detailSettlementLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content" id="detailSettlementContent">
      <div class="modal-header">
        <h5 class="modal-title" id="detailSettlementLabel">Detail Settlement</h5>
        <button class="btn btn-sm btn-outline-secondary me-2" id="downloadPdfSettlementBtn">
          <i class="bi bi-download"></i> Unduh PDF
        </button>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="detailSettlementBody">
        <!-- Content akan diisi via JavaScript -->
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('script')
    <script>
        // Function untuk menampilkan detail pengajuan
        // Function untuk menampilkan detail pengajuan
        function showDetailPengajuan(id) {
            fetch(`/pengajuan/detail/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const pengajuan = data.data;
                        
                        let detailHtml = `
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Informasi Umum</h6>
                                    <table class="table table-sm">
                                        <tr><td>Nomor Pengajuan</td><td><strong>${pengajuan.nomor_pengajuan}</strong></td></tr>
                                        <tr><td>Kategori</td><td>${pengajuan.kategori_pengajuan.nama}</td></tr>
                                        
                                        <tr><td>Tanggal Pengajuan</td><td>${new Date(pengajuan.tanggal_pengajuan).toLocaleDateString('id-ID')}</td></tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6>Status & Progress</h6>
                                    <table class="table table-sm">
                                        <tr><td>Status</td><td><span class="badge badge-${getStatusClass(pengajuan.status_pengajuan)}">${pengajuan.status_pengajuan}</span></td></tr>
                                        <tr><td>Progress</td><td>${pengajuan.current_step}/${pengajuan.total_step}</td></tr>
                                        <tr><td>Requester</td><td>${pengajuan.requester.nama}</td></tr>
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
        
                        // Tampilkan detail fields dari database jika ada
                        if (pengajuan.detail_fields && pengajuan.detail_fields.length > 0) {
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
                                    displayValue = new Intl.NumberFormat('id-ID').format(field.value);
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
                        
                        document.getElementById('detailPengajuanLabel').textContent = `Detail Pengajuan ${pengajuan.nomor_pengajuan}`;
                        document.getElementById('detailPengajuanBody').innerHTML = detailHtml;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('detailPengajuanBody').innerHTML = '<div class="alert alert-danger">Gagal memuat data</div>';
                });
        }

        // Function untuk menampilkan timeline pengajuan
        function showTimelinePengajuan(id) {
            fetch(`/pengajuan/timeline/${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const histories = data.data;
                        if (histories.length === 0) {
                            document.getElementById('timelineContent').innerHTML = '<div class="alert alert-warning">Tidak ada riwayat untuk pengajuan ini.</div>';
                            return;
                        }
                        
                        let timelineHtml = '<ul class="timeline-modern list-unstyled m-0 p-0 position-relative">';
                        
                        histories.forEach((history, index) => {
                            const isCompleted = history.status_sesudah !== 'pending';
                            const isCurrent = history.status_sesudah === 'pending' && index === histories.length - 1;
                            
                            timelineHtml += `
                                <li class="timeline-item ${isCompleted ? 'completed' : ''} ${isCurrent ? 'current' : ''} show">
                                    <div class="timeline-content">
                                        <small>${new Date(history.created_at).toLocaleDateString('id-ID', { 
                                            day: '2-digit', 
                                            month: 'short', 
                                            year: 'numeric',
                                            hour: '2-digit',
                                            minute: '2-digit'
                                        })}</small>
                                        <h6 style="color: ${isCompleted ? '#0e6a39' : (isCurrent ? '#ffba57' : '#666')}">
                                            <strong>
                                                <i class="feather icon-${isCompleted ? 'check-circle' : (isCurrent ? 'loader' : 'circle')} mr-2"></i>
                                                ${history.aksi}
                                            </strong>
                                        </h6>
                                        <p>${history.catatan || 'Tidak ada catatan'}</p>
                                        ${history.approver ? `<small>Oleh: ${history.approver.name}</small>` : ''}
                                    </div>
                                </li>
                            `;
                        });
                        
                        timelineHtml += '</ul>';
                        document.getElementById('timelineContent').innerHTML = timelineHtml;
                    } else {
                        document.getElementById('timelineContent').innerHTML = '<div class="alert alert-danger">Gagal memuat data timeline</div>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('timelineContent').innerHTML = '<div class="alert alert-danger">Gagal memuat data timeline</div>';
                });
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
                                        <tr><td>Nomor Settlement</td><td><strong>${settlement.nomor_settlement}</strong></td></tr>
                                        <tr><td>Tanggal Settlement</td><td>${new Date(settlement.tanggal_settlement).toLocaleDateString('id-ID')}</td></tr>
                                        <tr><td>Total Actual</td><td><strong>${new Intl.NumberFormat().format(settlement.total_actual)}</strong></td></tr>
                                        <tr><td>Selisih</td><td><strong>${new Intl.NumberFormat().format(settlement.selisih)}</strong></td></tr>
                                        <tr><td>Status</td><td><span class="badge badge-${getStatusClass(settlement.status_settlement)}">${settlement.status_settlement}</span></td></tr>
                                    </table>
                                </div>
                            </div>
                        `;
                        
                        document.getElementById('detailSettlementLabel').textContent = `Detail Settlement ${settlement.nomor_settlement}`;
                        document.getElementById('detailSettlementBody').innerHTML = settlementHtml;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('detailSettlementBody').innerHTML = '<div class="alert alert-danger">Gagal memuat data settlement</div>';
                });
        }

        // Helper function untuk mendapatkan class status
        function getStatusClass(status) {
            switch(status) {
                case 'pending': return 'warning';
                case 'approved': return 'success';
                case 'rejected': return 'danger';
                case 'completed': return 'info';
                default: return 'secondary';
            }
        }

        // PDF Download functionality
        document.getElementById('downloadPdfBtn').addEventListener('click', () => {
            const element = document.getElementById('detailPengajuanContent');
            html2pdf()
            .set({ filename: 'detail-pengajuan.pdf' })
            .from(element)
            .save();
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