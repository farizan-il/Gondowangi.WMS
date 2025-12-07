@extends('Approval-app.Layout.approver-main')

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Edit Settlement - {{ $settlement->nomor_settlement }}</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('pengajuan.index') }}">Pengajuan</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('settlement.index') }}">Settlement</a></li>
                    <li class="breadcrumb-item active">Edit Settlement</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Informasi Pengajuan</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr><td><strong>Nomor Pengajuan:</strong></td><td>{{ $settlement->pengajuan->nomor_pengajuan }}</td></tr>
                            <tr><td><strong>Kategori:</strong></td><td>{{ $settlement->pengajuan->kategoriPengajuan->nama }}</td></tr>
                            <tr><td><strong>Judul:</strong></td><td>{{ $settlement->pengajuan->judul }}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr><td><strong>Nominal Pengajuan:</strong></td><td>{{ $settlement->pengajuan->mata_uang }} {{ number_format($settlement->pengajuan->nominal_pengajuan, 0, ',', '.') }}</td></tr>
                            <tr><td><strong>Tanggal Pengajuan:</strong></td><td>{{ $settlement->pengajuan->tanggal_pengajuan->format('d/m/Y') }}</td></tr>
                            <tr><td><strong>Status Settlement:</strong></td><td><span class="badge badge-warning">{{ ucfirst($settlement->status_settlement) }}</span></td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Edit Settlement - Biaya Actual</h5>
                <small class="text-muted">Lengkapi biaya actual yang telah dikeluarkan berdasarkan item yang diajukan.</small>
            </div>
            <div class="card-body">
                <form id="settlementForm" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-4 table-responsive">
                        <table class="table table-bordered table-striped table-hover mb-0" id="actualDetailsTable">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 25%;">Keterangan Item</th>
                                    <th style="width: 15%;" class="text-end">Nominal Awal</th>
                                    <th style="width: 20%;">Nominal Actual <span class="text-danger">*</span></th>
                                    <th style="width: 20%;">Catatan Tambahan</th>
                                    <th style="width: 15%;">File Bukti (Max 5MB)</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                        <small class="text-muted mt-2 d-block">
                            <i class="feather icon-alert-triangle"></i> Setiap item wajib mengisi Nominal Actual.
                        </small>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6 ms-auto">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <table class="table table-sm mb-0">
                                        <tr>
                                            <td><strong>Nominal Pengajuan:</strong></td>
                                            <td class="text-end">{{ $settlement->pengajuan->mata_uang }} {{ number_format($settlement->pengajuan->nominal_pengajuan, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Actual:</strong></td>
                                            <td class="text-end"><strong id="totalActual">{{ $settlement->pengajuan->mata_uang }} {{ number_format($settlement->total_actual, 0, ',', '.') }}</strong></td>
                                        </tr>
                                        <tr class="table-secondary">
                                            <td><strong>Selisih:</strong></td>
                                            <td class="text-end"><strong id="selisih">{{ $settlement->pengajuan->mata_uang }} {{ number_format($settlement->selisih, 0, ',', '.') }}</strong></td>
                                        </tr>
                                    </table>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="feather icon-info"></i> 
                                            Selisih positif = penghematan, negatif = kelebihan biaya
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="catatan_settlement" class="form-label">Catatan Settlement</label>
                        <textarea class="form-control" id="catatan_settlement" name="catatan_settlement" rows="3" placeholder="Tambahkan catatan atau penjelasan terkait settlement ini...">{{ $settlement->catatan_settlement }}</textarea>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('settlement.index') }}" class="btn btn-secondary">
                            <i class="feather icon-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-success" id="submitBtn">
                            <i class="feather icon-save"></i> Update Settlement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

<!-- Template for detail item -->
<template id="detailItemTemplate">
    <div class="detail-item border rounded p-3 mb-3" style="background-color: #f8f9fa;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0 text-primary">
                <i class="feather icon-file-text"></i> Item <span class="item-number">1</span>
            </h6>
            <button type="button" class="btn btn-outline-danger btn-sm remove-detail">
                <i class="feather icon-trash-2"></i> Hapus
            </button>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Keterangan/Deskripsi <span class="text-danger">*</span></label>
                    <input type="text" class="form-control keterangan" name="details[INDEX][keterangan]" 
                           placeholder="Contoh: Biaya transportasi ke kantor cabang" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal Transaksi <span class="text-danger">*</span></label>
                    <input type="date" class="form-control tanggal_transaksi" name="details[INDEX][tanggal_transaksi]" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kategori Biaya <span class="text-danger">*</span></label>
                    <select class="form-select kategori_biaya" name="details[INDEX][kategori_biaya]" required>
                        <option value="">Pilih Kategori</option>
                        <option value="Transportasi">Transportasi</option>
                        <option value="Akomodasi">Akomodasi</option>
                        <option value="Konsumsi">Konsumsi</option>
                        <option value="Material">Material</option>
                        <option value="Jasa">Jasa</option>
                        <option value="Operasional">Operasional</option>
                        <option value="Komunikasi">Komunikasi</option>
                        <option value="Peralatan">Peralatan</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">Nominal Actual <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">{{ $settlement->pengajuan->mata_uang }}</span>
                        <input type="number" class="form-control nominal" name="details[INDEX][nominal]" 
                               min="0" step="0.01" placeholder="0" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">File Bukti</label>
                    <input type="file" class="form-control file_bukti" name="details[INDEX][file_bukti]" 
                           accept=".pdf,.jpg,.jpeg,.png">
                    <small class="text-muted">
                        <i class="feather icon-info"></i> 
                        Format: PDF, JPG, JPEG, PNG. Max: 5MB
                    </small>
                    <div class="existing-file mt-1" style="display: none;">
                        <small class="text-success">
                            <i class="feather icon-file"></i> 
                            File existing: <a href="#" target="_blank" class="existing-file-link">Lihat file</a>
                        </small>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan Tambahan</label>
                    <textarea class="form-control catatan" name="details[INDEX][catatan]" rows="2" 
                              placeholder="Catatan atau keterangan tambahan (opsional)"></textarea>
                </div>
            </div>
        </div>
    </div>
</template>
@endsection

@section('script')
<script>
    const nominalPengajuan = {{ $settlement->pengajuan->nominal_pengajuan }};
    const mataUang = '{{ $settlement->pengajuan->mata_uang }}';
    // Gunakan relasi detailPengajuan untuk mendapatkan Nominal Awal yang sudah dikalikan hari/malam
    const originalDetailsMap = @json($settlement->pengajuan->detailPengajuan->keyBy('form_field_id'));
    const existingDetails = @json($settlement->details);

    // ===============================================
    // --- HELPER UNTUK FORMAT RUPIAH ---
    // ===============================================

    function formatRupiah(number) {
        if (number === null || number === undefined || isNaN(number)) {
            return '0';
        }
        // Pastikan input adalah angka absolut dan diformat sebagai IDR
        const absoluteNum = Math.abs(parseFloat(number));
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: 0 // Tidak menampilkan desimal
        }).format(absoluteNum);
    }
    
    function unformatRupiah(text) {
        // Menghapus semua karakter selain angka dan titik (untuk desimal, jika ada)
        // Karena kita tidak menampilkan desimal, kita hanya hapus titik/koma ribuan
        return parseFloat(text.replace(/\./g, '')) || 0;
    }

    function handleRupiahInput(event) {
        let input = event.target;
        let unformattedValue = unformatRupiah(input.value.toString());
        
        // Terapkan format saat input
        input.value = formatRupiah(unformattedValue);
        
        // Perbarui total setelah input diformat
        updateTotals();
    }
    
    // ===============================================
    // --- UTILITY FUNGSI UTAMA ---
    // ===============================================

    function calculateOriginalNominal(detail) {
        // ... (Fungsi ini tetap sama untuk memastikan Nominal Awal akurat) ...
        const detailPengajuan = originalDetailsMap[detail.form_field_id];
        if (detailPengajuan && detailPengajuan.form_field.tipe_field === 'currency') {
            const originalValue = parseFloat(detailPengajuan.nilai) || 0;
            const jumlahHari = parseFloat(detailPengajuan.jumlah_hari) || 1;
            
            const labelLower = detailPengajuan.form_field.label.toLowerCase();
            const needsMultiplication = (
                labelLower.includes('hotel') || 
                labelLower.includes('penginapan') ||
                labelLower.includes('akomodasi') ||
                labelLower.includes('makan') || 
                labelLower.includes('konsumsi') ||
                labelLower.includes('meal') ||
                labelLower.includes('uang_saku') || 
                labelLower.includes('uang_harian')
            );

            if (needsMultiplication && jumlahHari > 1) {
                return originalValue * jumlahHari;
            }
            return originalValue;
        }
        return 0;
    }
    
    function loadExistingDetails() {
        const tableBody = $('#actualDetailsTable tbody');
        tableBody.empty();
        
        if (existingDetails.length === 0) {
            tableBody.append(`<tr><td colspan="6" class="text-center text-muted py-3">Tidak ada item biaya yang diajukan untuk settlement ini.</td></tr>`);
            return;
        }

        existingDetails.forEach(function(detail, index) {
            const rowId = index;
            const originalNominal = calculateOriginalNominal(detail);
            
            const fileName = detail.file_bukti ? detail.file_bukti.split('/').pop() : '-';
            const fileLink = detail.file_bukti ? `/storage/${detail.file_bukti}` : '#';
            const isFileExisting = !!detail.file_bukti;

            // ✅ Perubahan di sini: Menggunakan formatRupiah untuk menampilkan nilai nominal awal dan actual
            const formattedNominalActual = formatRupiah(detail.nominal);
            
            const newRow = `
                <tr data-row-id="${rowId}">
                    <td>${index + 1}</td>
                    <td>
                        <strong class="d-block">${detail.keterangan}</strong>
                        <input type="hidden" name="details[${rowId}][form_field_id]" value="${detail.form_field_id}">
                        <input type="hidden" name="details[${rowId}][detail_pengajuan_id]" value="${detail.detail_pengajuan_id}">
                        <input type="hidden" name="details[${rowId}][keterangan]" value="${detail.keterangan}">
                        <input type="hidden" name="details[${rowId}][kategori_biaya]" value="${detail.kategori_biaya}">
                        <small class="text-muted d-block">${detail.kategori_biaya}</small>
                        <input type="hidden" name="details[${rowId}][tanggal_transaksi]" value="${detail.tanggal_transaksi || new Date().toISOString().split('T')[0]}">
                    </td>
                    <td class="text-end text-muted">
                        ${mataUang} ${formatRupiah(originalNominal)}
                    </td>
                    <td>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">Rp</span>
                            <input type="text" 
                                   class="form-control nominal text-end" 
                                   value="${formattedNominalActual}"
                                   placeholder="0" required>
                            </div>
                    </td>
                    <td>
                        <textarea class="form-control catatan form-control-sm" 
                                  name="details[${rowId}][catatan]" 
                                  rows="1" placeholder="Catatan...">${detail.catatan || ''}</textarea>
                    </td>
                    <td>
                        <input type="file" class="form-control form-control-sm file_bukti" 
                               name="details[${rowId}][file_bukti]" accept=".pdf,.jpg,.jpeg,.png">
                        ${isFileExisting ? `
                            <small class="text-success d-block mt-1">
                                <i class="feather icon-file"></i> 
                                File: <a href="${fileLink}" target="_blank">Lihat</a>
                            </small>` : ''}
                    </td>
                </tr>
            `;
            tableBody.append(newRow);
        });
    }

    function updateTotals() {
        let total = 0;
        
        $('#actualDetailsTable .nominal').each(function() {
            // ✅ Menggunakan unformatRupiah untuk kalkulasi
            const value = unformatRupiah($(this).val());
            total += value;
        });
        
        const selisih = nominalPengajuan - total;
        
        // ✅ Menggunakan formatRupiah untuk menampilkan total
        $('#totalActual').text(mataUang + ' ' + formatRupiah(total));
        
        const selisihElement = $('#selisih');
        selisihElement.text(mataUang + ' ' + formatRupiah(selisih));
        
        const selisihParent = selisihElement.closest('td');
        selisihParent.removeClass('text-info text-danger text-muted fw-bold');
        
        if (selisih > 0) {
            selisihParent.addClass('text-info fw-bold'); 
        } else if (selisih < 0) {
            selisihParent.addClass('text-danger fw-bold'); 
        } else {
            selisihParent.addClass('text-muted'); 
        }
    }

    function validateForm() {
        // ... (Fungsi validasi tetap sama, hanya perlu memastikan nominal tidak negatif) ...
        if ($('#actualDetailsTable tbody tr').length === 0 || $('#actualDetailsTable .nominal').length === 0) {
            showAlert('Tidak ada item untuk diselesaikan', 'error');
            return false;
        }
        
        let isValid = true;
        let errorMessages = [];
        
        $('#actualDetailsTable tbody tr').each(function(index) {
            const itemNumber = index + 1;
            // ✅ Menggunakan unformatRupiah untuk validasi
            const nominal = unformatRupiah($(this).find('.nominal').val());
            const fileInput = $(this).find('.file_bukti')[0];
            
            if (nominal < 0) {
                errorMessages.push(`Item ${itemNumber}: Nominal Actual tidak boleh negatif`);
                isValid = false;
            } else if (nominal === 0) {
                 errorMessages.push(`Item ${itemNumber}: Nominal Actual harus diisi`);
                isValid = false;
            }

            if (fileInput.files[0]) {
                const fileSize = fileInput.files[0].size / 1024 / 1024; // MB
                if (fileSize > 5) {
                    errorMessages.push(`Item ${itemNumber}: Ukuran file bukti maksimal 5MB`);
                    isValid = false;
                }
            }
        });
        
        if (!isValid) {
            showAlert('Validasi gagal:\n' + errorMessages.join('\n'), 'error');
        }
        
        return isValid;
    }

    function submitSettlement() {
        if (!validateForm()) {
            return;
        }
        
        $('#submitBtn').prop('disabled', true).html('<div class="spinner-border spinner-border-sm me-2"></div>Mengupdate...');
        
        const formData = new FormData();
        
        // Collect form data dari tabel
        $('#actualDetailsTable tbody tr').each(function(index) {
            const row = $(this);
            // ✅ 1. AMBIL NILAI DARI HIDDEN INPUT
            const formFieldId = row.find('input[name$="[form_field_id]"]').val();
            const detailPengajuanId = row.find('input[name$="[detail_pengajuan_id]"]').val();
    
            // ✅ 2. AMBIL NILAI DARI INPUT/TEXTAREA
            const nominal = unformatRupiah(row.find('.nominal').val());
            const catatan = row.find('.catatan').val();
            const fileInput = row.find('.file_bukti')[0];
            
            // Hidden fields (data statis dari existingDetails)
            const rowId = row.data('row-id'); // Index dari existingDetails
            
            // 3. TAMBAHKAN SEMUA FIELD KE formData
            
            // 🚨 Field-field yang HARUS DIKIRIM KE CONTROLLER
            formData.append(`details[${index}][form_field_id]`, formFieldId);
            formData.append(`details[${index}][detail_pengajuan_id]`, detailPengajuanId); 
            
            // Data statis yang disembunyikan
            formData.append(`details[${index}][keterangan]`, existingDetails[rowId].keterangan);
            formData.append(`details[${index}][tanggal_transaksi]`, existingDetails[rowId].tanggal_transaksi || new Date().toISOString().split('T')[0]);
            formData.append(`details[${index}][kategori_biaya]`, existingDetails[rowId].kategori_biaya);
            
            // Data yang diedit/diperbarui
            formData.append(`details[${index}][nominal]`, nominal);
            formData.append(`details[${index}][catatan]`, catatan);
            
            // File
            if (fileInput.files[0]) {
                formData.append(`details[${index}][file_bukti]`, fileInput.files[0]);
            }
        });
        
        formData.append('catatan_settlement', $('#catatan_settlement').val());
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
        formData.append('_method', 'PUT'); // Penting untuk routing PUT
    
        // Submit via AJAX
        $.ajax({
            url: '{{ route("settlement.update", $settlement->id) }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showAlert('Settlement berhasil diupdate', 'success');
                    setTimeout(() => {
                        window.location.href = response.redirect;
                    }, 1500);
                } else {
                    showAlert('Error: ' + response.message, 'error');
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                let errorMsg = 'Terjadi kesalahan saat mengupdate data';
    
                if (response) {
                    if (response.message) errorMsg = 'Error: ' + response.message;
                    if (response.errors) {
                        errorMsg = 'Validasi gagal:\n';
                        Object.keys(response.errors).forEach(key => {
                            errorMsg += '- ' + response.errors[key].join('\n- ') + '\n';
                        });
                    }
                }
                showAlert(errorMsg, 'error');
            },
            complete: function() {
                $('#submitBtn').prop('disabled', false).html('<i class="feather icon-save"></i> Update Settlement');
            }
        });
    }

    function showAlert(message, type) {
        // ... (Fungsi showAlert tetap sama) ...
        const alertClass = type === 'success' ? 'alert-success' : (type === 'warning' ? 'alert-warning' : 'alert-danger');
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message.replace(/\n/g, '<br>')}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('.alert').remove();
        $('.card-body').first().prepend(alertHtml);
        $('html, body').animate({ scrollTop: 0 }, 'slow');
        
        if (type === 'success') {
            setTimeout(() => {
                $('.alert-success').fadeOut();
            }, 3000);
        }
    }


    $(document).ready(function() {
        loadExistingDetails();
        
        // ✅ Ganti Event Listener untuk menggunakan handler Rupiah
        $(document).on('input', '#actualDetailsTable .nominal', handleRupiahInput);
        
        $('#settlementForm').on('submit', function(e) {
            e.preventDefault();
            submitSettlement();
        });
        
        updateTotals();
    });

</script>
@endsection

