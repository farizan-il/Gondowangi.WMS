<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Revisi Finance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 8px 8px;
            border: 1px solid #e9ecef;
        }
        .alert {
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
            background-color: #fff3cd;
            color: #856404;
        }
        .info-box {
            background: white;
            padding: 20px;
            border-radius: 6px;
            margin: 15px 0;
            border-left: 4px solid #007bff;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .table th, .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        .table th {
            background: #0e6a39;
            color: white;
            font-weight: 600;
        }
        .table tr:last-child td {
            border-bottom: none;
        }
        .nominal-summary {
            background: #e8f4fd;
            padding: 20px;
            border-radius: 6px;
            margin: 20px 0;
            border: 1px solid #bee5eb;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 10px 5px;
            text-align: center;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding: 20px;
            color: #6c757d;
            font-size: 14px;
            border-top: 1px solid #e9ecef;
        }
        .change-up { color: #dc3545; font-weight: bold; }
        .change-down { color: #28a745; font-weight: bold; }
        .change-neutral { color: #6c757d; }
    </style>
</head>
<body>
    <div class="header">
        <h2>🔧 Notifikasi Revisi Finance</h2>
        <p>Pengajuan Anda telah direvisi oleh Tim Finance</p>
    </div>
    
    <div class="content">
        <div class="alert">
            <strong>⚠️ PEMBERITAHUAN PENTING:</strong><br>
            Tim Finance telah melakukan revisi pada detail pengajuan Anda untuk penyesuaian nilai.
        </div>

        <div class="info-box">
            <h3 style="margin-top: 0;">📋 Informasi Pengajuan</h3>
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="border: none; padding: 5px 0;"><strong>Nomor Pengajuan:</strong></td>
                    <td style="border: none; padding: 5px 0;">{{ $pengajuan->nomor_pengajuan }}</td>
                </tr>
                <tr>
                    <td style="border: none; padding: 5px 0;"><strong>Judul:</strong></td>
                    <td style="border: none; padding: 5px 0;">{{ $pengajuan->judul }}</td>
                </tr>
                <tr>
                    <td style="border: none; padding: 5px 0;"><strong>Tanggal Revisi:</strong></td>
                    <td style="border: none; padding: 5px 0;">{{ now()->format('d/m/Y H:i') }} WIB</td>
                </tr>
                <tr>
                    <td style="border: none; padding: 5px 0;"><strong>Direvisi oleh:</strong></td>
                    <td style="border: none; padding: 5px 0;">{{ $finance_user }} (Finance)</td>
                </tr>
            </table>
        </div>

        <h3>📝 Detail Perubahan</h3>
        <table class="table">
            <thead style="background-color: #0e6a39;">
                <tr>
                    <th>Item Detail</th>
                    <th>Nilai Sebelum</th>
                    <th>Nilai Setelah</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($interventions as $intervention)
                <tr>
                    <td><strong>{{ $intervention['field_name'] }}</strong></td>
                    <td>
                        @if(is_numeric($intervention['nilai_awal']))
                            Rp ({{ number_format($intervention['nilai_awal'], 0, ',', '.') }})
                        @else
                            {{ $intervention['nilai_awal'] }}
                        @endif
                    </td>
                    <td>
                        <strong>
                            @if(is_numeric($intervention['nilai_final']))
                                Rp ({{ number_format($intervention['nilai_final'], 0, ',', '.') }})
                            @else
                                {{ $intervention['nilai_final'] }}
                            @endif
                        </strong>
                    </td>
                    <td>
                        @if($intervention['nilai_awal'] != $intervention['nilai_final'])
                            <span style="color: #ffc107;">✏️ Diubah</span>
                        @else
                            <span style="color: #28a745;">✅ Sama</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        @if($nominal_lama != $nominal_baru)
        <div class="nominal-summary">
            <h3 style="margin-top: 0;">💰 Ringkasan Perubahan Nominal</h3>
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="border: none; padding: 8px 0;"><strong>Nominal Sebelum:</strong></td>
                    <td style="border: none; padding: 8px 0; text-align: right;">
                        Rp ({{ number_format($nominal_lama, 0, ',', '.') }})
                    </td>
                </tr>
                <tr>
                    <td style="border: none; padding: 8px 0;"><strong>Nominal Setelah:</strong></td>
                    <td style="border: none; padding: 8px 0; text-align: right;">
                        <strong>Rp ({{ number_format($nominal_baru, 0, ',', '.') }})</strong>
                    </td>
                </tr>
                <tr>
                    <td style="border: none; padding: 8px 0;"><strong>Selisih:</strong></td>
                    <td style="border: none; padding: 8px 0; text-align: right;">
                        @if($selisih_nominal > 0)
                            <span class="change-down">+Rp ({{ number_format($selisih_nominal, 0, ',', '.') }}) (Naik)</span>
                        @elseif($selisih_nominal < 0)
                            <span class="change-up ">-Rp ({{ number_format(abs($selisih_nominal), 0, ',', '.') }}) (Turun)</span>
                        @else
                            <span class="change-neutral">Rp (0) (Tidak Berubah)</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        @endif

        @if($catatan_intervensi)
        <div class="info-box">
            <h3 style="margin-top: 0;">💬 Catatan dari Finance</h3>
            <h4 style="font-style: italic; margin: 0;">
                "{{ $catatan_intervensi }}"
            </h4>
        </div>
        @endif

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/pengajuan/detail/' . $pengajuan->id) }}" class="btn">
                👁️ Lihat Detail Pengajuan
            </a>
            <a href="{{ url('/dashboard') }}" class="btn" style="background: #28a745;">
                🏠 Ke Dashboard
            </a>
        </div>

        <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; margin-top: 20px;">
            <p style="margin: 0; font-size: 14px; color: #6c757d;">
                <strong>ℹ️ Informasi:</strong><br>
                • Perubahan ini dilakukan oleh tim Finance untuk keperluan penyesuaian anggaran<br>
                • Pengajuan Anda akan tetap diproses sesuai alur approval yang berlaku<br>
                • Jika ada pertanyaan, silakan hubungi tim Finance
            </p>
        </div>
    </div>

    <div class="footer">
        <p>
            Email ini dikirim secara otomatis oleh sistem.<br>
            Jangan membalas email ini. Untuk pertanyaan, hubungi administrator sistem.
        </p>
        <p style="font-size: 12px; color: #adb5bd;">
            © {{ date('Y') }} Sistem Pengajuan. Semua hak dilindungi.
        </p>
    </div>
</body>
</html>