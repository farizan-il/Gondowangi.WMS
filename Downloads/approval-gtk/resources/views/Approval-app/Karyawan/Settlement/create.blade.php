@extends('Approval-app.Layout.approver-main')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <div class="page-header-title">
                    <h5 class="m-b-10">Buat Settlement - {{ $pengajuan->nomor_pengajuan }}</h5>
                </div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('pengajuan.index') }}">Pengajuan</a></li>
                    <li class="breadcrumb-item active">Buat Settlement</li>
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
                            <tr><td><strong>Nama Pengaju:</strong></td><td>{{ $pengajuan->requester->nama }}</td></tr>
                            <tr><td><strong>Nomor Pengajuan:</strong></td><td>{{ $pengajuan->nomor_pengajuan }}</td></tr>
                            <tr><td><strong>Kategori:</strong></td><td>{{ $pengajuan->kategoriPengajuan->nama }}</td></tr>
                            <!--<tr><td><strong>Total Pengajuan:</strong></td><td>{{ number_format($pengajuan->nominal_pengajuan, 0, ',', '.') }}</td></tr>-->
                            <!--<tr><td><strong>Tanggal Pengajuan:</strong></td><td>{{ $pengajuan->tanggal_pengajuan->format('d/m/Y') }}</td></tr>-->
                            
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr><td><strong>Total Pengajuan:</strong></td><td>{{ number_format($pengajuan->nominal_pengajuan, 0, ',', '.') }}</td></tr>
                            <tr><td><strong>Tanggal Pengajuan:</strong></td><td>{{ $pengajuan->tanggal_pengajuan->format('d/m/Y') }}</td></tr>
                            
                            <tr><td><strong>Status:</strong></td><td><span class="badge badge-success text-dark"><strong>Proses Settlement</strong></span></td></tr>
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
                <h5>Settlement - {{ $pengajuan->nomor_pengajuan }}</h5>
                <small class="text-muted">Masukkan biaya actual untuk setiap item pengajuan dan upload bukti pendukung</small>
            </div>
            <div class="card-body">
                <form class="mt-4" id="settlementForm" enctype="multipart/form-data">
                    @csrf
                    
                    <!-- Detail Settlement Table -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th width="25%">Label / Detail</th>
                                    <th width="15%">Biaya Original</th>
                                    <th width="15%">Biaya Actual <span class="text-danger">*</span></th>
                                    <th width="15%">Selisih</th>
                                    <th width="20%">Upload Bukti</th>
                                    <th width="10%">Catatan</th>
                                </tr>
                            </thead>
                            <tbody id="settlementTableBody">
                                @php 
                                    $totalOriginal = 0; 
                                    $currencyIndex = 0;
                                @endphp
                                
                                @if(isset($calculatedDetails))
                                    @foreach($calculatedDetails['details'] as $calculatedDetail)
                                        @php
                                            $originalValue = $calculatedDetail['calculated_value']; // Sudah dikali jumlah hari
                                            $totalOriginal += $originalValue;
                                            
                                            // Cek apakah ada data settlement yang sudah ada
                                            $existingSettlement = null;
                                            if($pengajuan->settlement && $pengajuan->settlement->details) {
                                                $existingSettlement = $pengajuan->settlement->details->where('form_field_id', $calculatedDetail['form_field_id'])->first();
                                            }
                                        @endphp
                                        
                                        <tr>
                                            <td>
                                                <strong>{{ $calculatedDetail['label'] }}</strong>
                                                @if($calculatedDetail['needs_multiplication'])
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $pengajuan->mata_uang }} {{ number_format($calculatedDetail['original_per_unit'], 0, ',', '.') }} × {{ $calculatedDetail['jumlah_hari'] }} hari
                                                    </small>
                                                @else
                                                    <br>
                                                    <small class="text-muted">Biaya sekali jalan</small>
                                                @endif
                                                <input type="hidden" name="details[{{ $currencyIndex }}][form_field_id]" value="{{ $calculatedDetail['form_field_id'] }}">
                                                <input type="hidden" name="details[{{ $currencyIndex }}][label]" value="{{ $calculatedDetail['label'] }}">
                                                <input type="hidden" name="details[{{ $currencyIndex }}][detail_pengajuan_id]" value="{{ $calculatedDetail['detail_id'] }}">
                                            </td>
                                            <td>
                                                <strong class="text-primary">{{ $pengajuan->mata_uang }} {{ number_format($originalValue, 0, ',', '.') }}</strong>
                                                <input type="hidden" class="original-amount" name="details[{{ $currencyIndex }}][original_amount]" value="{{ $originalValue }}">
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="text" class="form-control actual-amount rupiah-input" 
                                                        value="{{ $existingSettlement ? number_format($existingSettlement->nominal, 0, ',', '.') : '' }}"
                                                        placeholder="0" required>
                                                    
                                                    <input type="hidden" class="actual-amount-raw" 
                                                        name="details[{{ $currencyIndex }}][actual_amount]" 
                                                        value="{{ $existingSettlement ? $existingSettlement->nominal : '0' }}">
                                                </div>
                                            </td>
                                            <td>
                                                <strong class="selisih-amount fw-bold">
                                                    @if($existingSettlement)
                                                        @php $selisih = $originalValue - $existingSettlement->nominal; @endphp
                                                        {{ $pengajuan->mata_uang }} {{ number_format(abs($selisih), 0, ',', '.') }}
                                                    @else
                                                        {{ $pengajuan->mata_uang }} {{ number_format($originalValue, 0, ',', '.') }}
                                                    @endif
                                                </strong>
                                            </td>
                                            <td>
                                                <input type="file" class="form-control form-control-sm file-bukti" 
                                                       name="details[{{ $currencyIndex }}][file_bukti]" 
                                                       accept=".pdf,.jpg,.jpeg,.png">
                                                @if($existingSettlement && $existingSettlement->file_bukti)
                                                    <small class="text-success d-block mt-1">
                                                        <i class="feather icon-file"></i> File sudah diupload
                                                        <a href="{{ asset('storage/' . $existingSettlement->file_bukti) }}" target="_blank" class="ms-1">Lihat</a>
                                                    </small>
                                                @endif
                                                <small class="text-muted">PDF, JPG, PNG (Max: 5MB)</small>
                                            </td>
                                            <td>
                                                <textarea class="form-control form-control-sm" 
                                                          name="details[{{ $currencyIndex }}][catatan]" 
                                                          rows="2" placeholder="Catatan...">{{ $existingSettlement ? $existingSettlement->catatan : '' }}</textarea>
                                            </td>
                                        </tr>
                                        @php $currencyIndex++; @endphp
                                    @endforeach
                                @endif
                            </tbody>
                            <tfoot class="table-secondary">
                                <tr>
                                    <th>TOTAL</th>
                                    <th><span id="totalOriginal">{{ $pengajuan->mata_uang }} {{ number_format($totalOriginal, 0, ',', '.') }}</span></th>
                                    <th>
                                        <span id="totalActual">
                                            @if($pengajuan->settlement)
                                                {{ $pengajuan->mata_uang }} {{ number_format($pengajuan->settlement->total_actual, 0, ',', '.') }}
                                            @else
                                                {{ $pengajuan->mata_uang }} 0
                                            @endif
                                        </span>
                                    </th>
                                    <th>-</th>
                                    <th>
                                        <span id="totalSelisih" class="fw-bold">
                                            @if($pengajuan->settlement)
                                                {{ $pengajuan->mata_uang }} {{ number_format($pengajuan->settlement->selisih, 0, ',', '.') }}
                                            @else
                                                {{ $pengajuan->mata_uang }} {{ number_format($totalOriginal, 0, ',', '.') }}
                                            @endif
                                        </span>
                                    </th>
                                    <th>-</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
        
                    <!-- Catatan Settlement -->
                    <div class="mb-3">
                        <label for="catatan_settlement" class="form-label">Catatan Settlement</label>
                        <textarea class="form-control" id="catatan_settlement" name="catatan_settlement" rows="3" 
                            placeholder="Tambahkan catatan atau penjelasan terkait settlement ini...">{{ $pengajuan->settlement ? $pengajuan->settlement->catatan_settlement : '' }}</textarea>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('pengajuan.index') }}" class="btn btn-secondary" style="
                            border-radius: 12px;
                            padding: 12px 24px;
                            font-weight: 600;
                            transition: all 0.3s ease;
                        ">
                            <i class="feather icon-arrow-left"></i> Kembali
                        </a>
                        <button type="button" class="btn btn-success" id="submitBtn" data-bs-toggle="modal" data-bs-target="#submitConfirmModal" style="
                            background: linear-gradient(135deg, #28a745, #20c997);
                            border: none;
                            border-radius: 12px;
                            padding: 12px 24px;
                            font-weight: 600;
                            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
                            transition: all 0.3s ease;
                        " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(40, 167, 69, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(40, 167, 69, 0.3)'">
                            <i class="feather icon-save"></i> 
                            Simpan Settlement
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

<div class="modal fade" id="submitConfirmModal" tabindex="-1" aria-labelledby="submitConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
        ">
            <!-- Header -->
            <div class="modal-header" style="
                border: none;
                border-radius: 20px 20px 0 0;
                padding: 25px 30px;
            ">
                <div class="d-flex align-items-center">
                    <div style="
                        width: 50px;
                        height: 50px;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin-right: 15px;
                        backdrop-filter: blur(10px);
                    ">
                        <i class="feather icon-check-circle" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="submitConfirmModalLabel" style="
                            font-weight: 700;
                            font-size: 22px;
                        ">Konfirmasi Penyimpanan</h5>
                        <small style="opacity: 0.9; font-size: 14px;">Pastikan data settlement sudah benar</small>
                    </div>
                </div>
            </div>
            
            <!-- Body -->
            <div class="modal-body" style="
                padding: 30px;
                background: white;
            ">
                <div class="text-center mb-4">
                    <div style="
                        width: 80px;
                        height: 80px;
                        background: linear-gradient(135deg, #28a745, #20c997);
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin: 0 auto 20px;
                        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
                    ">
                        <i class="feather icon-save" style="font-size: 36px; color: white;"></i>
                    </div>
                    <h6 style="
                        color: #495057;
                        font-weight: 600;
                        font-size: 18px;
                        margin-bottom: 10px;
                    ">Apakah Anda yakin ingin menyimpan settlement ini?</h6>
                    <p style="
                        color: #6c757d;
                        font-size: 14px;
                        line-height: 1.6;
                        margin-bottom: 0;
                    ">Data yang sudah disimpan tidak dapat diubah kembali. Pastikan semua informasi sudah benar.</p>
                </div>
                
                <!-- Warning untuk file yang belum diupload -->
                <div id="fileWarning" style="
                    display: none;
                    background: linear-gradient(135deg, #fff3cd, #ffeaa7);
                    border: 1px solid #ffc107;
                    border-radius: 12px;
                    padding: 15px;
                    margin-bottom: 20px;
                ">
                    <div class="d-flex align-items-center">
                        <i class="feather icon-alert-triangle" style="
                            color: #856404;
                            font-size: 20px;
                            margin-right: 10px;
                        "></i>
                        <div>
                            <strong style="color: #856404; font-size: 14px;">Peringatan!</strong>
                            <p style="color: #856404; margin: 0; font-size: 13px;">
                                Anda belum mengupload bukti pendukung. Apakah Anda yakin ingin melanjutkan?
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="modal-footer" style="
                padding: 20px 30px 30px;
                border: none;
                background: #f8f9fa;
                border-radius: 0 0 20px 20px;
            ">
                <button type="button" class="btn" data-bs-dismiss="modal" style="
                    background: #e9ecef;
                    color: #495057;
                    border: none;
                    border-radius: 12px;
                    padding: 12px 24px;
                    font-weight: 600;
                    font-size: 14px;
                    transition: all 0.3s ease;
                " onmouseover="this.style.background='#dee2e6'" onmouseout="this.style.background='#e9ecef'">
                    <i class="feather icon-x" style="margin-right: 8px;"></i>
                    Batal
                </button>
                <button type="button" class="btn" id="confirmSubmitBtn" style="
                    background: linear-gradient(135deg, #28a745, #20c997);
                    color: white;
                    border: none;
                    border-radius: 12px;
                    padding: 12px 24px;
                    font-weight: 600;
                    font-size: 14px;
                    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
                    transition: all 0.3s ease;
                " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(40, 167, 69, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(40, 167, 69, 0.3)'">
                    <i class="feather icon-check" style="margin-right: 8px;"></i>
                    Ya, Simpan Settlement
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('settlementForm');
        // Target input text baru untuk format (tampilan)
        const rupiahInputs = document.querySelectorAll('.rupiah-input'); 
        // Target input hidden untuk nilai numerik murni (yang dikirim ke DB)
        const actualAmountRawInputs = document.querySelectorAll('.actual-amount-raw');
        // Ambil mata uang dari Laravel/Blade
        const mataUang = '{{ $pengajuan->mata_uang }}' || 'Rp'; 

        // --- Money Formatting Functions ---
        
        /**
         * Menghapus format (titik) dan mengembalikan nilai integer murni.
         * @param {string} formattedNum - Nilai input text (e.g., "1.000.000")
         * @returns {number} Nilai integer murni (e.g., 1000000)
         */
        function unformatNumber(formattedNum) {
            // Hilangkan semua karakter selain digit
            let rawValue = formattedNum.replace(/[^0-9]/g, '');
            // Menggunakan parseInt untuk memastikan integer (sesuai kebutuhan database)
            return parseInt(rawValue) || 0; 
        }

        /**
         * Memformat angka menjadi string dengan pemisah ribuan (titik).
         * @param {number} num - Nilai numerik murni (e.g., 1000000)
         * @returns {string} Nilai terformat (e.g., "1.000.000")
         */
        function formatNumber(num) {
            let numStr = Math.round(num).toString(); 
            // Tambahkan pemisah ribuan (titik)
            return numStr.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
    
        // Function to calculate selisih for a row
        function calculateRowSelisih(row) {
            const originalInput = row.querySelector('.original-amount');
            const actualRawInput = row.querySelector('.actual-amount-raw'); 
            const selisihSpan = row.querySelector('.selisih-amount');
            
            if (originalInput && actualRawInput && selisihSpan) {
                const originalValue = parseFloat(originalInput.value) || 0;
                const actualValue = parseFloat(actualRawInput.value) || 0; 
                const selisih = originalValue - actualValue;
                
                selisihSpan.textContent = `${mataUang} ${formatNumber(Math.abs(selisih))}`;
                
                // Add color coding
                if (selisih > 0) {
                    selisihSpan.className = 'selisih-amount fw-bold text-primary'; // Sisa lebih (Hemat)
                } else if (selisih < 0) {
                    selisihSpan.className = 'selisih-amount fw-bold text-danger'; // Kurang bayar (Over budget)
                } else {
                    selisihSpan.className = 'selisih-amount fw-bold text-muted'; // Pas
                }
            }
        }
    
        // Function to calculate total values
        function calculateTotals() {
            let totalActual = 0;
            let totalOriginal = 0;
            
            const tbody = document.getElementById('settlementTableBody');
            const rows = tbody.querySelectorAll('tr');
            
            rows.forEach((row) => {
                const originalInput = row.querySelector('input[name*="[original_amount]"]');
                const actualRawInput = row.querySelector('.actual-amount-raw'); 
                
                if (originalInput && actualRawInput) {
                    const originalValue = parseFloat(originalInput.value) || 0;
                    const actualValue = parseFloat(actualRawInput.value) || 0; 
                    
                    totalOriginal += originalValue;
                    totalActual += actualValue;
                    
                    // Hitung selisih baris saat menghitung total (memastikan tampilan baris terupdate)
                    calculateRowSelisih(row);
                }
            });
            
            const totalSelisih = totalOriginal - totalActual;
            
            // Update footer totals
            const totalOriginalSpan = document.getElementById('totalOriginal');
            const totalActualSpan = document.getElementById('totalActual');
            const totalSelisihSpan = document.getElementById('totalSelisih');
            
            if (totalOriginalSpan) {
                totalOriginalSpan.textContent = `${mataUang} ${formatNumber(totalOriginal)}`;
            }
            
            if (totalActualSpan) {
                totalActualSpan.textContent = `${mataUang} ${formatNumber(totalActual)}`;
            }
            
            if (totalSelisihSpan) {
                totalSelisihSpan.textContent = `${mataUang} ${formatNumber(Math.abs(totalSelisih))}`;
                
                if (totalSelisih > 0) {
                    totalSelisihSpan.className = 'fw-bold text-primary';
                } else if (totalSelisih < 0) {
                    totalSelisihSpan.className = 'fw-bold text-danger';
                } else {
                    totalSelisihSpan.className = 'fw-bold text-muted';
                }
            }
        }
        
        // ----------------------------------------------------------------------
        // 1. Event Listeners untuk Real-time Formatting & Perhitungan
        // ----------------------------------------------------------------------

        rupiahInputs.forEach(input => {
            // Event listener 'input' untuk format real-time
            input.addEventListener('input', function(e) {
                // 1. Ambil nilai yang diketik (raw/belum terformat)
                let rawValue = unformatNumber(this.value); 
                
                // 2. Format nilai untuk ditampilkan di input text
                this.value = formatNumber(rawValue);

                // 3. Simpan nilai murni (numerik) ke hidden input
                const row = this.closest('tr');
                const actualRawInput = row.querySelector('.actual-amount-raw');
                
                if (actualRawInput) {
                    actualRawInput.value = rawValue;
                }

                // 4. Hitung ulang total
                calculateTotals();
            });
            
            // Initial check/calculation pada load
            // Jika ada nilai lama, pastikan terhitung saat load
            calculateRowSelisih(input.closest('tr'));
        });

        // ----------------------------------------------------------------------
        // 2. Event Listeners untuk File Input
        // ----------------------------------------------------------------------
        
        const fileInputs = document.querySelectorAll('.file-bukti');
        fileInputs.forEach(input => {
            input.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    if (file.size > 5 * 1024 * 1024) {
                        alert('Ukuran file tidak boleh lebih dari 5MB');
                        this.value = '';
                        return;
                    }
                    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
                    if (!allowedTypes.includes(file.type)) {
                        alert('Tipe file tidak didukung. Gunakan PDF, JPG, JPEG, atau PNG');
                        this.value = '';
                        return;
                    }
                    
                    const fileName = file.name;
                    // Cari elemen yang menampilkan info file lama atau buat baru
                    let fileInfo = this.parentNode.querySelector('.text-success'); 
                    if (fileInfo) {
                        fileInfo.innerHTML = `<i class="feather icon-file"></i> ${fileName}`;
                        // Hapus link "Lihat" jika ada
                        const linkLihat = fileInfo.querySelector('a');
                        if(linkLihat) linkLihat.remove();
                    } else {
                         // Buat elemen info file baru
                        fileInfo = document.createElement('small');
                        fileInfo.className = 'file-info text-success d-block mt-1';
                        fileInfo.innerHTML = `<i class="feather icon-file"></i> ${fileName}`;
                        this.parentNode.appendChild(fileInfo);
                    }
                }
            });
        });

        // ----------------------------------------------------------------------
        // 3. Initial Calculation and Form Submission Logic
        // ----------------------------------------------------------------------

        // Initial total calculation (Hanya dipanggil sekali setelah DOM siap)
        setTimeout(calculateTotals, 100); 

        // Form submission logic
        if (form) {
            const modal = document.getElementById('submitConfirmModal');
            const fileWarning = document.getElementById('fileWarning');
            
            modal.addEventListener('show.bs.modal', function (e) {
                // Cek apakah ada file baru atau file lama yang sudah diupload
                const hasNewFile = Array.from(fileInputs).some(input => input.files && input.files.length > 0);
                const hasExistingFile = form.querySelectorAll('.text-success a').length > 0;
                
                if (!hasNewFile && !hasExistingFile) {
                    fileWarning.style.display = 'block';
                } else {
                    fileWarning.style.display = 'none';
                }
            });
            
            document.getElementById('confirmSubmitBtn').addEventListener('click', function() {
                const modalInstance = bootstrap.Modal.getInstance(document.getElementById('submitConfirmModal'));
                modalInstance.hide();
                submitForm();
            });
            
            function submitForm() {
                const submitBtn = document.getElementById('submitBtn');
                const confirmBtn = document.getElementById('confirmSubmitBtn');
                const originalText = submitBtn.innerHTML;
                
                let isValid = true;
                let hasEmptyActual = false;
                
                // VALIDASI: Memastikan actual amount terisi/valid (menggunakan hidden input)
                actualAmountRawInputs.forEach(input => {
                    const row = input.closest('tr');
                    const originalInput = row.querySelector('.original-amount');
                    const actualDisplayInput = row.querySelector('.rupiah-input');
                    const originalValue = parseFloat(originalInput.value) || 0;
                    
                    if (originalValue > 0) {
                        const actualValue = parseFloat(input.value);
                        // Cek apakah nilai numerik tidak valid ATAU input display kosong
                        if (isNaN(actualValue) || actualValue < 0 || actualDisplayInput.value.trim() === '') {
                            hasEmptyActual = true;
                            actualDisplayInput.classList.add('is-invalid');
                        } else {
                            actualDisplayInput.classList.remove('is-invalid');
                        }
                    } else {
                        actualDisplayInput.classList.remove('is-invalid');
                    }
                });
                
                if (hasEmptyActual) {
                    showAlert('Mohon isi semua field Biaya Actual dengan nilai yang valid (≥ 0) untuk setiap item pengajuan.', 'error');
                    return;
                }
                
                // Validate required fields (Catatan Settlement)
                const catatanSettlementInput = form.querySelector('#catatan_settlement');
                if (catatanSettlementInput && catatanSettlementInput.hasAttribute('required') && catatanSettlementInput.value.trim() === '') {
                    isValid = false;
                    catatanSettlementInput.classList.add('is-invalid');
                } else if(catatanSettlementInput) {
                    catatanSettlementInput.classList.remove('is-invalid');
                }

                if (!isValid) {
                    showAlert('Mohon lengkapi semua field yang wajib diisi', 'error');
                    return;
                }
                
                // Disable buttons and show loading
                submitBtn.disabled = true;
                confirmBtn.disabled = true;
                submitBtn.style.pointerEvents = 'none';
                confirmBtn.style.pointerEvents = 'none';
                submitBtn.innerHTML = '<i class="feather icon-loader"></i> Menyimpan...';
                confirmBtn.innerHTML = '<i class="feather icon-loader"></i> Menyimpan...';
                
                const formData = new FormData(form);
                const pengajuanId = window.location.pathname.split('/').pop();
                const baseUrl = window.location.origin;
                const url = `${baseUrl}/settlement/create/${pengajuanId}`;
                
                fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            try {
                                const errorData = JSON.parse(text);
                                throw new Error(errorData.message || errorData.error || `HTTP ${response.status}: ${response.statusText}`);
                            } catch (parseError) {
                                throw new Error(`HTTP ${response.status}: ${text || response.statusText}`);
                            }
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showAlert(data.message || 'Settlement berhasil disimpan', 'success');
                        setTimeout(() => {
                            if (data.redirect) {
                                window.location.href = data.redirect;
                            } else {
                                window.location.reload();
                            }
                        }, 1500);
                    } else {
                        throw new Error(data.error || 'Terjadi kesalahan');
                    }
                })
                .catch(error => {
                    console.error('Error details:', error);
                    showAlert('Error: ' + error.message, 'error');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    confirmBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    confirmBtn.innerHTML = '<i class="feather icon-check" style="margin-right: 8px;"></i>Ya, Simpan Settlement';
                    submitBtn.style.pointerEvents = 'auto';
                    confirmBtn.style.pointerEvents = 'auto';
                });
            }
            
            // Modern alert function
            function showAlert(message, type = 'info') {
                const alertColors = {
                    success: '#28a745',
                    error: '#dc3545',
                    warning: '#ffc107',
                    info: '#17a2b8'
                };
                
                const alertDiv = document.createElement('div');
                alertDiv.innerHTML = `
                    <div style="
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        z-index: 9999;
                        background: ${alertColors[type]};
                        color: white;
                        padding: 15px 20px;
                        border-radius: 12px;
                        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
                        font-weight: 600;
                        max-width: 400px;
                        animation: slideIn 0.3s ease-out;
                    ">
                        ${message}
                    </div>
                `;
                
                document.body.appendChild(alertDiv);
                
                setTimeout(() => {
                    alertDiv.remove();
                }, 3000);
            }
        }
    });
</script>
@endsection