<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Settlement Menunggu Approval</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #ffc107; color: #212529; padding: 20px; text-align: center; }
        .content { background: #f8f9fa; padding: 20px; margin: 20px 0; }
        .info-box { background: white; border-left: 4px solid #ffc107; padding: 15px; margin: 10px 0; }
        .urgent { background: #fff3cd; border-left-color: #ffc107; }
        .footer { text-align: center; color: #666; font-size: 12px; margin-top: 20px; }
        .btn { display: inline-block; background: #ffc107; color: #212529; padding: 12px 24px; text-decoration: none; border-radius: 4px; font-weight: bold; margin: 10px 5px; }
        .btn-approve { background: #28a745; color: white; }
        .btn-reject { background: #dc3545; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🔔 Settlement Menunggu Approval Anda</h2>
        </div>
        
        <div class="content">
            <p>Halo <strong>{{ $approver->nama }}</strong>,</p>
            
            <div class="info-box urgent">
                <h4>⚠️ ACTION REQUIRED</h4>
                <p>Terdapat settlement yang memerlukan approval Anda sebagai <strong>{{ $stepInfo->step_name }}</strong> (Step {{ $stepInfo->urutan }} dari {{ $settlement->total_step }}).</p>
            </div>
            
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
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $requester->nama }} ({{ $requester->department->nama ?? 'N/A' }})</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Kategori:</strong></td>
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $pengajuan->kategoriPengajuan->nama }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Judul:</strong></td>
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $pengajuan->judul }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Budget Original:</strong></td>
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $pengajuan->mata_uang }} {{ number_format($pengajuan->nominal_pengajuan, 0, ',', '.') }}</td>
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
                        <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{ $settlement->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
            
            @if($settlement->selisih > 0)
            <div class="info-box" style="border-left-color: {{ $settlement->file_bukti_transfer ? '#28a745' : '#ffc107' }};">
                <h4>Status Bukti Transfer:</h4>
                @if($settlement->file_bukti_transfer)
                    <p>✅ <strong>Bukti transfer telah diupload</strong></p>
                    <p>Tanggal transfer: {{ $settlement->tanggal_transfer ? \Carbon\Carbon::parse($settlement->tanggal_transfer)->format('d/m/Y') : '-' }}</p>
                    <p><small>Sisa uang sebesar {{ $pengajuan->mata_uang }} {{ number_format($settlement->selisih, 0, ',', '.') }} telah ditransfer kembali.</small></p>
                @else
                    <p>⚠️ <strong>Bukti transfer belum diupload</strong></p>
                    <p><small>Requester perlu mengupload bukti transfer untuk sisa uang sebesar {{ $pengajuan->mata_uang }} {{ number_format($settlement->selisih, 0, ',', '.') }}</small></p>
                @endif
            </div>
            @endif
            
            @if($settlement->catatan_settlement)
            <div class="info-box">
                <h4>Catatan Settlement:</h4>
                <p>"{{ $settlement->catatan_settlement }}"</p>
            </div>
            @endif
            
            <div style="text-align: center; margin: 20px 0;">
                <a href="{{ route('approval.settlement.index') }}" class="btn">
                    🔍 Lihat Detail & Proses Approval
                </a>
            </div>
            
            <p><small><strong>Catatan:</strong> Silakan login ke sistem untuk melihat detail lengkap dan melakukan approval/reject settlement ini.</small></p>
        </div>
        
        <div class="footer">
            <p>Email ini dikirim secara otomatis dari sistem. Mohon tidak membalas email ini.</p>
            <p><small>{{ config('app.name') }} - {{ now()->format('Y') }}</small></p>
        </div>
    </div>
</body>
</html>