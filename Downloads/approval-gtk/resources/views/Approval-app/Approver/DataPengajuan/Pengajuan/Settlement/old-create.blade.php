@extends('Approval-app.Layout.main')

@section('head')
<style>
    /* Card Modern Styles */
    .card-modern {
      border: none;
      border-radius: .75rem;
      overflow: hidden;
      position: relative;
      transform: translateY(20px);
      opacity: 0;
      transition: all 0.6s ease-out;
      box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.1);
    }
    .card-modern.show {
      transform: translateY(0);
      opacity: 1;
    }
    .card-modern .card-body {
      padding: 2rem 1.5rem;
    }
    .card-modern .icon-bg {
      width: 4rem;
      height: 4rem;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      margin: 0 auto 1rem;
    }
    .card-modern .card-title {
      font-size: 1.25rem;
      margin-bottom: .5rem;
    }
    .card-modern .card-text {
      font-size: .9rem;
      margin-bottom: 1rem;
      color: #6c757d;
    }
    .card-modern:hover {
      transform: translateY(-5px) scale(1.02);
      box-shadow: 0 1rem 1.5rem rgba(0,0,0,0.2);
    }
    /* Icon backgrounds */
    .card-reim .icon-bg { background: rgba(40,167,69,0.1); color: #28a745; }
    .card-pur .icon-bg { background: rgba(0,123,255,0.1); color: #007bff; }
    .card-settle .icon-bg { background: rgba(255,193,7,0.1); color: #ffc107; }
    .card-perdin .icon-bg { background: rgba(220,53,69,0.1); color: #dc3545; }
    .card-asset .icon-bg { background: rgba(102,16,242,0.1); color: #6610f2; }
    .card-leave .icon-bg { background: rgba(23,162,184,0.1); color: #17a2b8; }
    .card-training .icon-bg { background: rgba(255,193,7,0.1); color: #ffc107; }
    .card-meeting .icon-bg { background: rgba(108,117,125,0.1); color: #6c757d; }
  </style>
  
<style>
    .pengajuan-info {
        background: #f8f9fa;
        border-left: 4px solid #007bff;
        padding: 1rem;
        margin-bottom: 1.5rem;
    }
    .detail-item {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .detail-item .remove-item {
        position: absolute;
        top: 10px;
        right: 10px;
    }
    .total-summary {
        background: #e3f2fd;
        border-radius: 0.375rem;
        padding: 1rem;
        margin-top: 1rem;
    }
</style>
@endsection

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="page-header-title">
                    <h5 class="m-b-10">Buat Settlement</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('settlement.index') }}">Settlement</a></li>
                    <li class="breadcrumb-item active">Buat Settlement</li>
                </ul>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('settlement.index') }}" class="btn btn-outline-secondary">
                    <i class="feather icon-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row justify-content-center">
    <div class="col-md-10">
        <!-- Informasi Pengajuan -->
        <div class="pengajuan-info">
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="feather icon-file-text"></i> Informasi Pengajuan</h6>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td width="40%">No. Pengajuan:</td>
                            <td><strong>{{ $pengajuan->nomor_pengajuan }}</strong></td>
                        </tr>
                        <tr>
                            <td>Kategori:</td>
                            <td>{{ $pengajuan->kategoriPengajuan->nama }}</td>
                        </tr>
                        <tr>
                            <td>Judul:</td>
                            <td>{{ $pengajuan->judul }}</td>
                        </tr>
                        <tr>
                            <td>Requester:</td>
                            <td>{{ $pengajuan->requester->nama }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6><i class="feather icon-dollar-sign"></i> Informasi Budget</h6>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td width="40%">Nominal Pengajuan:</td>
                            <td><strong class="text-primary">{{ $pengajuan->mata_uang }} {{ number_format($pengajuan->nominal_pengajuan, 0, ',', '.') }}</strong></td>
                        </tr>
                        <tr>
                            <td>Tanggal Pengajuan:</td>
                            <td>{{ $pengajuan->tanggal_pengajuan->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td>Status:</td>
                            <td><span class="badge badge-success">{{ ucfirst($pengajuan->status_pengajuan) }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Form Settlement -->
        <div class="card">
            <div class="card-header">
                <h5><i class="feather icon-edit"></i> Form Settlement - Laporan Biaya Aktual</h5>
            </div>
            <div class="card-body">
                <form id="settlementForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="pengajuan_id" value="{{ $pengajuan->id }}">
                    
                    <!-- Catatan Settlement -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="form-label">Catatan Settlement</label>
                            <textarea name="catatan_settlement" class="form-control" rows="3" 
                                      placeholder="Catatan atau keterangan tambahan untuk settlement ini..."></textarea>
                        </div>
                    </div>

                    <!-- File Bukti Utama -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <label class="form-label">File Bukti Pendukung <small class="text-muted">(Opsional)</small></label>
                            <input type="file" name="file_bukti[]" class="form-control" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <small class="form-text text-muted">
                                Upload file pendukung seperti kwitansi, invoice, atau dokumen lainnya. Max 10MB per file.
                            </small>
                        </div>
                    </div>

                    <hr>

                    <!-- Detail Items -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6><i class="feather icon-list"></i> Detail Biaya Aktual</h6>
                        <button type="button" id="addDetailItem" class="btn btn-sm btn-primary">
                            <i class="feather icon-plus"></i> Tambah Item
                        </button>
                    </div>

                    <div id="detailItemsContainer">
                        <!-- Item pertama akan ditambahkan otomatis oleh JavaScript -->
                    </div>

                    <!-- Summary -->
                    <div class="total-summary">
                        <div class="row">
                            <div class="col-md-8">
                                <h6>Summary</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted">Nominal Pengajuan:</small><br>
                                        <strong id="nominalPengajuan">{{ $pengajuan->mata_uang }} {{ number_format($pengajuan->nominal_pengajuan, 0, ',', '.') }}</strong>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">Total Actual:</small><br>
                                        <strong id="totalActual" class="text-info">{{ $pengajuan->mata_uang }} 0</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <small class="text-muted">Selisih:</small><br>
                                <h5 id="selisih" class="mb-0">{{ $pengajuan->mata_uang }} {{ number_format($pengajuan->nominal_pengajuan, 0, ',', '.') }}</h5>
                                <small id="selisihStatus" class="text-success">Sisa Budget</small>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-end mt-4">
                        <button type="button" class="btn btn-outline-secondary me-2" onclick="window.history.back()">
                            <i class="feather icon-x"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="feather icon-send"></i> Kirim Settlement untuk Approval
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(document).ready(function() {
    let itemCounter = 0;
    const nominalPengajuan = {{ $pengajuan->nominal_pengajuan }};
    const mataUang = "{{ $pengajuan->mata_uang }}";
    
    // Tambah item pertama otomatis
    addDetailItem();
    
    // Event handler untuk tombol tambah item
    $('#addDetailItem').click(function() {
        addDetailItem();
    });
    
    // Event handler untuk submit form
    $('#settlementForm').submit(function(e) {
        e.preventDefault();
        
        if (!validateForm()) {
            return false;
        }
        
        // Disable submit button
        $('#submitBtn').prop('disabled', true).html('<i class="spinner-border spinner-border-sm me-1"></i>Processing...');
        
        // Prepare form data
        let formData = new FormData(this);
        
        // Collect detail items
        let detailItems = [];
        $('.detail-item').each(function(index) {
            let item = {
                keterangan: $(this).find('input[name="keterangan[]"]').val(),
                nominal: parseFloat($(this).find('input[name="nominal[]"]').val()) || 0,
                tanggal_transaksi: $(this).find('input[name="tanggal_transaksi[]"]').val(),
                kategori_biaya: $(this).find('input[name="kategori_biaya[]"]').val(),
                catatan: $(this).find('textarea[name="catatan_item[]"]').val()
            };
            
            // Handle file bukti per item
            let fileInput = $(this).find('input[name="file_bukti_item[]"]')[0];
            if (fileInput && fileInput.files[0]) {
                item.file_bukti = fileInput.files[0];
                formData.append(`detail_items[${index}][file_bukti]`, fileInput.files[0]);
            }
            
            // Append other detail item data
            Object.keys(item).forEach(key => {
                if (key !== 'file_bukti') {
                    formData.append(`detail_items[${index}][${key}]`, item[key]);
                }
            });
        });
        
        $.ajax({
            url: "{{ route('settlement.store') }}",
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
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        window.location.href = "{{ route('settlement.index') }}";
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: response.message
                    });
                    $('#submitBtn').prop('disabled', false).html('<i class="feather icon-send"></i> Kirim Settlement untuk Approval');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                    
                    if (xhr.responseJSON.errors) {
                        let errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMessage += ':\n' + errors.join('\n');
                    }
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: errorMessage
                });
                
                $('#submitBtn').prop('disabled', false).html('<i class="feather icon-send"></i> Kirim Settlement untuk Approval');
            }
        });
    });
    
    function addDetailItem() {
        itemCounter++;
        const itemHtml = `
            <div class="detail-item position-relative" data-item="${itemCounter}">
                ${itemCounter > 1 ? `
                    <button type="button" class="btn btn-sm btn-outline-danger remove-item" onclick="removeDetailItem(${itemCounter})">
                        <i class="feather icon-x"></i>
                    </button>
                ` : ''}
                
                <h6 class="mb-3">Item ${itemCounter}</h6>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Keterangan Biaya <span class="text-danger">*</span></label>
                        <input type="text" name="keterangan[]" class="form-control" required 
                               placeholder="Contoh: Transport, Konsumsi, dll">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Nominal <span class="text-danger">*</span></label>
                        <input type="number" name="nominal[]" class="form-control nominal-input" required 
                               placeholder="0" min="0" step="0.01">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_transaksi[]" class="form-control" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kategori Biaya</label>
                        <input type="text" name="kategori_biaya[]" class="form-control" 
                               placeholder="Contoh: Operasional, Marketing, dll">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">File Bukti</label>
                        <input type="file" name="file_bukti_item[]" class="form-control" 
                               accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                        <small class="form-text text-muted">Max 5MB per file</small>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Catatan Item</label>
                        <textarea name="catatan_item[]" class="form-control" rows="2" 
                                  placeholder="Catatan tambahan untuk item ini..."></textarea>
                    </div>
                </div>
            </div>
        `;
        
        $('#detailItemsContainer').append(itemHtml);
        
        // Add event listener untuk update total
        $(`[data-item="${itemCounter}"] .nominal-input`).on('input', updateTotal);
        
        // Set tanggal hari ini sebagai default
        $(`[data-item="${itemCounter}"] input[name="tanggal_transaksi[]"]`).val(new Date().toISOString().split('T')[0]);
        
        updateTotal();
    }
    
    function updateTotal() {
        let totalActual = 0;
        $('.nominal-input').each(function() {
            const value = parseFloat($(this).val()) || 0;
            totalActual += value;
        });
        
        const selisih = nominalPengajuan - totalActual;
        
        // Update display
        $('#totalActual').text(mataUang + ' ' + totalActual.toLocaleString('id-ID'));
        $('#selisih').text(mataUang + ' ' + selisih.toLocaleString('id-ID'));
        
        // Update status dan warna selisih
        if (selisih > 0) {
            $('#selisih').removeClass('text-danger text-warning').addClass('text-success');
            $('#selisihStatus').text('Sisa Budget').removeClass('text-danger text-warning').addClass('text-success');
        } else if (selisih < 0) {
            $('#selisih').removeClass('text-success text-warning').addClass('text-danger');
            $('#selisihStatus').text('Over Budget').removeClass('text-success text-warning').addClass('text-danger');
        } else {
            $('#selisih').removeClass('text-success text-danger').addClass('text-warning');
            $('#selisihStatus').text('Pas Budget').removeClass('text-success text-danger').addClass('text-warning');
        }
    }
    
    // Global function untuk remove item
    window.removeDetailItem = function(itemId) {
        $(`[data-item="${itemId}"]`).remove();
        updateTotal();
        
        // Re-number items
        $('.detail-item').each(function(index) {
            $(this).find('h6').text('Item ' + (index + 1));
        });
    };
    
    function validateForm() {
        let isValid = true;
        let errorMessages = [];
        
        // Check if there's at least one detail item
        if ($('.detail-item').length === 0) {
            errorMessages.push('Minimal harus ada satu detail biaya');
            isValid = false;
        }
        
        // Validate each detail item
        $('.detail-item').each(function(index) {
            const keterangan = $(this).find('input[name="keterangan[]"]').val().trim();
            const nominal = parseFloat($(this).find('input[name="nominal[]"]').val()) || 0;
            const tanggal = $(this).find('input[name="tanggal_transaksi[]"]').val();
            
            if (!keterangan) {
                errorMessages.push(`Item ${index + 1}: Keterangan biaya harus diisi`);
                isValid = false;
            }
            
            if (nominal <= 0) {
                errorMessages.push(`Item ${index + 1}: Nominal harus lebih dari 0`);
                isValid = false;
            }
            
            if (!tanggal) {
                errorMessages.push(`Item ${index + 1}: Tanggal transaksi harus diisi`);
                isValid = false;
            }
        });
        
        if (!isValid) {
            Swal.fire({
                icon: 'warning',
                title: 'Validasi Gagal',
                html: errorMessages.join('<br>')
            });
        }
        
        return isValid;
    }
});
</script>
@endsection