@extends('Approval-app.Layout.approver-main')
@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    /* Timeline Horizontal Styles */
    .timeline-horizontal {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 20px 0;
        margin: 20px 0;
        position: relative;
    }
    
    .timeline-horizontal::before {
        content: '';
        position: absolute;
        top: 40px;
        left: 50px;
        right: 50px;
        height: 4px;
        background: #e9ecef;
        z-index: 1;
    }
    
    .timeline-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        position: relative;
        z-index: 2;
        flex: 1;
        max-width: 200px;
        margin: 0 10px;
    }
    
    .timeline-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
        font-weight: bold;
        color: white;
        position: relative;
        z-index: 3;
    }
    
    .timeline-circle.completed {
        background: #28a745;
    }
    
    .timeline-circle.current {
        background: #ffc107;
        color: #212529;
        animation: pulse 2s infinite;
    }
    
    .timeline-circle.pending {
        background: #6c757d;
    }
    
    .timeline-circle.rejected {
        background: #dc3545;
    }
    
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
    
    .timeline-content {
        text-align: center;
    }
    
    .timeline-title {
        font-weight: bold;
        font-size: 14px;
        margin-bottom: 5px;
        color: #212529;
    }
    
    .timeline-approver {
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 3px;
    }
    
    .timeline-date {
        font-size: 11px;
        color: #6c757d;
        font-style: italic;
    }
    
    .timeline-status {
        font-size: 11px;
        font-weight: bold;
        margin-top: 5px;
    }
    
    .timeline-status.completed {
        color: #28a745;
    }
    
    .timeline-status.current {
        color: #ffc107;
    }
    
    .timeline-status.pending {
        color: #6c757d;
    }
    
    .timeline-status.rejected {
        color: #dc3545;
    }
    
    /* Responsive timeline */
    @media (max-width: 768px) {
        .timeline-horizontal {
            flex-direction: column;
            align-items: stretch;
        }
        
        .timeline-horizontal::before {
            display: none;
        }
        
        .timeline-step {
            flex-direction: row;
            text-align: left;
            margin: 10px 0;
            max-width: 100%;
        }
        
        .timeline-circle {
            margin-right: 15px;
            margin-bottom: 0;
        }
        
        .timeline-content {
            text-align: left;
        }
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
                    <h5 class="m-b-10">Daftar Settlement</h5>
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
                                <th>No. Settlement</th>
                                <th>No. Pengajuan</th>
                                <th>Kategori</th>
                                <th>Nominal Awal</th>
                                <th>Total Actual</th>
                                <th>Selisih</th>
                                <th>Bukti Transfer</th>
                                <th>Tanggal Settlement</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($settlements as $settlement)
                            <tr>
                                <td>
                                    
                                    
                                    @if($settlement->status_settlement === 'draft')
                                        <button type="button" 
                                                class="btn btn-sm btn-primary rounded mr-2" 
                                                onclick="confirmSubmitSettlement({{ $settlement->id }}, '{{ $settlement->pengajuan->nomor_pengajuan }}')"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#confirmSubmitModal">
                                            <i class="feather icon-send"></i> Ajukan
                                        </button>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $settlement->nomor_settlement }}</strong>
                                </td>
                                <td>
                                    <a href="#"
                                       onclick="showDetailPengajuan({{ $settlement->pengajuan->id }})"
                                       data-bs-toggle="modal"
                                       data-bs-target="#detailModal"
                                       class="text-decoration-underline text-primary fw-semibold">
                                        {{ $settlement->pengajuan->nomor_pengajuan }}
                                    </a>
                                </td>
                                <td>
                                    <span class="badge badge-light">
                                        {{ $settlement->pengajuan->kategoriPengajuan->nama }}
                                    </span>
                                </td>
                                <td>
                                    <strong>Rp. {{ number_format($settlement->pengajuan->nominal_pengajuan, 0, ',', '.') }}</strong>
                                </td>
                                <td>
                                    <strong class="text-primary">Rp. {{ number_format($settlement->total_actual, 0, ',', '.') }}</strong>
                                </td>
                                <td>
                                    @php
                                        $selisihClass = $settlement->selisih > 0 ? 'text-info fw-bold' : ($settlement->selisih < 0 ? 'text-danger' : 'text-muted');
                                    @endphp
                                    <strong class="{{ $selisihClass }}">
                                        Rp. {{ number_format($settlement->selisih, 0, ',', '.') }}
                                    </strong>
                                </td>
                                <td>
                                    @if($settlement->file_bukti_transfer)
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-success" 
                                                onclick="showBuktiTransfer('{{ asset('storage/' . $settlement->file_bukti_transfer) }}', '{{ $settlement->nomor_settlement }}', '{{ pathinfo($settlement->file_bukti_transfer, PATHINFO_EXTENSION) }}')"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#buktiTransferModal">
                                            <i class="feather icon-image"></i> Lihat Bukti
                                        </button>
                                        <br>
                                        <small class="text-muted">{{ basename($settlement->file_bukti_transfer) }}</small>
                                    @elseif($settlement->selisih > 0  && $settlement->status_settlement == 'proses')
                                        @if(($settlement->total_step - $settlement->current_step) < 3)
                                        <button type="button" 
                                                class="btn btn-sm btn-warning" 
                                                onclick="showUploadBuktiModal({{ $settlement->id }}, '{{ $settlement->nomor_settlement }}', {{ $settlement->selisih }}, )"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#uploadBuktiTransferModal">
                                            <i class="feather icon-upload"></i> Upload Bukti 
                                        </button>
                                        @else
                                        <span class="text-muted">Menunggu Keputusan Finance</span>
                                        @endif
                                    @elseif($settlement->selisih < 0  && $settlement->status_settlement == 'approved')
                                        <span class="badge bg-warning text-dark me-2" title="Pembayaran perusahaan belum selesai">
                                            <i class="fas fa-exclamation-triangle"></i> Belum Dibayar
                                        </span>
                                    @else
                                        <span class="text-muted">Menunggu Keputusan Finance</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $settlement->tanggal_settlement->format('d/m/Y') }}
                                </td>
                                <td>
                                    @php
                                        $statusClass = '';
                                        switch($settlement->status_settlement) {
                                            case 'proses':
                                                $statusClass = 'badge-warning';
                                                break;
                                            case 'submitted':
                                                $statusClass = 'badge-info';
                                                break;
                                            case 'approved':
                                                $statusClass = 'status-paid';
                                                break;
                                            case 'rejected':
                                                $statusClass = 'badge-danger';
                                                break;
                                            default:
                                                $statusClass = 'badge-secondary';
                                        }
                                    @endphp
                                    <span class="badge {{ $statusClass }}">
                                        @if($settlement->status_settlement == 'approved')
                                            🎉 Pengajuan Selesai
                                        @else
                                            {{ ucfirst($settlement->status_settlement) }}
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-primary" 
                                                onclick="showDetailSettlement({{ $settlement->id }})"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#detailSettlementModal">
                                            <i class="feather icon-eye"></i> Detail
                                        </button>
                                        
                                        {{-- Button Settlement - Muncul jika pengajuan sudah approved dan belum ada settlement serta status TR nya paid dan milik user yang login --}}
                                        @if(
                                            $settlement->pengajuan->status_pengajuan === "approved" && 
                                            !$settlement && 
                                            $settlement->pengajuan->transactionRequest && 
                                            $settlement->pengajuan->transactionRequest->status === "paid" &&
                                            $settlement->pengajuan->requester_id === Auth::id()
                                        )
                                            <a href="{{ route('settlement.create', $settlement->pengajuan->id) }}" 
                                               class="btn btn-sm btn-success rounded">
                                                <i class="feather icon-file-plus"></i> Buat Settlement
                                            </a>
                                        @endif
                                        
                                        {{-- Button Edit Settlement - Muncul jika sudah ada settlement tapi statusnya masih draft/pending --}}
                                        @if($settlement && in_array($settlement->status_settlement, ['draft', 'pending', 'submitted']))
                                            <a href="{{ route('settlement.edit', $settlement->id) }}" 
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
                                        <i class="feather icon-file-text" style="font-size: 48px;"></i>
                                        <h6 class="mt-2">Belum ada settlement</h6>
                                        <p>Settlement akan otomatis dibuat setelah pengajuan disetujui</p>
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

