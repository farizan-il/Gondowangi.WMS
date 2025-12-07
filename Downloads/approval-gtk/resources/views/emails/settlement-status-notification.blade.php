<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Status Settlement</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #007bff;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .content {
            margin-bottom: 30px;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 10px 0;
        }
        .status-paid {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-proses {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info-box {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            color: #666;
            min-width: 140px;
        }
        .info-value {
            color: #333;
            flex: 1;
            text-align: right;
        }
        .catatan-box {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .catatan-box h4 {
            margin: 0 0 10px 0;
            color: #856404;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        .button {
            display: inline-block;
            padding: 12px 25px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 15px 0;
            font-weight: bold;
        }
        .button:hover {
            background-color: #0056b3;
        }
        @media (max-width: 600px) {
            .info-row {
                flex-direction: column;
            }
            .info-value {
                text-align: left;
                margin-top: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h2 style="margin: 0; color: #333;">Pengembalian Dana</h2>
        </div>

        <div class="content">
            <p>Halo <strong>{{ $settlement->pengajuan->requester->nama }}</strong>,</p>
            
            <p>Status settlement untuk pengajuan Anda telah diperbarui:</p>

            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">No. Settlement:</span>
                    <span class="info-value"><strong>{{ $settlement->nomor_settlement }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">No. Pengajuan:</span>
                    <span class="info-value">{{ $settlement->pengajuan->nomor_pengajuan }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Kategori:</span>
                    <span class="info-value">{{ $settlement->pengajuan->kategoriPengajuan->nama ?? 'N/A' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Total Pengembalian:</span>
                    <span class="info-value"><strong>Rp {{ number_format($settlement->selisih, 0, ',', '.') }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status Baru:</span>
                    <span class="info-value">
                        <span class="status-badge status-{{ $status }}">
                            @if($status === 'paid')
                                Dibayarkan
                            @elseif($status === 'proses')
                                Sedang Diproses
                            @elseif($status === 'rejected')
                                Ditolak
                            @else
                                {{ ucfirst($status) }}
                            @endif
                        </span>
                    </span>
                </div>
                @if($transactionRequest->tanggal_transfer)
                <div class="info-row">
                    <span class="info-label">Tanggal Transfer:</span>
                    <span class="info-value">{{ \Carbon\Carbon::parse($transactionRequest->tanggal_transfer)->format('d F Y') }}</span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Diproses oleh:</span>
                    <span class="info-value">{{ $financeName }} (Tim Finance)</span>
                </div>
            </div>

            @if($catatan)
            <div class="catatan-box">
                <h4>📝 Catatan dari Finance:</h4>
                <p style="margin: 0;">{{ $catatan }}</p>
            </div>
            @endif

            @if($status === 'paid')
                <div style="background-color: #d4edda; border: 1px solid #c3e6cb; padding: 20px; border-radius: 8px; margin: 20px 0;">
                    <h4 style="color: #155724; margin: 0 0 10px 0;">✅ Settlement Telah Dibayarkan</h4>
                    <p style="margin: 0; color: #155724;">
                        Dana settlement telah berhasil ditransfer ke rekening Anda. Silakan cek rekening bank Anda dalam beberapa saat.
                        @if($transactionRequest->bukti_transfer)
                        <br><em>Bukti transfer telah tersedia di sistem.</em>
                        @endif
                    </p>
                </div>
            @elseif($status === 'proses')
                <div style="background-color: #d1ecf1; border: 1px solid #bee5eb; padding: 20px; border-radius: 8px; margin: 20px 0;">
                    <h4 style="color: #0c5460; margin: 0 0 10px 0;">⏳ Settlement Sedang Diproses</h4>
                    <p style="margin: 0; color: #0c5460;">
                        Settlement Anda sedang dalam proses pembayaran oleh tim finance. Kami akan menginformasikan status selanjutnya segera.
                    </p>
                </div>
            @elseif($status === 'rejected')
                <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 20px; border-radius: 8px; margin: 20px 0;">
                    <h4 style="color: #721c24; margin: 0 0 10px 0;">❌ Settlement Ditolak</h4>
                    <p style="margin: 0; color: #721c24;">
                        Maaf, settlement Anda telah ditolak. Silakan periksa catatan dari tim finance di atas untuk informasi lebih lanjut.
                    </p>
                </div>
            @endif

            <p>Jika Anda memiliki pertanyaan mengenai status settlement ini, silakan hubungi tim finance kami.</p>
        </div>

        <div class="footer">
            <p><strong>Tim Finance</strong><br>
            Sistem Pengajuan Dana</p>
            <p style="font-size: 12px; color: #888; margin-top: 20px;">
                Email ini dikirim secara otomatis oleh sistem pada {{ now()->format('d F Y, H:i') }} WIB.<br>
                Mohon jangan membalas email ini.
            </p>
        </div>
    </div>
</body>
</html>