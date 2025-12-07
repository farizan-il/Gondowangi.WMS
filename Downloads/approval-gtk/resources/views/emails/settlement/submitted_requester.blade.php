<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Settlement Telah Diajukan</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #12713d; color: white; padding: 20px; text-align: center; border-radius: 10px; }
        .content { background: #f8f9fa; padding: 20px; margin: 20px 0; }
        .info-box { background: white; border-left: 4px solid #28a745; padding: 15px; margin: 10px 0; }
        .footer { text-align: center; color: #666; font-size: 12px; margin-top: 20px; }
        .btn { display: inline-block; background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Settlement Berhasil Diajukan</h2>
        </div>
        
        <div class="content">
            <p>Halo <strong>{{ $requester->nama }}</strong>,</p>
            
            <p>Settlement Anda telah berhasil diajukan dan masuk ke proses approval.</p>
            
            <div class="info-box">
                <h4>Detail Settlement:</h4>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>No. Settlement:</strong></td>
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $settlement->nomor_settlement }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>No. Pengajuan:</strong></td>
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $pengajuan->nomor_pengajuan }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Kategori:</strong></td>
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $pengajuan->kategoriPengajuan->nama }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Total Actual:</strong></td>
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $pengajuan->mata_uang }} {{ number_format($settlement->total_actual, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Selisih:</strong></td>
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;">
                            <span style="color: {{ $settlement->selisih > 0 ? '#28a745' : ($settlement->selisih < 0 ? '#dc3545' : '#6c757d') }};">
                                {{ $pengajuan->mata_uang }} {{ number_format($settlement->selisih, 0, ',', '.') }}
                                @if($settlement->selisih > 0)
                                    (Penghematan)
                                @elseif($settlement->selisih < 0)
                                    (Kelebihan)
                                @endif
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Tanggal Diajukan:</strong></td>
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ now()->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
            
            @if($settlement->selisih > 0 && $settlement->file_bukti_transfer)
            <div class="info-box" style="border-left-color: #28a745;">
                <p>✅ <strong>Bukti transfer sisa uang telah diupload</strong></p>
                <p>Tanggal transfer: {{ $settlement->tanggal_transfer ? \Carbon\Carbon::parse($settlement->tanggal_transfer)->format('d/m/Y') : '-' }}</p>
            </div>
            @endif
            
            <p><strong>Status Saat Ini:</strong> Menunggu approval dari atasan</p>
            <p>Settlement Anda akan diproses melalui {{ $settlement->total_step }} tahap approval sesuai dengan alur persetujuan yang berlaku.</p>
            
            <p>Anda akan mendapat notifikasi lebih lanjut setelah proses approval selesai.</p>
        </div>
        
        <div class="footer">
            <p>Email ini dikirim secara otomatis dari sistem. Mohon tidak membalas email ini.</p>
            <p><small>{{ config('app.name') }} - {{ now()->format('Y') }}</small></p>
        </div>
    </div>
</body>
</html>