<!-- Modal Upload Bukti Transfer -->
<div class="modal fade" id="uploadBuktiTransferModal" tabindex="-1" aria-labelledby="uploadBuktiTransferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-white" id="uploadBuktiTransferModalLabel">
                    <i class="fas fa-upload me-2"></i>Upload Bukti Transfer Pengembalian
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="uploadBuktiForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Nomor Settlement:</strong></label>
                                <p id="uploadSettlementNumber" class="text-primary fw-bold"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label"><strong>Jumlah Pengembalian:</strong></label>
                                <p id="uploadRefundAmount" class="text-danger fw-bold"></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Perhatian:</strong> Silakan upload bukti transfer pengembalian sisa dana sesuai dengan nominal yang tertera.
                    </div>
                    
                    <div class="mb-3">
                        <label for="tanggal_transfer" class="form-label">Tanggal Transfer <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="tanggal_transfer" name="tanggal_transfer" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="file_bukti_transfer" class="form-label">File Bukti Transfer <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="file_bukti_transfer" name="file_bukti_transfer" 
                               accept=".jpg,.jpeg,.png,.pdf" required>
                        <div class="form-text">
                            Format yang didukung: JPG, JPEG, PNG, PDF. Maksimal 5MB.
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="catatan_transfer" class="form-label">Catatan Transfer (Opsional)</label>
                        <textarea class="form-control" id="catatan_transfer" name="catatan_transfer" rows="3" 
                                  placeholder="Masukkan catatan tambahan mengenai transfer ini..."></textarea>
                    </div>
                    
                    <!-- Preview area untuk file yang dipilih -->
                    <div id="filePreview" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Preview:</label>
                            <div class="border rounded p-3 text-center" id="previewContent">
                                <!-- Preview content will be inserted here -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <button type="submit" class="btn btn-warning" id="submitBuktiTransfer">
                        <i class="fas fa-upload me-1"></i>Upload Bukti Transfer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Preview Bukti Transfer -->
