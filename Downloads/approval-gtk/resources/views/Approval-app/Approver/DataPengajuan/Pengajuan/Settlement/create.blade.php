@extends('Approval-app.Layout.main')

@section('head')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.settlement-form .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}
.detail-item {
    border-left: 4px solid #007bff;
    background-color: #f8f9fa;
}
.add-detail-btn {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border: none;
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
                    <h5 class="m-b-10">Buat Settlement</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('kategori-pengajuan.index') }}">Pengajuan</a></li>
                    <li class="breadcrumb-item active">Buat Settlement</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Info Pengajuan -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="feather icon-file-text me-2"></i>Informasi Pengajuan</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="150"><strong>No. Pengajuan</strong></td>
                                <td>: {{ $pengajuan->nomor_pengajuan }}</td>
                            </tr>
                            <tr>
                                <td><strong>Kategori</strong></td>
                                <td>: {{ $pengajuan->kategoriPengajuan->nama }}</td>
                            </tr>
                            <tr>
                                <td><strong>Judul</strong></td>
                                <td>: {{ $pengajuan->judul }}</td>
                            </tr>
                            <tr>
                                <td><strong>Nominal Disetujui</strong></td>
                                <td>: <strong class="text-success">{{ $pengajuan->mata_uang }} {{ number_format($pengajuan->nominal_pengajuan, 0, ',', '.') }}</strong></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td width="150"><strong>Requester</strong></td>
                                <td>: {{ $pengajuan->requester->nama }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal Pengajuan</strong></td>
                                <td>: {{ $pengajuan->tanggal_pengajuan->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Department</strong></td>
                                <td>: {{ $pengajuan->requester->department->nama }}</td>
                            </tr>
                            <tr>
                                <td><strong>Status</strong></td>
                                <td>: <span class="badge badge-success">{{ ucfirst($pengajuan->status_pengajuan) }}</span></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Pengajuan -->
        <div class="card mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="feather icon-list me-2"></i>Detail Pengajuan Awal</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($detailData as $detail)
                    <div class="col-md-6 mb-3">
                        <div class="detail-item p-3 rounded">
                            <label class="form-label fw-bold text-primary">{{ $detail['label'] }}</label>
                            <div class="detail-value">
                                @if($detail['type'] == 'file')
                                    @if($detail['value'])
                                        <a href="{{ Storage::url($detail['value']) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="feather icon-download"></i> Lihat File
                                        </a>
                                    @else
                                        <span class="text-muted">Tidak ada file</span>
                                    @endif
                                @elseif($detail['type'] == 'currency')
                                    <strong>{{ number_format($detail['value'], 0, ',', '.') }}</strong>
                                @elseif($detail['type'] == 'date')
                                    {{ \Carbon\Carbon::parse($detail['value'])->format('d/m/Y') }}
                                @else
                                    {{ $detail['value'] }}
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Form Settlement -->
        <div class="card settlement-form">
            <div class="card-header">
                <h5 class="mb-0"><i class="feather icon-dollar-sign me-2"></i>Laporan Biaya Settlement (LBS)</h5>
            </div>
            <div class="card-body">
                <form id="settlementForm" action="{{ route('settlement.store', $pengajuan->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Catatan Settlement -->
                    <div class="mb-4">
                        <label for="catatan_settlement" class="form-label">Catatan Settlement</label>
                        <textarea class="form-control" id="catatan_settlement" name="catatan_settlement" rows="3" 
                                  placeholder="Masukkan catatan atau keterangan tambahan untuk settlement ini..."></textarea>
                    </div>

                    <!-- Detail Settlement -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="text-primary fw-bold mb-0">Detail Actual Cost</h6>
                            <button type="button" class="btn btn-sm add-detail-btn text-white" onclick="addDetailSettlement()">
                                <i class="feather icon-plus"></i> Tambah Item
                            </button>
                        </div>
                        
                        <div id="detailSettlementContainer">
                            <!-- Template untuk item detail settlement -->
                            <div class="detail-settlement-item border rounded p-3 mb-3" data-index="0">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h6 class="text-secondary mb-0">Item #<span class="item-number">1</span></h6>
                                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeDetailSettlement(this)">
                                        <i class="feather icon-trash-2"></i>
                                    </button>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Keterangan <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="detail_settlement[0][keterangan]" 
                                               placeholder="Masukkan keterangan pengeluaran" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" name="detail_settlement[0][tanggal_transaksi]" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nominal (IDR) <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control nominal-input" name="detail_settlement[0][nominal]" 
                                               placeholder="0" min="0" step="0.01" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Kategori Biaya</label>
                                        <select class="form-control" name="detail_settlement[0][kategori_biaya]">
                                            <option value="">Pilih Kategori</option>
                                            <option value="Transportasi">Transportasi</option>
                                            <option value="Akomodasi">Akomodasi</option>
                                            <option value="Konsumsi">Konsumsi</option>
                                            <option value="Material">Material</option>
                                            <option value="Jasa">Jasa</option>
                                            <option value="Operasional">Operasional</option>
                                            <option value="Lainnya">Lainnya</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">File Bukti</label>
                                        <input type="file" class="form-control" name="detail_settlement[0][file_bukti]" 
                                               accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                        <small class="text-muted">Format: JPG, PNG, PDF, DOC (Max: 10MB)</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Catatan</label>
                                        <textarea class="form-control" name="detail_settlement[0][catatan]" rows="2" 
                                                  placeholder="Catatan tambahan (opsional)"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Summary -->
                        <div class="bg-light p-3 rounded">
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Total Disetujui:</strong><br>
                                    <span class="text-primary fs-5">{{ $pengajuan->mata_uang }} <span id="totalDisetujui">{{ number_format($pengajuan->nominal_pengajuan, 0, ',', '.') }}</span></span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Total Actual:</strong><br>
                                    <span class="text-success fs-5">IDR <span id="totalActual">0</span></span>
                                </div>
                                <div class="col-md-4">
                                    <strong>Selisih:</strong><br>
                                    <span class="fs-5" id="selisihAmount">IDR 0</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('kategori-pengajuan.index') }}" class="btn btn-secondary">
                            <i class="feather icon-arrow-left"></i> Kembali
                        </a>
                        <div class="btn-group">
                            <button type="button" id="checkStatus" class="btn btn-outline-info">
                                <i class="feather icon-check-circle"></i> Cek Status
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="feather icon-send"></i> Kirim Settlement
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
let detailIndex = 1;
const nominalDisetujui = {{ $pengajuan->nominal_pengajuan }};

$(document).ready(function() {
    // Initialize Select2
    $('select').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Calculate totals on page load
    calculateTotals();

    // Handle form submission
    $('#settlementForm').on('submit', function(e) {
        e.preventDefault();
        
        if (!validateForm()) {
            return;
        }

        const formData = new FormData(this);
        
        // Show loading
        Swal.fire({
            title: 'Memproses Settlement...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message || 'Terjadi kesalahan'
                    });
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                let errorMessage = 'Terjadi kesalahan sistem';
                
                if (response && response.errors) {
                    errorMessage = Object.values(response.errors).flat().join('\n');
                } else if (response && response.message) {
                    errorMessage = response.message;
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: errorMessage
                });
            }
        });
    });

    // Handle nominal input changes
    $(document).on('input', '.nominal-input', function() {
        calculateTotals();
    });

    // Check status button
    $('#checkStatus').on('click', function() {
        Swal.fire({
            icon: 'info',
            title: 'Status Settlement',
            html: `
                <div class="text-start">
                    <p><strong>Pengajuan:</strong> {{ $pengajuan->nomor_pengajuan }}</p>
                    <p><strong>Status:</strong> <span class="badge badge-success">{{ ucfirst($pengajuan->status_pengajuan) }}</span></p>
                    <p><strong>Total Disetujui:</strong> {{ $pengajuan->mata_uang }} {{ number_format($pengajuan->nominal_pengajuan, 0, ',', '.') }}</p>
                    <p><strong>Settlement:</strong> <span class="badge badge-warning">Sedang Dibuat</span></p>
                </div>
            `
        });
    });
});

