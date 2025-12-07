<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pengajuan - {{ $pengajuan->nomor_pengajuan }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; }
            .card { break-inside: avoid; }
            .table { font-size: 11px; }
        }
        
        body { 
            font-family: Arial, sans-serif; 
            font-size: 12px; 
            line-height: 1.4;
            margin: 20px;
        }
        
        .company-header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #127e3f;
            padding-bottom: 15px;
        }
        
        .print-controls {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 1000;
            background: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .table { 
            font-size: 11px; 
            margin-bottom: 20px;
        }
        
        .table th, .table td {
            padding: 8px;
            vertical-align: middle;
        }
        
        .perjalanan-table th, .perjalanan-table td {
            border: 1px solid #000 !important;
            
            font-size: 10px;
            padding: 5px;
        }
        
        .badge-success { background: #28a745; color: white; padding: 3px 8px; border-radius: 3px; }
        .badge-warning { background: #ffc107; color: black; padding: 3px 8px; border-radius: 3px; }
        .badge-danger { background: #dc3545; color: white; padding: 3px 8px; border-radius: 3px; }
        .badge-info { background: #17a2b8; color: white; padding: 3px 8px; border-radius: 3px; }
        
        h4 { 
            color: #007bff; 
            border-bottom: 1px solid #dee2e6; 
            padding-bottom: 5px; 
            margin-top: 25px; 
            margin-bottom: 15px; 
        }
        
        .total-row { background-color: #e3f2fd !important; font-weight: bold; }
        .grand-total { background-color: #1976d2 !important; color: white !important; }
    </style>
</head>
@php
    // Helper functions untuk formatting
    function formatCurrencyPHP($value) {
        if (!$value || $value === '' || $value === '0' || $value === 0) return '-';
        $numValue = is_string($value) ? floatval(preg_replace('/[^\d.-]/', '', $value)) : floatval($value);
        return is_nan($numValue) || $numValue == 0 ? '-' : 'Rp ' . number_format($numValue, 0, ',', '.');
    }
    
    function formatNumberPHP($value) {
        if (!$value || $value === '' || $value === '0' || $value === 0) return '0';
        $numValue = is_string($value) ? floatval(preg_replace('/[^\d.-]/', '', $value)) : floatval($value);
        return is_nan($numValue) ? '0' : number_format($numValue, 0, ',', '.');
    }

    // Akses data yang sudah dihitung di controller
    $fieldData = $pengajuan->field_data ?? [];
    $biayaData = $pengajuan->biaya_perjalanan_data ?? [];
    
    // Debug di view
    if (empty($fieldData)) {
        \Log::warning('Field data kosong di view');
    } else {
        \Log::info('Field data di view: ' . count($fieldData) . ' items');
    }
    
    if (empty($biayaData)) {
        \Log::warning('Biaya data kosong di view');
    } else {
        \Log::info('Biaya data di view, grand total: ' . ($biayaData['grand_total'] ?? 'NOT SET'));
    }
    
    
    function formatTanggalRange($tanggal) {
        if (!$tanggal || $tanggal === '-') return '-';
        
        // Jika ada range " - "
        if (strpos($tanggal, ' - ') !== false) {
            $parts = explode(' - ', $tanggal); // langsung pisahkan awal & akhir
            if (count($parts) === 2) {
                try {
                    $start = \Carbon\Carbon::parse(trim($parts[0]))->format('d/m/y');
                    $end   = \Carbon\Carbon::parse(trim($parts[1]))->format('d/m/y');
                    return $start . ' - ' . $end;
                } catch (\Exception $e) {
                    return $tanggal; // fallback kalau gagal parse
                }
            }
        }
    
        // Kalau single date
        try {
            return \Carbon\Carbon::parse($tanggal)->format('d/m/y');
        } catch (\Exception $e) {
            return $tanggal; // fallback
        }
    }

@endphp

<body>
    <!-- Print Controls -->
    <div class="print-controls no-print">
        <button onclick="window.print()" class="btn btn-primary">💾 Simpan sebagai PDF</button>
        <a href="/BuatPengajuan" class="btn btn-secondary">✖️ Tutup</a>
    </div>

    <!-- Header -->
    <div class="company-header">
        <h2 style="margin: 0; color: #127e3f;"><strong>PT. GONDOWANGI TRADISIONAL KOSMETIKA</strong></h2>
    </div>

    <!-- Informasi Umum -->
    <h6 style="color: #127e3f;">INFORMASI PENGAJUAN</h6>
    <table class="table">
        <tr>
            <td width="30%"><strong>Nomor Pengajuan</strong></td>
            <td>{{ $pengajuan->nomor_pengajuan }}</td>
        </tr>
        <tr>
            <td><strong>Kategori</strong></td>
            <td>{{ $pengajuan->kategoriPengajuan->nama ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Nominal Pengajuan</strong></td>
            <td><strong>Rp {{ number_format($pengajuan->nominal_pengajuan, 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td><strong>Tanggal Pengajuan</strong></td>
            <td>{{ $pengajuan->tanggal_pengajuan->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td><strong>Status</strong></td>
            <td>
                @php
                    $statusClass = match($pengajuan->status_pengajuan) {
                        'proses' => 'badge-warning',
                        'approved' => 'badge-success',
                        'rejected' => 'badge-danger',
                        'completed' => 'badge-success',
                        default => 'badge-warning'
                    };
                @endphp
                <span class="badge {{ $statusClass }}">{{ ucfirst($pengajuan->status_pengajuan) }}</span>
            </td>
        </tr>
        <tr>
            <td><strong>Requester</strong></td>
            <td>{{ $pengajuan->requester->nama ?? '-' }}</td>
        </tr>
    </table>

    @if($pengajuan->deskripsi)
    <h6 style="margin: 0; color: #127e3f;">DESKRIPSI</h6>
    <p style="padding: 10px; background: #f8f9fa; border-left: 4px solid #007bff;">
        {{ $pengajuan->deskripsi }}
    </p>
    @endif

    <h6 style="margin: 0; color: #127e3f;">DETAIL PENGAJUAN</h6>
    
    <!-- Debug Info (hapus di production) -->
    <!--@if(config('app.debug'))-->
    <!--<div style="background: #fff3cd; padding: 10px; margin: 10px 0; border: 1px solid #ffeaa7;">-->
    <!--    <small>-->
    <!--        <strong>Debug Info:</strong><br>-->
    <!--        Field Data Count: {{ count($fieldData) }}<br>-->
    <!--        Biaya Data Available: {{ !empty($biayaData) ? 'Yes' : 'No' }}<br>-->
    <!--        Grand Total: {{ $biayaData['grand_total'] ?? 'NOT SET' }}<br>-->
    <!--        Detail Pengajuan Count: {{ $pengajuan->detailPengajuan->count() }}-->
    <!--    </small>-->
    <!--</div>-->
    <!--@endif-->
    
    <!-- Info Karyawan -->
    <table class="table">
        <tr>
            <td width="20%"><strong>Nama</strong></td>
            <td width="30%">{{ $fieldData['nama_karyawan'] ?? ($fieldData['nama'] ?? ($pengajuan->requester->nama ?? '-')) }}</td>
            <td width="20%"><strong>Periode</strong></td>
            <td width="30%">{{ $fieldData['periode'] ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Area</strong></td>
            <td colspan="3">{{ $fieldData['area'] ?? '-' }}</td>
        </tr>
    </table>

    

    <h6 style="margin: 0; color: #127e3f;">A. BIAYA YANG DIPERLUKAN</h6>
    <table class="table perjalanan-table">
        <thead>
            <tr>
                <th rowspan="3" style="width: 5%; vertical-align: middle; text-align: center;">#</th>
                <th rowspan="3" style="width: 35%; vertical-align: middle; text-align: center;">URAIAN</th>
                <th colspan="3" class="text-center">PERJALANAN</th>
                <th rowspan="3" style="width: 15%; vertical-align: middle; text-align: center;">TOTAL</th>
            </tr>
            <tr>
                <th class="text-center" style="width: 15%;">Perjalanan 1</th>
                <th class="text-center" style="width: 15%;">Perjalanan 2</th>
                <th class="text-center" style="width: 15%;">Perjalanan 3</th>
            </tr>
            <tr>
                <th class="text-center" style="width: 15%;">
                    {{ formatTanggalRange($biayaData['detail_perjalanan'][1]['tanggal'] ?? '-') }}
                </th>
                <th class="text-center" style="width: 15%;">
                    {{ formatTanggalRange($biayaData['detail_perjalanan'][2]['tanggal'] ?? '-') }}
                </th>
                <th class="text-center" style="width: 15%;">
                    {{ formatTanggalRange($biayaData['detail_perjalanan'][3]['tanggal'] ?? '-') }}
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
                <td style="padding-left: 20px;">a. Darat</td>
                <td class="text-center">{{ formatCurrencyPHP($biayaData['transportasi_darat'][0] ?? 0) }}</td>
                <td class="text-center">{{ formatCurrencyPHP($biayaData['transportasi_darat'][1] ?? 0) }}</td>
                <td class="text-center">{{ formatCurrencyPHP($biayaData['transportasi_darat'][2] ?? 0) }}</td>
                <td class="text-center total-row">{{ formatCurrencyPHP($biayaData['total_transportasi_darat'] ?? 0) }}</td>
            </tr>
            <tr>
                <td></td>
                <td style="padding-left: 20px;">b. Airport Tax</td>
                <td class="text-center">{{ formatCurrencyPHP($biayaData['transportasi_taxi'][0] ?? 0) }}</td>
                <td class="text-center">{{ formatCurrencyPHP($biayaData['transportasi_taxi'][1] ?? 0) }}</td>
                <td class="text-center">{{ formatCurrencyPHP($biayaData['transportasi_taxi'][2] ?? 0) }}</td>
                <td class="text-center total-row">{{ formatCurrencyPHP($biayaData['total_transportasi_taxi'] ?? 0) }}</td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td><strong>HOTEL</strong></td>
                <td class="text-center">
                    {{ formatCurrencyPHP($biayaData['total_hotel_per_perjalanan'][0] ?? 0) }}
                    @if(($biayaData['hari_per_perjalanan'][0] ?? 1) > 1)
                        <br><small>({{ max(0, $biayaData['hari_per_perjalanan'][0] - 1) }} malam)</small>
                    @endif
                </td>
                <td class="text-center">
                    {{ formatCurrencyPHP($biayaData['total_hotel_per_perjalanan'][1] ?? 0) }}
                    @if(($biayaData['hari_per_perjalanan'][1] ?? 1) > 1)
                        <br><small>({{ max(0, $biayaData['hari_per_perjalanan'][1] - 1) }} malam)</small>
                    @endif
                </td>
                <td class="text-center">
                    {{ formatCurrencyPHP($biayaData['total_hotel_per_perjalanan'][2] ?? 0) }}
                    @if(($biayaData['hari_per_perjalanan'][2] ?? 1) > 1)
                        <br><small>({{ max(0, $biayaData['hari_per_perjalanan'][2] - 1) }} malam)</small>
                    @endif
                </td>

                <td class="text-center total-row">{{ formatCurrencyPHP($biayaData['total_hotel'] ?? 0) }}</td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td><strong>MAKAN</strong></td>
                <td class="text-center">
                    {{ formatCurrencyPHP(($biayaData['makan_biaya'][0] ?? 0) * ($biayaData['hari_per_perjalanan'][0] ?? 1)) }}
                    @if(($biayaData['hari_per_perjalanan'][0] ?? 1) > 1)
                        <br><small>({{ $biayaData['hari_per_perjalanan'][0] }} hari)</small>
                    @endif
                </td>
                <td class="text-center">
                    {{ formatCurrencyPHP(($biayaData['makan_biaya'][1] ?? 0) * ($biayaData['hari_per_perjalanan'][1] ?? 1)) }}
                    @if(($biayaData['hari_per_perjalanan'][1] ?? 1) > 1)
                        <br><small>({{ $biayaData['hari_per_perjalanan'][1] }} hari)</small>
                    @endif
                </td>
                <td class="text-center">
                    {{ formatCurrencyPHP(($biayaData['makan_biaya'][2] ?? 0) * ($biayaData['hari_per_perjalanan'][2] ?? 1)) }}
                    @if(($biayaData['hari_per_perjalanan'][2] ?? 1) > 1)
                        <br><small>({{ $biayaData['hari_per_perjalanan'][2] }} hari)</small>
                    @endif
                </td>
                <td class="text-center total-row">{{ formatCurrencyPHP($biayaData['total_makan'] ?? 0) }}</td>
            </tr>
            <tr>
                <td class="text-center">4</td>
                <td><strong>UANG SAKU</strong></td>
                <td class="text-center">{{ formatCurrencyPHP($biayaData['uang_saku'][0] ?? 0) }}</td>
                <td class="text-center">{{ formatCurrencyPHP($biayaData['uang_saku'][1] ?? 0) }}</td>
                <td class="text-center">{{ formatCurrencyPHP($biayaData['uang_saku'][2] ?? 0) }}</td>
                <td class="text-center total-row">{{ formatCurrencyPHP($biayaData['total_uang_saku'] ?? 0) }}</td>
            </tr>
            <tr>
                <td class="text-center">5</td>
                <td><strong>TELEPHONE & FAX</strong></td>
                <td class="text-center">{{ formatCurrencyPHP($biayaData['telephone_fax'][0] ?? 0) }}</td>
                <td class="text-center">{{ formatCurrencyPHP($biayaData['telephone_fax'][1] ?? 0) }}</td>
                <td class="text-center">{{ formatCurrencyPHP($biayaData['telephone_fax'][2] ?? 0) }}</td>
                <td class="text-center total-row">{{ formatCurrencyPHP($biayaData['total_telephone_fax'] ?? 0) }}</td>
            </tr>
            <tr>
                <td class="text-center">6</td>
                <td><strong>ENTERTAINMENT</strong></td>
                <td class="text-center">{{ formatCurrencyPHP($biayaData['entertainment'][0] ?? 0) }}</td>
                <td class="text-center">{{ formatCurrencyPHP($biayaData['entertainment'][1] ?? 0) }}</td>
                <td class="text-center">{{ formatCurrencyPHP($biayaData['entertainment'][2] ?? 0) }}</td>
                <td class="text-center total-row">{{ formatCurrencyPHP($biayaData['total_entertainment'] ?? 0) }}</td>
            </tr>
            <tr>
                <td class="text-center">7</td>
                <td><strong>DOKUMENTASI</strong></td>
                <td class="text-center">{{ formatCurrencyPHP($biayaData['dokumentasi'][0] ?? 0) }}</td>
                <td class="text-center">{{ formatCurrencyPHP($biayaData['dokumentasi'][1] ?? 0) }}</td>
                <td class="text-center">{{ formatCurrencyPHP($biayaData['dokumentasi'][2] ?? 0) }}</td>
                <td class="text-center total-row">{{ formatCurrencyPHP($biayaData['total_dokumentasi'] ?? 0) }}</td>
            </tr>
            <tr>
                <td class="text-center">8</td>
                <td><strong>LAIN-LAIN</strong></td>
                <td class="text-center">{{ formatCurrencyPHP($biayaData['lain_lain'][0] ?? 0) }}</td>
                <td class="text-center">{{ formatCurrencyPHP($biayaData['lain_lain'][1] ?? 0) }}</td>
                <td class="text-center">{{ formatCurrencyPHP($biayaData['lain_lain'][2] ?? 0) }}</td>
                <td class="text-center total-row">{{ formatCurrencyPHP($biayaData['total_lain_lain'] ?? 0) }}</td>
            </tr>
            <tr class="grand-total">
                <td colspan="2" class="text-center"><strong>TOTAL</strong></td>
                <td class="text-center"><strong>{{ formatCurrencyPHP($biayaData['total_perjalanan'][0] ?? 0) }}</strong></td>
                <td class="text-center"><strong>{{ formatCurrencyPHP($biayaData['total_perjalanan'][1] ?? 0) }}</strong></td>
                <td class="text-center"><strong>{{ formatCurrencyPHP($biayaData['total_perjalanan'][2] ?? 0) }}</strong></td>
                <td class="text-center"><strong>{{ formatCurrencyPHP($biayaData['grand_total'] ?? 0) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <h6 style="margin: 0; color: #127e3f;">B. TUJUAN PERJALANAN</h6>
    <p style="padding: 10px; background: #f8f9fa; border: 1px solid #ddd;">
        {{ $fieldData['tujuan_perjalanan'] ?? ($fieldData['tujuan'] ?? 'Tidak ada keterangan tujuan') }}
    </p>

    <!-- Detail Perjalanan -->
    <h6 style="color: #127e3f; margin-top: 290px;">DETAIL PERJALANAN</h6>
    <table class="table perjalanan-table">
        <thead>
            <tr>
                <th style="width: 8%;" class="text-center">NO</th>
                <th style="width: 20%;" class="text-center">TANGGAL</th>
                <th style="width: 20%;" class="text-center">DAERAH</th>
                <th style="width: 17%;" class="text-center">SALES RATE - RATA PER BULAN</th>
                <th style="width: 17%;" class="text-center">ESTIMASI SALES</th>
                <th style="width: 18%;" class="text-center">JUMLAH OUTLET YG AKAN DIKUNJUNGI</th>
            </tr>
        </thead>
        <tbody>
            @for($i = 1; $i <= 3; $i++)
            <tr>
                <td class="text-center">{{ $i }}</td>
                <td class="text-center">{{ $biayaData['detail_perjalanan'][$i]['tanggal'] ?? '-' }}</td>
                <td class="text-center">{{ $biayaData['detail_perjalanan'][$i]['daerah'] ?? '-' }}</td>
                <td class="text-center">{{ formatCurrencyPHP($biayaData['detail_perjalanan'][$i]['sales_rate'] ?? 0) }}</td>
                <td class="text-center">{{ formatCurrencyPHP($biayaData['detail_perjalanan'][$i]['estimasi'] ?? 0) }}</td>
                <td class="text-center">{{ formatNumberPHP($biayaData['detail_perjalanan'][$i]['outlet'] ?? 0) }}</td>
            </tr>
            @endfor
            <tr class="grand-total">
                <td colspan="3" class="text-center"><strong>TOTAL</strong></td>
                <td class="text-center"><strong>{{ formatCurrencyPHP($biayaData['total_sales_rate'] ?? 0) }}</strong></td>
                <td class="text-center"><strong>{{ formatCurrencyPHP($biayaData['total_estimasi'] ?? 0) }}</strong></td>
                <td class="text-center"><strong>{{ formatNumberPHP($biayaData['total_outlet'] ?? 0) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <!-- Jika masih tidak ada data, tampilkan semua detail fields yang tersedia -->
    @if(empty($biayaData) || ($biayaData['grand_total'] ?? 0) == 0)
    <div style="background: #f8d7da; padding: 15px; margin: 15px 0; border: 1px solid #f5c6cb; border-radius: 5px;">
        <h6 style="color: #721c24;">Debug: Data Detail Pengajuan</h6>
        @if(!empty($pengajuan->detail_fields))
            <p><strong>Available Detail Fields:</strong></p>
            @foreach($pengajuan->detail_fields as $field)
                <div style="margin: 5px 0; padding: 5px; background: #fff;">
                    <strong>{{ $field['label'] ?? $field['name'] }}:</strong> {{ $field['value'] }} 
                    @if($field['jumlah_hari'] > 0)
                        ({{ $field['jumlah_hari'] }} hari)
                    @endif
                </div>
            @endforeach
        @else
            <p>Tidak ada detail fields yang ditemukan</p>
        @endif

        @if(!empty($fieldData))
            <p style="margin-top: 15px;"><strong>Raw Field Data:</strong></p>
            @foreach($fieldData as $key => $value)
                <div style="font-size: 12px;">{{ $key }}: {{ $value }}</div>
            @endforeach
        @endif
    </div>
    @endif
    
    <h5 style="margin: 0; color: #127e3f;">RIWAYAT APPROVAL</h5>
    <table class="table">
        <thead>
            <tr>
                <th style="width: 8%;">Step</th>
                <th style="width: 25%;">Approver</th>
                <th style="width: 20%;">Jabatan</th>
                <th style="width: 12%;">Status</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 20%;">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pengajuan->progressApprovals as $history)
            <tr>
                <td class="text-center">{{ $history['urutan'] }}</td>
                <td>{{ $history['approver_name'] }}</td>
                <td>{{ $history['approver_jabatan'] }}</td>
                <td>
                    @php
                        $statusClass = match($history['status']) {
                            'approved' => 'badge-success',
                            'rejected' => 'badge-danger',
                            'proses' => 'badge-warning',
                            'pending' => 'badge-warning',
                            'completed' => 'badge-success',
                            default => 'badge-secondary'
                        };
                    @endphp
                    <span class="badge {{ $statusClass }}">{{ ucfirst($history['status']) }}</span>
                </td>
                <td>{{ $history['tanggal_approval'] ? \Carbon\Carbon::parse($history['tanggal_approval'])->format('d/m/Y H:i') : '-' }}</td>
                <td>{{ $history['catatan'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted">Tidak ada data approval</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    
    <div style="margin-top: 40px; text-align: center; font-size: 10px; color: #666; border-top: 1px solid #eee; padding-top: 15px;">
        <p><strong>PT. GONDOWANGI TRADISIONAL KOSMETIKA</strong></p>
        <p>Dokumen digenerate pada: {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <script>
        // Auto open print dialog ketika halaman dimuat
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 1000);
        }
        
        // Handle setelah print
        window.addEventListener('afterprint', function() {
            // Optional: close window setelah print
            // window.close();
        });
    </script>
</body>
</html>