<div class="modal fade" id="buktiTransferModal" tabindex="-1" aria-labelledby="buktiTransferModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="buktiTransferModalLabel">
                    Bukti Transfer - <span id="modalSettlementNumber"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                 <!--Loading indicator -->
                <div id="fileLoading" class="d-flex justify-content-center mb-3" style="display: none !important;">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                
                <!-- Image preview -->
                <div id="imageContainer" style="display: none;">
                    <img id="buktiTransferImage" 
                         src="" 
                         alt="Bukti Transfer" 
                         class="img-fluid border rounded shadow-sm" 
                         style="max-height: 70vh; max-width: 100%;">
                </div>
                
                <!-- PDF preview -->
                <div id="pdfContainer" style="display: none;">
                    <iframe id="buktiTransferPDF" 
                            src="" 
                            width="100%" 
                            height="70vh" 
                            frameborder="0" 
                            class="border rounded">
                    </iframe>
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="feather icon-info"></i> 
                            Jika PDF tidak tampil dengan baik, silakan klik tombol Download di bawah.
                        </small>
                    </div>
                </div>
                
                <!-- Error message -->
                <div id="errorContainer" style="display: none;" class="alert alert-warning">
                    <i class="feather icon-alert-triangle"></i>
                    File tidak dapat ditampilkan. Silakan download untuk melihat.
                </div>
            </div>
            <div class="modal-footer">
                <a id="downloadBuktiTransfer" 
                   href="" 
                   target="_blank" 
                   class="btn btn-primary">
                    <i class="feather icon-download"></i> Download
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="feather icon-x"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Submit Settlement -->
<div class="modal fade" id="confirmSubmitModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
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

<!-- Detail Pengajuan Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailPengajuanLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content" id="detailPengajuanContent">
      <div class="modal-header bg-primary text-white">
          <h5 class="modal-title text-white" id="detailPengajuanLabel">
              <i class="feather icon-file-text me-2"></i>
              Detail Pengajuan
          </h5>
          <div class="ms-auto d-flex align-items-center">
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

<!-- Detail Settlement Modal -->
<div class="modal fade" id="detailSettlementModal" tabindex="-1" aria-labelledby="detailSettlementLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content" id="detailSettlementContent">
        <div class="modal-header d-flex justify-content-between align-items-center">
            <h5 class="modal-title" id="detailSettlementLabel">Detail Settlement</h5>
            <!--<div class="d-flex align-items-center">-->
            <!--    <button class="btn btn-sm btn-outline-primary me-2" id="downloadPdfBtn">-->
            <!--        <i class="bi bi-download"></i> Unduh PDF-->
            <!--    </button>-->
            <!--    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>-->
            <!--</div>-->
        </div>
        <div class="modal-body" id="detailSettlementBody">
            <!-- Content akan diisi via JavaScript -->
            <div class="text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
            <tfoot>
    <tr class="table-info">
        <th colspan="2" class="text-end">TOTAL KESELURUHAN</th>
        <th class="text-end">... ${formatNumber(totalOriginal)}</th>
        <th class="text-end">... ${formatNumber(totalActual)}</th>
        <th class="text-end">... ${formatNumber(Math.abs(totalSelisih))}</th>
        <th colspan="2"></th> 
    </tr>