function addDetailSettlement() {
    const container = $('#detailSettlementContainer');
    const template = `
        <div class="detail-settlement-item border rounded p-3 mb-3" data-index="${detailIndex}">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="text-secondary mb-0">Item #<span class="item-number">${detailIndex + 1}</span></h6>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeDetailSettlement(this)">
                    <i class="feather icon-trash-2"></i>
                </button>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Keterangan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="detail_settlement[${detailIndex}][keterangan]" 
                           placeholder="Masukkan keterangan pengeluaran" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="detail_settlement[${detailIndex}][tanggal_transaksi]" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nominal (IDR) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control nominal-input" name="detail_settlement[${detailIndex}][nominal]" 
                           placeholder="0" min="0" step="0.01" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kategori Biaya</label>
                    <select class="form-control" name="detail_settlement[${detailIndex}][kategori_biaya]">
                        <option value="">Pilih Kategori</option>
                        <option value="Transportasi">Transportasi</option>
                        <option value="Akomodasi">Akomodasi</option>
                        <option value="Konsumsi">Konsumsi</option>
                        <option value="Material">Material</option>
                        <option value="Jasa">Jasa</option>
                        <option value="Operasional">Operasional</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">File Bukti</label>
                    <input type="file" class="form-control" name="detail_settlement[${detailIndex}][file_bukti]" 
                           accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                    <small class="text-muted">Format: JPG, PNG, PDF, DOC (Max: 10MB)</small>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-control" name="detail_settlement[${detailIndex}][catatan]" rows="2" 
                              placeholder="Catatan tambahan (opsional)"></textarea>
                </div>
            </div>
        </div>
    `;
    
    container.append(template);
    
    // Initialize Select2 for new select elements
    container.find(`[data-index="${detailIndex}"] select`).select2({
        theme: 'bootstrap4',
        width: '100%'
    });
    
    detailIndex++;
    updateItemNumbers();
}

