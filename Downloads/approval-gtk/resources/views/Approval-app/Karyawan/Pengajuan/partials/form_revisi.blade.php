{{-- File: resources/views/Approval-app/Karyawan/Pengajuan/partials/form_revisi.blade.php --}}

{{-- Header Judul & Deskripsi --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-warning border-warning">
            <label class="form-label fw-bold text-dark"><i class="feather icon-alert-triangle"></i> Catatan Revisi Sebelumnya (Dari Approver):</label>
            {{-- Ambil catatan dari progress approval terakhir yang statusnya revision --}}
            @php
                $lastRevision = $pengajuan->progressApprovals()
                    ->whereIn('status', ['revision', 'rejected'])
                    ->latest('updated_at')
                    ->first();
            @endphp
            <div class="bg-white p-2 rounded text-danger">
                {{ $lastRevision ? $lastRevision->catatan : 'Tidak ada catatan khusus.' }}
            </div>
        </div>
    </div>
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Judul Pengajuan</label>
        <input type="text" class="form-control" name="judul" value="{{ $pengajuan->judul }}" required>
    </div>
    <div class="col-md-12 mb-3">
        <label class="form-label fw-bold">Tujuan / Deskripsi</label>
        <textarea class="form-control" name="deskripsi" rows="2">{{ $pengajuan->deskripsi }}</textarea>
    </div>
</div>

{{-- ======================================================================= --}}
{{-- LOGIKA TAMPILAN: Jika Kategori Perjalanan Dinas (Sesuaikan ID/Nama) --}}
{{-- ======================================================================= --}}

{{-- Contoh: Jika ID Kategori Pengajuan = 1 (Perjalanan Dinas) --}}
{{-- Ganti $pengajuan->kategori_pengajuan_id == 1 dengan ID yang sesuai di database Anda --}}
@if($pengajuan->kategoriPengajuan->nama == 'Perjalanan Dinas' || $pengajuan->kategori_pengajuan_id == 1) 

    {{-- Helper function untuk ambil value dengan aman --}}
    @php
        function getVal($data, $key, $default = '') {
            return isset($data[$key]) ? $data[$key] : $default;
        }
    @endphp

    <div class="perjalanan-dinas-form">
        {{-- Header Form --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0 text-primary fw-bold">DATA KARYAWAN</h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Nama</label>
                        <input type="text" class="form-control" name="form_data[nama_karyawan]" value="{{ getVal($currentData, 'nama_karyawan') }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Periode</label>
                        <input type="text" class="form-control" name="form_data[periode]" value="{{ getVal($currentData, 'periode') }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Area</label>
                        <input type="text" class="form-control" name="form_data[area]" value="{{ getVal($currentData, 'area') }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- TABEL A: BIAYA YANG DIPERLUKAN --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">A. BIAYA YANG DIPERLUKAN (REVISI)</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 perjalanan-table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th rowspan="2" class="text-center align-middle" width="40">#</th>
                                <th rowspan="2" class="align-middle" style="min-width: 150px;">URAIAN</th>
                                <th colspan="3" class="text-center">PERJALANAN</th>
                                <th rowspan="2" class="text-center align-middle" width="120">TOTAL</th>
                            </tr>
                            <tr>
                                {{-- Loop Header Perjalanan 1-3 --}}
                                @for ($i = 1; $i <= 3; $i++)
                                    <th class="text-center p-2">
                                        <div class="fw-bold mb-2">Perjalanan {{ $i }}</div>
                                        <div class="d-flex flex-column gap-2">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-white border-end-0">Dari</span>
                                                <input type="date" class="form-control date-from ps-1" 
                                                    name="form_data[perjalanan{{ $i }}_tanggal_dari]" 
                                                    data-row="{{ $i }}"
                                                    value="{{ getVal($currentData, "perjalanan{$i}_tanggal_dari") }}">
                                            </div>
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-white border-end-0">Smp</span>
                                                <input type="date" class="form-control date-to ps-1" 
                                                    name="form_data[perjalanan{{ $i }}_tanggal_sampai]" 
                                                    data-row="{{ $i }}"
                                                    value="{{ getVal($currentData, "perjalanan{$i}_tanggal_sampai") }}">
                                            </div>
                                        </div>
                                        {{-- Span untuk kalkulasi hari JS --}}
                                        <div class="mt-1 small text-muted">
                                            <span id="revisi_days_{{ $i }}">0 hari</span> | 
                                            <span id="revisi_nights_{{ $i }}">0 malam</span>
                                        </div>
                                    </th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Helper Array untuk Baris Biaya --}}
                            @php
                                $rows = [
                                    ['label' => 'TRANSPORTASI', 'items' => [
                                        ['key' => 'transportasi_darat', 'label' => 'a. Darat'],
                                        ['key' => 'transportasi_udara', 'label' => 'b. Udara'], // Sesuaikan key jika database beda
                                        ['key' => 'transportasi_taxi', 'label' => 'c. Airport Tax'],
                                    ]],
                                    ['label' => 'AKOMODASI', 'items' => [
                                        ['key' => 'hotel_biaya', 'label' => 'HOTEL (per malam)', 'readonly' => true, 'default' => Auth::user()->golongan->biaya_hotel_per_hari],
                                        ['key' => 'makan_biaya', 'label' => 'MAKAN (per hari)', 'readonly' => true, 'default' => Auth::user()->golongan->biaya_makan_per_hari],
                                    ]],
                                    ['label' => 'LAINNYA', 'items' => [
                                        ['key' => 'uang_saku', 'label' => 'UANG SAKU'],
                                        ['key' => 'telephone_fax', 'label' => 'TELEPHONE & FAX'],
                                        ['key' => 'entertainment', 'label' => 'ENTERTAINMENT'],
                                        ['key' => 'dokumentasi', 'label' => 'DOKUMENTASI'],
                                        ['key' => 'lain_lain', 'label' => 'LAIN-LAIN'],
                                    ]]
                                ];
                                $no = 1;
                            @endphp

                            @foreach($rows as $section)
                                @foreach($section['items'] as $index => $item)
                                    <tr>
                                        @if($index == 0) <td class="text-center">{{ $no++ }}</td> @else <td></td> @endif
                                        
                                        <td>
                                            @if($index == 0) <strong>{{ $section['label'] }}</strong><br> @endif
                                            <span class="ps-2">{{ $item['label'] }}</span>
                                        </td>

                                        {{-- Loop Columns 1-3 --}}
                                        @for ($c = 1; $c <= 3; $c++)
                                            @php 
                                                $suffix = ($c == 1) ? '' : '_' . $c; 
                                                $keyName = $item['key'] . ($item['key'] == 'transportasi_udara' && $c == 1 ? '_1' : $suffix);
                                                // Fix khusus untuk transportasi udara yang mungkin formatnya _1, _2, _3 di DB
                                                if($item['key'] == 'transportasi_udara' && $c == 1) { $keyName = 'transportasi_udara_1'; }
                                            @endphp
                                            <td>
                                                <input type="number" 
                                                    class="form-control form-control-sm currency-input revisi-biaya-input {{ isset($item['readonly']) ? ($item['key'] == 'hotel_biaya' ? 'hotel-rate' : 'makan-rate') : '' }}"
                                                    name="form_data[{{ $keyName }}]"
                                                    data-row="{{ $item['key'] }}" data-col="{{ $c }}"
                                                    value="{{ getVal($currentData, $keyName, 0) }}"
                                                    step="0.01" placeholder="0"
                                                    {{ isset($item['readonly']) ? 'readonly' : '' }}>
                                            </td>
                                        @endfor
                                        
                                        {{-- Total Row --}}
                                        <td class="text-end fw-bold bg-light total-row-cell" id="revisi_total_{{ $item['key'] }}">
                                            0
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach

                            {{-- Grand Total --}}
                            <tr class="table-primary fw-bold">
                                <td colspan="2" class="text-center">TOTAL ESTIMASI</td>
                                <td class="text-end" id="revisi_col_total_1">0</td>
                                <td class="text-end" id="revisi_col_total_2">0</td>
                                <td class="text-end" id="revisi_col_total_3">0</td>
                                <td class="text-end fs-6" id="revisi_grand_total">0</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- SECTION B: TUJUAN (Sudah ada di atas deskripsi, tapi jika form butuh field khusus) --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0 text-primary fw-bold">B. TUJUAN PERJALANAN</h6>
            </div>
            <div class="card-body">
                <textarea class="form-control" name="form_data[tujuan_perjalanan]" rows="2" placeholder="Detail tujuan...">{{ getVal($currentData, 'tujuan_perjalanan') }}</textarea>
            </div>
        </div>

        {{-- SECTION DETAIL PERJALANAN (Sales Rate dll) --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0 text-primary fw-bold">DETAIL PERJALANAN</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0 text-center">
                        <thead class="table-light">
                            <tr>
                                <th>NO</th>
                                <th>DAERAH</th>
                                <th>SALES RATE (Rata/Bln)</th>
                                <th>ESTIMASI SALES</th>
                                <th>JML OUTLET</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 1; $i <= 3; $i++)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" 
                                            name="form_data[perjalanan{{ $i }}_daerah]" 
                                            value="{{ getVal($currentData, "perjalanan{$i}_daerah") }}">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm" 
                                            name="form_data[perjalanan{{ $i }}_sales_rate]" 
                                            value="{{ getVal($currentData, "perjalanan{$i}_sales_rate") }}">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm" 
                                            name="form_data[perjalanan{{ $i }}_estimasi]" 
                                            value="{{ getVal($currentData, "perjalanan{$i}_estimasi") }}">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm" 
                                            name="form_data[perjalanan{{ $i }}_outlet]" 
                                            value="{{ getVal($currentData, "perjalanan{$i}_outlet") }}">
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@else
    {{-- FORM GENERIC (Untuk kategori lain selain Perjalanan Dinas) --}}
    <div class="row">
        @foreach($formFields as $field)
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">{{ $field->label }}</label>
                @if($field->tipe_field == 'textarea')
                    <textarea class="form-control" name="form_data[{{ $field->nama_field }}]">{{ $currentData[$field->nama_field] ?? '' }}</textarea>
                @elseif($field->tipe_field == 'date')
                    <input type="date" class="form-control" name="form_data[{{ $field->nama_field }}]" value="{{ $currentData[$field->nama_field] ?? '' }}">
                @else
                    <input type="text" class="form-control" name="form_data[{{ $field->nama_field }}]" value="{{ $currentData[$field->nama_field] ?? '' }}">
                @endif
            </div>
        @endforeach
    </div>
@endif

{{-- File Pendukung --}}
<div class="card mt-3">
    <div class="card-body bg-light">
        <label class="form-label fw-bold">Upload File Pendukung Tambahan (Opsional)</label>
        <input type="file" class="form-control" name="file_pendukung[]" multiple>
        <small class="text-muted d-block mt-1">File yang sudah ada:</small>
        @if(count($pengajuan->file_pendukung) > 0)
            <ul class="list-unstyled mb-0">
                @foreach($pengajuan->file_pendukung as $file)
                    <li><a href="{{ asset('storage/'.$file) }}" target="_blank"><i class="feather icon-paperclip"></i> Lihat File</a></li>
                @endforeach
            </ul>
        @else
            <span class="text-muted">- Tidak ada file -</span>
        @endif
    </div>
</div>

{{-- Catatan Revisi Baru --}}
<div class="form-group mt-4">
    <label class="form-label fw-bold text-primary">Catatan Perbaikan untuk Approver</label>
    <textarea class="form-control border-primary" name="catatan_requester" rows="3" placeholder="Jelaskan apa yang Anda perbaiki..." required>{{ $pengajuan->catatan_requester }}</textarea>
</div>

{{-- SCRIPT KHUSUS REVISI --}}
{{-- Script ini akan dieksekusi saat modal load via AJAX --}}
<script>
    $(document).ready(function() {
        // Fungsi format currency
        const fmt = (num) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num);

        // Fungsi hitung hari
        function calcDays(d1, d2) {
            if(!d1 || !d2) return 0;
            const start = new Date(d1); const end = new Date(d2);
            if(end < start) return 0;
            return Math.floor((end - start) / (1000 * 60 * 60 * 24)) + 1;
        }
        function calcNights(d1, d2) {
            if(!d1 || !d2) return 0;
            const start = new Date(d1); const end = new Date(d2);
            if(end <= start) return 0;
            return Math.floor((end - start) / (1000 * 60 * 60 * 24));
        }

        // Recalculate Logic
        function recalculateRevisi() {
            let grandTotal = 0;
            let colTotals = [0, 0, 0]; // Index 0 = col 1

            // Iterate columns 1 to 3
            for(let c=1; c<=3; c++) {
                const dateFrom = $(`input[name="form_data[perjalanan${c}_tanggal_dari]"]`).val();
                const dateTo = $(`input[name="form_data[perjalanan${c}_tanggal_sampai]"]`).val();
                
                const days = calcDays(dateFrom, dateTo);
                const nights = calcNights(dateFrom, dateTo);

                // Update span text
                $(`#revisi_days_${c}`).text(days + ' hari');
                $(`#revisi_nights_${c}`).text(nights + ' malam');

                // Update Hotel & Makan Values based on days/nights
                // Selector spesifik untuk kolom ini
                const hotelInput = $(`.hotel-rate[data-col="${c}"]`);
                const makanInput = $(`.makan-rate[data-col="${c}"]`);
                
                // Rate standar (bisa ambil dari data attribute jika perlu dinamis dari PHP)
                const hotelRate = {{ Auth::user()->golongan->biaya_hotel_per_hari }};
                const makanRate = {{ Auth::user()->golongan->biaya_makan_per_hari }};

                // Jika tanggal valid, set nilai, jika tidak 0.
                // NOTE: Input type number valuenya angka murni, tidak perlu hitung total di input, tapi input menyimpan RATE.
                // Namun, logika di create form Anda tampaknya input menyimpan RATE, lalu total dihitung di JS?
                // Mari ikuti logika create form: Input menampilkan RATE, tapi kalkulasi total baris memperhitungkan hari.
                
                // Tapi tunggu, di create form Anda: 
                // hotelInput.value = hotelPerHari; (Hanya menampilkan rate)
                // calculateRowTotal -> total += rate * nights; (Kalkulasi sebenarnya)
                
                if(days > 0) {
                    makanInput.val(makanRate); // Set Rate
                    if(nights > 0) hotelInput.val(hotelRate); else hotelInput.val(0);
                } else {
                    makanInput.val(0);
                    hotelInput.val(0);
                }
            }

            // Kalkulasi Total per Baris dan Grand Total
            // List key row yang ada
            const rowKeys = ['transportasi_darat', 'transportasi_udara', 'transportasi_taxi', 'hotel_biaya', 'makan_biaya', 'uang_saku', 'telephone_fax', 'entertainment', 'dokumentasi', 'lain_lain'];

            rowKeys.forEach(key => {
                let rowTotal = 0;
                for(let c=1; c<=3; c++) {
                    // Cari input
                    // Nama field agak tricky, ada yang pakai _1 ada yang tidak. Kita pakai data-row selector saja lebih aman
                    const input = $(`.revisi-biaya-input[data-row="${key}"][data-col="${c}"]`);
                    let val = parseFloat(input.val()) || 0;

                    // Khusus Hotel & Makan dikali hari/malam
                    if(key === 'hotel_biaya') {
                        const d1 = $(`input[name="form_data[perjalanan${c}_tanggal_dari]"]`).val();
                        const d2 = $(`input[name="form_data[perjalanan${c}_tanggal_sampai]"]`).val();
                        val = val * calcNights(d1, d2);
                    }
                    else if(key === 'makan_biaya') {
                        const d1 = $(`input[name="form_data[perjalanan${c}_tanggal_dari]"]`).val();
                        const d2 = $(`input[name="form_data[perjalanan${c}_tanggal_sampai]"]`).val();
                        val = val * calcDays(d1, d2);
                    }

                    rowTotal += val;
                    colTotals[c-1] += val;
                }
                grandTotal += rowTotal;
                $(`#revisi_total_${key}`).text(fmt(rowTotal));
            });

            // Update Column Totals & Grand Total Display
            $(`#revisi_col_total_1`).text(fmt(colTotals[0]));
            $(`#revisi_col_total_2`).text(fmt(colTotals[1]));
            $(`#revisi_col_total_3`).text(fmt(colTotals[2]));
            $(`#revisi_grand_total`).text(fmt(grandTotal));
        }

        // Trigger Events
        $('.revisi-biaya-input, .date-from, .date-to').on('input change', function() {
            recalculateRevisi();
        });

        // Init Calculation saat modal terbuka
        recalculateRevisi();
    });
</script>