<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Notifikasi Intervensi Settlement</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .content { background-color: #fff; padding: 20px; border: 1px solid #ddd; border-radius: 8px; }
        .intervention-item { background-color: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .summary { background-color: #e3f2fd; padding: 15px; border-radius: 5px; margin-top: 20px; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; font-size: 12px; color: #666; }
        .highlight { background-color: #fff3cd; padding: 2px 5px; border-radius: 3px; }
        .amount { font-weight: bold; color: #2c5530; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🔄 Notifikasi Intervensi Settlement</h2>
            <p><strong>Settlement pengajuan Anda telah diubah oleh departemen Finance</strong></p>
        </div>

        <div class="content">
            <h3>Detail Pengajuan</h3>
            <ul>
                <li><strong>Judul:</strong> {{ $pengajuan->judul_pengajuan }}</li>
                <li><strong>Kode Pengajuan:</strong> {{ $pengajuan->kode_pengajuan }}</li>
                <li><strong>Kategori:</strong> {{ $pengajuan->kategoriPengajuan->nama_kategori ?? '-' }}</li>
                <li><strong>Finance yang mengintervensi:</strong> {{ $finance_user }}</li>
                <li><strong>Tanggal Intervensi:</strong> {{ now()->format('d/m/Y H:i') }}</li>
            </ul>

            <h3>Detail Perubahan Settlement</h3>
            <p><strong>Catatan Intervensi:</strong></p>
            <div class="highlight">{{ $catatan_intervensi }}</div>

            <h4>Item yang Diubah ({{ $total_items_changed }} item):</h4>
            @foreach($interventions as $intervention)
            <div class="intervention-item">
                <p><strong>Detail Settlement ID:</strong> {{ $intervention['detail_settlement_id'] }}</p>
                
                @if($intervention['keterangan_awal'] != $intervention['keterangan_final'])
                <p><strong>Keterangan:</strong><br>
                   <span style="color: #dc3545;">Sebelum:</span> {{ $intervention['keterangan_awal'] ?? 'Kosong' }}<br>
                   <span style="color: #28a745;">Sesudah:</span> {{ $intervention['keterangan_final'] ?? 'Kosong' }}
                </p>
                @endif

                @if($intervention['nominal_awal'] != $intervention['nominal_final'])
                <p><strong>Nominal:</strong><br>
                   <span style="color: #dc3545;">Sebelum:</span> <span class="amount">Rp {{ number_format($intervention['nominal_awal'], 2, ',', '.') }}</span><br>
                   <span style="color: #28a745;">Sesudah:</span> <span class="amount">Rp {{ number_format($intervention['nominal_final'], 2, ',', '.') }}</span>
                </p>
                @endif

                @if($intervention['kategori_awal'] != $intervention['kategori_final'])
                <p><strong>Kategori Biaya:</strong><br>
                   <span style="color: #dc3545;">Sebelum:</span> {{ $intervention['kategori_awal'] ?? 'Kosong' }}<br>
                   <span style="color: #28a745;">Sesudah:</span> {{ $intervention['kategori_final'] ?? 'Kosong' }}
                </p>
                @endif
            </div>
            @endforeach

            <div class="summary">
                <h4>📊 Ringkasan Perubahan</h4>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td><strong>Total Actual Sebelum:</strong></td>
                        <td class="amount">Rp {{ number_format($total_actual_lama, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Total Actual Sesudah:</strong></td>
                        <td class="amount">Rp {{ number_format($total_actual_baru, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Perubahan Total:</strong></td>
                        <td class="amount" style="color: {{ ($total_actual_baru - $total_actual_lama) >= 0 ? '#28a745' : '#dc3545' }}">
                            {{ ($total_actual_baru - $total_actual_lama) >= 0 ? '+' : '' }}Rp {{ number_format($total_actual_baru - $total_actual_lama, 2, ',', '.') }}
                        </td>
                    </tr>
                    <tr style="border-top: 1px solid #ddd; margin-top: 10px;">
                        <td><strong>Selisih vs Pengajuan (Sebelum):</strong></td>
                        <td class="amount">Rp {{ number_format($selisih_lama, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Selisih vs Pengajuan (Sesudah):</strong></td>
                        <td class="amount">Rp {{ number_format($selisih_baru, 2, ',', '.') }}</td>
                    </tr>
                </table>
            </div>

            <p style="margin-top: 20px;">
                <strong>Tindakan Selanjutnya:</strong><br>
                Silakan login ke sistem untuk melihat detail lengkap settlement yang telah diubah dan melakukan tindakan yang diperlukan.
            </p>
        </div>

        <div class="footer">
            <p>
                <strong>Tim Finance</strong><br>
                Email ini dikirim secara otomatis oleh sistem. Mohon jangan membalas email ini.<br>
                Jika ada pertanyaan, silakan hubungi tim Finance melalui kanal komunikasi resmi.
            </p>
            <hr style="margin: 20px 0; border: none; border-top: 1px solid #dee2e6;">
            <p style="font-size: 12px;">
                © {{ date('Y') }} Sistem Pengajuan. All rights reserved.<br>
                Dikirim pada {{ date('d/m/Y H:i:s') }}
            </p>
        </div>
    </div>
</body>
</html>