function removeDetailSettlement(button) {
    if ($('.detail-settlement-item').length > 1) {
        $(button).closest('.detail-settlement-item').remove();
        updateItemNumbers();
        calculateTotals();
    } else {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Minimal harus ada satu item settlement'
        });
    }
}

function updateItemNumbers() {
    $('.detail-settlement-item').each(function(index) {
        $(this).find('.item-number').text(index + 1);
    });
}

function calculateTotals() {
    let totalActual = 0;
    
    $('.nominal-input').each(function() {
        const value = parseFloat($(this).val()) || 0;
        totalActual += value;
    });
    
    const selisih = nominalDisetujui - totalActual;
    
    // Update display
    $('#totalActual').text(number_format(totalActual, 0, ',', '.'));
    $('#selisihAmount').text('IDR ' + number_format(Math.abs(selisih), 0, ',', '.'));
    
    // Change color based on selisih
    const selisihElement = $('#selisihAmount');
    if (selisih > 0) {
        selisihElement.removeClass('text-danger').addClass('text-success');
        selisihElement.prepend('+ ');
    } else if (selisih < 0) {
        selisihElement.removeClass('text-success').addClass('text-danger');
        selisihElement.prepend('- ');
    } else {
        selisihElement.removeClass('text-success text-danger').addClass('text-muted');
    }
}

function validateForm() {
    let isValid = true;
    const errors = [];
    
    // Check if there's at least one detail item
    if ($('.detail-settlement-item').length === 0) {
        errors.push('Minimal harus ada satu item settlement');
        isValid = false;
    }
    
    // Check required fields
    $('.detail-settlement-item').each(function(index) {
        const keterangan = $(this).find('input[name$="[keterangan]"]').val();
        const tanggal = $(this).find('input[name$="[tanggal_transaksi]"]').val();
        const nominal = $(this).find('input[name$="[nominal]"]').val();
        
        if (!keterangan) {
            errors.push(`Item #${index + 1}: Keterangan harus diisi`);
            isValid = false;
        }
        
        if (!tanggal) {
            errors.push(`Item #${index + 1}: Tanggal transaksi harus diisi`);
            isValid = false;
        }
        
        if (!nominal || parseFloat(nominal) <= 0) {
            errors.push(`Item #${index + 1}: Nominal harus diisi dan lebih dari 0`);
            isValid = false;
        }
    });
    
    if (!isValid) {
        Swal.fire({
            icon: 'warning',
            title: 'Validasi Gagal',
            html: errors.join('<br>')
        });
    }
    
    return isValid;
}

function number_format(number, decimals, dec_point, thousands_sep) {
    number = (number + '').replace(',', '').replace(' ', '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
        dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');
    }
    return s.join(dec);
}
</script>
@endsection