</tfoot>
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
    // JavaScript untuk handle upload bukti transfer
    $(document).ready(function() {
        let currentUploadSettlementId = null;
        
        // Fungsi untuk show modal upload bukti
        window.showUploadBuktiModal = function(settlementId, settlementNumber, refundAmount, currency) {
            currentUploadSettlementId = settlementId;
            $('#uploadSettlementNumber').text(settlementNumber);
            $('#uploadRefundAmount').text(' ' + number_format(refundAmount, 0, ',', '.'));
            
            // Reset form
            $('#uploadBuktiForm')[0].reset();
            $('#filePreview').hide();
        };
        
        // Handle file input change untuk preview
        $('#file_bukti_transfer').on('change', function() {
            const file = this.files[0];
            if (file) {
                const fileType = file.type;
                const previewContent = $('#previewContent');
                
                if (fileType.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewContent.html(`<img src="${e.target.result}" alt="Preview" style="max-width: 100%; max-height: 300px;">`);
                    };
                    reader.readAsDataURL(file);
                    $('#filePreview').show();
                } else if (fileType === 'application/pdf') {
                    previewContent.html(`<i class="fas fa-file-pdf" style="font-size: 48px; color: #dc3545;"></i><br><span class="mt-2">${file.name}</span>`);
                    $('#filePreview').show();
                }
            } else {
                $('#filePreview').hide();
            }
        });
        
        // Handle form submit
        $('#uploadBuktiForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            $.ajax({
                url: '/settlement/' + currentUploadSettlementId + '/upload-bukti-transfer',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function() {
                    $('#submitBuktiTransfer').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Uploading...');
                },
                success: function(response) {
                    $('#uploadBuktiTransferModal').modal('hide');
                    alert('Bukti transfer berhasil diupload!');
                    location.reload(); // Refresh halaman untuk update tampilan
                },
                error: function(xhr) {
                    console.log('Full error response:', xhr);
                    console.log('Status:', xhr.status);
                    console.log('Response text:', xhr.responseText);
                    
                    let errorMessage = 'Gagal mengupload bukti transfer';
                    
                    if (xhr.responseJSON) {
                        console.log('Response JSON:', xhr.responseJSON);
                        if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        } else if (xhr.responseJSON.errors) {
                            errorMessage = Object.values(xhr.responseJSON.errors).flat().join(', ');
                        }
                    } else if (xhr.responseText) {
                        errorMessage = xhr.responseText;
                    }
                    
                    alert('Error Detail: ' + errorMessage);
                    console.error('Upload failed:', errorMessage);
                },
                complete: function() {
                    $('#submitBuktiTransfer').prop('disabled', false).html('<i class="fas fa-upload me-1"></i>Upload Bukti Transfer');
                }
            });
        });
    });
    
    // Helper function untuk format number
    function number_format(number, decimals, dec_point, thousands_sep) {
        const n = !isFinite(+number) ? 0 : +number;
        const prec = !isFinite(+decimals) ? 0 : Math.abs(decimals);
        const sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep;
        const dec = (typeof dec_point === 'undefined') ? '.' : dec_point;
        
        const s = (prec ? n.toFixed(prec) : Math.round(n)).toString();
        const parts = s.split('.');
        parts[0] = parts[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        
        return parts.join(dec);
    }
</script>
<script>
    function showBuktiTransfer(fileUrl, settlementNumber, fileExtension) {
        // Reset modal content
        document.getElementById('modalSettlementNumber').textContent = settlementNumber;
        document.getElementById('downloadBuktiTransfer').href = fileUrl;
        
        // Hide all containers first
        const containers = ['fileLoading', 'imageContainer', 'pdfContainer', 'errorContainer'];
        containers.forEach(id => {
            document.getElementById(id).style.display = 'none';
        });
        
        // Show loading
        document.getElementById('fileLoading').style.display = 'block';
        
        // Determine file type
        const extension = fileExtension.toLowerCase();
        const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(extension);
        const isPDF = extension === 'pdf';
        
        setTimeout(() => {
            document.getElementById('fileLoading').style.display = 'none';
            
            if (isPDF) {
                // Handle PDF
                const pdfElement = document.getElementById('buktiTransferPDF');
                const pdfContainer = document.getElementById('pdfContainer');
                
                // Use PDF.js viewer for better compatibility
                const pdfViewerUrl = fileUrl + '#toolbar=1&navpanes=0&scrollbar=1&view=FitH';
                pdfElement.src = pdfViewerUrl;
                pdfContainer.style.display = 'block';
                
                // Handle PDF load error
                pdfElement.onload = function() {
                    pdfContainer.style.display = 'block';
                };
                
                pdfElement.onerror = function() {
                    pdfContainer.style.display = 'none';
                    document.getElementById('errorContainer').style.display = 'block';
                };
                
            } else if (isImage) {
                // Handle Image
                const imgElement = document.getElementById('buktiTransferImage');
                const imgContainer = document.getElementById('imageContainer');
                
                imgElement.onload = function() {
                    imgContainer.style.display = 'block';
                };
                
                imgElement.onerror = function() {
                    imgContainer.style.display = 'none';
                    document.getElementById('errorContainer').style.display = 'block';
                };
                
                imgElement.src = fileUrl;
                
            } else {
                // Unsupported file type
                document.getElementById('errorContainer').style.display = 'block';
            }
        }, 500); // Small delay for better UX
    }
    
    // Handle modal close event to cleanup
    document.getElementById('buktiTransferModal').addEventListener('hidden.bs.modal', function() {
        // Reset iframe and image sources to prevent memory leaks
        document.getElementById('buktiTransferPDF').src = '';
        document.getElementById('buktiTransferImage').src = '';
    });
    
    // Zoom functionality for images
    document.getElementById('buktiTransferImage').addEventListener('click', function() {
        if (this.style.cursor === 'zoom-out') {
            this.style.transform = 'scale(1)';
            this.style.cursor = 'zoom-in';
            this.style.transition = 'transform 0.3s ease';
        } else {
            this.style.transform = 'scale(1.5)';
            this.style.cursor = 'zoom-out';
            this.style.transition = 'transform 0.3s ease';
        }
    });
</script>
<script>
    let currentSettlementId = null;
    
    function confirmSubmitSettlement(settlementId, nomorPengajuan) {
        currentSettlementId = settlementId;
        document.getElementById('confirmPengajuanNumber').textContent = nomorPengajuan;
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const confirmSubmitBtn = document.getElementById('confirmSubmitBtn');
        if (confirmSubmitBtn) {
            confirmSubmitBtn.addEventListener('click', function() {
                if (currentSettlementId) {
                    submitSettlement(currentSettlementId);
                }
            });
        }
    });
    
    function submitSettlement(settlementId) {
        // Disable button saat proses
        const submitBtn = document.getElementById('confirmSubmitBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        
        fetch(`/settlement/${settlementId}/submit`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(async response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            const contentType = response.headers.get('content-type');
            console.log('Content type:', contentType);
            
            if (!response.ok) {
                // Coba parse sebagai JSON dulu
                let errorMessage;
                try {
                    const errorData = await response.json();
                    errorMessage = errorData.message || 'Unknown error occurred';
                    console.log('Error data:', errorData);
                } catch (parseError) {
                    // Jika tidak bisa parse JSON, ambil sebagai text
                    const errorText = await response.text();
                    errorMessage = `HTTP ${response.status}: ${errorText}`;
                    console.log('Error text:', errorText);
                }
                throw new Error(`Server Error: ${errorMessage}`);
            }
            
            // Parse response JSON
            const data = await response.json();
            console.log('Success data:', data);
            return data;
        })
        .then(data => {
            if (data.success) {
                // Tutup modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('confirmSubmitModal'));
                if (modal) {
                    modal.hide();
                }
                
                // Show success message
                alert('Success: ' + data.message);
                
                // Reload halaman
                setTimeout(() => {
                    location.reload();
                }, 500);
            } else {
                throw new Error(data.message || 'Unexpected response format');
            }
        })
        .catch(error => {
            console.error('Full error object:', error);
            console.error('Error message:', error.message);
            console.error('Error stack:', error.stack);
            
            // Show detailed error
            alert(`Error Details:\n${error.message}\n\nCheck browser console for more details.`);
        })
        .finally(() => {
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
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
    
        return `
            <div class="row mt-2">
                <div class="col-12">
                    <div class="perjalanan-dinas-form">
                        <!-- Header Section 
                        
    
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
                                                 <td class="ps-4">c. Udara (Pesawat)</td>
                                                 <td class="text-center">${formatCurrency(transportasiUdara1)}</td>
                                                 <td class="text-center">${formatCurrency(transportasiUdara2)}</td>
                                                 <td class="text-center">${formatCurrency(transportasiUdara3)}</td>
                                                 <td class="total-cell text-center">${formatCurrency(totalTransportasiUdara)}</td>
                                                 </tr>
                                            <tr>
                                                <td></td>
                                                <td class="ps-4">c. Airport Tax</td>
                                                <td class="text-center">${formatCurrency(transportasiTaxi1)}</td>
                                                <td class="text-center">${formatCurrency(transportasiTaxi2)}</td>
                                                <td class="text-center">${formatCurrency(transportasiTaxi3)}</td>
                                                <td class="total-cell text-center">${formatCurrency(totalTransportasiTaxi)}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">2</td>
                                                <td><strong>HOTEL</strong> <small class="text-muted">(${hotelMalam1 || hotelMalam2 || hotelMalam3 ? `${Math.max(hotelMalam1, hotelMalam2, hotelMalam3)} malam` : 'per malam'})</small></td>
                                                <td class="text-center">${formatCurrency(hotelBiaya1)}</td>
                                                <td class="text-center">${formatCurrency(hotelBiaya2)}</td>
                                                <td class="text-center">${formatCurrency(hotelBiaya3)}</td>
                                                <td class="total-cell text-center">${formatCurrency(totalHotel)}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-center">3</td>
                                                <td><strong>MAKAN</strong> <small class="text-muted">(${makanHari1 || makanHari2 || makanHari3 ? `${Math.max(makanHari1, makanHari2, makanHari3)} hari` : 'per hari'})</small></td>
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
                    
                </div>
            `;
        });
    
        timelineHtml += '</div>';
        return timelineHtml;
    }
    
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
    // Akhir Function untuk menampilkan detail pengajuan
        
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

    // Function untuk menampilkan detail settlement
    function showDetailSettlement(id) {
        document.getElementById('detailSettlementBody').innerHTML = `
             <div class="text-center py-5">
                 <div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div>
                 <p class="mt-3 text-muted">Memuat detail settlement...</p>
             </div>
        `;
        
        fetch(`/settlement/detail/${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    const settlement = data.data;
                    const currency = settlement.pengajuan?.mata_uang || 'Rp';
                    
                    let settlementHtml = `
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informasi Settlement</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm table-borderless">
                                            <tr><td><strong>No. Settlement:</strong></td><td>${settlement.nomor_settlement || '-'}</td></tr>
                                            <tr><td><strong>No. Pengajuan:</strong></td><td>${settlement.pengajuan?.nomor_pengajuan || '-'}</td></tr>
                                            <tr><td><strong>Kategori:</strong></td><td>${settlement.pengajuan?.judul || '-'}</td></tr>
                                            <tr><td><strong>Requester:</strong></td><td>${settlement.pengajuan?.requester?.nama || '-'}</td></tr>
                                            <tr><td><strong>Tanggal Settlement:</strong></td><td>${settlement.tanggal_settlement ? new Date(settlement.tanggal_settlement).toLocaleDateString('id-ID') : '-'}</td></tr>
                                            <tr><td><strong>Status Settlement:</strong></td><td>${getStatusBadge(settlement.status_settlement, 'settlement')}</td></tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header bg-success text-white">
                                        <h6 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Informasi Finansial</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm table-borderless">
                                            <tr>
                                                <td><strong>Total Pengajuan:</strong></td>
                                                <td>${currency} ${formatNumber(settlement.pengajuan.nominal_pengajuan || 0)}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Total Actual:</strong></td>
                                                <td>${currency} ${formatNumber(settlement.total_actual || 0)}</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Selisih:</strong></td>
                                                <td>${getSelisihDisplay(settlement)}</td>
                                            </tr>
                                        </table>
                                        ${getWarningAlert(settlement)}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        ${getBuktiTransferSection(settlement)}
                        ${getCatatanSection(settlement)}
                    `;
    
                    // Timeline Approval Settlement
                    if (settlement.timeline_data && settlement.timeline_data.length > 0) {
                        settlementHtml += getTimelineHtml(settlement.timeline_data);
                    }
    
                    // Detail items actual
                    if (settlement.details && settlement.details.length > 0) {
                        settlementHtml += getActualDetailsTable(settlement.details);
                    }
                    
                    document.getElementById('detailSettlementLabel').textContent = `Detail Settlement ${settlement.nomor_settlement || 'N/A'}`;
                    document.getElementById('detailSettlementBody').innerHTML = settlementHtml;
                    
                } else {
                    // Tampilkan pesan tidak ada data
                    document.getElementById('detailSettlementLabel').textContent = 'Detail Settlement';
                    document.getElementById('detailSettlementBody').innerHTML = `
                        <div class="text-center py-5">
                            <i class="fas fa-inbox text-muted" style="font-size: 64px;"></i>
                            <h5 class="text-muted mt-3">Data Settlement Tidak Ditemukan</h5>
                            <p class="text-muted">Settlement dengan ID tersebut tidak tersedia atau telah dihapus.</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('detailSettlementLabel').textContent = 'Error';
                document.getElementById('detailSettlementBody').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Error!</strong> Gagal memuat data settlement. Silakan coba lagi atau hubungi administrator.
                    </div>
                `;
            });
    }
    
    // Helper functions
    function formatNumber(number) {
        return new Intl.NumberFormat('id-ID').format(number || 0);
    }
    
    function getStatusBadge(status, type) {
        let badgeClass = 'badge-secondary';
        let statusText = status ? status.charAt(0).toUpperCase() + status.slice(1) : 'N/A';

        if (type === 'settlement') {
            switch(status) {
                case 'proses': badgeClass = 'badge-warning'; break;
                case 'submitted': badgeClass = 'badge-info'; break;
                case 'approved': badgeClass = 'badge-success'; break;
                case 'rejected': badgeClass = 'badge-danger'; break;
                case 'completed': statusText = 'Selesai'; badgeClass = 'badge-success'; break;
            }
        } else if (type === 'realisasi') {
             switch(status) {
                case 'balance': badgeClass = 'badge-success'; break;
                case 'over': badgeClass = 'badge-info'; break; // Over budget/perlu dikembalikan
                case 'under': badgeClass = 'badge-danger'; break; // Under budget/kekurangan
                case 'proses': badgeClass = 'badge-warning'; break;
            }
        }
        
        return `<span class="badge ${badgeClass}">${statusText}</span>`;
    }
    
    function getSelisihDisplay(settlement) {
        const selisih = settlement.selisih || 0;
        const currency = settlement.pengajuan?.mata_uang || 'Rp';
        let selisihClass = 'text-muted';
        let note = '(Balance)';
        
        if (selisih > 0) {
            selisihClass = 'text-info fw-bold';
            note = '(Perlu dikembalikan)';
        } else if (selisih < 0) {
            selisihClass = 'text-danger fw-bold';
            note = '(Kekurangan dana)';
        }
        
        return `
            <span class="${selisihClass}">
                ${currency} ${formatNumber(Math.abs(selisih))}
            </span>
            <small class="text-muted d-block">${note}</small>
        `;
    }
    
    function getWarningAlert(settlement) {
        if (settlement.status_realisasi === 'over' && settlement.selisih > 0) {
            return `
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Perhatian:</strong> Settlement ini memerlukan pengembalian dana sebesar 
                    <strong>${settlement.pengajuan?.mata_uang || 'Rp'} ${formatNumber(Math.abs(settlement.selisih))}</strong>
                </div>
            `;
        }
        return '';
    }
    
    // function getSettlementDetailsTable(settlement) {
    //     if (!settlement.details || settlement.details.length === 0) {
    //         return `
    //             <div class="text-center py-3">
    //                 <i class="fas fa-inbox text-muted" style="font-size: 48px;"></i>
    //                 <p class="text-muted mt-2">Tidak ada detail items</p>
    //             </div>
    //         `;
    //     }
        
    //     let tableHtml = `
    //         <div class="table-responsive">
    //             <table class="table table-sm table-hover">
    //                 <thead class="table-light">
    //                     <tr>
    //                         <th>Nilai Pengajuan</th>
    //                         <th>Nilai Actual</th>
    //                         <th>Selisih</th>
    //                         <th>Keterangan</th>
    //                     </tr>
    //                 </thead>
    //                 <tbody>
    //     `;
        
    //     settlement.details.forEach(detail => {
    //         const detailSelisih = (detail.total_actual || 0) - (detail.original_nominal || 0);
    //         const detailClass = detailSelisih > 0 ? 'text-info' : 
    //                           (detailSelisih < 0 ? 'text-danger' : 'text-muted');
            
    //         tableHtml += `
    //             <tr>
    //                 <td>Rp ${formatNumber(detail.original_nominal || 0)}</td>
    //                 <td>Rp ${formatNumber(detail.total_actual || 0)}</td>
    //                 <td>
    //                     <span class="${detailClass}">
    //                         ${settlement.pengajuan?.mata_uang || 'Rp'} ${formatNumber(detailSelisih)}
    //                     </span>
    //                 </td>
    //                 <td>
    //                     ${detail.keterangan ? 
    //                         `<small class="text-muted">${detail.keterangan}</small>` : 
    //                         '<small class="text-muted">-</small>'
    //                     }
    //                 </td>
    //             </tr>
    //         `;
    //     });
        
    //     tableHtml += `
    //                 </tbody>
    //             </table>
    //         </div>
    //     `;
        
    //     return tableHtml;
    // }
    
    function getBuktiTransferSection(settlement) {
        if (!settlement.file_bukti_transfer) {
            return '';
        }
        
        return `
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="fas fa-receipt me-2"></i>Bukti Transfer Pengembalian</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Tanggal Transfer:</strong> ${settlement.tanggal_transfer ? new Date(settlement.tanggal_transfer).toLocaleDateString('id-ID') : '-'}</p>
                                    <p><strong>Nama File:</strong> ${settlement.file_bukti_transfer.split('/').pop()}</p>
                                    ${settlement.catatan_transfer ? `<p><strong>Catatan:</strong> ${settlement.catatan_transfer}</p>` : ''}
                                </div>
                                <div class="col-md-6 text-end">
                                    <a href="/storage/${settlement.file_bukti_transfer}" 
                                       target="_blank" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i>Lihat Bukti Transfer
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    function getCatatanSection(settlement) {
        if (!settlement.catatan_settlement) {
            return '';
        }
        
        return `
            <div class="row mt-3">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header text-primary">
                            <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Catatan Settlement</h6>
                        </div>
                        <div class="card-body">
                            <pre class="mb-0" style="white-space: pre-wrap; font-family: inherit;">${settlement.catatan_settlement}</pre>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    function getTimelineHtml(timelineData) {
        let timelineHtml = `
            <div class="row mt-4">
                <div class="col-12">
                    <h6>Timeline Approval Settlement</h6>
                    <div class="timeline-horizontal">
        `;
        
        timelineData.forEach((step, index) => {
            let circleClass = 'pending';
            let statusText = 'Menunggu';
            
            if (step.is_completed) {
                circleClass = 'completed';
                statusText = 'Disetujui';
            } else if (step.is_rejected) {
                circleClass = 'rejected';
                statusText = 'Ditolak';
            } else if (step.is_current) {
                circleClass = 'current';
                statusText = 'Sedang Proses';
            }
            
            timelineHtml += `
                <div class="timeline-step">
                    <div class="timeline-circle ${circleClass}">
                        ${step.is_completed ? '<i class="feather icon-check text-white"></i>' : 
                          step.is_rejected ? '<i class="bi bi-x"></i>' : 
                          step.is_current ? '<i class="feather icon-clock text-white"></i>' : 
                          (index + 1)}
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-approver">${step.approver_name || 'N/A'}</div>
                        <div class="timeline-approver">${step.approver_jabatan || ''}</div>
                        ${step.tanggal_approval ? 
                            `<div class="timeline-date">${new Date(step.tanggal_approval).toLocaleDateString('id-ID')} ${new Date(step.tanggal_approval).toLocaleTimeString('id-ID', {hour: '2-digit', minute: '2-digit'})}</div>` : 
                            ''
                        }
                        <div class="timeline-status ${circleClass}">${statusText}</div>
                        ${step.catatan ? `<div class="timeline-date mt-1">"${step.catatan}"</div>` : ''}
                    </div>
                </div>
            `;
        });
        
        timelineHtml += `
                    </div>
                </div>
            </div>
        `;
        
        return timelineHtml;
    }

    function getActualDetailsTable(details) {
        // Ambil mata uang dari detail pertama atau default ke 'Rp'
        const currency = details[0]?.pengajuan?.mata_uang || 'Rp';
        
        // Inisialisasi variabel total
        let totalOriginal = 0;
        let totalActual = 0;
    
        // Mapping details dan menghitung total
        const rowsHtml = details.map((detail, index) => {
            // Mengambil nilai original dari field pengajuan, jika tersedia
            const originalNominal = parseFloat(detail.original_nominal) || 0;
            const actualNominal = parseFloat(detail.nominal) || 0;
            
            // Akumulasi total
            totalOriginal += originalNominal;
            totalActual += actualNominal;
            
            const selisih = originalNominal - actualNominal; // Original - Actual
            
            let selisihClass = 'text-muted';
            if (selisih > 0) { // Lebih hemat (kelebihan)
                selisihClass = 'text-info fw-bold';
            } else if (selisih < 0) { // Kurang bayar (kekurangan)
                selisihClass = 'text-danger fw-bold';
            }
            
            return `
                <tr>
                    <td>${index + 1}</td>
                    <td>${detail.keterangan || '-'}</td>
                    
                    <td class="text-end">
                        <strong class="text-muted">${currency} ${formatNumber(originalNominal)}</strong>
                    </td>
                    
                    <td class="text-end">
                        <strong class="text-primary">${currency} ${formatNumber(actualNominal)}</strong>
                    </td>
                    
                    <td class="text-end">
                        <span class="${selisihClass}">
                            ${currency} ${formatNumber(Math.abs(selisih))}
                        </span>
                    </td>
                    
                    <td class="text-center">
                        ${detail.file_bukti ? 
                            `<a href="/storage/${detail.file_bukti}" target="_blank" class="btn btn-xs btn-outline-primary"><i class="feather icon-image"></i> Lihat</a>` : 
                            '<span class="text-muted">-</span>'
                        }
                    </td>
                    <td>${detail.catatan || '<span class="text-muted">-</span>'}</td>
                </tr>
            `;
        }).join('');
    
        // Hitung total selisih keseluruhan
        const totalSelisih = totalOriginal - totalActual;
        let totalSelisihClass = 'text-muted';
        if (totalSelisih > 0) {
            totalSelisihClass = 'text-info fw-bold';
        } else if (totalSelisih < 0) {
            totalSelisihClass = 'text-danger fw-bold';
        }
    
        return `
            <div class="row mt-3">
                <div class="col-12">
                    <h6 class="text-primary" ><i class="feather icon-list me-1"></i>Detail Biaya Item</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead>
                                <tr class="table-light">
                                    <th width="5%">No</th>
                                    <th width="30%">Keterangan Item</th>
                                    <th width="15%" class="text-center">Nominal Awal</th>
                                    <th width="15%" class="text-center">Nominal Actual</th>
                                    <th width="15%" class="text-center">Selisih</th>
                                    <th width="10%" class="text-center">Bukti</th>
                                    <th width="10%">Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${rowsHtml}
                            </tbody>
                            <tfoot>
                                <tr class="table-info">
                                    <th colspan="2" class="text-end">TOTAL KESELURUHAN</th>
                                    <th class="text-end">
                                        <span class="text-muted">${currency} ${formatNumber(totalOriginal)}</span>
                                    </th>
                                    <th class="text-end">
                                        <span class="text-primary">${currency} ${formatNumber(totalActual)}</span>
                                    </th>
                                    <th class="text-end">
                                        <span class="${totalSelisihClass}">
                                            ${currency} ${formatNumber(Math.abs(totalSelisih))}
                                        </span>
                                    </th>
                                    <th colspan="2"></th> </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        `;
    }

    // Helper function untuk mendapatkan class status
    function getStatusClass(status) {
        switch(status) {
            case 'proses': return 'warning';
            case 'submitted': return 'info';
            case 'settlement_created': return 'info';
            case 'approved': return 'success';
            case 'rejected': return 'danger';
            case 'completed': return 'success';
            case 'disetujui': return 'success';
            default: return 'secondary';
        }
    }
</script>
@endsection