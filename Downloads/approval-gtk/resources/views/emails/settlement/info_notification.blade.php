<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Info: Settlement Diajukan</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #17a2b8; color: white; padding: 20px; text-align: center; }
        .content { background: #f8f9fa; padding: 20px; margin: 20px 0; }
        .info-box { background: white; border-left: 4px solid #17a2b8; padding: 15px; margin: 10px 0; }
        .footer { text-align: center; color: #666; font-size: 12px; margin-top: 20px; }
        .btn { display: inline-block; background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>ℹ️ Info: Settlement Diajukan</h2>
        </div>
        
        <div class="content">
            <p>Halo <strong>{{ $approver->nama }}</strong>,</p>
            
            <p>Untuk informasi Anda, terdapat settlement baru yang telah diajukan dan akan melalui proses approval.</p>
            
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
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Requester:</strong></td>
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $requester->nama }}</td>
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
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Posisi Anda:</strong></td>
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $stepInfo->step_name }} (Step {{ $stepInfo->urutan }})</td>
                    </tr>
                </table>
            </div>
            
            <div class="info-box" style="border-left-color: #6c757d;">
                <p>📋 <strong>Status:</strong> Settlement ini akan mencapai tahap Anda setelah approval dari tahap sebelumnya selesai.</p>
                <p><small>Anda akan mendapat notifikasi khusus ketika giliran Anda untuk melakukan approval.</small></p>
            </div>
        </div>
        
        <div class="footer">
            <p>Email ini dikirim secara otomatis sebagai informasi. Mohon tidak membalas email ini.</p>
            <p><small>{{ config('app.name') }} - {{ now()->format('Y') }}</small></p>
        </div>
    </div>
</body>
</html>